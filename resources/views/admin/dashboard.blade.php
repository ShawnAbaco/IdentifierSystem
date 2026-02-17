{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .stat-card {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-radius: 10px;
        padding: 20px;
        text-align: center;
        transition: transform 0.3s;
        margin-bottom: 20px;
    }
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
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
    .chart-card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="container">
    <h2 class="mb-4"><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-md-3">
            <div class="stat-card">
                <i class="fas fa-users"></i>
                <div class="stat-value">{{ $totalUsers }}</div>
                <div class="stat-label">Total Users</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(45deg, #007bff, #00bcd4);">
                <i class="fas fa-leaf"></i>
                <div class="stat-value">{{ $totalIdentifications }}</div>
                <div class="stat-label">Total Identifications</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(45deg, #ffc107, #fd7e14);">
                <i class="fas fa-seedling"></i>
                <div class="stat-value">{{ $totalSpecies }}</div>
                <div class="stat-label">Total Species</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card" style="background: linear-gradient(45deg, #dc3545, #c82333);">
                <i class="fas fa-tags"></i>
                <div class="stat-value">{{ $totalCategories }}</div>
                <div class="stat-label">Categories</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row">
        <div class="col-md-6">
            <div class="chart-card">
                <h5><i class="fas fa-chart-line"></i> User Growth (Last 30 Days)</h5>
                <canvas id="userGrowthChart" height="200"></canvas>
            </div>
        </div>
        <div class="col-md-6">
            <div class="chart-card">
                <h5><i class="fas fa-chart-pie"></i> Top Species</h5>
                <canvas id="topSpeciesChart" height="200"></canvas>
            </div>
        </div>
    </div>

    <!-- Recent Users and Identifications -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-users"></i> Recent Users</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Joined</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentUsers as $user)
                                <tr>
                                    <td>
                                        <img src="{{ $user->avatar_url }}" class="rounded-circle me-2" width="30" height="30">
                                        {{ $user->name }}
                                    </td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        <span class="badge bg-{{ $user->role === 'admin' ? 'danger' : 'success' }}">
                                            {{ $user->role }}
                                        </span>
                                    </td>
                                    <td>{{ $user->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-leaf"></i> Recent Identifications</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>User</th>
                                    <th>Species</th>
                                    <th>Confidence</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentIdentifications as $identification)
                                <tr>
                                    <td>
                                        <img src="{{ $identification->user->avatar_url }}" class="rounded-circle me-2" width="30" height="30">
                                        {{ $identification->user->name }}
                                    </td>
                                    <td>
                                        @if($identification->species)
                                            <a href="{{ route('admin.species.show', $identification->species) }}">
                                                {{ $identification->species->common_name }}
                                            </a>
                                        @else
                                            {{ $identification->identified_as }}
                                        @endif
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $identification->confidence > 0.7 ? 'success' : 'warning' }}">
                                            {{ $identification->confidence_percentage }}
                                        </span>
                                    </td>
                                    <td>{{ $identification->created_at->diffForHumans() }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Species -->
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-bar"></i> Most Identified Species</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Species</th>
                                    <th>Scientific Name</th>
                                    <th>Category</th>
                                    <th>Identification Count</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topSpecies as $species)
                                <tr>
                                    <td>{{ $species->common_name }}</td>
                                    <td><em>{{ $species->scientific_name ?? 'N/A' }}</em></td>
                                    <td>
                                        <span class="badge bg-info">{{ $species->category->name }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">{{ $species->identifications_count }}</span>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.species.show', $species) }}" class="btn btn-sm btn-success">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart').getContext('2d');
    new Chart(userGrowthCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($userGrowth->pluck('date')->map(function($date) {
                return \Carbon\Carbon::parse($date)->format('M d');
            })) !!},
            datasets: [{
                label: 'New Users',
                data: {!! json_encode($userGrowth->pluck('count')) !!},
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            }
        }
    });

    // Top Species Chart
    const topSpeciesCtx = document.getElementById('topSpeciesChart').getContext('2d');
    new Chart(topSpeciesCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($topSpecies->pluck('common_name')) !!},
            datasets: [{
                data: {!! json_encode($topSpecies->pluck('identifications_count')) !!},
                backgroundColor: [
                    '#28a745',
                    '#20c997',
                    '#17a2b8',
                    '#ffc107',
                    '#dc3545',
                    '#fd7e14',
                    '#6f42c1',
                    '#e83e8c'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
});
</script>
@endpush
