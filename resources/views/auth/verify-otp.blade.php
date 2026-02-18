{{-- resources/views/auth/verify-otp.blade.php --}}
@extends('layouts.app')

@section('title', 'Verify OTP')

@push('styles')
<style>
    .otp-card {
        border: none;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    }
    .otp-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border-radius: 15px 15px 0 0 !important;
        padding: 20px;
    }
    .otp-body {
        padding: 30px;
    }
    .otp-input {
        width: 60px;
        height: 70px;
        font-size: 32px;
        text-align: center;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        margin: 0 5px;
        font-weight: bold;
        color: #28a745;
        transition: all 0.3s;
    }
    .otp-input:focus {
        border-color: #28a745;
        box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        outline: none;
    }
    .timer-text {
        font-size: 1.2rem;
        font-weight: bold;
        color: #28a745;
    }
    .email-display {
        background: #f8f9fa;
        padding: 10px;
        border-radius: 5px;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card otp-card">
                <div class="card-header otp-header">
                    <h4 class="mb-0"><i class="fas fa-shield-alt"></i> Verify OTP</h4>
                </div>
                <div class="card-body otp-body">
                    <div class="text-center mb-4">
                        <i class="fas fa-envelope-open-text fa-4x text-success mb-3"></i>
                        <h5>Email Verification</h5>
                        <p class="text-muted">We've sent a 6-digit code to</p>
                        <div class="email-display">
                            <i class="fas fa-envelope text-success me-2"></i>
                            {{ session('reset_email') ?? old('email') }}
                        </div>
                    </div>

                    <form method="POST" action="{{ route('password.verify.otp') }}" id="otpForm">
                        @csrf
                        <input type="hidden" name="email" value="{{ session('reset_email') ?? old('email') }}">

                        <div class="text-center mb-4">
                            <div class="d-flex justify-content-center">
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                                <input type="text" class="form-control otp-input" name="otp[]" maxlength="1" inputmode="numeric" pattern="[0-9]*" required>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <div class="timer-text" id="timer"></div>
                            <small class="text-muted">OTP expires in 10 minutes</small>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success btn-lg" id="verifyBtn">
                                <i class="fas fa-check-circle"></i> Verify & Continue
                            </button>
                            <button type="button" class="btn btn-outline-success" id="resendBtn" onclick="resendOtp()" disabled>
                                <i class="fas fa-redo-alt"></i> Resend OTP (<span id="resendTimer">60</span>s)
                            </button>
                            <a href="{{ route('password.request') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left"></i> Try Again
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
// OTP input handling
const otpInputs = document.querySelectorAll('.otp-input');
let countdownInterval;
let resendCountdown = 60;

otpInputs.forEach((input, index) => {
    input.addEventListener('keyup', function(e) {
        // Move to next input
        if (this.value.length === 1 && index < otpInputs.length - 1) {
            otpInputs[index + 1].focus();
        }

        // Handle backspace
        if (e.key === 'Backspace' && index > 0 && this.value.length === 0) {
            otpInputs[index - 1].focus();
        }
    });

    // Allow only numbers
    input.addEventListener('keypress', function(e) {
        if (!/^\d$/.test(e.key)) {
            e.preventDefault();
        }
    });
});

// Start countdown on page load
document.addEventListener('DOMContentLoaded', function() {
    startCountdown(600); // 10 minutes in seconds
    startResendTimer();
});

function startCountdown(seconds) {
    const timerDiv = document.getElementById('timer');
    const verifyBtn = document.getElementById('verifyBtn');

    countdownInterval = setInterval(() => {
        const minutes = Math.floor(seconds / 60);
        const remainingSeconds = seconds % 60;

        timerDiv.textContent = `${minutes}:${remainingSeconds.toString().padStart(2, '0')}`;

        if (seconds <= 0) {
            clearInterval(countdownInterval);
            timerDiv.textContent = '00:00';
            verifyBtn.disabled = true;
            Swal.fire({
                icon: 'error',
                title: 'OTP Expired',
                text: 'Your OTP has expired. Please request a new one.'
            });
        }
        seconds--;
    }, 1000);
}

function startResendTimer() {
    const resendBtn = document.getElementById('resendBtn');
    const resendTimer = document.getElementById('resendTimer');

    const timer = setInterval(() => {
        resendCountdown--;
        resendTimer.textContent = resendCountdown;

        if (resendCountdown <= 0) {
            clearInterval(timer);
            resendBtn.disabled = false;
            resendBtn.innerHTML = '<i class="fas fa-redo-alt"></i> Resend OTP';
        }
    }, 1000);
}

function resendOtp() {
    const email = document.querySelector('input[name="email"]').value;

    fetch('{{ route("password.email") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'OTP Resent',
                text: 'A new OTP has been sent to your email.'
            });

            // Reset timers
            clearInterval(countdownInterval);
            startCountdown(600);
            resendCountdown = 60;
            startResendTimer();

            // Clear OTP inputs
            otpInputs.forEach(input => input.value = '');
            otpInputs[0].focus();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: data.message || 'Failed to resend OTP'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to resend OTP'
        });
    });
}

// Prevent form resubmission
document.getElementById('otpForm')?.addEventListener('submit', function(e) {
    const btn = document.getElementById('verifyBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Verifying...';
});
</script>
@endpush
