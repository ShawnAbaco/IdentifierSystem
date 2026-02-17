@extends('layouts.app')

@section('title', 'My Profile')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-user"></i> Profile Information</h5>
                </div>
                <div class="card-body text-center">
                    <img src="{{ Auth::user()->avatar_url }}" alt="Avatar" class="rounded-circle mb-3" width="150" height="150">
                    <h4>{{ Auth::user()->name }}</h4>
                    <p class="text-muted">{{ Auth::user()->email }}</p>
                    @if(Auth::user()->bio)
                        <p>{{ Auth::user()->bio }}</p>
                    @endif
                    <p class="text-muted"><small>Member since {{ Auth::user()->created_at->format('F j, Y') }}</small></p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-success">Edit Profile</a>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Recent Identifications</h5>
                </div>
                <div class="card-body">
                    @if($recentIdentifications->count() > 0)
                        <div class="list-group">
                            @foreach($recentIdentifications as $identification)
                                <a href="{{ route('identification.show', $identification) }}" class="list-group-item list-group-item-action">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <strong>{{ $identification->identified_as }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $identification->created_at->diffForHumans() }}</small>
                                        </div>
                                        <span class="badge bg-primary">{{ $identification->confidence_percentage }}</span>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('history') }}" class="btn btn-outline-success">View All History</a>
                        </div>
                    @else
                        <p class="text-muted text-center">No identifications yet.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
