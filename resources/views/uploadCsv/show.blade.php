@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1>list records </h1><a href="{{ route('csv.index') }}">back</a>


                <div class="form-group mb-3">
                    <label for="filter">Filtrar por:</label>
                    <select id="filter" class="form-control">
                        <option value="">-- Selecciona --</option>
                        <option value="duplicates">Registros duplicados</option>
                        <option value="errors">Registros erróneos</option>
                        <option value="gaps">Saltos entre fechas</option>
                    </select>
                </div>

                <table id="csv-table" class="table table-striped table-bordered">
                    <thead>
                    <tr>
                        <th>CSV ID</th>
                        <th>Fecha desde</th>
                        <th>Fecha hasta</th>
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
            let table = $('#csv-table').DataTable({
                searching: false,
                processing: true,
                serverSide: true,
                ajax: {
                    url: '{{ route("csv.show", $csv->id) }}',
                    data: function (d) {
                        d.filter = $('#filter').val(); // envía filtro personalizado
                    }
                },
                columns: [
                    {data: 'csv_id', name: 'csv_id'},
                    {data: 'start_date', name: 'start_date'},
                    {data: 'end_date', name: 'end_date'},
                ]
            });

            $('#filter').change(function () {
                table.ajax.reload();
            });
        });


    </script>
@endsection
