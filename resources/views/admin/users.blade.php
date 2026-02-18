{{-- resources/views/admin/users.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Users - Admin')

@push('styles')
<style>
    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
        transition: all 0.3s;
    }
    .filter-card:hover {
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .status-badge {
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .status-badge.active {
        background: #d4edda;
        color: #155724;
    }
    .status-badge.inactive {
        background: #f8d7da;
        color: #721c24;
    }
    .status-badge.admin {
        background: #cce5ff;
        color: #004085;
    }
    .action-btn {
        padding: 5px 10px;
        margin: 0 2px;
        border-radius: 5px;
        transition: all 0.2s;
    }
    .action-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    .filter-badge {
        background: #28a745;
        color: white;
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 0.8rem;
        margin-left: 5px;
    }
    .clear-filters {
        color: #dc3545;
        cursor: pointer;
        text-decoration: none;
        font-size: 0.9rem;
    }
    .clear-filters:hover {
        text-decoration: underline;
    }
    .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-users-cog"></i> Manage Users</h2>
        </div>
        <div class="col-md-4 text-end">
            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addUserModal">
                <i class="fas fa-user-plus"></i> Add New User
            </button>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0"><i class="fas fa-filter"></i> Filter Users</h5>
            @if(request()->anyFilled(['search', 'role', 'sort', 'date']))
                <span class="filter-badge">
                    <i class="fas fa-filter"></i> Filters Applied
                </span>
            @endif
        </div>

        <form method="GET" action="{{ route('admin.users') }}" class="row g-3" id="filterForm">
            <div class="col-md-3">
                <label for="search" class="form-label">
                    <i class="fas fa-search"></i> Search
                </label>
                <input type="text" class="form-control" id="search" name="search"
                       placeholder="Name or email..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <label for="role" class="form-label">
                    <i class="fas fa-user-tag"></i> Role
                </label>
                <select class="form-select" id="role" name="role">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sort" class="form-label">
                    <i class="fas fa-sort"></i> Sort By
                </label>
                <select class="form-select" id="sort" name="sort">
                    <option value="latest" {{ request('sort') == 'latest' ? 'selected' : '' }}>Latest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                    <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name A-Z</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                    <option value="identifications" {{ request('sort') == 'identifications' ? 'selected' : '' }}>Most IDs</option>
                    <option value="identifications_desc" {{ request('sort') == 'identifications_desc' ? 'selected' : '' }}>Least IDs</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">
                    <i class="fas fa-calendar"></i> Joined Date
                </label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100" id="applyFilter">
                    <i class="fas fa-filter"></i> Apply
                </button>
            </div>
        </form>

        <!-- Active Filters Display -->
        @if(request()->anyFilled(['search', 'role', 'sort', 'date']))
            <div class="mt-3 pt-2 border-top">
                <div class="d-flex align-items-center flex-wrap">
                    <small class="text-muted me-2">Active filters:</small>
                    @if(request('search'))
                        <span class="badge bg-light text-dark me-2 mb-1 p-2">
                            <i class="fas fa-search"></i> "{{ request('search') }}"
                        </span>
                    @endif
                    @if(request('role'))
                        <span class="badge bg-light text-dark me-2 mb-1 p-2">
                            <i class="fas fa-user-tag"></i> {{ ucfirst(request('role')) }}
                        </span>
                    @endif
                    @if(request('sort'))
                        <span class="badge bg-light text-dark me-2 mb-1 p-2">
                            <i class="fas fa-sort"></i> {{ str_replace('_', ' ', request('sort')) }}
                        </span>
                    @endif
                    @if(request('date'))
                        <span class="badge bg-light text-dark me-2 mb-1 p-2">
                            <i class="fas fa-calendar"></i> {{ request('date') }}
                        </span>
                    @endif
                    <a href="{{ route('admin.users') }}" class="clear-filters ms-2">
                        <i class="fas fa-times-circle"></i> Clear all filters
                    </a>
                </div>
            </div>
        @endif
    </div>

    <!-- Results Summary -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="pagination-info">
            Showing {{ $users->firstItem() ?? 0 }} to {{ $users->lastItem() ?? 0 }} of {{ $users->total() }} users
        </div>
        <div class="btn-group btn-group-sm" role="group">
            <button type="button" class="btn btn-outline-secondary" onclick="exportUsers()">
                <i class="fas fa-download"></i> Export
            </button>
            <button type="button" class="btn btn-outline-secondary" onclick="refreshTable()">
                <i class="fas fa-sync-alt"></i> Refresh
            </button>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Avatar</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Identifications</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                        <tr>
                            <td>#{{ $user->id }}</td>
                            <td>
                                <img src="{{ $user->avatar_url }}" alt="Avatar" class="user-avatar">
                            </td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="status-badge {{ $user->role === 'admin' ? 'admin' : 'active' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </td>
                            <td>
                                <span class="status-badge {{ $user->is_active ?? true ? 'active' : 'inactive' }}">
                                    {{ isset($user->is_active) ? ($user->is_active ? 'Active' : 'Inactive') : 'Active' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $user->identifications_count }}</span>
                            </td>
                            <td>{{ $user->created_at->format('M d, Y') }}</td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-info action-btn"
                                            onclick="viewUser({{ $user->id }})" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-warning action-btn"
                                            onclick="editUser({{ $user->id }})" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger action-btn"
                                            onclick="confirmDelete({{ $user->id }})" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-users fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">No users found</h5>
                                @if(request()->anyFilled(['search', 'role', 'sort', 'date']))
                                    <p class="text-muted">Try adjusting your filters</p>
                                    <a href="{{ route('admin.users') }}" class="btn btn-outline-success">
                                        <i class="fas fa-times-circle"></i> Clear Filters
                                    </a>
                                @else
                                    <p class="text-muted">Click "Add New User" to create one</p>
                                @endif
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-between align-items-center mt-4">
                <div class="pagination-info">
                    Page {{ $users->currentPage() }} of {{ $users->lastPage() }}
                </div>
                <div class="d-flex justify-content-center">
                    {{ $users->appends(request()->query())->links() }}
                </div>
                <div class="pagination-info">
                    Total: {{ $users->total() }} users
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-user-plus"></i> Add New User</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addUserForm" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email Address *</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password *</label>
                            <input type="password" class="form-control" id="password" name="password" required minlength="8">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="password_confirmation" class="form-label">Confirm Password *</label>
                            <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="role" class="form-label">Role *</label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="user">User</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="avatar" class="form-label">Avatar</label>
                            <input type="file" class="form-control" id="avatar" name="avatar" accept="image/*">
                            <small class="text-muted">Max size: 2MB. Formats: JPG, PNG, GIF</small>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">Bio</label>
                        <textarea class="form-control" id="bio" name="bio" rows="3" maxlength="500"></textarea>
                        <small class="text-muted">Maximum 500 characters</small>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" form="addUserForm" class="btn btn-success">
                    <i class="fas fa-save"></i> Add User
                </button>
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
                <div class="text-center mb-3">
                    <i class="fas fa-user-times fa-4x text-danger"></i>
                </div>
                <p class="text-center">Are you sure you want to delete this user?</p>
                <p class="text-center text-danger"><strong>This action cannot be undone.</strong></p>
                <p class="text-center text-muted small">All identifications made by this user will also be deleted.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <form id="deleteForm" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete User
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="exportModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-download"></i> Export Users</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3">Choose export format. The file will open in a new tab.</p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportFormat('csv')">
                                <i class="fas fa-file-csv"></i> CSV
                                <br>
                                <small class="text-muted">Comma separated values</small>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportFormat('excel')">
                                <i class="fas fa-file-excel"></i> Excel
                                <br>
                                <small class="text-muted">XLSX format</small>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportFormat('pdf')">
                                <i class="fas fa-file-pdf"></i> PDF
                                <br>
                                <small class="text-muted">Document format</small>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="d-grid">
                            <button class="btn btn-outline-success" onclick="exportFormat('docx')">
                                <i class="fas fa-file-word"></i> DOCX
                                <br>
                                <small class="text-muted">Word document</small>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
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

    // Handle export errors
window.addEventListener('load', function() {
    // Check for export error in URL
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('export_error')) {
        Swal.fire({
            icon: 'error',
            title: 'Export Failed',
            text: decodeURIComponent(urlParams.get('export_error'))
        });
    }
});


// View user details
function viewUser(userId) {
    window.location.href = `/admin/users/${userId}`;
}

// Edit user
function editUser(userId) {
    window.location.href = `/admin/users/${userId}/edit`;
}

// Confirm delete
function confirmDelete(userId) {
    const deleteForm = document.getElementById('deleteForm');
    deleteForm.action = `/admin/users/${userId}`;

    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
}

// Export users
function exportUsers() {
    const exportModal = new bootstrap.Modal(document.getElementById('exportModal'));
    exportModal.show();
}

function exportFormat(format) {
    // Get current filter parameters
    const search = document.getElementById('search').value;
    const role = document.getElementById('role').value;
    const sort = document.getElementById('sort').value;
    const date = document.getElementById('date').value;

    // Build URL with filters
    let url = `/admin/users/export?format=${format}`;
    if (search) url += `&search=${encodeURIComponent(search)}`;
    if (role) url += `&role=${role}`;
    if (sort) url += `&sort=${sort}`;
    if (date) url += `&date=${date}`;

    // Close modal
    const exportModal = bootstrap.Modal.getInstance(document.getElementById('exportModal'));
    exportModal.hide();

    // Show loading message
    Swal.fire({
        title: 'Generating Export...',
        text: 'Your file will open in a new tab.',
        icon: 'info',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    // Open in new tab
    window.open(url, '_blank');

    // Close loading after 2 seconds
    setTimeout(() => {
        Swal.close();
        // Show success message
        Swal.fire({
            icon: 'success',
            title: 'Export Started',
            text: 'Your file should open in a new tab.',
            timer: 2000,
            showConfirmButton: false
        });
    }, 2000);
}

// Refresh table
function refreshTable() {
    // Reload current page with same filters
    window.location.reload();
}

// Auto-submit filters when select changes (optional)
document.addEventListener('DOMContentLoaded', function() {
    // Enable tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto-submit on select changes (optional - remove if you don't want this)
    // const autoSubmit = false; // Set to true to enable auto-submit
    // if (autoSubmit) {
    //     document.getElementById('role').addEventListener('change', function() {
    //         document.getElementById('filterForm').submit();
    //     });
    //     document.getElementById('sort').addEventListener('change', function() {
    //         document.getElementById('filterForm').submit();
    //     });
    //     document.getElementById('date').addEventListener('change', function() {
    //         document.getElementById('filterForm').submit();
    //     });
    // }

    // Show success message from session
    @if(session('success'))
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successMessage').innerHTML = "{{ session('success') }}";
        successModal.show();
    @endif
});

// Debounce search input to prevent too many requests
let searchTimeout;
document.getElementById('search')?.addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        // Uncomment to enable live search
        // document.getElementById('filterForm').submit();
    }, 500);
});

// Keyboard shortcut for search (Ctrl+F to focus search)
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.getElementById('search').focus();
    }
});

// Export to CSV function (can be expanded)
function exportToCSV(users) {
    // This would normally call a backend endpoint
    console.log('Exporting to CSV...');
}

// Quick filter by role
function filterByRole(role) {
    document.getElementById('role').value = role;
    document.getElementById('filterForm').submit();
}
</script>
@endpush
