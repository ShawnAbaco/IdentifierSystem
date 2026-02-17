{{-- resources/views/identify.blade.php --}}
@extends('layouts.app')

@section('title', 'Identify Plant or Flower')

@push('styles')
<style>
    #webcam-container {
        margin: 20px 0;
        text-align: center;
        min-height: 300px;
        background: #f8f9fa;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    video, canvas {
        border: 2px solid #28a745;
        border-radius: 8px;
        max-width: 100%;
        height: auto;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .species-card {
        cursor: pointer;
        transition: all 0.3s;
        border: 2px solid transparent;
    }
    .species-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        border-color: #28a745;
    }
    .species-card.selected {
        border-color: #28a745;
        background-color: #f0fff0;
    }
    .prediction-item {
        padding: 8px;
        border-bottom: 1px solid #eee;
    }
    .prediction-item:last-child {
        border-bottom: none;
    }
    .progress {
        height: 10px;
        border-radius: 5px;
    }
    .camera-placeholder {
        padding: 50px;
        text-align: center;
        color: #6c757d;
    }
    .camera-placeholder i {
        font-size: 64px;
        margin-bottom: 15px;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0"><i class="fas fa-camera"></i> Plant & Flower Identifier</h4>
                </div>
                <div class="card-body">
                    <!-- Category Tabs -->
                    <ul class="nav nav-tabs mb-3" id="categoryTabs">
                        <li class="nav-item">
                            <a class="nav-link {{ $category === 'all' ? 'active' : '' }}"
                               href="{{ route('identify', ['category' => 'all']) }}">
                                <i class="fas fa-globe"></i> All
                            </a>
                        </li>
                        @foreach($categories as $cat)
                            <li class="nav-item">
                                <a class="nav-link {{ $category === $cat->slug ? 'active' : '' }}"
                                   href="{{ route('identify', ['category' => $cat->slug]) }}">
                                    <i class="fas {{ $cat->icon ?? 'fa-leaf' }}"></i> {{ $cat->name }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Camera Controls -->
                    <div class="mb-3">
                        <button id="start-button" class="btn btn-success">
                            <i class="fas fa-video"></i> Start Camera
                        </button>
                        <button id="predict-button" class="btn btn-primary" disabled>
                            <i class="fas fa-search"></i> Identify
                        </button>
                        <button id="capture-button" class="btn btn-info" disabled>
                            <i class="fas fa-camera"></i> Capture Photo
                        </button>
                        <button id="save-button" class="btn btn-warning" style="display: none;" data-bs-toggle="modal" data-bs-target="#saveModal">
                            <i class="fas fa-save"></i> Save Result
                        </button>
                    </div>

                    <!-- Camera Container -->
                    <div id="webcam-container">
                        <div class="camera-placeholder">
                            <i class="fas fa-camera-retro"></i>
                            <h5>Click "Start Camera" to begin</h5>
                            <p class="text-muted">Make sure your camera is connected and you've granted permission</p>
                        </div>
                    </div>

                    <!-- Results Section -->
                    <div id="result-section" style="display: none;" class="mt-4">
                        <div class="alert alert-success" id="result-alert">
                            <h5>Identification Result:</h5>
                            <p id="result-text" class="h4"></p>
                        </div>

                        <div id="species-details" class="card mt-3" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Species Information</h5>
                            </div>
                            <div class="card-body">
                                <h4 id="species-common-name"></h4>
                                <p class="text-muted" id="species-scientific-name"></p>
                                <p id="species-description"></p>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <strong>Habitat:</strong>
                                        <p id="species-habitat"></p>
                                    </div>
                                    <div class="col-md-6">
                                        <strong>Conservation Status:</strong>
                                        <p id="species-status"></p>
                                    </div>
                                </div>

                                <div id="fun-facts" class="mt-3" style="display: none;">
                                    <h6>Fun Facts:</h6>
                                    <ul id="fun-facts-list"></ul>
                                </div>

                                <a id="view-details-link" href="#" class="btn btn-outline-primary mt-2">
                                    View Full Details
                                </a>
                            </div>
                        </div>

                        <div class="card mt-3">
                            <div class="card-header">
                                <h5 class="mb-0"><i class="fas fa-chart-bar"></i> All Predictions</h5>
                            </div>
                            <div class="card-body">
                                <div id="predictions-list"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar with Quick Reference -->
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-book"></i> Quick Reference</h5>
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <input type="text" class="form-control mb-3" id="search-species"
                           placeholder="Search species...">

                    <div id="species-list">
                        @forelse($species as $specie)
                            <div class="card species-card mb-2"
                                 onclick="showSpeciesPreview({{ $specie->id }})"
                                 data-species-id="{{ $specie->id }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $specie->common_name }}</strong>
                                            <br>
                                            <small class="text-muted">
                                                <em>{{ $specie->scientific_name ?? 'N/A' }}</em>
                                            </small>
                                        </div>
                                        <span class="badge bg-{{ $specie->category->slug === 'trees' ? 'success' : 'danger' }}">
                                            {{ $specie->category->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <p class="text-muted">No species available in this category.</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-question-circle"></i> How to Use</h5>
                </div>
                <div class="card-body">
                    <ol class="mb-0">
                        <li class="mb-2">Click "Start Camera" to enable your webcam</li>
                        <li class="mb-2">Position the plant/flower in the frame</li>
                        <li class="mb-2">Click "Capture Photo" or "Identify" directly</li>
                        <li class="mb-2">View the identification results</li>
                        <li class="mb-2">Save results to your history with notes</li>
                        <li>Check the Quick Reference for species information</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Save Modal -->
<div class="modal fade" id="saveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="fas fa-save"></i> Save Identification</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="saveForm">
                    @csrf
                    <input type="hidden" id="identified_as" name="identified_as">
                    <input type="hidden" id="confidence" name="confidence">
                    <input type="hidden" id="all_predictions" name="all_predictions">
                    <input type="hidden" id="image_data" name="image">

                    <div class="mb-3">
                        <label class="form-label">Identified Species</label>
                        <p class="form-control-plaintext fw-bold" id="identified_species_display"></p>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Confidence Score</label>
                        <div class="progress">
                            <div class="progress-bar bg-success" id="confidence_display_bar" style="width: 0%"></div>
                        </div>
                        <p class="text-end mt-1" id="confidence_display_text">0%</p>
                    </div>

                    <div class="mb-3">
                        <label for="user_notes" class="form-label">Notes (Optional)</label>
                        <textarea class="form-control" id="user_notes" name="user_notes" rows="3"
                                  placeholder="Where did you find this? Any special observations?"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="location" class="form-label">Location (Optional)</label>
                        <input type="text" class="form-control" id="location" name="location"
                               placeholder="e.g., Garden, Park, Forest">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-warning" id="confirm-save">Save Identification</button>
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
// Global variables
let model, video, canvas, ctx;
let currentPredictions = [];
let currentImageData = null;
let modelLoaded = false;
let speciesData = @json($species);

// Initialize event listeners
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('start-button').addEventListener('click', startCamera);
    document.getElementById('predict-button').addEventListener('click', predict);
    document.getElementById('capture-button').addEventListener('click', capturePhoto);
    document.getElementById('confirm-save').addEventListener('click', saveIdentification);
    document.getElementById('search-species').addEventListener('keyup', searchSpecies);
});

// Camera functions
async function startCamera() {
    const startBtn = document.getElementById('start-button');
    const predictBtn = document.getElementById('predict-button');
    const captureBtn = document.getElementById('capture-button');

    try {
        // Create video element if not exists
        if (!video) {
            video = document.createElement('video');
            video.width = 400;
            video.height = 300;
            video.autoplay = true;
            video.playsInline = true;
        }

        // Get camera stream
        const stream = await navigator.mediaDevices.getUserMedia({
            video: {
                width: { ideal: 400 },
                height: { ideal: 300 },
                facingMode: 'environment' // Use back camera on mobile
            }
        });

        video.srcObject = stream;
        await video.play();

        // Create canvas for capturing
        if (!canvas) {
            canvas = document.createElement('canvas');
            canvas.width = 400;
            canvas.height = 300;
            ctx = canvas.getContext('2d');
        }

        // Update container
        const container = document.getElementById('webcam-container');
        container.innerHTML = '';
        container.appendChild(video);

        // Load model if not loaded
        if (!modelLoaded) {
            Swal.fire({
                title: 'Loading AI Model',
                text: 'Please wait...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });

            try {
                model = await tmImage.load('./model/model.json', './model/metadata.json');
                modelLoaded = true;
                Swal.close();
            } catch (error) {
                Swal.fire('Error', 'Failed to load AI model', 'error');
                console.error('Model load error:', error);
            }
        }

        // Enable buttons
        predictBtn.disabled = false;
        captureBtn.disabled = false;
        startBtn.disabled = true;

    } catch (error) {
        console.error('Camera error:', error);
        Swal.fire('Error', 'Could not access camera. Please check permissions.', 'error');
    }
}

function capturePhoto() {
    if (!video || !canvas || !ctx) return;

    // Draw video frame to canvas
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);

    // Save image data
    currentImageData = canvas.toDataURL('image/png');

    // Show preview
    Swal.fire({
        title: 'Photo Captured',
        text: 'Click Identify to analyze this photo',
        imageUrl: currentImageData,
        imageWidth: 400,
        imageHeight: 300,
        imageAlt: 'Captured photo',
        showCancelButton: true,
        confirmButtonText: 'Identify',
        cancelButtonText: 'Retake'
    }).then((result) => {
        if (result.isConfirmed) {
            predict();
        }
    });
}

async function predict() {
    if (!model || !video || !canvas) {
        Swal.fire('Warning', 'Please start the camera first!', 'warning');
        return;
    }

    Swal.fire({
        title: 'Analyzing...',
        text: 'Please wait while AI identifies your plant',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading()
    });

    try {
        // Draw current video frame
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        currentImageData = canvas.toDataURL('image/png');

        // Run prediction
        const predictions = await model.predict(canvas);
        currentPredictions = predictions;

        // Find best match
        let best = predictions.reduce((max, p) => p.probability > max.probability ? p : max);

        Swal.close();

        // Display results
        displayResults(best, predictions);

        // Show save button
        document.getElementById('save-button').style.display = 'inline-block';

    } catch (error) {
        Swal.fire('Error', 'Prediction failed. Please try again.', 'error');
        console.error('Prediction error:', error);
    }
}

function displayResults(best, predictions) {
    // Update result text
    const resultText = document.getElementById('result-text');
    resultText.innerHTML = `
        <strong>${best.className}</strong><br>
        <small class="text-muted">Confidence: ${(best.probability * 100).toFixed(1)}%</small>
    `;

    // Show result section
    document.getElementById('result-section').style.display = 'block';

    // Check if we have detailed info for this species
    const species = speciesData.find(s => s.common_name === best.className);
    if (species) {
        displaySpeciesDetails(species);
    } else {
        document.getElementById('species-details').style.display = 'none';
    }

    // Display all predictions
    const predictionsList = document.getElementById('predictions-list');
    predictionsList.innerHTML = '';

    predictions.sort((a, b) => b.probability - a.probability).forEach(p => {
        const percentage = (p.probability * 100).toFixed(1);
        const colorClass = p.probability > 0.5 ? 'bg-success' :
                          p.probability > 0.3 ? 'bg-warning' : 'bg-secondary';

        predictionsList.innerHTML += `
            <div class="prediction-item">
                <div class="d-flex justify-content-between mb-1">
                    <span>${p.className}</span>
                    <span class="badge bg-${p.probability > 0.5 ? 'success' : 'secondary'}">${percentage}%</span>
                </div>
                <div class="progress">
                    <div class="progress-bar ${colorClass}" style="width: ${percentage}%"></div>
                </div>
            </div>
        `;
    });

    // Prepare save data
    document.getElementById('identified_as').value = best.className;
    document.getElementById('confidence').value = best.probability;
    document.getElementById('all_predictions').value = JSON.stringify(predictions);
}

function displaySpeciesDetails(species) {
    const detailsDiv = document.getElementById('species-details');
    detailsDiv.style.display = 'block';

    document.getElementById('species-common-name').textContent = species.common_name;
    document.getElementById('species-scientific-name').textContent = species.scientific_name || 'Scientific name not available';
    document.getElementById('species-description').textContent = species.description;
    document.getElementById('species-habitat').textContent = species.habitat || 'Not specified';
    document.getElementById('species-status').textContent = species.conservation_status || 'Not assessed';

    // Fun facts
    if (species.fun_facts && species.fun_facts.length > 0) {
        const factsList = document.getElementById('fun-facts-list');
        factsList.innerHTML = '';
        species.fun_facts.forEach(fact => {
            factsList.innerHTML += `<li><i class="fas fa-star text-warning"></i> ${fact}</li>`;
        });
        document.getElementById('fun-facts').style.display = 'block';
    } else {
        document.getElementById('fun-facts').style.display = 'none';
    }

    // Set view details link
    document.getElementById('view-details-link').href = `/species/${species.id}`;
}

function showSpeciesPreview(speciesId) {
    const species = speciesData.find(s => s.id === speciesId);
    if (!species) return;

    const modal = new bootstrap.Modal(document.getElementById('speciesPreviewModal'));
    document.getElementById('preview-title').textContent = species.common_name;

    let content = `
        <div class="row">
            <div class="col-md-6">
                ${species.image_url ?
                    `<img src="${species.image_url}" class="img-fluid rounded" alt="${species.common_name}">` :
                    `<div class="bg-light text-center p-5 rounded"><i class="fas fa-leaf fa-5x text-success"></i></div>`
                }
            </div>
            <div class="col-md-6">
                <h5>${species.scientific_name || ''}</h5>
                <p>${species.description}</p>
                <p><strong>Habitat:</strong> ${species.habitat || 'N/A'}</p>
                <p><strong>Conservation Status:</strong>
                    <span class="badge bg-${species.conservation_status_color || 'secondary'}">
                        ${species.conservation_status || 'Unknown'}
                    </span>
                </p>
            </div>
        </div>
    `;

    if (species.medicinal_uses && species.medicinal_uses.length > 0) {
        content += `
            <div class="mt-3">
                <h6>Medicinal Uses:</h6>
                <ul>${species.medicinal_uses.map(u => `<li>${u}</li>`).join('')}</ul>
            </div>
        `;
    }

    if (species.cultural_significance && species.cultural_significance.length > 0) {
        content += `
            <div class="mt-3">
                <h6>Cultural Significance:</h6>
                <ul>${species.cultural_significance.map(c => `<li>${c}</li>`).join('')}</ul>
            </div>
        `;
    }

    document.getElementById('preview-content').innerHTML = content;
    modal.show();
}

function searchSpecies() {
    const searchTerm = document.getElementById('search-species').value.toLowerCase();
    const speciesCards = document.querySelectorAll('.species-card');

    speciesCards.forEach(card => {
        const speciesId = card.dataset.speciesId;
        const species = speciesData.find(s => s.id == speciesId);

        if (species && (species.common_name.toLowerCase().includes(searchTerm) ||
            (species.scientific_name && species.scientific_name.toLowerCase().includes(searchTerm)))) {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
}

async function saveIdentification() {
    // Prepare save modal
    const identifiedAs = document.getElementById('identified_as').value;
    const confidence = parseFloat(document.getElementById('confidence').value);

    document.getElementById('identified_species_display').textContent = identifiedAs;
    document.getElementById('confidence_display_bar').style.width = (confidence * 100) + '%';
    document.getElementById('confidence_display_text').textContent = (confidence * 100).toFixed(1) + '%';
    document.getElementById('image_data').value = currentImageData;

    // Handle save
    document.getElementById('confirm-save').onclick = async function() {
        const formData = new FormData(document.getElementById('saveForm'));

        try {
            const response = await fetch('{{ route("identify.store") }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const result = await response.json();

            if (result.success) {
                $('#saveModal').modal('hide');
                Swal.fire({
                    icon: 'success',
                    title: 'Saved!',
                    text: 'Identification saved to your history',
                    timer: 2000,
                    showConfirmButton: false
                });
                document.getElementById('save-button').style.display = 'none';
            }
        } catch (error) {
            Swal.fire('Error', 'Failed to save identification', 'error');
        }
    };
}

// Clean up on page unload
window.addEventListener('beforeunload', function() {
    if (video && video.srcObject) {
        video.srcObject.getTracks().forEach(track => track.stop());
    }
});
</script>
@endpush
