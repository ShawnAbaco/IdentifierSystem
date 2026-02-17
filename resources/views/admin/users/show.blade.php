{{-- resources/views/admin/users/show.blade.php --}}
@extends('layouts.app')

@section('title', 'User Details - ' . $user->name)

@push('styles')
<style>
    .profile-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 0 0 20px 20px;
    }
    .profile-avatar {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        border: 5px solid white;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        object-fit: cover;
    }
    .info-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }
    .info-card h5 {
        color: #28a745;
        margin-bottom: 15px;
        font-weight: bold;
    }
    .info-card h5 i {
        margin-right: 8px;
    }
    .detail-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .detail-item:last-child {
        border-bottom: none;
    }
    .detail-label {
        font-weight: bold;
        color: #555;
        width: 120px;
        display: inline-block;
    }
    .detail-value {
        color: #333;
    }
    .identification-card {
        transition: all 0.3s;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    .identification-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #28a745;
    }
    .badge-role {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .badge-role.admin {
        background: #cce5ff;
        color: #004085;
    }
    .badge-role.user {
        background: #d4edda;
        color: #155724;
    }
    .action-btn {
        padding: 8px 20px;
        border-radius: 5px;
        margin: 0 5px;
    }
</style>
@endpush

@section('content')
<!-- Profile Header -->
<div class="profile-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-2 text-center">
                <img src="{{ $user->avatar_url }}" alt="{{ $user->name }}" class="profile-avatar">
            </div>
            <div class="col-md-6">
                <h1 class="display-5">{{ $user->name }}</h1>
                <p class="lead mb-2">
                    <i class="fas fa-envelope"></i> {{ $user->email }}
                </p>
                <p class="mb-2">
                    <span class="badge-role {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                        <i class="fas fa-{{ $user->role === 'admin' ? 'crown' : 'user' }}"></i>
                        {{ ucfirst($user->role) }}
                    </span>
                    <span class="ms-3">
                        <i class="fas fa-calendar-alt"></i> Joined: {{ $user->created_at->format('F j, Y') }}
                    </span>
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-light action-btn">
                    <i class="fas fa-edit"></i> Edit User
                </a>
                <button type="button" class="btn btn-danger action-btn" onclick="confirmDelete({{ $user->id }})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Left Column - User Details -->
        <div class="col-md-4">
            <!-- Personal Information -->
            <div class="info-card">
                <h5><i class="fas fa-user-circle"></i> Personal Information</h5>
                <div class="detail-item">
                    <span class="detail-label">Full Name:</span>
                    <span class="detail-value">{{ $user->name }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email Address:</span>
                    <span class="detail-value">{{ $user->email }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Role:</span>
                    <span class="detail-value">
                        <span class="badge-role {{ $user->role === 'admin' ? 'admin' : 'user' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">User ID:</span>
                    <span class="detail-value">#{{ $user->id }}</span>
                </div>
            </div>

            <!-- Account Statistics -->
            <div class="info-card">
                <h5><i class="fas fa-chart-bar"></i> Account Statistics</h5>
                <div class="detail-item">
                    <span class="detail-label">Total Identifications:</span>
                    <span class="detail-value">
                        <span class="badge bg-primary">{{ $user->identifications_count ?? $user->identifications->count() }}</span>
                    </span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Member Since:</span>
                    <span class="detail-value">{{ $user->created_at->format('F j, Y') }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Last Updated:</span>
                    <span class="detail-value">{{ $user->updated_at->diffForHumans() }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Email Verified:</span>
                    <span class="detail-value">
                        @if($user->email_verified_at)
                            <span class="badge bg-success">Yes</span>
                            <small class="text-muted">({{ $user->email_verified_at->diffForHumans() }})</small>
                        @else
                            <span class="badge bg-warning">No</span>
                        @endif
                    </span>
                </div>
            </div>

            <!-- Bio -->
            @if($user->bio)
            <div class="info-card">
                <h5><i class="fas fa-quote-right"></i> Bio</h5>
                <p class="mb-0">{{ $user->bio }}</p>
            </div>
            @endif
        </div>

        <!-- Right Column - Recent Identifications -->
        <div class="col-md-8">
            <div class="info-card">
                <h5><i class="fas fa-leaf"></i> Recent Identifications</h5>

                @if($recentIdentifications && $recentIdentifications->count() > 0)
                    <div class="row">
                        @foreach($recentIdentifications as $identification)
                            <div class="col-md-6">
                                <div class="identification-card">
                                    @if($identification->image_url)
                                        <div class="position-relative">
                                            <img src="{{ $identification->image_url }}"
                                                 class="img-fluid"
                                                 style="width: 100%; height: 150px; object-fit: cover;"
                                                 alt="{{ $identification->identified_as }}">
                                            <span class="position-absolute top-0 end-0 bg-dark text-white p-2 small">
                                                {{ $identification->confidence_percentage }}
                                            </span>
                                        </div>
                                    @endif
                                    <div class="p-3">
                                        <h6 class="mb-1">
                                            @if($identification->species)
                                                <a href="{{ route('species.show', $identification->species) }}" class="text-decoration-none">
                                                    {{ $identification->species->common_name }}
                                                </a>
                                            @else
                                                {{ $identification->identified_as }}
                                            @endif
                                        </h6>
                                        <p class="small text-muted mb-2">
                                            <i class="fas fa-calendar"></i> {{ $identification->created_at->format('M d, Y') }}
                                        </p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="badge bg-{{ $identification->confidence > 0.7 ? 'success' : 'warning' }}">
                                                {{ $identification->confidence_percentage }}
                                            </span>
                                            <a href="{{ route('identification.show', $identification) }}"
                                               class="btn btn-sm btn-outline-success">
                                                View Details
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="text-center mt-3">
                        <a href="{{ route('history') }}?user={{ $user->id }}" class="btn btn-outline-success">
                            <i class="fas fa-history"></i> View All Identifications
                        </a>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-leaf fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No identifications yet.</p>
                    </div>
                @endif
            </div>

            <!-- Quick Actions -->
            <div class="info-card">
                <h5><i class="fas fa-bolt"></i> Quick Actions</h5>
                <div class="row">
                    <div class="col-md-6">
                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-success w-100 mb-2">
                            <i class="fas fa-edit"></i> Edit Profile
                        </a>
                    </div>
                    <div class="col-md-6">
                        <button onclick="resetPassword({{ $user->id }})" class="btn btn-warning w-100 mb-2">
                            <i class="fas fa-key"></i> Reset Password
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button onclick="toggleUserStatus({{ $user->id }})" class="btn btn-info w-100 mb-2">
                            <i class="fas fa-toggle-on"></i> Toggle Status
                        </button>
                    </div>
                    <div class="col-md-6">
                        <button onclick="confirmDelete({{ $user->id }})" class="btn btn-danger w-100 mb-2">
                            <i class="fas fa-trash"></i> Delete User
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete user <strong>{{ $user->name }}</strong>?</p>
                <p class="text-danger"><strong>Note:</strong> All identifications made by this user will also be deleted. This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Delete User</button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal fade" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-key"></i> Reset Password</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="resetPasswordForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password</label>
                        <input type="password" class="form-control" id="new_password" name="password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="password_confirmation" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="resetPasswordForm" class="btn btn-warning">Reset Password</button>
            </div>
        </div>
    </div>
</div>

<!-- Toggle Status Modal -->
<div class="modal fade" id="toggleStatusModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-toggle-on"></i> Toggle User Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to change the status of user <strong>{{ $user->name }}</strong>?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="toggleStatusForm" method="POST">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-info">Confirm</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Confirm delete
function confirmDelete(userId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/users/${userId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Reset password
function resetPassword(userId) {
    const resetForm = document.getElementById('resetPasswordForm');
    resetForm.action = `/admin/users/${userId}/reset-password`;

    const resetModal = new bootstrap.Modal(document.getElementById('resetPasswordModal'));
    resetModal.show();
}

// Toggle user status
function toggleUserStatus(userId) {
    const toggleForm = document.getElementById('toggleStatusForm');
    toggleForm.action = `/admin/users/${userId}/toggle-status`;

    const toggleModal = new bootstrap.Modal(document.getElementById('toggleStatusModal'));
    toggleModal.show();
}

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});
</script>
@endpush
