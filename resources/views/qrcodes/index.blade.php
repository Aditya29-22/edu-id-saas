@extends('layouts.app')
@section('title', 'QR Codes')
@section('page-title', 'QR Code Management')

@section('header-actions')
    @if(request('school_id'))
        <form method="POST" action="{{ route('qrcodes.bulk') }}" style="display:inline;">
            @csrf
            <input type="hidden" name="school_id" value="{{ request('school_id') }}">
            <button type="submit" class="btn btn-primary"><i class="fas fa-qrcode"></i> Generate All QR</button>
        </form>
    @endif
@endsection

@section('content')
<!-- Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <div class="search-input-wrap">
                <i class="fas fa-search"></i>
                <input type="text" name="search" class="form-control" placeholder="Search student..." value="{{ request('search') }}">
            </div>
            <select name="school_id" class="form-control" style="max-width:200px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        </form>
    </div>
</div>

<!-- QR Grid -->
@if($students->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-qrcode"></i>
            <h4>No students found</h4>
            <p>Add students first, then generate their QR codes</p>
        </div>
    </div>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px;margin-bottom:24px;">
        @foreach($students as $student)
        <div class="card qr-card" style="overflow:visible;">
            <div class="card-body" style="text-align:center;padding:24px;">
                <div style="display:flex;align-items:center;gap:12px;margin-bottom:18px;text-align:left;">
                    <div style="width:42px;height:42px;border-radius:12px;background:var(--gold-bg);display:flex;align-items:center;justify-content:center;color:var(--gold-dark);font-weight:800;font-size:15px;">
                        {{ strtoupper(substr($student->first_name, 0, 1)) }}{{ strtoupper(substr($student->last_name, 0, 1)) }}
                    </div>
                    <div>
                        <div style="font-weight:700;font-size:14px;color:var(--text-primary);">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div style="font-size:12px;color:var(--text-muted);">{{ $student->school->name ?? '' }} · {{ $student->class_name }}-{{ $student->section }}</div>
                    </div>
                </div>

                <!-- QR Display Area -->
                <div id="qr-{{ $student->id }}" class="qr-display" style="width:200px;height:200px;margin:0 auto 16px;background:var(--bg-cream);border-radius:14px;display:flex;align-items:center;justify-content:center;border:2px dashed var(--border);">
                    @if($student->qr_token)
                        <div style="text-align:center;">
                            <i class="fas fa-qrcode" style="font-size:48px;color:var(--gold);opacity:0.6;"></i>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:8px;">QR Ready</div>
                        </div>
                    @else
                        <div style="text-align:center;">
                            <i class="fas fa-circle-exclamation" style="font-size:32px;color:var(--text-muted);opacity:0.4;"></i>
                            <div style="font-size:11px;color:var(--text-muted);margin-top:8px;">Not Generated</div>
                        </div>
                    @endif
                </div>

                <div style="display:flex;gap:8px;justify-content:center;">
                    <button onclick="generateQR({{ $student->id }})" class="btn btn-primary btn-sm">
                        <i class="fas fa-qrcode"></i> {{ $student->qr_token ? 'View QR' : 'Generate' }}
                    </button>
                    <span class="badge {{ $student->qr_token ? 'badge-success' : 'badge-warning' }}" style="display:flex;align-items:center;">
                        {{ $student->qr_token ? 'Active' : 'Pending' }}
                    </span>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($students->hasPages())
    <div class="card">
        <div class="pagination-wrap">
            <div class="pagination-info">Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }}</div>
            <div class="pagination-links">{{ $students->appends(request()->query())->links('pagination.custom') }}</div>
        </div>
    </div>
    @endif
@endif

<!-- QR Modal -->
<div id="qr-modal" style="display:none;position:fixed;inset:0;z-index:1000;background:rgba(0,0,0,0.5);backdrop-filter:blur(6px);align-items:center;justify-content:center;">
    <div style="background:white;border-radius:20px;padding:36px;max-width:380px;width:90%;text-align:center;box-shadow:0 20px 60px rgba(0,0,0,0.15);position:relative;">
        <button onclick="closeModal()" style="position:absolute;top:12px;right:16px;background:none;border:none;font-size:20px;color:var(--text-muted);cursor:pointer;">&times;</button>
        <h3 id="modal-student-name" style="font-size:18px;font-weight:800;margin-bottom:4px;color:var(--text-primary);"></h3>
        <p id="modal-student-info" style="font-size:13px;color:var(--text-muted);margin-bottom:20px;"></p>
        <div id="modal-qr" style="display:inline-block;padding:16px;background:white;border-radius:16px;border:2px solid var(--border);"></div>
        <div style="margin-top:16px;font-size:11px;color:var(--text-muted);">Scan this QR for attendance</div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function generateQR(studentId) {
    fetch(`/qr-codes/generate/${studentId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const modal = document.getElementById('qr-modal');
                document.getElementById('modal-student-name').textContent = `${data.student.first_name} ${data.student.last_name}`;
                document.getElementById('modal-student-info').textContent = `Roll: ${data.student.roll_number} · Class: ${data.student.class_name}-${data.student.section || ''}`;
                document.getElementById('modal-qr').innerHTML = `<img src="data:image/svg+xml;base64,${data.qr_svg}" width="220" height="220" style="display:block;">`;
                modal.style.display = 'flex';

                // Update card status
                const card = document.getElementById(`qr-${studentId}`);
                if (card) {
                    card.innerHTML = '<div style="text-align:center;"><i class="fas fa-qrcode" style="font-size:48px;color:var(--gold);opacity:0.6;"></i><div style="font-size:11px;color:var(--text-muted);margin-top:8px;">QR Ready</div></div>';
                }
            }
        })
        .catch(err => alert('Error generating QR code'));
}

function closeModal() {
    document.getElementById('qr-modal').style.display = 'none';
}

document.getElementById('qr-modal').addEventListener('click', function(e) {
    if (e.target === this) closeModal();
});
</script>
@endsection
