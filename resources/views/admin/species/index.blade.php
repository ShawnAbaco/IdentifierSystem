{{-- resources/views/admin/species/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Species - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-leaf"></i> Manage Species</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.species.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Species
            </a>
            <a href="{{ route('admin.species.export') }}" class="btn btn-info">
                <i class="fas fa-download"></i> Export
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.species.index') }}" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Search by name..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="category" class="form-select">
                        <option value="">All Categories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">All Status</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Species Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Common Name</th>
                            <th>Scientific Name</th>
                            <th>Category</th>
                            <th>Conservation Status</th>
                            <th>Status</th>
                            <th>Identifications</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($species as $specie)
                            <tr>
                                <td>{{ $specie->id }}</td>
                                <td>
                                    <a href="{{ route('admin.species.show', $specie) }}">
                                        {{ $specie->common_name }}
                                    </a>
                                </td>
                                <td><em>{{ $specie->scientific_name ?? 'N/A' }}</em></td>
                                <td>
                                    <span class="badge bg-info">{{ $specie->category->name }}</span>
                                </td>
                                <td>
                                    @if($specie->conservation_status)
                                        <span class="badge bg-{{ $specie->conservation_status_color }}">
                                            {{ $specie->conservation_status }}
                                        </span>
                                    @else
                                        <span class="text-muted">Not specified</span>
                                    @endif
                                </td>
                                <td>
                                    @if($specie->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary">{{ $specie->identifications_count ?? 0 }}</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.species.edit', $specie) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.species.toggle-status', $specie) }}"
                                              method="POST" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-info"
                                                    title="{{ $specie->is_active ? 'Deactivate' : 'Activate' }}">
                                                <i class="fas fa-{{ $specie->is_active ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        <form action="{{ route('admin.species.destroy', $specie) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure? This cannot be undone.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">No species found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $species->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
