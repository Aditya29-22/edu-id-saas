@extends('layouts.app')
@section('title', 'Add School')
@section('page-title', 'Add New School')

@section('content')
<div style="max-width:680px;">
    <div class="card">
        <div class="card-header">
            <h3><i class="fas fa-building-columns" style="color:#818cf8;margin-right:8px;"></i> School Information</h3>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('schools.store') }}">
                @csrf
                <div class="grid-2">
                    <div class="form-group">
                        <label>School Name *</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Delhi Public School" value="{{ old('name') }}" required>
                    </div>
                    <div class="form-group">
                        <label>School Code *</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. DPS-001" value="{{ old('code') }}" required>
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Email *</label>
                        <input type="email" name="email" class="form-control" placeholder="school@example.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="form-group">
                        <label>Phone *</label>
                        <input type="text" name="phone" class="form-control" placeholder="+91 9876543210" value="{{ old('phone') }}" required>
                    </div>
                </div>

                <div class="form-group">
                    <label>Street Address</label>
                    <input type="text" name="street" class="form-control" placeholder="123 Main Road" value="{{ old('street') }}">
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>City</label>
                        <input type="text" name="city" class="form-control" placeholder="New Delhi" value="{{ old('city') }}">
                    </div>
                    <div class="form-group">
                        <label>State</label>
                        <input type="text" name="state" class="form-control" placeholder="Delhi" value="{{ old('state') }}">
                    </div>
                </div>

                <div class="grid-2">
                    <div class="form-group">
                        <label>Pincode</label>
                        <input type="text" name="pincode" class="form-control" placeholder="110001" value="{{ old('pincode') }}">
                    </div>
                    <div class="form-group">
                        <label>Country</label>
                        <input type="text" name="country" class="form-control" value="{{ old('country', 'India') }}">
                    </div>
                </div>

                <div style="display:flex;gap:12px;margin-top:8px;">
                    <button type="submit" class="btn btn-primary"><i class="fas fa-check"></i> Create School</button>
                    <a href="{{ route('schools') }}" class="btn btn-ghost">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
