@extends('miniapp.layout')
@section('title', 'Login')

@section('content')
    <div style="padding-top: 2rem;">
        <h1 style="margin-bottom: 1.5rem; font-size: 1.5rem;">Enter PIN</h1>
        <p style="color: #666; margin-bottom: 1.5rem;">Use the PIN provided to add parts and vehicles from your phone.</p>
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
