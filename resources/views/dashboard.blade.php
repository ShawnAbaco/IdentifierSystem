{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container">
    <h2 class="mb-4">Welcome, {{ Auth::user()->name }}!</h2>

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Total Identifications</h6>
                            <h2 class="mb-0">{{ $totalIdentifications }}</h2>
                        </div>
                        <i class="fas fa-leaf fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Today's IDs</h6>
                            <h2 class="mb-0">{{ $todayCount }}</h2>
                        </div>
                        <i class="fas fa-calendar-day fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Accuracy</h6>
                            <h2 class="mb-0">{{ number_format($accuracy->accuracy ?? 0, 1) }}%</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="card-title">Species Found</h6>
                            <h2 class="mb-0">{{ $topIdentified->count() }}</h2>
                        </div>
                        <i class="fas fa-seedling fa-3x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-history"></i> Recent Identifications</h5>
                </div>
                <div class="card-body">
                    @if($recentIdentifications->count() > 0)
                        <div class="list-group">
                            @foreach($recentIdentifications as $identification)
                                <a href="{{ route('identification.show', $identification) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1">
                                            @if($identification->species)
                                                {{ $identification->species->common_name }}
                                            @else
                                                {{ $identification->identified_as }}
                                            @endif
                                        </h6>
                                        <small class="text-muted">{{ $identification->created_at->diffForHumans() }}</small>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <small>Confidence:
                                            <span class="badge bg-{{ $identification->confidence > 0.7 ? 'success' : 'warning' }}">
                                                {{ $identification->confidence_percentage }}
                                            </span>
                                        </small>
                                        @if($identification->is_correct !== null)
                                            <i class="fas fa-{{ $identification->is_correct ? 'check-circle text-success' : 'times-circle text-danger' }}"></i>
                                        @endif
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('history') }}" class="btn btn-outline-primary">View All History</a>
                        </div>
                    @else
                        <p class="text-muted text-center">
                            No identifications yet.
                            <a href="{{ route('identify') }}">Start identifying now!</a>
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5><i class="fas fa-chart-pie"></i> Most Identified</h5>
                </div>
                <div class="card-body">
                    @if($topIdentified->count() > 0)
                        @foreach($topIdentified as $item)
                            <div class="mb-3">
                                <div class="d-flex justify-content-between">
                                    <span>{{ $item->identified_as }}</span>
                                    <span class="badge bg-primary">{{ $item->total }} times</span>
                                </div>
                                <div class="progress">
                                    @php
                                        $percentage = ($item->total / $totalIdentifications) * 100;
                                    @endphp
                                    <div class="progress-bar bg-success" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-muted text-center">No data available yet.</p>
                    @endif
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header">
                    <h5><i class="fas fa-clock"></i> Recent Activity</h5>
                </div>
                <div class="card-body">
                    @foreach($recentActivity as $activity)
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <i class="fas fa-{{ $activity->species ? 'leaf' : 'question-circle' }}
                                   text-{{ $activity->species ? 'success' : 'warning' }}"></i>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <small>
                                    You identified
                                    <strong>{{ $activity->identified_as }}</strong>
                                    {{ $activity->created_at->diffForHumans() }}
                                </small>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
