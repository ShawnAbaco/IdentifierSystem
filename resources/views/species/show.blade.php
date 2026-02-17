{{-- resources/views/species/show.blade.php --}}
@extends('layouts.app')

@section('title', $species->common_name . ' - Species Details')

@push('styles')
<style>
    .species-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        padding: 40px 0;
        margin-bottom: 30px;
        border-radius: 0 0 20px 20px;
    }
    .species-image {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        border-radius: 10px;
        border: 2px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    .info-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border-left: 4px solid #28a745;
    }
    .info-section h3 {
        color: #28a745;
        margin-bottom: 15px;
        font-weight: bold;
    }
    .info-section h4 {
        color: #28a745;
        margin: 15px 0 10px;
        font-size: 1.2rem;
    }
    .badge-status {
        font-size: 0.9rem;
        padding: 8px 15px;
        border-radius: 20px;
    }
    .fact-card {
        background: white;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 10px;
        border-left: 3px solid #28a745;
        box-shadow: 0 2px 5px rgba(0,0,0,0.05);
    }
    .fact-card i {
        color: #28a745;
        margin-right: 10px;
    }
    .related-species-card {
        transition: all 0.3s;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
        margin-bottom: 15px;
    }
    .related-species-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #28a745;
    }
    .related-species-image {
        height: 150px;
        background-size: cover;
        background-position: center;
        background-color: #f0f0f0;
    }
    .characteristic-item {
        padding: 8px;
        background: white;
        border-radius: 5px;
        margin-bottom: 5px;
    }
    .characteristic-item i {
        color: #28a745;
        margin-right: 8px;
    }
</style>
@endpush

@section('content')
<!-- Header -->
<div class="species-header">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-4">{{ $species->common_name }}</h1>
                @if($species->scientific_name)
                    <p class="lead"><em>{{ $species->scientific_name }}</em></p>
                @endif
                <p>
                    <span class="badge bg-light text-dark p-2 me-2">
                        <i class="fas fa-folder"></i> {{ $species->category->name }}
                    </span>
                    @if($species->conservation_status)
                        <span class="badge bg-{{ $species->conservation_status_color ?? 'secondary' }} p-2">
                            <i class="fas fa-shield-alt"></i> {{ $species->conservation_status }}
                        </span>
                    @endif
                </p>
            </div>
            <div class="col-md-4 text-end">
                <a href="{{ route('identify') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-camera"></i> Identify New
                </a>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Image Section -->
            @if($species->image_url)
                <div class="text-center mb-4">
                    <img src="{{ $species->image_url }}" class="species-image" alt="{{ $species->common_name }}">
                </div>
            @endif

            <!-- Description -->
            <div class="info-section">
                <h3><i class="fas fa-align-left"></i> Description</h3>
                <p class="lead">{{ $species->description }}</p>
            </div>

            <!-- Characteristics -->
            @if($species->characteristics && count($species->characteristics) > 0)
                <div class="info-section">
                    <h3><i class="fas fa-list"></i> Characteristics</h3>
                    <div class="row">
                        @foreach($species->characteristics as $characteristic)
                            <div class="col-md-6">
                                <div class="characteristic-item">
                                    <i class="fas fa-check-circle"></i> {{ $characteristic }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Habitat -->
            @if($species->habitat)
                <div class="info-section">
                    <h3><i class="fas fa-tree"></i> Habitat</h3>
                    <p>{{ $species->habitat }}</p>
                </div>
            @endif

            <!-- Fun Facts -->
            @if($species->fun_facts && count($species->fun_facts) > 0)
                <div class="info-section">
                    <h3><i class="fas fa-star"></i> Fun Facts</h3>
                    @foreach($species->fun_facts as $fact)
                        <div class="fact-card">
                            <i class="fas fa-lightbulb text-warning"></i> {{ $fact }}
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Medicinal Uses -->
            @if($species->medicinal_uses && count($species->medicinal_uses) > 0)
                <div class="info-section">
                    <h3><i class="fas fa-medkit"></i> Medicinal Uses</h3>
                    @foreach($species->medicinal_uses as $use)
                        <div class="fact-card">
                            <i class="fas fa-leaf"></i> {{ $use }}
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Cultural Significance -->
            @if($species->cultural_significance && count($species->cultural_significance) > 0)
                <div class="info-section">
                    <h3><i class="fas fa-globe-asia"></i> Cultural Significance</h3>
                    @foreach($species->cultural_significance as $significance)
                        <div class="fact-card">
                            <i class="fas fa-history"></i> {{ $significance }}
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Quick Info Card -->
            <div class="info-section">
                <h4><i class="fas fa-info-circle"></i> Quick Info</h4>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-folder text-success"></i>
                        <strong>Category:</strong> {{ $species->category->name }}
                    </li>
                    @if($species->scientific_name)
                        <li class="mb-2">
                            <i class="fas fa-microscope text-success"></i>
                            <strong>Scientific Name:</strong><br>
                            <em>{{ $species->scientific_name }}</em>
                        </li>
                    @endif
                    @if($species->conservation_status)
                        <li class="mb-2">
                            <i class="fas fa-shield-alt text-success"></i>
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $species->conservation_status_color ?? 'secondary' }}">
                                {{ $species->conservation_status }}
                            </span>
                        </li>
                    @endif
                    @if($species->habitat)
                        <li class="mb-2">
                            <i class="fas fa-map-marker-alt text-success"></i>
                            <strong>Habitat:</strong><br>
                            {{ $species->habitat }}
                        </li>
                    @endif
                </ul>
            </div>

            <!-- Related Species -->
            @if($relatedSpecies && $relatedSpecies->count() > 0)
                <div class="info-section">
                    <h4><i class="fas fa-seedling"></i> Related Species</h4>
                    @foreach($relatedSpecies as $related)
                        <div class="related-species-card" onclick="window.location.href='/species/{{ $related->id }}'">
                            @if($related->image_url)
                                <div class="related-species-image" style="background-image: url('{{ $related->image_url }}')"></div>
                            @else
                                <div class="related-species-image bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-leaf fa-3x text-white"></i>
                                </div>
                            @endif
                            <div class="p-2 text-center">
                                <strong>{{ $related->common_name }}</strong>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Actions -->
            <div class="info-section">
                <h4><i class="fas fa-actions"></i> Actions</h4>
                <a href="{{ route('identify') }}" class="btn btn-success w-100 mb-2">
                    <i class="fas fa-camera"></i> Identify This Plant
                </a>
                <a href="{{ route('history') }}" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-history"></i> View My History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
