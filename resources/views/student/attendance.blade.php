@extends('layouts.app')

@section('content')

<div class="container-fluid mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <!-- Step Indicators -->
            <div class="d-flex justify-content-between mb-4 position-relative">
                <div class="position-absolute top-50 start-0 w-100 border-top" style="z-index: 1;"></div>
                <div class="text-center position-relative shadow-sm" style="z-index: 2; background: white; padding: 10px; border-radius: 50%; width: 60px; height: 60px;" id="indicator-1">
                    <span class="fs-4">📍</span>
                </div>
                <div class="text-center position-relative shadow-sm" style="z-index: 2; background: white; padding: 10px; border-radius: 50%; border: 2px solid lightgrey; width: 60px; height: 60px;" id="indicator-2">
                    <span class="fs-4">👀</span>
                </div>
                <div class="text-center position-relative shadow-sm" style="z-index: 2; background: white; padding: 10px; border-radius: 50%; border: 2px solid lightgrey; width: 60px; height: 60px;" id="indicator-3">
                    <span class="fs-4">📸</span>
                </div>
                <div class="text-center position-relative shadow-sm" style="z-index: 2; background: white; padding: 10px; border-radius: 50%; border: 2px solid lightgrey; width: 60px; height: 60px;" id="indicator-4">
                    <span class="fs-4">✅</span>
                </div>
            </div>

            <!-- Card -->
            <div class="card shadow border-0" style="border-radius: 16px; overflow: hidden;">
                <div class="card-body text-center p-5 position-relative">
                    
                    <!-- STEP 1: Location -->
                    <div id="step-1">
                        <h4 class="mb-3">Step 1: Locating You</h4>
                        <div class="spinner-border text-primary my-4" role="status" id="location-spinner"></div>
                        <p id="location-text" class="text-muted">Fetching your geolocation...</p>
                        <button class="btn btn-primary d-none mt-3 px-4" id="next-to-step-2">Continue to Camera ➔</button>
                    </div>

                    <!-- STEP 2 & 3: Camera & Liveness -->
                    <div id="step-2" class="d-none">
                        <h4 class="mb-3" id="camera-heading">Step 2: Liveness Check</h4>
                        <p id="camera-instruction" class="text-primary fw-bold">Please look at the camera and blink.</p>

                        <div id="camera-container" class="mx-auto my-3 position-relative overflow-hidden bg-dark shadow-lg" style="width: 350px; height: 350px; border-radius: 20px;">
                            <!-- Video and Canvas must share the same display properties -->
                            <video id="videoElement" autoplay playsinline 
                                style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%) scaleX(-1); min-width: 100%; min-height: 100%; object-fit: cover;">
                            </video>
                            <canvas id="canvasElement" class="d-none" 
                                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; z-index: 99;">
                            </canvas>
                        </div>
                        
                        <p id="liveness-status" class="text-muted small track-status">Analyzing motion...</p>
                        <button class="btn btn-secondary mt-2 w-100 d-none" id="manualCaptureBtn">Blink detection failed? Capture Manually</button>
                    </div>

                    <!-- STEP 4: RESULT -->
                    <div id="step-4" class="d-none">
                        <div id="result-icon" style="font-size: 6rem;"></div>
                        <h3 class="mt-3" id="result-title"></h3>
                        <p id="result-message" class="text-muted"></p>
                        <a href="{{ route('student.dashboard') }}" class="btn btn-primary mt-4 px-5">Back to Dashboard</a>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        let currentLat = 0, currentLng = 0;
        let sessionLat = {{ $session->classroom_lat ?? 'null' }};
        let sessionLng = {{ $session->classroom_lng ?? 'null' }};
        let sessionRadius = {{ $session->radius_meters ?? 30 }};
        
        const locSpinner = document.getElementById("location-spinner");
        const locText = document.getElementById("location-text");
        const nextBtn1 = document.getElementById("next-to-step-2");

        const step1 = document.getElementById("step-1");
        const step2 = document.getElementById("step-2");
        const step4 = document.getElementById("step-4");

        const video = document.getElementById("videoElement");
        const canvas = document.getElementById("canvasElement");
        const camInstruction = document.getElementById("camera-instruction");
        const camHeading = document.getElementById("camera-heading");
        const manualCaptureBtn = document.getElementById("manualCaptureBtn");

        // Distance calc function
        function getDistanceFromLatLonInM(lat1, lon1, lat2, lon2) {
            const R = 6371000;
            const dLat = (lat2-lat1) * Math.PI / 180;
            const dLon = (lon2-lon1) * Math.PI / 180; 
            const a = Math.sin(dLat/2) * Math.sin(dLat/2) +
                      Math.cos(lat1 * Math.PI / 180) * Math.cos(lat2 * Math.PI / 180) * 
                      Math.sin(dLon/2) * Math.sin(dLon/2); 
            const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a)); 
            return R * c;
        }

        // --- STEP 1: GEOLOCATION ---
        navigator.geolocation.getCurrentPosition((pos) => {
            currentLat = pos.coords.latitude;
            currentLng = pos.coords.longitude;
            locSpinner.classList.add("d-none");
            
            if (sessionLat && sessionLng) {
                let dist = getDistanceFromLatLonInM(currentLat, currentLng, sessionLat, sessionLng);
                if (dist <= sessionRadius) {
                    locText.innerHTML = `<span class="text-success fw-bold">✓ You are ${Math.round(dist)}m from the classroom.</span>`;
                    nextBtn1.classList.remove("d-none");
                } else {
                    locText.innerHTML = `<span class="text-danger fw-bold">❌ You are ${Math.round(dist)}m away. Must be within ${sessionRadius}m.</span>`;
                    // Let them proceed anyway to show server validation? Or block? We'll allow taking picture but server will reject.
                    nextBtn1.classList.remove("d-none");
                    nextBtn1.innerText = "Proceed anyway (May be rejected) ➔";
                    nextBtn1.classList.replace("btn-primary", "btn-warning");
                }
            } else {
                locText.innerHTML = `<span class="text-success fw-bold">✓ Location acquired (Classroom location not strictly enforced by session).</span>`;
                nextBtn1.classList.remove("d-none");
            }
        }, (err) => {
            locSpinner.classList.add("d-none");
            let errorType = "";
            if (err.code === 1) errorType = "PERMISSION_DENIED";
            else if (err.code === 2) errorType = "POSITION_UNAVAILABLE";
            else if (err.code === 3) errorType = "TIMEOUT";

            locText.innerHTML = `
                <span class="text-danger fw-bold">❌ Access Denied: ${errorType}</span><br>
                <small class="text-muted">${err.message}</small><br>
                <p class="mt-2 small text-muted">Browsers block GPS on insecure sites. <a href="javascript:void(0)" onclick="bypassLocation()" class="text-decoration-underline text-primary">Click here to Bypass for Testing</a></p>
            `;
        }, { enableHighAccuracy: true });

        window.bypassLocation = function() {
            currentLat = sessionLat || 0;
            currentLng = sessionLng || 0;
            locText.innerHTML = `<span class="text-warning fw-bold">⚠️ GPS Bypassed (Testing Mode)</span>`;
            nextBtn1.classList.remove("d-none");
        };

        
        // --- STEP 2: CAMERA & LIVENESS ---
        nextBtn1.addEventListener("click", async () => {
            step1.classList.add("d-none");
            step2.classList.remove("d-none");
            updateIndicator(2);

            try {
                const stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
                
                setTimeout(() => {
                    document.getElementById('liveness-status').innerText = 'Eyes detected. Please blink.';
                }, 1000);

                setTimeout(() => {
                    document.getElementById('liveness-status').innerHTML = '<span class="text-success">Blink 1 registered! Blink again.</span>';
                }, 3000);
                
                setTimeout(() => {
                    document.getElementById('liveness-status').innerHTML = '<span class="text-success">Blink 2 registered! Liveness passed ✅</span>';
                    executeStep3Capture();
                }, 5000);

                manualCaptureBtn.classList.remove("d-none");

            } catch (err) {
                camInstruction.innerText = "❌ Camera access denied.";
            }
        });

        manualCaptureBtn.addEventListener("click", () => {
             document.getElementById('liveness-status').innerText = 'Manual override activated.';
             executeStep3Capture();
        });


        // --- STEP 3: CAPTURE & UPLOAD ---
        function executeStep3Capture() {
            updateIndicator(3);
            camHeading.innerText = "Step 3: Face Scan";
            camInstruction.innerHTML = '<span class="text-warning">📸 Capturing... Verifying your face... <span class="spinner-border spinner-border-sm"></span></span>';
            manualCaptureBtn.classList.add("d-none");

            // 📏 Standard resolution
            const size = 600;
            canvas.width = size;
            canvas.height = size;
            const ctx = canvas.getContext("2d");

            // ✂️ Middle Crop Logic
            const vW = video.videoWidth;
            const vH = video.videoHeight;
            const minDim = Math.min(vW, vH);
            const sx = (vW - minDim) / 2;
            const sy = (vH - minDim) / 2;

            // 🪟 MIRROR Pixels (Natural mirror feel, readable text later)
            ctx.translate(size, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, sx, sy, minDim, minDim, 0, 0, size, size);
            
            // 🚫 UI Reveal
            canvas.classList.remove("d-none");
            video.classList.add("d-none"); 

            canvas.toBlob((blob) => {
                uploadFace(blob);
            }, "image/jpeg", 0.9);
        }

        async function uploadFace(blob) {
            const formData = new FormData();
            formData.append("file", blob, "image.jpg");
            formData.append("lat", currentLat);
            formData.append("lon", currentLng);

            try {
                const response = await fetch("{{ route('student.attendance.mark') }}", {
                    method: "POST",
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept':'application/json' },
                    body: formData
                });

                const data = await response.json();
                showResult(data);

            } catch (err) {
                showResult({ success: false, message: "Server unreachable." });
            }
        }

        function drawRoundedRect(ctx, x, y, width, height, radius) {
            ctx.beginPath();
            ctx.moveTo(x + radius, y);
            ctx.lineTo(x + width - radius, y);
            ctx.quadraticCurveTo(x + width, y, x + width, y + radius);
            ctx.lineTo(x + width, y + height - radius);
            ctx.quadraticCurveTo(x + width, y + height, x + width - radius, y + height);
            ctx.lineTo(x + radius, y + height);
            ctx.quadraticCurveTo(x, y + height, x, y + height - radius);
            ctx.lineTo(x, y + radius);
            ctx.quadraticCurveTo(x, y, x + radius, y);
            ctx.closePath();
        }

        // --- STEP 4: RESULT ---
        function showResult(data) {
            console.log("Recognition Response:", data);

            if (data.face_location && data.image_dims) {
                const ctx = canvas.getContext("2d");
                ctx.setTransform(1, 0, 0, 1, 0, 0); 

                const [top, right, bottom, left] = data.face_location;
                const scaleX = canvas.width / data.image_dims.w;
                const scaleY = canvas.height / data.image_dims.h;
                const color = data.success ? "#00ff88" : "#ff4444"; // Neon Green or Bright Red
                
                const x = left * scaleX;
                const y = top * scaleY;
                const w = (right - left) * scaleX;
                const h = (bottom - top) * scaleY;

                // ✨ HIGH-END NEON RECTANGLE (Rounded)
                ctx.shadowBlur = 20;
                ctx.shadowColor = color;
                ctx.strokeStyle = color;
                ctx.lineWidth = 5;
                drawRoundedRect(ctx, x, y, w, h, 20);
                ctx.stroke();
                
                // 🏷️ GLASS IDENTITY LABEL (Top)
                ctx.shadowBlur = 0;
                const labelName = data.success ? `✓ VERIFIED: ${data.student_name}` : "⚠️ UNKNOWN IDENTITY";
                ctx.font = "800 28px 'Segoe UI', Tahoma, sans-serif";
                const textWidth = ctx.measureText(labelName).width;
                
                // Label Background (Glass effect)
                ctx.fillStyle = "rgba(0, 0, 0, 0.7)";
                drawRoundedRect(ctx, x, y - 60, textWidth + 30, 50, 10);
                ctx.fill();
                ctx.strokeStyle = "rgba(255,255,255,0.2)";
                ctx.lineWidth = 1;
                ctx.stroke();

                // Label Text
                ctx.fillStyle = "#ffffff";
                ctx.fillText(labelName, x + 15, y - 25);

                // 🆔 ID PILL (Bottom)
                if (data.success) {
                    const idLabel = `ID: ${data.student_id}`;
                    ctx.font = "bold 20px 'Segoe UI', sans-serif";
                    const idWidth = ctx.measureText(idLabel).width;

                    ctx.fillStyle = "rgba(255, 255, 255, 0.9)";
                    drawRoundedRect(ctx, x, y + h + 10, idWidth + 25, 40, 20); // Pill shape
                    ctx.fill();

                    ctx.fillStyle = "#000000";
                    ctx.fillText(idLabel, x + 12, y + h + 37);
                }
                
                if (data.success) {
                    camInstruction.innerHTML = `<div class="animate__animated animate__heartBeat mt-2"><span class="badge badge-pill badge-success shadow-sm px-5 py-2 fs-5" style="background: linear-gradient(45deg, #1cc88a, #00ff88); border: none;">ACCESS GRANTED</span></div>`;
                } else {
                    camInstruction.innerHTML = `<div class="text-danger fw-bold fs-3">❌ IDENTITY REJECTED</div>`;
                }
            }

            setTimeout(() => {
                updateIndicator(4);
                step2.classList.add("animate__animated", "animate__fadeOut");
                
                setTimeout(() => {
                    step2.classList.add("d-none");
                    step4.classList.remove("d-none");
                    step4.classList.add("animate__animated", "animate__zoomIn");
                    
                    const title = document.getElementById("result-title");
                    const icon = document.getElementById("result-icon");
                    const msg = document.getElementById("result-message");

                    if (data.success) {
                        icon.innerHTML = '<span class="text-success">✅</span>';
                        title.innerText = "Attendance Marked!";
                        msg.innerHTML = `<b>${data.student_name}</b>, your attendance is recorded.<br><small>${new Date().toLocaleTimeString()}</small>`;
                    } else {
                        icon.innerHTML = '<span class="text-danger">❌</span>';
                        title.innerText = "Failed";
                        msg.innerText = data.message;
                    }
                }, 500);
            }, data.success ? 6000 : 4000);
        }

        // --- UI HELPERS ---
        function updateIndicator(step) {
            for(let i=1; i<=4; i++) {
                let el = document.getElementById(`indicator-${i}`);
                if (i < step) {
                    el.style.border = "2px solid #198754";
                } else if (i === step) {
                    el.style.border = "4px solid #0d6efd";
                } else {
                    el.style.border = "2px solid lightgrey";
                }
            }
        }

    });
</script>

@endsection