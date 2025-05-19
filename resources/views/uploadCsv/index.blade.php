@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>list csv </h1> <a href="{{ route('csv.create') }}">back</a>

                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <table id="csv-table" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fichero</th>
                        <th>Estado</th>
                       <th>Acciones</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
<script>
    $(document).ready(function () {
        $('#csv-table').DataTable({
            searching: false,
            processing: true,
            serverSide: true,
            ajax: '{{ route("csv.index") }}',
            columns: [
                {data: 'id', name: 'id'},
                {data: 'path', name: 'path'},
                {data: 'status_id', name: 'status_id'},
                {data: 'actions', name: 'actions', orderable: false, searchable: false}
            ]
        });
    });
</script>
@endsection
