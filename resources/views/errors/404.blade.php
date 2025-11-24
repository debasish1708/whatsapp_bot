@extends('layouts.app')

@section('content')
<div class="text-center mt-5">
    <h1 class="text-danger">404 - Job Not Found</h1>
    <p>The job application you're looking for could not be found or may have been removed.</p>
    <a href="{{ url('/') }}" class="btn btn-primary mt-3">Return to Home</a>
</div>
@endsection