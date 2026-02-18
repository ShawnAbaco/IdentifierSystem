{{-- resources/views/auth/reset-password.blade.php --}}
@extends('layouts.app')

@section('title', 'Reset Password')

@push('styles')
<style>
    .reset-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .reset-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
    .reset-body {
        padding: 30px;
    }
    .password-requirements {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-top: 10px;
        font-size: 0.9rem;
    }
    .requirement {
        color: #6c757d;
        margin-bottom: 5px;
    }
    .requirement i {
        margin-right: 8px;
        font-size: 0.8rem;
    }
    .requirement.valid {
        color: #28a745;
    }
    .requirement.invalid {
        color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card reset-card">
                <div class="card-header reset-header">
                    <h4 class="mb-0"><i class="fas fa-lock"></i> Reset Password</h4>
                </div>
                <div class="card-body reset-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                        <h5>Create New Password</h5>
                        <p class="text-muted">Please enter your new password below.</p>
                    </div>

                    <form method="POST" action="{{ route('password.update') }}" id="resetForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">
                        <input type="hidden" name="otp" value="{{ session('reset_otp') ?? old('otp') }}">

                        <div class="mb-3">
                            <label for="password" class="form-label">New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-lock text-success"></i>
                                </span>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required minlength="8">
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                    <i class="fas fa-eye" id="togglePasswordIcon"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm New Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light">
                                    <i class="fas fa-lock text-success"></i>
                                </span>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                    <i class="fas fa-eye" id="toggleConfirmIcon"></i>
                                </button>
                            </div>
                        </div>

                        <!-- Password Requirements -->
                        <div class="password-requirements">
                            <p class="mb-2"><i class="fas fa-info-circle text-info"></i> Password must contain:</p>
                            <div class="requirement" id="req-length">
                                <i class="fas fa-circle"></i> At least 8 characters
                            </div>
                            <div class="requirement" id="req-number">
                                <i class="fas fa-circle"></i> At least 1 number
                            </div>
                            <div class="requirement" id="req-uppercase">
                                <i class="fas fa-circle"></i> At least 1 uppercase letter
                            </div>
                            <div class="requirement" id="req-match">
                                <i class="fas fa-circle"></i> Passwords match
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="submitBtn">
                                <i class="fas fa-save"></i> Reset Password
                            </button>
                            <a href="{{ route('login') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Back to Login
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                    <i class="fas fa-exclamation-circle"></i> {{ $errors->first() }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Toggle password visibility
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const icon = fieldId === 'password' ?
                 document.getElementById('togglePasswordIcon') :
                 document.getElementById('toggleConfirmIcon');

    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password validation
const password = document.getElementById('password');
const confirm = document.getElementById('password_confirmation');

function validatePassword() {
    const val = password.value;
    const requirements = {
        length: val.length >= 8,
        number: /\d/.test(val),
        uppercase: /[A-Z]/.test(val),
        match: val === confirm.value && val !== ''
    };

    // Update requirement indicators
    document.getElementById('req-length').className = requirements.length ? 'requirement valid' : 'requirement invalid';
    document.getElementById('req-number').className = requirements.number ? 'requirement valid' : 'requirement invalid';
    document.getElementById('req-uppercase').className = requirements.uppercase ? 'requirement valid' : 'requirement invalid';
    document.getElementById('req-match').className = requirements.match ? 'requirement valid' : 'requirement invalid';

    // Update icons
    document.querySelectorAll('.requirement i').forEach(icon => {
        icon.className = 'fas fa-circle';
    });
}

password.addEventListener('input', validatePassword);
confirm.addEventListener('input', validatePassword);

// Prevent form submission if requirements not met
document.getElementById('resetForm')?.addEventListener('submit', function(e) {
    const val = password.value;
    const confirmVal = confirm.value;

    if (val.length < 8 || !/\d/.test(val) || !/[A-Z]/.test(val) || val !== confirmVal) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Invalid Password',
            text: 'Please make sure your password meets all requirements.'
        });
        return;
    }

    const btn = document.getElementById('submitBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Resetting...';
});
</script>
@endpush
