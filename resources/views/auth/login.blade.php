@extends('layouts.app')

@section('content')
    <link href="{{ asset('assets/css/login.css') }}" rel="stylesheet">
    <div class="login-container">
        <div class="login-header">
            <h1>Australia Sports Institute and Fitness Academy</h1>
        </div>
        <form action="{{ route('login') }}" method="post">
            @csrf
            <div class="mb-3">
                <input type="email" class="form-control @error('email') is-invalid @enderror" placeholder="Email" required name="email">
            </div>
            <div class="mb-3">
                <input type="password" class="form-control @error('password') is-invalid @enderror" placeholder="Password" required name="password">
            </div>
            <button type="submit" class="btn btn-login">Giriş Yap</button>
        </form>
    </div>
@endsection
