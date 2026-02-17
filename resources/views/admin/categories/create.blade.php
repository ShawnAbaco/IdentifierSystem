{{-- resources/views/admin/categories/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Category - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-plus-circle"></i> Add New Category</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.categories.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="name" class="form-label">Category Name *</label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror"
                           id="name" name="name" value="{{ old('name') }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="icon" class="form-label">Font Awesome Icon Class</label>
                    <input type="text" class="form-control @error('icon') is-invalid @enderror"
                           id="icon" name="icon" value="{{ old('icon') }}"
                           placeholder="e.g., fa-tree, fa-seedling, fa-leaf">
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Enter Font Awesome icon class without the 'fas' prefix.
                        <a href="https://fontawesome.com/icons" target="_blank">Browse icons</a>
                    </small>
                    @error('icon')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="3">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Create Category
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
