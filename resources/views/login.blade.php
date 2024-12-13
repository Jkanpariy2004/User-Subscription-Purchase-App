@extends('layouts.auth-layouts')

@section('content')
    <form action="{{ route('userLogin') }}" method="post">
        @csrf
        <h1>Sign in</h1>
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
            <label for="mail">Email:</label>
            <input type="email" name="email">

            <label for="password">Password:</label>
            <input type="password" name="password">
        </fieldset>
        <button type="submit">Login</button>
    </form>
@endsection
