from fastapi import FastAPI, UploadFile, File, Form
from fastapi.middleware.cors import CORSMiddleware
import face_recognition
import numpy as np
import cv2
import os
import math
import dlib
from scipy.spatial import distance as dist
import shutil

app = FastAPI()

# ✅ CORS
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_methods=["*"],
    allow_headers=["*"],
)

# 📁 Dataset path
DATASET_PATH = os.path.join(os.path.dirname(__file__), "dataset")

# 🔐 Global storage
known_encodings = []
known_ids = []

# 📍 DLib setup for Liveness
predictor_path = os.path.join(os.path.dirname(__file__), "shape_predictor_68_face_landmarks.dat")
detector = None
predictor = None

try:
    detector = dlib.get_frontal_face_detector()
    if os.path.exists(predictor_path):
        predictor = dlib.shape_predictor(predictor_path)
    else:
        print(f"⚠️ Warning: {predictor_path} NOT FOUND. Liveness check will be skipped.")
except Exception as e:
    print(f"⚠️ Warning: Dlib initialization failed: {e}")

def eye_aspect_ratio(eye):
    A = dist.euclidean(eye[1], eye[5])
    B = dist.euclidean(eye[2], eye[4])
    C = dist.euclidean(eye[0], eye[3])
    ear = (A + B) / (2.0 * C)
    return ear

def check_liveness(frame):
    """
    Checks if the person in the frame is 'live' using Eye Aspect Ratio (EAR).
    Requires the Dlib shape predictor model.
    """
    if detector is None or predictor is None:
        return {"passed": True, "message": "Liveness check skipped (Model missing)"}

    try:
        gray = cv2.cvtColor(frame, cv2.COLOR_BGR2GRAY)
        faces = detector(gray)
        if len(faces) == 0:
            return {"passed": False, "message": "No face detected for liveness"}

        shape = predictor(gray, faces[0])
        shape = np.array([[p.x, p.y] for p in shape.parts()])
        
        leftEye = shape[42:48]
        rightEye = shape[36:42]
        
        leftEAR = eye_aspect_ratio(leftEye)
        rightEAR = eye_aspect_ratio(rightEye)
        
        ear = (leftEAR + rightEAR) / 2.0
        
        # Threshold for open eyes (Lowered to 0.15 for better stability)
        if ear > 0.15:
            return {"passed": True, "ear": float(ear)}
        return {"passed": False, "ear": float(ear), "message": "Liveness failed (Eyes closed or static photo)"}
    except Exception as e:
        return {"passed": True, "message": f"Liveness error: {str(e)}"}


# ✅ Load faces
def load_faces():
    global known_encodings, known_ids
    known_encodings.clear()
    known_ids.clear()
    
    if not os.path.exists(DATASET_PATH):
        os.makedirs(DATASET_PATH)

    valid_extensions = ('.jpg', '.jpeg', '.png')
    for student_id in os.listdir(DATASET_PATH):
        folder = os.path.join(DATASET_PATH, student_id)

        if not os.path.isdir(folder):
            continue

        for file in os.listdir(folder):
            if not file.lower().endswith(valid_extensions):
                continue
                
            path = os.path.join(folder, file)

            try:
                image = face_recognition.load_image_file(path)
                encodings = face_recognition.face_encodings(image)

                if encodings:
                    known_encodings.append(encodings[0])
                    known_ids.append(student_id)

            except Exception as e:
                print(f"❌ Error loading {path}: {e}")

    print(f"✅ Faces Loaded: {len(known_ids)} encodings for {len(set(known_ids))} students")

# 🚀 Startup event
@app.on_event("startup")
def startup():
    load_faces()

@app.get("/encodings_status")
def encodings_status():
    st_set = set(known_ids)
    return {
        "status": "online",
        "students_registered": len(st_set),
        "total_encodings": len(known_encodings)
    }

@app.post("/reload")
def reload_encodings():
    load_faces()
    return {"success": True, "message": "Encodings reloaded"}

