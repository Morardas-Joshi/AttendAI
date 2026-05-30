@extends('layouts.app')

@section('content')
<!-- Leaflet CSS for Maps -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
<style>
    #map { height: 250px; border-radius: 8px; z-index: 1; }
    .leaflet-control-attribution { display: none; }
</style>

<div class="container-fluid mt-4">
    
    <div class="d-flex justify-content-between mb-4">
        <h3>Create Attendance Session</h3>
        <a href="{{ route('faculty.sessions') }}" class="btn btn-outline-secondary">← Back to Sessions</a>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow border-0" style="border-radius: 12px;">
                <div class="card-body p-4">
                    
                    @if($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach($errors->all() as $err)
                                    <li>{{ $err }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('faculty.sessions.store') }}" method="POST">
                        @csrf
                        
                        <!-- Subject -->
                        <div class="mb-4">
                            <label class="form-label fw-bold">Select Subject</label>
                            <select name="subject_id" class="form-select form-select-lg" required>
                                <option value="" disabled selected>-- Choose Subject --</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">
                                        {{ $subject->subject_name }} 
                                        ({{ $subject->class->course_name ?? 'N/A' }} 
                                         | Year {{ $subject->class->year ?? '-' }}, Sem {{ $subject->class->semester ?? '-' }} 
                                         | Div: {{ $subject->class->division ?? '-' }})
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Location & Geofencing -->
                        <div class="mb-4 p-3 bg-light rounded border">
                            <label class="form-label fw-bold"><i class="bi bi-geo-alt-fill text-danger"></i> Geofencing Options</label>
                            <p class="text-muted small mb-2">Require students to be within a certain radius of your current location.</p>
                            
                            <div class="d-grid mb-3">
                                <button type="button" id="getLocationBtn" class="btn btn-outline-primary">
                                    <span id="locSpinner" class="spinner-border spinner-border-sm d-none"></span> 
                                    📍 Use My Current Location
                                </button>
                            </div>

                            <div id="locStatus" class="small text-center fw-bold mb-3"></div>

                            <!-- Interactive Map -->
                            <div id="map" class="mb-3 d-none"></div>

                            <!-- Hidden coords -->
                            <input type="hidden" name="lat" id="latInput" value="">
                            <input type="hidden" name="lon" id="lonInput" value="">
                            
                            <!-- Radius Slider -->
                            <label class="form-label fw-bold mt-2">Allowed Radius: <span class="text-primary" id="radiusVal">30</span> meters <br><small class="text-muted fw-normal">(Adjust to see circle on map change)</small></label>
                            <input type="range" class="form-range" name="radius_meters" min="10" max="200" step="5" value="30" id="radiusSlider">
                        </div>

                        <button type="submit" class="btn btn-success btn-lg w-100 fw-bold shadow-sm">🚀 Start Session</button>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", () => {
        const getLocBtn = document.getElementById("getLocationBtn");
        const latInput = document.getElementById("latInput");
        const lonInput = document.getElementById("lonInput");
        const locStatus = document.getElementById("locStatus");
        const locSpinner = document.getElementById("locSpinner");
        const radiusSlider = document.getElementById("radiusSlider");
        const radiusVal = document.getElementById("radiusVal");
        const mapDiv = document.getElementById("map");

        let map = null;
        let marker = null;
        let circle = null;

        function updateMapAndInputs(lat, lng, radius) {
            latInput.value = lat;
            lonInput.value = lng;
            radiusVal.innerText = radius;

            if (!map) {
                mapDiv.classList.remove("d-none");
                // Initialize map
                map = L.map('map').setView([lat, lng], 18);
                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    maxZoom: 19
                }).addTo(map);

                // Add draggable marker
                marker = L.marker([lat, lng], {draggable: true}).addTo(map);
                
                // When dragged, update coordinates and redraw circle
                marker.on('dragend', function (e) {
                    let position = marker.getLatLng();
                    updateMapAndInputs(position.lat, position.lng, radiusSlider.value);
                });
            } else {
                map.setView([lat, lng], map.getZoom());
                marker.setLatLng([lat, lng]);
            }

            // Draw or update radius circle
            if (circle) { map.removeLayer(circle); }
            circle = L.circle([lat, lng], {
                color: 'red',
                fillColor: '#f03',
                fillOpacity: 0.2,
                radius: parseInt(radius)
            }).addTo(map);
        }

        // Live slider update
        radiusSlider.addEventListener('input', function() {
            let lat = latInput.value;
            let lng = lonInput.value;
            if(lat && lng) {
                updateMapAndInputs(lat, lng, this.value);
            } else {
                radiusVal.innerText = this.value;
            }
        });

        getLocBtn.addEventListener("click", () => {
            locSpinner.classList.remove("d-none");
            getLocBtn.disabled = true;
            locStatus.innerHTML = "Requesting location...";
            locStatus.className = "small text-center text-muted mb-3";

            navigator.geolocation.getCurrentPosition((pos) => {
                locSpinner.classList.add("d-none");
                getLocBtn.disabled = false;
                getLocBtn.classList.replace("btn-outline-primary", "btn-success");
                
                locStatus.innerHTML = "<span class='text-success'>✅ Location acquired! <br>You can drag the blue marker to precisely locate the classroom.</span>";
                
                updateMapAndInputs(pos.coords.latitude, pos.coords.longitude, radiusSlider.value);

            }, (err) => {
                locSpinner.classList.add("d-none");
                getLocBtn.disabled = false;
                
                // Fallback location if GPS fails (New Delhi or any central coordinate)
                let fallbackLat = 28.6139;
                let fallbackLng = 77.2090;
                
                locStatus.innerHTML = `<span class='text-danger'>❌ Location failed: ${err.message}. <br>A default map is loaded. Drag the marker to the college manually!</span>`;
                updateMapAndInputs(fallbackLat, fallbackLng, radiusSlider.value);

            }, { enableHighAccuracy: true });
        });
    });
</script>
@endsection