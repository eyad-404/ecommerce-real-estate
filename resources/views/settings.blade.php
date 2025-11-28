@extends('layouts.settings')

@section('title', 'Settings - EL Kayan')

@section('content')
<div class="settings-page-wrapper">
    <div class="settings-card">
        <h2 class="text-center">Settings</h2>
        <div class="settings-container">
            @auth
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users-management') }}" class="btn btn-settings btn-primary">
                    Users Management
                </a>
            @endif
             @endauth
            <a href="{{ route('property-management') }}" class="btn btn-settings btn-success">
                Property Management
            </a>
        </div>
    </div>
</div>
@endsection
