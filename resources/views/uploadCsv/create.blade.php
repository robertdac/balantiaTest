@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
           <h1>Upload CSV File</h1>

@if ($errors->any())
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
@endif

<form action="{{ route('csv.store') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" accept=".csv" required>
    <button type="submit">Upload</button>
</form>
        </div>
    </div>
</div>
@endsection
