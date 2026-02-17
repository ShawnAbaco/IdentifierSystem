{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit Profile - ' . Auth::user()->name)

@push('styles')
<style>
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 4px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        object-fit: cover;
        cursor: pointer;
        transition: all 0.3s;
    }
    .profile-avatar:hover {
        opacity: 0.8;
        transform: scale(1.05);
        border-color: #20c997;
    }
    .profile-card {
        background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
        border-radius: 15px;
        padding: 30px;
        margin-bottom: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-left: 4px solid #28a745;
    }
    .profile-card h4 {
        color: #28a745;
        margin-bottom: 20px;
        font-weight: bold;
    }
    .profile-card h4 i {
        margin-right: 10px;
    }
    .info-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 5px;
    }
    .avatar-upload {
        position: relative;
        display: inline-block;
    }
    .avatar-upload-overlay {
        position: absolute;
        bottom: 0;
        right: 0;
        background: #28a745;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid white;
        transition: all 0.3s;
    }
    .avatar-upload-overlay:hover {
        background: #20c997;
        transform: scale(1.1);
    }
    .stat-badge {
        background: #e9ecef;
        border-radius: 20px;
        padding: 8px 15px;
        font-size: 0.9rem;
        margin-right: 10px;
        margin-bottom: 10px;
        display: inline-block;
    }
    .stat-badge i {
        color: #28a745;
        margin-right: 5px;
    }
    .activity-item {
        padding: 12px;
        border-bottom: 1px solid #e9ecef;
        transition: all 0.2s;
    }
    .activity-item:hover {
        background: #f8f9fa;
        transform: translateX(5px);
    }
    .activity-item:last-child {
        border-bottom: none;
    }
    .btn-update {
        background: #28a745;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-update:hover {
        background: #218838;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
    }
    .btn-cancel {
        background: #6c757d;
        color: white;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s;
    }
    .btn-cancel:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(108, 117, 125, 0.3);
    }
    .password-section {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        border-radius: 10px;
        padding: 20px;
        margin-top: 20px;
    }
    .password-section h5 {
        color: #856404;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-user-edit text-success"></i> Edit Profile</h2>
            <p class="text-muted">Update your personal information and settings</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Back to Profile
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Left Column - Avatar & Stats -->
        <div class="col-md-4">
            <!-- Avatar Card -->
            <div class="profile-card text-center">
                <h4><i class="fas fa-camera"></i> Profile Picture</h4>

                <div class="avatar-upload mb-3">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="profile-avatar" id="avatarPreview">
                    <div class="avatar-upload-overlay" onclick="document.getElementById('avatar').click();">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>

                <p class="text-muted small">Click the camera icon to change your profile picture</p>
                <p class="text-muted small mb-0">Max size: 2MB. Formats: JPG, PNG, GIF</p>
            </div>

            <!-- Statistics Card -->
            <div class="profile-card">
                <h4><i class="fas fa-chart-bar"></i> Your Statistics</h4>

                <div class="stat-badge">
                    <i class="fas fa-leaf"></i>
                    <strong>{{ $totalIdentifications ?? Auth::user()->identifications()->count() }}</strong> Identifications
                </div>

                <div class="stat-badge">
                    <i class="fas fa-calendar-alt"></i>
                    Member since {{ Auth::user()->created_at->format('M Y') }}
                </div>

                <div class="stat-badge">
                    <i class="fas fa-clock"></i>
                    Last updated {{ Auth::user()->updated_at->diffForHumans() }}
                </div>

                @if(Auth::user()->email_verified_at)
                    <div class="stat-badge">
                        <i class="fas fa-check-circle text-success"></i>
                        Email Verified
                    </div>
                @else
                    <div class="stat-badge">
                        <i class="fas fa-exclamation-triangle text-warning"></i>
                        Email Not Verified
                    </div>
                @endif
            </div>

            <!-- Recent Activity Preview -->
            @if(isset($recentIdentifications) && $recentIdentifications->count() > 0)
            <div class="profile-card">
                <h4><i class="fas fa-history"></i> Recent Activity</h4>

                @foreach($recentIdentifications->take(3) as $identification)
                <div class="activity-item">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong>{{ $identification->identified_as }}</strong>
                            <br>
                            <small class="text-muted">{{ $identification->created_at->diffForHumans() }}</small>
                        </div>
                        <span class="badge bg-{{ $identification->confidence > 0.7 ? 'success' : 'warning' }}">
                            {{ $identification->confidence_percentage }}
                        </span>
                    </div>
                </div>
                @endforeach

                <div class="text-center mt-3">
                    <a href="{{ route('history') }}" class="btn btn-sm btn-outline-success">
                        View All History
                    </a>
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Edit Form -->
        <div class="col-md-8">
            <!-- Profile Edit Form -->
            <div class="profile-card">
                <h4><i class="fas fa-user-circle"></i> Personal Information</h4>

                <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" id="profileForm">
                    @csrf
                    @method('POST')

                    <input type="file" class="d-none" id="avatar" name="avatar" accept="image/*" onchange="previewImage(this);">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label info-label">Full Name</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror"
                                   id="name" name="name" value="{{ old('name', Auth::user()->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label info-label">Email Address</label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror"
                                   id="email" name="email" value="{{ old('email', Auth::user()->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="bio" class="form-label info-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror"
                                  id="bio" name="bio" rows="4" placeholder="Tell us about yourself...">{{ old('bio', Auth::user()->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted" id="bioCounter">Maximum 500 characters</small>
                    </div>

                    <div class="text-end">
                        <button type="reset" class="btn btn-cancel me-2">
                            <i class="fas fa-undo"></i> Reset
                        </button>
                        <button type="submit" class="btn btn-update">
                            <i class="fas fa-save"></i> Update Profile
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change Section -->
            <div class="password-section">
                <h5><i class="fas fa-key"></i> Change Password</h5>
                <p class="small mb-3">Want to change your password? Click the button below.</p>
                <button type="button" class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#passwordModal">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-key"></i> Change Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="{{ route('profile.update-password') }}" method="POST" id="passwordForm">
                    @csrf
                    @method('POST')

                    <div class="mb-3">
                        <label for="current_password" class="form-label">Current Password</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>

                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <small class="text-muted">Minimum 8 characters</small>
                    </div>

                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="new_password_confirmation" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="passwordForm" class="btn btn-warning">
                    <i class="fas fa-save"></i> Update Password
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-check-circle"></i> Success</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="successMessage">
                <!-- Success message will be displayed here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Preview image before upload
function previewImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatarPreview').src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}

// Bio character counter
const bio = document.getElementById('bio');
const bioCounter = document.getElementById('bioCounter');

if (bio) {
    bio.addEventListener('input', function() {
        const maxLength = 500;
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;

        bioCounter.textContent = `${remaining} characters remaining`;
        bioCounter.style.color = remaining < 0 ? '#dc3545' : '#6c757d';
    });
}

// Confirm before leaving if form is dirty
let formChanged = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('profileForm');
    const inputs = form.querySelectorAll('input, select, textarea');

    inputs.forEach(input => {
        input.addEventListener('change', () => formChanged = true);
        input.addEventListener('keyup', () => formChanged = true);
    });

    window.addEventListener('beforeunload', function(e) {
        if (formChanged) {
            e.preventDefault();
            e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        }
    });

    form.addEventListener('submit', function() {
        formChanged = false;
    });

    // Show success message from session
    @if(session('success'))
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successMessage').innerHTML = "{{ session('success') }}";
        successModal.show();
    @endif

    @if(session('error'))
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: "{{ session('error') }}"
        });
    @endif
});

// Password validation
document.getElementById('passwordForm')?.addEventListener('submit', function(e) {
    const newPass = document.getElementById('new_password').value;
    const confirmPass = document.getElementById('new_password_confirmation').value;

    if (newPass !== confirmPass) {
        e.preventDefault();
        Swal.fire({
            icon: 'error',
            title: 'Password Mismatch',
            text: 'New password and confirmation do not match!'
        });
    }
});

// Toggle password visibility (optional)
function togglePasswordVisibility(inputId) {
    const input = document.getElementById(inputId);
    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
    input.setAttribute('type', type);
}
</script>
@endpush
