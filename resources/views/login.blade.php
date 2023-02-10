@extends('layouts.master')

@section('htmlTitle', 'Login')
@section('title', 'Login')

@section('content')

    @if ($message = Session::get('success'))
        <div class="font-bold text-center">{{ $message }}</div>
    @endif
    @if ($message = Session::get('error'))
        <div class="error"><strong>{{$message}}</strong></div>
    @endif
    @foreach ($errors->all(':message') as $error)
        <div class="error"><strong>{{$error}}</strong></div>
    @endforeach

    <form action="{{route("login.attempt")}}" method="POST" enctype="multipart/form-data" class="w-full flex flex-col justify-center items-center mt-10 mr-0">
        @csrf
        <label for="email" class="text-lg">Email Address:</label>
        <input type="email" name="email" class="px-3 text-center bg-secondary" required>

        <br>
        <label for="password" class="text-lg">Password:</label>
        <input type="password" name="password" class="px-3 text-center bg-secondary" required>
        <br>
        <input type="submit" value="Log In" class="interactive">
        <br>
        <div class="interactive place-content-center"><a href="{{route("register.show")}}">Or create an account</a></div>
    </form>
@stop