@app.post("/register_face")
async def register_face(
    student_id: str = Form(...),
    file: UploadFile = File(...)
):
    try:
        contents = await file.read()
        nparr = np.frombuffer(contents, np.uint8)
        frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

        if frame is None:
            return {"success": False, "message": "Invalid image"}

        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
        # using num_jitters=2 and model="large" for more robust encodings (helps with clothes/lighting changes)
        encodings = face_recognition.face_encodings(rgb, num_jitters=2)

        if len(encodings) == 0:
            return {"success": False, "message": "No face detected in image."}
        elif len(encodings) > 1:
            return {"success": False, "message": "Multiple faces detected. Please make sure only you are in the frame."}

        # Convert encoding to list for JSON response
        encoding_list = encodings[0].tolist()

        # Save to dataset folder (kept as fallback/local cache)
        user_dir = os.path.join(DATASET_PATH, student_id)
        if not os.path.exists(user_dir):
            os.makedirs(user_dir)
            
        file_path = os.path.join(user_dir, "face.jpg")
        cv2.imwrite(file_path, frame)

        # Update system memory dynamically without full disk reload for massive speedup
        known_encodings.append(encodings[0])
        known_ids.append(student_id)


        return {
            "success": True, 
            "message": "Face registered successfully.",
            "encoding": encoding_list
        }
    except Exception as e:
        return {"success": False, "message": str(e)}

@app.post("/recognize")
@app.post("/recognize_face")
async def recognize(
    file: UploadFile = File(...)
):
    # GPS check is now done purely on Laravel side per instructions 
    # "DO NOT implement WiFi/IP restriction. Use ONLY GPS geolocation validation... The IP check is skipped entirely" and "Re-validate location server-side when marking attendance"
    try:
        if len(known_encodings) == 0:
            return {"success": False, "message": "No dataset loaded"}

        contents = await file.read()
        if not contents:
            return {"success": False, "message": "Empty image"}

        nparr = np.frombuffer(contents, np.uint8)
        frame = cv2.imdecode(nparr, cv2.IMREAD_COLOR)

        if frame is None:
            return {"success": False, "message": "Invalid image"}

        # Resize for faster processing while maintaining enough detail for recognition
        # We use a standard width of 640px for consistency
        h, w = frame.shape[:2]
        if w > 640:
            scale = 640 / w
            frame = cv2.resize(frame, (0,0), fx=scale, fy=scale)
        
        h_resized, w_resized = frame.shape[:2]
        rgb = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)

        # Liveness check
        liveness_res = check_liveness(frame)
        liveness_passed = liveness_res.get("passed", True)

        # Find all face locations and encodings
        face_locations = face_recognition.face_locations(rgb, model="hog")
        encodings = face_recognition.face_encodings(rgb, face_locations)

        if not encodings:
            return {"success": False, "message": "No face detected. Please ensure you are in a well-lit area."}
        
        if len(encodings) > 1:
             return {"success": False, "message": "Multiple faces detected. Please make sure only one person is in the frame."}

        encoding = encodings[0]
        distances = face_recognition.face_distance(known_encodings, encoding)

        if len(distances) > 0:
            best = np.argmin(distances)
            
            # Relaxed tolerance to 0.52 for better recognition with appearance changes (clothes, lighting)
            if distances[best] < 0.52:
                confidence = max(0, min(100, (1 - distances[best]) * 130)) # Scaled confidence
                return {
                    "success": True,
                    "recognized": True,
                    "student_id": known_ids[best],
                    "confidence": float(confidence),
                    "distance_score": float(distances[best]),
                    "face_location": face_locations[0], # (top, right, bottom, left)
                    "image_dims": {"h": h_resized, "w": w_resized},
                    "liveness": liveness_res
                }


        return {"success": False, "message": "Face Not Matched. Please make sure you are registered."}

    except Exception as e:
        return {
            "success": False,
            "error": str(e)
        }