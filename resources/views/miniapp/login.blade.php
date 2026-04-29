@extends('miniapp.layout')
@section('title', 'Login')

@section('content')
    <div class="card-app" style="margin-top: 1.25rem;">
        <h1 class="page-title">Enter PIN</h1>
        <p class="lead-text">Use the PIN provided to add parts and vehicles from your phone.</p>
        <form method="post" action="{{ route('app.login.post') }}">
            @csrf
            <div class="form-group">
                <label for="pin">PIN</label>
                <input type="password" id="pin" name="pin" inputmode="numeric" autocomplete="off" placeholder="Enter PIN" required autofocus>
            </div>
            <button type="submit" class="btn-app btn-primary-app">Continue</button>
        </form>
    </div>
@endsection
