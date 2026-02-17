{{-- resources/views/history.blade.php --}}
@extends('layouts.app')

@section('title', 'Identification History')

@push('styles')
<style>
    .history-card {
        transition: all 0.3s;
        border: 1px solid #e0e0e0;
        border-radius: 10px;
        overflow: hidden;
    }
    .history-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #28a745;
    }
    .history-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        cursor: pointer;
        transition: all 0.3s;
    }
    .history-image:hover {
        opacity: 0.8;
    }
    .confidence-badge {
        position: absolute;
        top: 10px;
        right: 10px;
        background: rgba(0,0,0,0.7);
        color: white;
        padding: 5px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
    }
    .stat-card {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
    }
    .stat-card i {
        font-size: 2.5rem;
        margin-bottom: 10px;
    }
    .stat-card .stat-value {
        font-size: 2rem;
        font-weight: bold;
    }
    .stat-card .stat-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }
    .filter-card {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
    }
    .empty-state {
        text-align: center;
        padding: 50px;
        background: #f8f9fa;
        border-radius: 10px;
    }
    .empty-state i {
        font-size: 5rem;
        color: #28a745;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-history"></i> Your Identification History</h2>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <i class="fas fa-leaf"></i>
                <div class="stat-value">{{ $statistics['total'] }}</div>
                <div class="stat-label">Total Identifications</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(45deg, #17a2b8, #0dcaf0);">
                <i class="fas fa-chart-line"></i>
                <div class="stat-value">{{ number_format($statistics['avg_confidence'] * 100, 1) }}%</div>
                <div class="stat-label">Average Confidence</div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card" style="background: linear-gradient(45deg, #ffc107, #fd7e14);">
                <i class="fas fa-check-circle"></i>
                <div class="stat-value">{{ $statistics['correct_count'] }}</div>
                <div class="stat-label">Correct Identifications</div>
            </div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-card">
        <form method="GET" action="{{ route('history') }}" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label">Search</label>
                <input type="text" class="form-control" id="search" name="search"
                       placeholder="Search by species name..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <label for="date" class="form-label">Date</label>
                <input type="date" class="form-control" id="date" name="date" value="{{ request('date') }}">
            </div>
            <div class="col-md-2">
                <label for="min_confidence" class="form-label">Min Confidence</label>
                <select class="form-select" id="min_confidence" name="min_confidence">
                    <option value="">Any</option>
                    <option value="90" {{ request('min_confidence') == '90' ? 'selected' : '' }}>90%+</option>
                    <option value="70" {{ request('min_confidence') == '70' ? 'selected' : '' }}>70%+</option>
                    <option value="50" {{ request('min_confidence') == '50' ? 'selected' : '' }}>50%+</option>
                    <option value="30" {{ request('min_confidence') == '30' ? 'selected' : '' }}>30%+</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="correct" class="form-label">Feedback</label>
                <select class="form-select" id="correct" name="correct">
                    <option value="">All</option>
                    <option value="true" {{ request('correct') == 'true' ? 'selected' : '' }}>Correct</option>
                    <option value="false" {{ request('correct') == 'false' ? 'selected' : '' }}>Incorrect</option>
                </select>
            </div>
            <div class="col-md-1 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-filter"></i>
                </button>
            </div>
        </form>
    </div>

    <!-- History Grid -->
    @if($identifications->count() > 0)
        <div class="row">
            @foreach($identifications as $identification)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card history-card">
                        <div class="position-relative">
                            @if($identification->image_url)
                                <img src="{{ $identification->image_url }}"
                                     class="history-image"
                                     alt="{{ $identification->identified_as }}"
                                     onclick="showImageModal('{{ $identification->image_url }}', '{{ $identification->identified_as }}')">
                            @else
                                <div class="history-image bg-secondary d-flex align-items-center justify-content-center">
                                    <i class="fas fa-leaf fa-4x text-white"></i>
                                </div>
                            @endif
                            <span class="confidence-badge">
                                <i class="fas fa-chart-pie"></i> {{ $identification->confidence_percentage }}
                            </span>
                        </div>
                        <div class="card-body">
                            <h5 class="card-title">
                                @if($identification->species)
                                    <a href="{{ route('identification.show', $identification) }}" class="text-decoration-none">
                                        {{ $identification->species->common_name }}
                                    </a>
                                @else
                                    <a href="{{ route('identification.show', $identification) }}" class="text-decoration-none">
                                        {{ $identification->identified_as }}
                                    </a>
                                @endif
                            </h5>

                            @if($identification->species)
                                <p class="text-muted small">
                                    <em>{{ $identification->species->scientific_name }}</em>
                                </p>
                            @endif

                            <p class="card-text">
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $identification->created_at->format('M d, Y - h:i A') }}
                                </small>
                            </p>

                            @if($identification->location)
                                <p class="card-text">
                                    <small class="text-muted">
                                        <i class="fas fa-map-marker-alt"></i> {{ $identification->location }}
                                    </small>
                                </p>
                            @endif

                            @if($identification->user_notes)
                                <p class="card-text">
                                    <small>
                                        <i class="fas fa-sticky-note"></i> "{{ Str::limit($identification->user_notes, 50) }}"
                                    </small>
                                </p>
                            @endif

                            <div class="d-flex justify-content-between align-items-center mt-3">
                                <div>
                                    @if($identification->is_correct !== null)
                                        @if($identification->is_correct)
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Correct
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times"></i> Incorrect
                                            </span>
                                        @endif
                                    @else
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-question"></i> No Feedback
                                        </span>
                                    @endif
                                </div>
                                <div class="btn-group">
                                    <a href="{{ route('identification.show', $identification) }}"
                                       class="btn btn-sm btn-outline-primary" title="View Details">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <form action="{{ route('identification.destroy', $identification) }}"
                                          method="POST"
                                          class="d-inline"
                                          onsubmit="return confirm('Are you sure you want to delete this identification?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-4">
            {{ $identifications->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="empty-state">
            <i class="fas fa-history"></i>
            <h4>No identification history yet</h4>
            <p class="text-muted">Start identifying plants and flowers to build your history!</p>
            <a href="{{ route('identify') }}" class="btn btn-success">
                <i class="fas fa-camera"></i> Start Identifying
            </a>
        </div>
    @endif
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
                <img src="" id="modalImage" class="img-fluid rounded" alt="Captured image">
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

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        });
    }, 5000);
});
</script>
@endpush
