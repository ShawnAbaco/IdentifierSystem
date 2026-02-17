{{-- resources/views/identification/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Identification Details - ' . ($identification->species->common_name ?? $identification->identified_as))

@push('styles')
<style>
    .detail-image {
        width: 100%;
        max-height: 400px;
        object-fit: contain;
        border-radius: 10px;
        border: 2px solid #28a745;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
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
    .prediction-item {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .prediction-item:last-child {
        border-bottom: none;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .similar-species-card {
        transition: all 0.3s;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        overflow: hidden;
        cursor: pointer;
    }
    .similar-species-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        border-color: #28a745;
    }
    .similar-species-image {
        height: 150px;
        background-size: cover;
        background-position: center;
    }
    .feedback-btn {
        border-radius: 20px;
        padding: 10px 20px;
        margin: 0 5px;
    }
    .feedback-btn.correct {
        background-color: #28a745;
        color: white;
        border-color: #28a745;
    }
    .feedback-btn.incorrect {
        background-color: #dc3545;
        color: white;
        border-color: #dc3545;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>
                <i class="fas fa-leaf text-success"></i>
                Identification Details
            </h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('history') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to History
            </a>
            <form action="{{ route('identification.destroy', $identification) }}"
                  method="POST"
                  class="d-inline"
                  onsubmit="return confirm('Are you sure you want to delete this identification?');">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </form>
        </div>
    </div>

    <div class="row">
        <!-- Main Content -->
        <div class="col-md-8">
            <!-- Image Section -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-image"></i> Captured Image</h5>
                </div>
                <div class="card-body text-center">
                    @if($identification->image_url)
                        <img src="{{ $identification->image_url }}"
                             class="detail-image"
                             alt="{{ $identification->identified_as }}"
                             onclick="showImageModal('{{ $identification->image_url }}', '{{ $identification->identified_as }}')">
                    @else
                        <div class="bg-secondary text-white p-5 rounded">
                            <i class="fas fa-image fa-5x"></i>
                            <p class="mt-3">No image available</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Basic Information -->
            <div class="info-card">
                <h5><i class="fas fa-info-circle"></i> Basic Information</h5>
                <table class="table table-borderless">
                    <tr>
                        <th style="width: 150px;">Identified As:</th>
                        <td>
                            <span class="fw-bold">{{ $identification->identified_as }}</span>
                            @if($identification->species)
                                <br>
                                <small class="text-muted">
                                    <em>{{ $identification->species->scientific_name }}</em>
                                </small>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Confidence:</th>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="progress flex-grow-1 me-3" style="height: 15px;">
                                    <div class="progress-bar {{ $identification->confidence > 0.7 ? 'bg-success' : ($identification->confidence > 0.5 ? 'bg-warning' : 'bg-danger') }}"
                                         style="width: {{ $identification->confidence * 100 }}%">
                                    </div>
                                </div>
                                <span class="fw-bold">{{ $identification->confidence_percentage }}</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Date & Time:</th>
                        <td>
                            <i class="fas fa-calendar"></i> {{ $identification->created_at->format('F j, Y') }}<br>
                            <i class="fas fa-clock"></i> {{ $identification->created_at->format('g:i:s A') }}
                        </td>
                    </tr>
                    @if($identification->location)
                    <tr>
                        <th>Location:</th>
                        <td><i class="fas fa-map-marker-alt"></i> {{ $identification->location }}</td>
                    </tr>
                    @endif
                    @if($identification->user_notes)
                    <tr>
                        <th>Notes:</th>
                        <td>
                            <i class="fas fa-sticky-note"></i>
                            "{{ $identification->user_notes }}"
                        </td>
                    </tr>
                    @endif
                </table>
            </div>

            <!-- All Predictions -->
            @if($identification->all_predictions)
            <div class="info-card">
                <h5><i class="fas fa-chart-bar"></i> All Predictions</h5>
                @foreach($identification->all_predictions as $prediction)
                    <div class="prediction-item">
                        <div class="d-flex justify-content-between mb-1">
                            <span>{{ $prediction['className'] }}</span>
                            <span class="badge bg-{{ $prediction['probability'] > 0.5 ? 'success' : 'secondary' }}">
                                {{ number_format($prediction['probability'] * 100, 1) }}%
                            </span>
                        </div>
                        <div class="progress">
                            <div class="progress-bar {{ $prediction['probability'] > 0.5 ? 'bg-success' : 'bg-secondary' }}"
                                 style="width: {{ $prediction['probability'] * 100 }}%">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            <!-- Feedback Section -->
            <div class="info-card">
                <h5><i class="fas fa-check-circle"></i> Feedback</h5>
                <p class="text-muted mb-3">Was this identification correct?</p>

                @if($identification->is_correct !== null)
                    <div class="alert alert-{{ $identification->is_correct ? 'success' : 'danger' }}">
                        <i class="fas fa-{{ $identification->is_correct ? 'check' : 'times' }}-circle"></i>
                        You marked this as {{ $identification->is_correct ? 'correct' : 'incorrect' }}
                    </div>
                @else
                    <div class="btn-group" role="group">
                        <button class="btn btn-outline-success feedback-btn correct" onclick="submitFeedback(true)">
                            <i class="fas fa-check"></i> Correct
                        </button>
                        <button class="btn btn-outline-danger feedback-btn incorrect" onclick="submitFeedback(false)">
                            <i class="fas fa-times"></i> Incorrect
                        </button>
                    </div>
                @endif
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Species Details -->
            @if($identification->species)
                <div class="info-card">
                    <h5><i class="fas fa-leaf"></i> Species Information</h5>
                    <h6 class="fw-bold">{{ $identification->species->common_name }}</h6>
                    @if($identification->species->scientific_name)
                        <p class="text-muted"><em>{{ $identification->species->scientific_name }}</em></p>
                    @endif

                    <p>{{ Str::limit($identification->species->description, 150) }}</p>

                    @if($identification->species->conservation_status)
                        <p>
                            <strong>Status:</strong>
                            <span class="badge bg-{{ $identification->species->conservation_status_color }}">
                                {{ $identification->species->conservation_status }}
                            </span>
                        </p>
                    @endif

                    <a href="#" class="btn btn-outline-success btn-sm w-100" onclick="showSpeciesPreview({{ $identification->species->id }})">
                        <i class="fas fa-info-circle"></i> View Full Details
                    </a>
                </div>
            @endif

            <!-- Similar Species -->
            @if($similarSpecies && $similarSpecies->count() > 0)
                <div class="info-card">
                    <h5><i class="fas fa-seedling"></i> Similar Species</h5>
                    @foreach($similarSpecies as $species)
                        <div class="similar-species-card mb-3" onclick="showSpeciesPreview({{ $species->id }})">
                            @if($species->image_url)
                                <div class="similar-species-image" style="background-image: url('{{ $species->image_url }}')"></div>
                            @else
                                <div class="similar-species-image bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-leaf fa-3x text-white"></i>
                                </div>
                            @endif
                            <div class="p-2 text-center">
                                <strong>{{ $species->common_name }}</strong>
                                <br>
                                <small class="text-muted">{{ $species->scientific_name }}</small>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <!-- Quick Stats -->
            <div class="info-card">
                <h5><i class="fas fa-chart-pie"></i> Quick Stats</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fas fa-calendar-day text-success"></i>
                        <strong>ID #:</strong> {{ $identification->id }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-user text-success"></i>
                        <strong>User:</strong> {{ $identification->user->name }}
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-clock text-success"></i>
                        <strong>Age:</strong> {{ $identification->created_at->diffForHumans() }}
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-image"></i> <span id="modalImageTitle"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="" id="modalImage" class="img-fluid" alt="Captured image">
            </div>
        </div>
    </div>
</div>

<!-- Species Preview Modal -->
<div class="modal fade" id="speciesPreviewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-leaf"></i> <span id="preview-title"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="preview-content">
                <!-- Dynamic content loaded via JavaScript -->
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Function to show image in modal
function showImageModal(imageUrl, title) {
    document.getElementById('modalImage').src = imageUrl;
    document.getElementById('modalImageTitle').textContent = title;

    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

// Function to show species preview
// Function to show species preview
function showSpeciesPreview(speciesId) {
    window.location.href = `/species/${speciesId}`;
}

// Function to submit feedback
function submitFeedback(isCorrect) {
    fetch(`/identification/{{ $identification->id }}/feedback`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            is_correct: isCorrect
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Swal.fire({
                icon: 'success',
                title: 'Feedback Submitted',
                text: 'Thank you for your feedback!',
                timer: 2000,
                showConfirmButton: false
            }).then(() => {
                location.reload();
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to submit feedback'
        });
    });
}
</script>
@endpush
