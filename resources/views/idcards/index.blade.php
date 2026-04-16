@extends('layouts.app')
@section('title', 'ID Cards')
@section('page-title', 'Student ID Cards')

@section('content')
<!-- Filter -->
<div class="card" style="margin-bottom: 20px;">
    <div class="card-body" style="padding:14px 22px;">
        <form method="GET" class="search-bar">
            <select name="school_id" class="form-control" style="max-width:220px;">
                <option value="">All Schools</option>
                @foreach($schools as $school)
                    <option value="{{ $school->id }}" {{ request('school_id') == $school->id ? 'selected' : '' }}>{{ $school->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="btn btn-ghost btn-sm">Filter</button>
        </form>
    </div>
</div>

@if($students->isEmpty())
    <div class="card">
        <div class="empty-state">
            <i class="fas fa-id-badge"></i>
            <h4>No students found</h4>
            <p>Add students to generate their ID cards</p>
        </div>
    </div>
@else
    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(340px, 1fr));gap:20px;">
        @foreach($students as $student)
        <div class="id-card-container">
            <!-- Front of Card -->
            <div class="id-card">
                <div class="id-card-header">
                    <div class="id-card-logo">
                        <i class="fas fa-building-columns"></i>
                    </div>
                    <div>
                        <div class="id-card-school">{{ $student->school->name ?? 'School Name' }}</div>
                        <div class="id-card-subtitle">Student Identity Card</div>
                    </div>
                </div>

                <div class="id-card-body">
                    <div class="id-card-photo" style="overflow:hidden;">
                        @if($student->photo_original_url)
                            <img src="{{ url($student->photo_original_url) }}" style="width:100%;height:100%;object-fit:cover;" alt="Photo">
                        @else
                            <i class="fas fa-user-graduate" style="font-size:32px;color:#b8941e;"></i>
                        @endif
                    </div>
                    <div class="id-card-info">
                        <div class="id-card-name">{{ $student->first_name }} {{ $student->last_name }}</div>
                        <div class="id-card-detail"><strong>Roll No:</strong> {{ $student->roll_number }}</div>
                        <div class="id-card-detail"><strong>Class:</strong> {{ $student->class_name }}{{ $student->section ? '-'.$student->section : '' }}</div>
                        <div class="id-card-detail"><strong>Blood:</strong> {{ $student->blood_group ?? 'N/A' }}</div>
                        @if($student->guardian_name)
                        <div class="id-card-detail"><strong>Guardian:</strong> {{ $student->guardian_name }}</div>
                        @endif
                    </div>
                </div>

                <div class="id-card-footer">
                    <div class="id-card-qr">
                        @if($student->qr_token)
                            <i class="fas fa-qrcode" style="font-size:28px;color:var(--gold-dark);"></i>
                        @else
                            <span style="font-size:9px;color:var(--text-muted);">No QR</span>
                        @endif
                    </div>
                    <div style="font-size:9px;color:var(--text-muted);text-align:right;">
                        ID: EDU-{{ str_pad($student->id, 6, '0', STR_PAD_LEFT) }}<br>
                        Valid: {{ date('Y') }}-{{ date('Y') + 1 }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    @if($students->hasPages())
    <div class="card" style="margin-top:20px;">
        <div class="pagination-wrap">
            <div class="pagination-info">Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }}</div>
            <div class="pagination-links">{{ $students->appends(request()->query())->links('pagination.custom') }}</div>
        </div>
    </div>
    @endif
@endif
@endsection

@section('styles')
<style>
    .id-card-container {
        perspective: 1000px;
    }

    .id-card {
        background: linear-gradient(145deg, #fffdf8, #fdf8f0);
        border: 2px solid var(--gold);
        border-radius: 16px;
        padding: 0;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(212, 175, 55, 0.12);
        transition: all 0.4s ease;
    }

    .id-card:hover {
        transform: translateY(-4px) rotateX(2deg);
        box-shadow: 0 12px 40px rgba(212, 175, 55, 0.2);
    }

    .id-card-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .id-card-logo {
        width: 40px;
        height: 40px;
        background: var(--gold-gradient);
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #1a1a2e;
        font-size: 18px;
    }

    .id-card-school {
        color: white;
        font-size: 14px;
        font-weight: 700;
    }

    .id-card-subtitle {
        color: rgba(255,255,255,0.5);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        font-weight: 600;
    }

    .id-card-body {
        padding: 20px;
        display: flex;
        gap: 16px;
    }

    .id-card-photo {
        width: 72px;
        height: 88px;
        border-radius: 10px;
        background: var(--gold-bg);
        border: 2px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .id-card-info {
        flex: 1;
    }

    .id-card-name {
        font-size: 16px;
        font-weight: 800;
        color: var(--text-primary);
        margin-bottom: 8px;
    }

    .id-card-detail {
        font-size: 11px;
        color: var(--text-secondary);
        margin-bottom: 4px;
        line-height: 1.5;
    }

    .id-card-detail strong {
        color: var(--text-primary);
        font-weight: 600;
    }

    .id-card-footer {
        padding: 12px 20px;
        border-top: 1px dashed var(--border);
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: var(--bg-cream);
    }

    .id-card-qr {
        width: 44px;
        height: 44px;
        border-radius: 8px;
        background: white;
        border: 1px solid var(--border);
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endsection
