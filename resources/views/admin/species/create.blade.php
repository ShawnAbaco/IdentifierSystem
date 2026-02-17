{{-- resources/views/admin/species/create.blade.php --}}
@extends('layouts.app')

@section('title', 'Add New Species - Admin')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2><i class="fas fa-plus-circle"></i> Add New Species</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('admin.species.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.species.store') }}" method="POST">
                @csrf

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="common_name" class="form-label">Common Name *</label>
                        <input type="text" class="form-control @error('common_name') is-invalid @enderror"
                               id="common_name" name="common_name" value="{{ old('common_name') }}" required>
                        @error('common_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="scientific_name" class="form-label">Scientific Name</label>
                        <input type="text" class="form-control @error('scientific_name') is-invalid @enderror"
                               id="scientific_name" name="scientific_name" value="{{ old('scientific_name') }}">
                        @error('scientific_name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="category_id" class="form-label">Category *</label>
                        <select class="form-select @error('category_id') is-invalid @enderror"
                                id="category_id" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label for="conservation_status" class="form-label">Conservation Status</label>
                        <select class="form-select @error('conservation_status') is-invalid @enderror"
                                id="conservation_status" name="conservation_status">
                            <option value="">Select Status</option>
                            <option value="Least Concern" {{ old('conservation_status') == 'Least Concern' ? 'selected' : '' }}>Least Concern</option>
                            <option value="Common" {{ old('conservation_status') == 'Common' ? 'selected' : '' }}>Common</option>
                            <option value="Rare" {{ old('conservation_status') == 'Rare' ? 'selected' : '' }}>Rare</option>
                            <option value="Vulnerable" {{ old('conservation_status') == 'Vulnerable' ? 'selected' : '' }}>Vulnerable</option>
                            <option value="Endangered" {{ old('conservation_status') == 'Endangered' ? 'selected' : '' }}>Endangered</option>
                            <option value="Critically Endangered" {{ old('conservation_status') == 'Critically Endangered' ? 'selected' : '' }}>Critically Endangered</option>
                        </select>
                        @error('conservation_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mb-3">
                    <label for="description" class="form-label">Description *</label>
                    <textarea class="form-control @error('description') is-invalid @enderror"
                              id="description" name="description" rows="4" required>{{ old('description') }}</textarea>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="habitat" class="form-label">Habitat</label>
                    <textarea class="form-control @error('habitat') is-invalid @enderror"
                              id="habitat" name="habitat" rows="2">{{ old('habitat') }}</textarea>
                    @error('habitat')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="image_url" class="form-label">Image URL</label>
                    <input type="url" class="form-control @error('image_url') is-invalid @enderror"
                           id="image_url" name="image_url" value="{{ old('image_url') }}">
                    @error('image_url')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="characteristics" class="form-label">Characteristics (one per line)</label>
                    <textarea class="form-control @error('characteristics') is-invalid @enderror"
                              id="characteristics" name="characteristics[]" rows="3">{{ old('characteristics') ? implode("\n", old('characteristics')) : '' }}</textarea>
                    <small class="text-muted">Enter each characteristic on a new line</small>
                    @error('characteristics')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="fun_facts" class="form-label">Fun Facts (one per line)</label>
                    <textarea class="form-control @error('fun_facts') is-invalid @enderror"
                              id="fun_facts" name="fun_facts[]" rows="3">{{ old('fun_facts') ? implode("\n", old('fun_facts')) : '' }}</textarea>
                    <small class="text-muted">Enter each fun fact on a new line</small>
                    @error('fun_facts')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="medicinal_uses" class="form-label">Medicinal Uses (one per line)</label>
                    <textarea class="form-control @error('medicinal_uses') is-invalid @enderror"
                              id="medicinal_uses" name="medicinal_uses[]" rows="3">{{ old('medicinal_uses') ? implode("\n", old('medicinal_uses')) : '' }}</textarea>
                    <small class="text-muted">Enter each medicinal use on a new line</small>
                    @error('medicinal_uses')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="cultural_significance" class="form-label">Cultural Significance (one per line)</label>
                    <textarea class="form-control @error('cultural_significance') is-invalid @enderror"
                              id="cultural_significance" name="cultural_significance[]" rows="3">{{ old('cultural_significance') ? implode("\n", old('cultural_significance')) : '' }}</textarea>
                    <small class="text-muted">Enter each cultural significance on a new line</small>
                    @error('cultural_significance')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="is_active" name="is_active"
                           {{ old('is_active', true) ? 'checked' : '' }}>
                    <label class="form-check-label" for="is_active">Active</label>
                </div>

                <div class="text-end">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Create Species
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
