@extends('layouts.app')
@section('title', 'Attendance Scanner')
@section('page-title', 'Attendance Scanner')

@section('content')
<div class="grid-2" style="gap:24px;">
    <!-- Scanner Panel -->
    <div>
        <div class="card" style="border:2px solid var(--gold);overflow:visible;">
            <div class="card-header" style="background:linear-gradient(135deg, var(--bg-cream), #fef3e2);">
                <h3><i class="fas fa-camera" style="color:var(--gold-dark);margin-right:8px;"></i> QR Scanner</h3>
                <span class="badge badge-success" id="scanner-status">Ready</span>
            </div>
            <div class="card-body" style="padding:28px;">
                <!-- Manual QR Input -->
                <div style="text-align:center;margin-bottom:24px;">
                    <div style="width:80px;height:80px;margin:0 auto 16px;background:var(--gold-bg);border-radius:20px;display:flex;align-items:center;justify-content:center;">
                        <i class="fas fa-qrcode" style="font-size:36px;color:var(--gold-dark);"></i>
                    </div>
                    <p style="color:var(--text-muted);font-size:13px;margin-bottom:20px;">Enter QR data or use a barcode scanner device</p>
                </div>

                <div class="form-group">
                    <label>QR Code Data</label>
                    <textarea id="qr-input" class="form-control" rows="3" placeholder='Paste or scan QR data here...' style="font-family:monospace;font-size:13px;resize:none;"></textarea>
                </div>

                <button onclick="processScan()" id="scan-btn" class="btn btn-primary" style="width:100%;justify-content:center;padding:14px;font-size:15px;">
                    <i class="fas fa-fingerprint"></i> Process Scan
                </button>

                <!-- Result display -->
                <div id="scan-result" style="display:none;margin-top:20px;padding:18px;border-radius:14px;text-align:center;animation:slideDown 0.3s ease;">
                </div>
            </div>
        </div>

        <!-- Quick Demo -->
        <div class="card" style="margin-top:16px;">
            <div class="card-header">
                <h3><i class="fas fa-flask" style="color:var(--info);margin-right:8px;"></i> Quick Test</h3>
            </div>
            <div class="card-body">
                <p style="font-size:13px;color:var(--text-muted);margin-bottom:12px;">Click a student below to simulate a QR scan:</p>
                <div id="test-students" style="display:flex;flex-wrap:wrap;gap:8px;">
                    <span style="font-size:12px;color:var(--text-muted);">Loading students...</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Panel - Stats & Recent -->
    <div>
        <!-- Today's Stats -->
        <div style="display:grid;grid-template-columns:repeat(2,1fr);gap:12px;margin-bottom:16px;">
            <div class="stat-card">
                <div class="stat-icon gold"><i class="fas fa-arrow-right-to-bracket"></i></div>
                <div class="stat-value">{{ $todayStats['entries'] }}</div>
                <div class="stat-label">Entries Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon purple"><i class="fas fa-arrow-right-from-bracket"></i></div>
                <div class="stat-value">{{ $todayStats['exits'] }}</div>
                <div class="stat-label">Exits Today</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon red"><i class="fas fa-clock"></i></div>
                <div class="stat-value">{{ $todayStats['late'] }}</div>
                <div class="stat-label">Late Arrivals</div>
            </div>
            <div class="stat-card">
                <div class="stat-icon green"><i class="fas fa-chart-simple"></i></div>
                <div class="stat-value">{{ $todayStats['total_scans'] }}</div>
                <div class="stat-label">Total Scans</div>
            </div>
        </div>

        <!-- Recent Scans -->
        <div class="card">
            <div class="card-header">
                <h3><i class="fas fa-clock-rotate-left" style="color:var(--gold-dark);margin-right:8px;"></i> Recent Scans</h3>
            </div>
            <div class="card-body" style="padding:0;max-height:400px;overflow-y:auto;" id="recent-scans">
                @forelse($recentScans as $scan)
                <div style="display:flex;align-items:center;gap:12px;padding:14px 18px;border-bottom:1px solid var(--border);">
                    <div style="width:38px;height:38px;border-radius:10px;background:{{ $scan->status === 'entered' ? 'var(--success-bg)' : 'var(--purple-bg)' }};display:flex;align-items:center;justify-content:center;color:{{ $scan->status === 'entered' ? 'var(--success)' : 'var(--purple)' }};flex-shrink:0;">
                        <i class="fas {{ $scan->status === 'entered' ? 'fa-arrow-right-to-bracket' : 'fa-arrow-right-from-bracket' }}"></i>
                    </div>
                    <div style="flex:1;min-width:0;">
                        <div style="font-size:13px;font-weight:600;color:var(--text-primary);">{{ $scan->student->first_name ?? '' }} {{ $scan->student->last_name ?? '' }}</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ $scan->student->school->name ?? '' }} · Roll: {{ $scan->student->roll_number ?? '' }}</div>
                    </div>
                    <div style="text-align:right;flex-shrink:0;">
                        <div class="badge {{ $scan->is_late ? 'badge-warning' : 'badge-success' }}" style="font-size:10px;">
                            {{ $scan->is_late ? 'Late' : 'On Time' }}
                        </div>
                        <div style="font-size:11px;color:var(--text-muted);margin-top:4px;">
                            {{ $scan->entry_time ? \Carbon\Carbon::parse($scan->entry_time)->format('h:i A') : '' }}
                        </div>
                    </div>
                </div>
                @empty
                <div class="empty-state" style="padding:32px;">
                    <i class="fas fa-clipboard-check" style="font-size:32px;"></i>
                    <h4 style="font-size:14px;">No scans today</h4>
                    <p style="font-size:12px;">Scan a student's QR code to get started</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

