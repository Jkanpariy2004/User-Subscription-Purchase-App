@extends('layouts.layout')

@section('content')
    <div class="container">
        <h1>Hii, {{ Auth::user()->name }}</h1>
    </div>
@endsection
