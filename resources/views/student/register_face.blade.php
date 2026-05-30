@extends('layouts.app')

@section('content')
<div class="container-fluid mt-4">
    <div class="d-flex justify-content-center mb-4">
        <h3>Face Registration</h3>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body text-center">

                    @if(auth()->user()->face_registered)
                        <div class="alert alert-info">
                            <strong>✅ Face already registered!</strong> 
                            You can capture a new photo below to update your face data.
                        </div>
                    @endif

                    <div id="toast-container" class="mb-3"></div>

                    <!-- Webcam Wrapper -->
                    <div class="video-container mx-auto position-relative" style="width: 300px; height: 300px; overflow: hidden; border-radius: 10px; background: #000;">
                        <!-- Video Stream -->
                        <video id="videoElement" autoplay playsinline style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></video>
                        
                        <!-- Overlay Guide -->
                        <div class="overlay-guide position-absolute top-50 start-50 translate-middle" style="width: 200px; height: 250px; border: 4px dashed rgba(255,255,255,0.7); border-radius: 50%; box-shadow: 0 0 0 1000px rgba(0,0,0,0.5);"></div>

                        <!-- Canvas for captured photo (Hidden initially) -->
                        <canvas id="canvasElement" class="d-none position-absolute top-0 start-0" style="width: 100%; height: 100%; object-fit: cover; transform: scaleX(-1);"></canvas>
                    </div>

                    <div class="mt-4">
                        <button id="captureBtn" class="btn btn-primary px-4">📸 Capture Photo</button>
                        <button id="retakeBtn" class="btn btn-secondary px-4 d-none">🔄 Retake</button>
                        <button id="submitBtn" class="btn btn-success px-4 d-none">
                            <span id="submitSpinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                            ✅ Submit Face
                        </button>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const video = document.getElementById("videoElement");
        const canvas = document.getElementById("canvasElement");
        const overlay = document.querySelector(".overlay-guide");
        const captureBtn = document.getElementById("captureBtn");
        const retakeBtn = document.getElementById("retakeBtn");
        const submitBtn = document.getElementById("submitBtn");
        const submitSpinner = document.getElementById("submitSpinner");
        const toastContainer = document.getElementById("toast-container");

        let stream;
        let capturedBlob = null;

        // Initialize webcam
        async function startCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ video: true });
                video.srcObject = stream;
            } catch (err) {
                toastContainer.innerHTML = `<div class="alert alert-danger">❌ Cannot access camera: ${err.message}</div>`;
            }
        }

        startCamera();

        // Capture photo
        captureBtn.addEventListener("click", () => {
            // Limit resolution for extremely fast uploads while keeping enough quality for recognition
            const MAX_WIDTH = 640;
            let scale = 1;
            if (video.videoWidth > MAX_WIDTH) {
                scale = MAX_WIDTH / video.videoWidth;
            }
            
            canvas.width = video.videoWidth * scale;
            canvas.height = video.videoHeight * scale;
            
            const ctx = canvas.getContext("2d");
            ctx.translate(canvas.width, 0);
            ctx.scale(-1, 1);
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            canvas.classList.remove("d-none");
            video.classList.add("opacity-50"); // dim video behind canvas
            overlay.classList.add("d-none");

            captureBtn.classList.add("d-none");
            retakeBtn.classList.remove("d-none");
            submitBtn.classList.remove("d-none");

            // Store as blob
            canvas.toBlob((blob) => {
                capturedBlob = blob;
            }, "image/jpeg", 0.9);
        });

        // Retake
        retakeBtn.addEventListener("click", () => {
            canvas.classList.add("d-none");
            video.classList.remove("opacity-50");
            overlay.classList.remove("d-none");

            captureBtn.classList.remove("d-none");
            retakeBtn.classList.add("d-none");
            submitBtn.classList.add("d-none");
            capturedBlob = null;
        });

        // Submit to Laravel
        submitBtn.addEventListener("click", async () => {
            if (!capturedBlob) return;

            // UI loading state
            submitBtn.disabled = true;
            retakeBtn.disabled = true;
            submitSpinner.classList.remove("d-none");
            toastContainer.innerHTML = '';

            const formData = new FormData();
            formData.append("image", capturedBlob, "face.jpg");

            try {
                const response = await fetch("{{ route('student.face.register.store') }}", {
                    method: "POST",
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: formData
                });

                const data = await response.json();

                if (response.ok && data.success) {
                    toastContainer.innerHTML = `<div class="alert alert-success mt-3">✅ ${data.message}</div>`;
                    setTimeout(() => {
                        window.location.href = "{{ route('student.dashboard') }}";
                    }, 2000);
                } else {
                    toastContainer.innerHTML = `<div class="alert alert-danger mt-3">❌ ${data.message || 'Validation failed.'}</div>`;
                    submitBtn.disabled = false;
                    retakeBtn.disabled = false;
                    submitSpinner.classList.add("d-none");
                }

            } catch (err) {
                toastContainer.innerHTML = `<div class="alert alert-danger mt-3">❌ Upload failed: ${err.message}</div>`;
                submitBtn.disabled = false;
                retakeBtn.disabled = false;
                submitSpinner.classList.add("d-none");
            }
        });
    });
</script>

<style>
/* Add a nice backdrop filter effect or general smooth transition */
.btn { transition: all 0.3s; }
.card { border: none; border-radius: 12px; }
</style>
@endsection
