{{-- resources/views/auth/login.blade.php --}}
@extends('layouts.app')

@section('title', 'Login')

@push('styles')
<style>
    .password-toggle {
        cursor: pointer;
        transition: all 0.3s;
    }
    .password-toggle:hover {
        color: #28a745 !important;
    }
    .input-group-text {
        background: transparent;
        border-left: none;
    }
    .input-group .form-control {
        border-right: none;
    }
    .input-group .form-control:focus {
        box-shadow: none;
        border-color: #dee2e6;
    }
    .input-group:focus-within {
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        border-radius: 0.375rem;
    }
    .input-group:focus-within .form-control {
        border-color: #28a745;
    }
    .input-group:focus-within .input-group-text {
        border-color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-sign-in-alt"></i> Login</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email') }}" required autofocus>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <div class="input-group">
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                <span class="input-group-text password-toggle" onclick="togglePassword()">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </span>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <small class="text-muted">Click the eye icon to show/hide password</small>
                        </div>

                        <div class="mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                <label class="form-check-label" for="remember">Remember Me</label>
                            </div>
                            <a href="{{ route('password.request') }}" class="text-success">
                                <i class="fas fa-key"></i> Forgot Password?
                            </a>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-sign-in-alt"></i> Login
                            </button>
                        </div>

                        <div class="text-center mt-3">
                            Don't have an account? <a href="{{ route('register') }}">Register here</a>
                        </div>
                    </form>
                </div>
            </div>

            @if(session('status'))
                <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-check-circle"></i> {{ session('status') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('toggleIcon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Optional: Add keyboard shortcut (Ctrl+E) to toggle password
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'e') {
        e.preventDefault();
        togglePassword();
    }
});

// Add tooltip to show keyboard shortcut
document.addEventListener('DOMContentLoaded', function() {
    const toggleIcon = document.getElementById('toggleIcon');
    if (toggleIcon) {
        toggleIcon.parentElement.setAttribute('title', 'Ctrl+E to toggle');
    }
});
</script>
@endpush
