{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Edit User - ' . $user->name)

@push('styles')
<style>
    .avatar-preview {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 3px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        object-fit: cover;
        cursor: pointer;
        transition: all 0.3s;
    }
    .avatar-preview:hover {
        opacity: 0.8;
        transform: scale(1.05);
    }
    .form-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }
    .form-card h5 {
        color: #28a745;
        margin-bottom: 15px;
        font-weight: bold;
    }
    .form-card h5 i {
        margin-right: 8px;
    }
    .preview-container {
        text-align: center;
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-user-edit"></i> Edit User: {{ $user->name }}</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
                <i class="fas fa-eye"></i> View Profile
            </a>
            <a href="{{ route('admin.users') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Users
            </a>
        </div>
    </div>

    <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Left Column - Avatar and Preview -->
            <div class="col-md-4">
                <div class="form-card">
                    <h5><i class="fas fa-camera"></i> Profile Picture</h5>
                    <div class="preview-container">
                        <img src="{{ $user->avatar_url }}" alt="Avatar Preview"
                             class="avatar-preview mb-3" id="avatarPreview"
                             onclick="document.getElementById('avatar').click();">

                        <div class="mt-3">
                            <label for="avatar" class="btn btn-outline-success w-100">
                                <i class="fas fa-upload"></i> Change Avatar
                            </label>
                            <input type="file" class="d-none" id="avatar" name="avatar"
                                   accept="image/*" onchange="previewImage(this);">
                            <small class="text-muted d-block mt-2">
                                Max size: 2MB. Formats: JPG, PNG, GIF
                            </small>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h5><i class="fas fa-info-circle"></i> User Statistics</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-leaf text-success"></i>
                            <strong>Identifications:</strong>
                            <span class="badge bg-primary">{{ $user->identifications_count ?? $user->identifications()->count() }}</span>
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-calendar-alt text-success"></i>
                            <strong>Joined:</strong> {{ $user->created_at->format('M d, Y') }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-clock text-success"></i>
                            <strong>Last Updated:</strong> {{ $user->updated_at->diffForHumans() }}
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-id-card text-success"></i>
                            <strong>User ID:</strong> #{{ $user->id }}
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Right Column - Edit Form -->
            <div class="col-md-8">
                <div class="form-card">
                    <h5><i class="fas fa-user-circle"></i> Basic Information</h5>

                    <div class="mb-3">
                        <label for="name" class="form-label">Full Name *</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name', $user->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address *</label>
                        <input type="email" class="form-control @error('email') is-invalid @enderror"
                               id="email" name="email" value="{{ old('email', $user->email) }}" required>
                        @error('email')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="role" class="form-label">Role *</label>
                        <select class="form-select @error('role') is-invalid @enderror"
                                id="role" name="role" required>
                            <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>User</option>
                            <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>Admin</option>
                        </select>
                        @error('role')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i>
                            Admin users have access to all management features.
                        </small>
                    </div>
                </div>

                <div class="form-card">
                    <h5><i class="fas fa-pencil-alt"></i> Bio / Additional Information</h5>

                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control @error('bio') is-invalid @enderror"
                                  id="bio" name="bio" rows="4"
                                  placeholder="Write a short bio about the user...">{{ old('bio', $user->bio) }}</textarea>
                        @error('bio')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Maximum 500 characters</small>
                    </div>
                </div>

                <div class="form-card">
                    <h5><i class="fas fa-shield-alt"></i> Account Status</h5>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Email Verification</label>
                                <div>
                                    @if($user->email_verified_at)
                                        <span class="badge bg-success p-2">
                                            <i class="fas fa-check-circle"></i> Verified
                                        </span>
                                        <small class="text-muted d-block mt-1">
                                            {{ $user->email_verified_at->diffForHumans() }}
                                        </small>
                                    @else
                                        <span class="badge bg-warning p-2">
                                            <i class="fas fa-exclamation-triangle"></i> Not Verified
                                        </span>
                                        <button type="button" class="btn btn-sm btn-outline-warning mt-2"
                                                onclick="verifyEmail({{ $user->id }})">
                                            <i class="fas fa-envelope"></i> Verify Now
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Account Created</label>
                                <div>
                                    <span class="badge bg-info p-2">
                                        <i class="fas fa-calendar"></i> {{ $user->created_at->format('M d, Y') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h5><i class="fas fa-key"></i> Password Management</h5>

                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        Leave password fields empty to keep the current password.
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <input type="password" class="form-control @error('password') is-invalid @enderror"
                                       id="password" name="password" minlength="8">
                                @error('password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control"
                                       id="password_confirmation" name="password_confirmation">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="text-end">
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Update User
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Verify Email Modal -->
<div class="modal fade" id="verifyEmailModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-envelope"></i> Verify Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Send email verification to <strong>{{ $user->email }}</strong>?</p>
                <p class="text-muted small">This will mark the email as verified immediately.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form action="{{ route('admin.users.verify-email', $user) }}" method="POST">
                    @csrf
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-check-circle"></i> Verify Email
                    </button>
                </form>
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

// // Verify email
// function verifyEmail(userId) {
//     const verifyModal = new bootstrap.Modal(document.getElementById('verifyEmailModal'));
//     verifyModal.show();
// }

// Confirm before leaving if form is dirty
let formChanged = false;

document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form');
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
});

// Character counter for bio
const bio = document.getElementById('bio');
if (bio) {
    bio.addEventListener('input', function() {
        const maxLength = 500;
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;

        let feedback = this.nextElementSibling;
        if (remaining < 0) {
            feedback.innerHTML = `Maximum ${maxLength} characters (exceeded by ${Math.abs(remaining)})`;
            feedback.classList.add('text-danger');
        } else {
            feedback.innerHTML = `${remaining} characters remaining`;
            feedback.classList.remove('text-danger');
        }
    });
}
</script>
@endpush
