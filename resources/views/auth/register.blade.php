{{-- resources/views/auth/register.blade.php --}}
@extends('layouts.app')

@section('title', 'Register')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-user-plus"></i> Register</h4>
                </div>
                <div class="card-body">
                    {{-- Success/Error Messages --}}
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    {{-- Registration Form --}}
                    <div id="registrationForm">
                        <form method="POST" action="{{ route('register.send-otp') }}" id="registerForm">
                            @csrf

                            <div class="mb-3">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror"
                                       id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" required>
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation" required>
                            </div>

                            <div class="d-grid">
                                <button type="submit" class="btn btn-success" id="sendOtpBtn">
                                    <i class="fas fa-paper-plane"></i> Send OTP
                                </button>
                            </div>

                            <div class="text-center mt-3">
                                Already have an account? <a href="{{ route('login') }}">Login here</a>
                            </div>
                        </form>
                    </div>

                    {{-- OTP Verification Form (Hidden by default) --}}
                    <div id="otpVerificationForm" style="display: none;">
                        <form method="POST" action="{{ route('register.verify-otp') }}" id="verifyOtpForm">
                            @csrf
                            <input type="hidden" id="verified_email" name="email">
                            <input type="hidden" id="verified_name" name="name">
                            <input type="hidden" id="verified_password" name="password">

                            <div class="text-center mb-4">
                                <div class="mb-3">
                                    <i class="fas fa-envelope-open-text text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h5>Email Verification</h5>
                                <p class="text-muted small">We've sent a verification code to</p>
                                <strong id="displayEmail" class="text-success"></strong>
                            </div>

                            <div class="mb-4">
                                <label for="otp" class="form-label">Enter 6-digit OTP Code</label>
                                <input type="text"
                                       class="form-control form-control-lg text-center @error('otp') is-invalid @enderror"
                                       id="otp"
                                       name="otp"
                                       placeholder="• • • • • •"
                                       required
                                       maxlength="6"
                                       pattern="\d*"
                                       inputmode="numeric"
                                       autocomplete="off">
                                @error('otp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="d-flex justify-content-between align-items-center mt-2">
                                    <small class="text-muted" id="timer">Code expires in 05:00</small>
                                    <button type="button" class="btn btn-link btn-sm p-0" id="resendOtpBtn" onclick="resendOtp()">
                                        Resend Code
                                    </button>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-success">
                                    <i class="fas fa-check-circle"></i> Verify & Complete Registration
                                </button>
                                <button type="button" class="btn btn-outline-secondary" onclick="goBack()">
                                    <i class="fas fa-arrow-left"></i> Back
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
let timerInterval;
let timeLeft = 300; // 5 minutes in seconds

// Handle registration form submission
document.getElementById('registerForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const formData = new FormData(this);
    const submitBtn = document.getElementById('sendOtpBtn');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    // Clear any existing alerts
    document.querySelectorAll('.alert').forEach(el => el.remove());

    fetch(this.action, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Store data in hidden fields
            document.getElementById('verified_email').value = formData.get('email');
            document.getElementById('verified_name').value = formData.get('name');
            document.getElementById('verified_password').value = formData.get('password');
            document.getElementById('displayEmail').textContent = formData.get('email');

            // Switch to OTP form with animation
            document.getElementById('registrationForm').style.display = 'none';
            document.getElementById('otpVerificationForm').style.display = 'block';

            // Start timer
            startTimer();

            // Focus OTP input
            document.getElementById('otp').focus();
        } else {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                ${data.message || 'Failed to send OTP. Please try again.'}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            An error occurred. Please try again.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

function startTimer() {
    timeLeft = 300;
    updateTimerDisplay();

    if (timerInterval) clearInterval(timerInterval);

    timerInterval = setInterval(() => {
        timeLeft--;
        updateTimerDisplay();

        if (timeLeft <= 0) {
            clearInterval(timerInterval);
            document.getElementById('timer').innerHTML = '<span class="text-danger">Code expired</span>';
        }
    }, 1000);
}

function updateTimerDisplay() {
    const minutes = Math.floor(timeLeft / 60);
    const seconds = timeLeft % 60;
    document.getElementById('timer').textContent = `Code expires in ${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
}

function resendOtp() {
    const email = document.getElementById('verified_email').value;
    const name = document.getElementById('verified_name').value;
    const password = document.getElementById('verified_password').value;

    const resendBtn = document.getElementById('resendOtpBtn');
    const originalText = resendBtn.innerHTML;

    resendBtn.disabled = true;
    resendBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';

    fetch('{{ route("register.resend-otp") }}', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
            'Content-Type': 'application/json',
            'Accept': 'application/json',
        },
        body: JSON.stringify({
            email: email,
            name: name,
            password: password
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                New OTP sent successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.card-body').insertBefore(alertDiv, document.querySelector('.card-body').firstChild);

            // Restart timer
            clearInterval(timerInterval);
            startTimer();

            // Clear OTP input
            document.getElementById('otp').value = '';
            document.getElementById('otp').focus();
        } else {
            alert(data.message || 'Failed to resend OTP. Please try again.');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    })
    .finally(() => {
        resendBtn.disabled = false;
        resendBtn.innerHTML = originalText;
    });
}

function goBack() {
    document.getElementById('registrationForm').style.display = 'block';
    document.getElementById('otpVerificationForm').style.display = 'none';
    clearInterval(timerInterval);

    // Clear OTP input
    document.getElementById('otp').value = '';
}

// Auto-submit OTP when 6 digits are entered
document.getElementById('otp').addEventListener('input', function() {
    if (this.value.length === 6) {
        document.getElementById('verifyOtpForm').submit();
    }
});

// Format OTP input to only accept numbers
document.getElementById('otp').addEventListener('keypress', function(e) {
    if (!/[0-9]/.test(e.key)) {
        e.preventDefault();
    }
});
</script>
@endpush
@endsection
