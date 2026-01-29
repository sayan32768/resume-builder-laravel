@extends('admin.layouts.app')

@section('title', 'User Details')
@section('pageTitle', 'User Details')

@section('content')
    <livewire:admin.user-details :user="$user" />
@endsection
