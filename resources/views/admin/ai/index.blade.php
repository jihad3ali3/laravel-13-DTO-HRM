@extends('layouts.admin-layout')

@section('title', 'AI HR Assistant Suite')

@section('container')
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="mb-1"><i class="icon-base ti tabler-sparkles text-primary me-2"></i>AI HR Assistant Suite</h4>
            <p class="mb-0 text-muted">Leverage artificial intelligence for policy inquiries, drafting communications, generating appraisals, and resume screening</p>
        </div>
    </div>

    <!-- AI Feature Tabs -->
    <div class="nav-align-top mb-6">
        <ul class="nav nav-pills mb-4" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#tab-chat" type="button" role="tab">
                    <i class="icon-base ti tabler-message-chatbot me-1"></i> Policy Q&amp;A Chat
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-announcement" type="button" role="tab">
                    <i class="icon-base ti tabler-pencil-plus me-1"></i> Notice Drafter
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-review" type="button" role="tab">
                    <i class="icon-base ti tabler-star me-1"></i> Appraisal Feedback Generator
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-receipt" type="button" role="tab">
                    <i class="icon-base ti tabler-receipt-2 me-1"></i> Receipt OCR Scanner
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" data-bs-toggle="tab" data-bs-target="#tab-resume" type="button" role="tab">
                    <i class="icon-base ti tabler-file-analytics me-1"></i> Resume Parser
                </button>
            </li>
        </ul>

        <div class="tab-content bg-transparent border-0 p-0">
            <!-- 1. AI Chat -->
            <div class="tab-pane fade show active" id="tab-chat" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Ask Company Policy &amp; HR Questions</h5></div>
                    <div class="card-body">
                        <div id="chatHistory" class="p-4 bg-label-secondary rounded mb-4" style="min-height: 200px; max-height: 400px; overflow-y: auto;">
                            <div class="d-flex mb-3">
                                <div class="avatar avatar-sm me-2"><span class="avatar-initial rounded-circle bg-primary"><i class="icon-base ti tabler-sparkles"></i></span></div>
                                <div class="bg-white p-3 rounded shadow-sm text-heading small" style="max-width: 80%;">
                                    Hello! I am your AI HR Assistant. You can ask me anything about work hours, leave policies, expense limits, or company guidelines.
                                </div>
                            </div>
                        </div>
                        <form id="chatForm" class="d-flex gap-2">
                            <input type="text" id="chatInput" class="form-control" placeholder="Ask e.g. What is our policy on sick leave and reimbursement limits?" required>
                            <button type="submit" class="btn btn-primary px-4"><i class="icon-base ti tabler-send"></i></button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- 2. Notice Drafter -->
            <div class="tab-pane fade" id="tab-announcement" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">AI Company Notice &amp; Circular Drafter</h5></div>
                    <div class="card-body">
                        <form id="announcementForm" class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label required">Topic / Event Title</label>
                                <input type="text" id="aiAnnTitle" class="form-control" placeholder="e.g. New Health Insurance Policy Rollout" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label required">Priority Level</label>
                                <select id="aiAnnPriority" class="form-select" required>
                                    <option value="low">Low</option>
                                    <option value="medium" selected>Medium</option>
                                    <option value="high">High</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Key Points or Specific Instructions</label>
                                <textarea id="aiAnnInstructions" rows="2" class="form-control" placeholder="e.g. All staff must submit claims before Friday..."></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-wand me-1"></i> Generate Notice Draft</button>
                            </div>
                        </form>

                        <div id="annDraftResult" class="p-4 bg-label-primary rounded d-none">
                            <h6 id="annDraftSubject" class="fw-bold mb-2"></h6>
                            <pre id="annDraftContent" class="mb-0 text-heading" style="white-space: pre-wrap; font-family: inherit;"></pre>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Appraisal Feedback Generator -->
            <div class="tab-pane fade" id="tab-review" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Generate Performance Appraisal Commentary</h5></div>
                    <div class="card-body">
                        <form id="reviewForm" class="row g-3 mb-4">
                            <div class="col-md-4">
                                <label class="form-label required">Employee Name</label>
                                <input type="text" id="aiRevName" class="form-control" placeholder="e.g. Rahul Verma" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Appraisal Cycle</label>
                                <input type="text" id="aiRevCycle" class="form-control" placeholder="e.g. Q3 2026 Appraisal" value="Q3 2026 Appraisal" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label required">Target Rating (1 - 5)</label>
                                <select id="aiRevRating" class="form-select" required>
                                    <option value="5">5 - Outstanding</option>
                                    <option value="4" selected>4 - Exceeds Expectations</option>
                                    <option value="3">3 - Meets Expectations</option>
                                    <option value="2">2 - Needs Improvement</option>
                                    <option value="1">1 - Unsatisfactory</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary"><i class="icon-base ti tabler-wand me-1"></i> Generate Evaluation</button>
                            </div>
                        </form>

                        <div id="reviewResult" class="p-4 bg-label-success rounded d-none">
                            <div id="reviewMarkdown" class="text-heading"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Receipt Scanner -->
            <div class="tab-pane fade" id="tab-receipt" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Simulated Receipt OCR &amp; Itemization</h5></div>
                    <div class="card-body">
                        <div class="p-4 border border-dashed rounded text-center mb-4">
                            <i class="icon-base ti tabler-receipt-2 icon-48px text-muted mb-2"></i>
                            <h6>Upload or analyze sample expense bill</h6>
                            <button id="btnScanReceipt" class="btn btn-primary"><i class="icon-base ti tabler-scan me-1"></i> Scan &amp; Extract Data</button>
                        </div>
                        <div id="receiptResult" class="p-4 bg-label-info rounded d-none">
                            <h6 class="fw-bold mb-3">Extracted Receipt Metadata</h6>
                            <div id="receiptDetails"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 5. Resume Parser -->
            <div class="tab-pane fade" id="tab-resume" role="tabpanel">
                <div class="card">
                    <div class="card-header"><h5 class="card-title mb-0">Candidate Resume / CV Parsing</h5></div>
                    <div class="card-body">
                        <div class="p-4 border border-dashed rounded text-center mb-4">
                            <i class="icon-base ti tabler-file-analytics icon-48px text-muted mb-2"></i>
                            <h6>Upload or analyze candidate resume</h6>
                            <button id="btnParseResume" class="btn btn-primary"><i class="icon-base ti tabler-user-search me-1"></i> Parse Resume</button>
                        </div>
                        <div id="resumeResult" class="p-4 bg-label-warning rounded d-none">
                            <h6 class="fw-bold mb-3">Parsed Candidate Profile</h6>
                            <div id="resumeDetails"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('page-script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Chat
    const chatForm = document.getElementById('chatForm');
    if (chatForm) {
        chatForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const input = document.getElementById('chatInput');
            const msg = input.value.trim();
            if (!msg) return;

            const chatHistory = document.getElementById('chatHistory');
            chatHistory.innerHTML += `
                <div class="d-flex justify-content-end mb-3">
                    <div class="bg-primary text-white p-3 rounded shadow-sm small" style="max-width: 80%;">
                        ${msg}
                    </div>
                </div>
            `;
            input.value = '';
            chatHistory.scrollTop = chatHistory.scrollHeight;

            try {
                const res = await fetch('{{ route("admin::ai.chat") }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ message: msg })
                });
                const data = await res.json();
                chatHistory.innerHTML += `
                    <div class="d-flex mb-3">
                        <div class="avatar avatar-sm me-2"><span class="avatar-initial rounded-circle bg-primary"><i class="icon-base ti tabler-sparkles"></i></span></div>
                        <div class="bg-white p-3 rounded shadow-sm text-heading small" style="max-width: 80%;">
                            ${data.response}
                        </div>
                    </div>
                `;
                chatHistory.scrollTop = chatHistory.scrollHeight;
            } catch (err) {
                console.error(err);
            }
        });
    }

    // 2. Announcement Drafter
    const annForm = document.getElementById('announcementForm');
    if (annForm) {
        annForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const title = document.getElementById('aiAnnTitle').value;
            const priority = document.getElementById('aiAnnPriority').value;
            const instructions = document.getElementById('aiAnnInstructions').value;

            const res = await fetch('{{ route("admin::ai.craftAnnouncement") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ title, priority, additional_instructions: instructions })
            });
            const data = await res.json();
            document.getElementById('annDraftSubject').innerText = data.subject;
            document.getElementById('annDraftContent').innerText = data.content;
            document.getElementById('annDraftResult').classList.remove('d-none');
        });
    }

    // 3. Review Generator
    const revForm = document.getElementById('reviewForm');
    if (revForm) {
        revForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            const name = document.getElementById('aiRevName').value;
            const cycle = document.getElementById('aiRevCycle').value;
            const rating = document.getElementById('aiRevRating').value;

            const res = await fetch('{{ route("admin::ai.generateReview") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({ employee_name: name, cycle: cycle, rating: rating })
            });
            const data = await res.json();
            document.getElementById('reviewMarkdown').innerHTML = `<pre style="white-space: pre-wrap; font-family: inherit;">${data.review_markdown}</pre>`;
            document.getElementById('reviewResult').classList.remove('d-none');
        });
    }

    // 4. Receipt Scanner
    const btnScan = document.getElementById('btnScanReceipt');
    if (btnScan) {
        btnScan.addEventListener('click', async function() {
            const res = await fetch('{{ route("admin::ai.scanReceipt") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({})
            });
            const data = await res.json();
            document.getElementById('receiptDetails').innerHTML = `
                <p class="mb-1"><strong>Vendor:</strong> ${data.vendor}</p>
                <p class="mb-1"><strong>Amount:</strong> ₹${data.amount} (${data.currency})</p>
                <p class="mb-1"><strong>Category:</strong> <span class="badge bg-label-info">${data.category}</span></p>
                <p class="mb-0"><strong>Extracted Date:</strong> ${data.date}</p>
            `;
            document.getElementById('receiptResult').classList.remove('d-none');
        });
    }

    // 5. Resume Parser
    const btnResume = document.getElementById('btnParseResume');
    if (btnResume) {
        btnResume.addEventListener('click', async function() {
            const res = await fetch('{{ route("admin::ai.parseResume") }}', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                body: JSON.stringify({})
            });
            const data = await res.json();
            document.getElementById('resumeDetails').innerHTML = `
                <p class="mb-1"><strong>Name:</strong> ${data.candidate_name}</p>
                <p class="mb-1"><strong>Email:</strong> ${data.email} | <strong>Phone:</strong> ${data.phone}</p>
                <p class="mb-1"><strong>Experience:</strong> ${data.experience_years} years | <strong>Education:</strong> ${data.education}</p>
                <p class="mb-0"><strong>Detected Skills:</strong> ${data.skills.map(s => `<span class="badge bg-label-primary me-1">${s}</span>`).join('')}</p>
            `;
            document.getElementById('resumeResult').classList.remove('d-none');
        });
    }
});
</script>
@endsection
