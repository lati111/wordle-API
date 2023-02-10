@extends('layouts.master')

@section('htmlTitle', 'Register')
@section('title', 'Register')

@section('content')
    @if ($message = Session::get('error'))
        <div class="error text-lg font-semibold text-center mt-5">{{$error}}</div>
    @endif
    @foreach ($errors->all(':message') as $error)
        <div class="error text-lg font-semibold text-center mt-5">{{$error}}</div>
    @endforeach

    <form action="{{route("register.attempt")}}" method="POST" enctype="multipart/form-data" class="w-full flex flex-col justify-center items-center mt-5 mr-0">
        @csrf
        <label for="username" class="text-lg">Username:</label>
        <input type="text" name="username" class="px-3 text-center bg-secondary"
            autocomplete="nickname" placeholder="username..." required>
        <br>

        <label for="email" class="text-lg">Email Address:</label>
        <input type="email" name="email" class="px-3 text-center bg-secondary"
            autocomplete="email" placeholder="email..." required>
        <br>

        <label for="password" class="text-lg">Password:</label>
        <input type="password" name="password" class="px-3 text-center bg-secondary"
            autocomplete="new-password" placeholder="password..." required>
        <br>

        <label for="passwordRepeat" class="text-lg">Repeat password:</label>
        <input type="password" name="passwordRepeat" class="px-3 text-center bg-secondary"
            autocomplete="off" placeholder="password..." required>
        <br>

        <input type="submit" value="Register" class="interactive">
        <br>
        <div class="interactive place-content-center"><a href="{{route("login.show")}}">Or sign in</a></div>
    </form>
@stop
