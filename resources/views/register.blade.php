@extends('layouts.auth-layouts')

@section('content')
    <form action="{{ route('userRegister') }}" method="post">
        @csrf
        <h1>Sign Up</h1>
        @if (\Session::has('success'))
            <div class="alert alert-success">
                {{ \Session::get('success') }}
            </div>
        @elseif (\Session::has('error'))
            <div class="alert alert-danger">
                {{ \Session::get('error') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                @foreach ($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        <fieldset>
            <label for="name">Name:</label>
            <input type="text" name="name">

            <label for="mail">Email:</label>
            <input type="email" name="email">

            <label for="password">Password:</label>
            <input type="password" name="password">
        </fieldset>
        <button type="submit">Register</button>
    </form>
@endsection
