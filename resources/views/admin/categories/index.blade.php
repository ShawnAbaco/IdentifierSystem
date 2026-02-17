{{-- resources/views/admin/categories/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Manage Categories - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-tags"></i> Manage Categories</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Category
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.categories.index') }}" class="row">
                <div class="col-md-10">
                    <input type="text" name="search" class="form-control" placeholder="Search categories..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Categories Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Icon</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Description</th>
                            <th>Species Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>
                                    @if($category->icon)
                                        <i class="fas {{ $category->icon }} fa-2x text-success"></i>
                                    @else
                                        <i class="fas fa-folder fa-2x text-secondary"></i>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('admin.categories.show', $category) }}">
                                        {{ $category->name }}
                                    </a>
                                </td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>{{ Str::limit($category->description, 50) }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $category->species_count }}</span>
                                </td>
                                <td>{{ $category->created_at->format('M d, Y') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.categories.edit', $category) }}"
                                           class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.categories.destroy', $category) }}"
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('Are you sure? This will delete the category.')">
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
                                <td colspan="8" class="text-center">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
