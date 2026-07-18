@extends('layouts.admin')

@section('content')
<div class="container">
    <h3>General Settings</h3>

    <!-- Display Success Message -->
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card mt-4">
        <div class="card-header">
            Auto Payment Time Setting
        </div>
        <div class="card-body">
            <form action="{{ route('admin.settings.auto_payment_time') }}" method="POST">
                @csrf

                <div class="form-group mb-3">
                    <label for="auto_payment_time"><strong>Auto Payment Time</strong> </label>
                    <input 
                        type="time" 
                        name="auto_payment_time" 
                        id="auto_payment_time" 
                        class="form-control @error('auto_payment_time') is-invalid @enderror" 
                        value="{{ $companyDetail ? \Carbon\Carbon::parse($companyDetail->auto_payment_time)->format('H:i') : '16:30' }}"
                        required
                    >
                    @error('auto_payment_time')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn btn-primary">Update Time</button>
            </form>
        </div>
    </div>
</div>
@endsection