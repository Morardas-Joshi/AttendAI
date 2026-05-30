@extends('layouts.app')

@section('content')
<!-- Include QRCode.js CDN -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

<div class="container-fluid mt-4">
    <div class="row">
        <!-- Live Info & QR -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow border-0" style="border-radius: 16px;">
                <div class="card-body text-center p-4">
                    <h5 class="text-muted text-uppercase fw-bold mb-1">Live Session</h5>
                    <h4 class="mb-4">{{ $session->subject->subject_name ?? 'Unknown' }}</h4>

                    <div class="p-4 rounded mb-4 shadow-sm" style="background: #f8f9fa;">
                        <p class="text-muted small mb-1 text-uppercase fw-bold">Session Code</p>
                        <h1 class="display-4 fw-bold" style="letter-spacing: 5px; color: #0d6efd;" id="sessionCode">{{ $session->session_code }}</h1>
                        <button class="btn btn-sm btn-outline-secondary mt-2 w-50" onclick="copyCode()">Copy Code</button>
                    </div>

                    <div class="d-flex justify-content-center mb-4">
                        <!-- QR Code Container -->
                        <div id="qrcode" class="p-3 bg-white shadow-sm rounded border"></div>
                    </div>

                    <div id="statusBtnContainer">
                        @if($session->status === 'active')
                            <button class="btn btn-danger btn-lg w-100 fw-bold shadow-sm" id="endSessionBtn">🛑 End Session</button>
                        @else
                            <div class="alert alert-secondary">This session is closed.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Attendees List -->
        <div class="col-lg-8">
            <div class="card shadow border-0" style="border-radius: 16px; height: 100%;">
                <div class="card-header bg-white border-0 pt-4 pb-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="bi bi-people-fill text-primary"></i> Live Attendees (<span id="attendeeCount">{{ count($session->records) }}</span>)</h5>
                    <span class="badge bg-success" id="autoUpdateBadge">Auto-updating...</span>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive" style="max-height: 600px; overflow-y: auto;">
                        <table class="table table-hover align-middle m-0 text-nowrap" id="attendeesTable">
                            <thead class="table-light sticky-top">
                                <tr>
                                    <th class="ps-4">Student Name</th>
                                    <th>Roll No</th>
                                    <th>Marked At</th>
                                    <th>Distance</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Injected by JS -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        // Init QR Code (link to join page with pre-filled code if we passed it in URL, or just the code itself)
        const joinUrl = "{{ rtrim(config('app.url'), '/') }}/student/join?code={{ $session->session_code }}";
        new QRCode(document.getElementById("qrcode"), {
            text: joinUrl,
            width: 180,
            height: 180,
            colorDark : "#000000",
            colorLight : "#ffffff",
            correctLevel : QRCode.CorrectLevel.H
        });

        // Copy Code
        window.copyCode = function() {
            const code = document.getElementById("sessionCode").innerText;
            // Attempt modern clipboard API
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(code).then(() => alert("Copied: " + code));
            } else {
                // Fallback for non-https/IP access
                const textArea = document.createElement("textarea");
                textArea.value = code;
                document.body.appendChild(textArea);
                textArea.select();
                try {
                    document.execCommand('copy');
                    alert("Copied: " + code);
                } catch (err) {
                    alert("Manual Copy: " + code);
                }
                document.body.removeChild(textArea);
            }
        };

        // Live updating
        const sessionId = {{ $session->id }};
        const tbody = document.querySelector("#attendeesTable tbody");
        const countBadge = document.getElementById("attendeeCount");
        const autoUpdateBadge = document.getElementById("autoUpdateBadge");

        async function fetchAttendees() {
            try {
                const res = await fetch(`/faculty/sessions/${sessionId}/attendees`, {
                    headers: { 'Accept': 'application/json' }
                });
                const data = await res.json();
                
                if (data.success) {
                    countBadge.innerText = data.attendees.length;
                    tbody.innerHTML = '';
                    
                    data.attendees.forEach(student => {
                        let statusHtml = '';
                        if (student.status === 'present') {
                            statusHtml = `<span class="badge bg-success">Present</span>`;
                            if (!student.liveness_passed) {
                                statusHtml += ` <span class="badge bg-warning text-dark" title="Liveness bypassed">Bypass</span>`;
                            }
                        } else {
                            statusHtml = `<span class="badge bg-danger">Rejected</span>`;
                        }

                        // Format time (assuming ISO)
                        let timeObj = new Date(student.time);
                        let timeStr = timeObj.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});

                        tbody.innerHTML += `
                            <tr>
                                <td class="ps-4 fw-bold text-primary">${student.name}</td>
                                <td>${student.roll_no}</td>
                                <td>${timeStr}</td>
                                <td>${student.distance}</td>
                                <td>${statusHtml}</td>
                            </tr>
                        `;
                    });

                    if (data.attendees.length === 0) {
                        tbody.innerHTML = `<tr><td colspan="5" class="text-center py-4 text-muted">No attendees yet. Waiting...</td></tr>`;
                    }
                }
            } catch (err) {
                console.error("Failed to fetch attendees", err);
                autoUpdateBadge.classList.replace("bg-success", "bg-danger");
                autoUpdateBadge.innerText = "Connection lost";
            }
        }

        // Fetch immediately and set interval
        fetchAttendees();
        let interval = setInterval(fetchAttendees, 10000); // every 10s

        // End Session
        const endBtn = document.getElementById("endSessionBtn");
        if (endBtn) {
            endBtn.addEventListener("click", async () => {
                if(!confirm("Are you sure you want to end this session? Students will no longer be able to mark attendance.")) return;
                
                endBtn.disabled = true;
                endBtn.innerText = "Closing...";

                try {
                    const res = await fetch(`/faculty/sessions/${sessionId}/end`, {
                        method: "PATCH",
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        }
                    });
                    const data = await res.json();
                    
                    if(data.success) {
                        clearInterval(interval);
                        autoUpdateBadge.classList.replace("bg-success", "bg-secondary");
                        autoUpdateBadge.innerText = "Session Closed";
                        document.getElementById("statusBtnContainer").innerHTML = `<div class="alert alert-secondary text-center">This session is closed.</div>`;
                    } else {
                        endBtn.disabled = false;
                        endBtn.innerText = "🛑 End Session";
                        alert("Error: " + data.message);
                    }
                } catch(err) {
                    endBtn.disabled = false;
                    endBtn.innerText = "🛑 End Session";
                    alert("Network error.");
                }
            });
        }
    });
</script>
@endsection