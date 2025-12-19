@extends('layouts.master')

@section('title', 'Data Murid TK')

@section('content')

    {{-- PANGGIL KOMPONEN LIVEWIRE --}}
    @livewire('murid-index')
    @include('murid.create')

@endsection