function processScan() {
    const input = document.getElementById('qr-input');
    const btn = document.getElementById('scan-btn');
    const result = document.getElementById('scan-result');
    const qrData = input.value.trim();

    if (!qrData) { alert('Please enter QR data'); return; }

    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

    fetch('/scanner/process', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
        body: JSON.stringify({ qr_data: qrData })
    })
    .then(r => r.json())
    .then(data => {
        result.style.display = 'block';
        if (data.success) {
            const isEntry = data.action === 'entry';
            const isLate = data.is_late;
            result.style.background = isLate ? 'var(--warning-bg)' : (isEntry ? 'var(--success-bg)' : 'var(--purple-bg)');
            result.style.border = `1px solid ${isLate ? 'rgba(230,126,34,0.2)' : (isEntry ? 'rgba(39,174,96,0.2)' : 'rgba(142,68,173,0.2)')}`;
            result.innerHTML = `
                <div style="font-size:32px;margin-bottom:8px;">${isEntry ? (isLate ? '⚠️' : '✅') : '🚪'}</div>
                <div style="font-size:16px;font-weight:700;color:var(--text-primary);margin-bottom:4px;">${data.student.name}</div>
                <div style="font-size:13px;color:var(--text-secondary);">${data.student.school} · ${data.student.class} · Roll: ${data.student.roll}</div>
                <div style="font-size:14px;font-weight:600;margin-top:8px;color:${isLate ? 'var(--warning)' : 'var(--success)'};">${data.message}</div>
            `;
        } else {
            result.style.background = 'var(--danger-bg)';
            result.style.border = '1px solid rgba(231,76,60,0.2)';
            result.innerHTML = `<div style="font-size:32px;margin-bottom:8px;">⛔</div><div style="font-size:14px;font-weight:600;color:var(--danger);">${data.message}</div>`;
        }
        input.value = '';
    })
    .catch(err => {
        result.style.display = 'block';
        result.style.background = 'var(--danger-bg)';
        result.innerHTML = '<div style="color:var(--danger);font-weight:600;">Network error. Please try again.</div>';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-fingerprint"></i> Process Scan';
    });
}

// Auto-process on Enter key
document.getElementById('qr-input').addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); processScan(); }
});

// Load test students
fetch('/api/students-for-scan')
    .catch(() => {
        document.getElementById('test-students').innerHTML = '<span style="font-size:12px;color:var(--text-muted);">Add students via Students page first</span>';
    });
</script>
@endsection
