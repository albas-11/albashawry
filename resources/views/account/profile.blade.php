@extends('layouts.app')

@section('title', 'Mon Profil')

@section('content')
    <h1>Mon Profil</h1>
    <p>Bienvenue, {{ auth()->user()->name }}</p>
@endsection
