<?php

    namespace App\Http\Controllers;


    use App\Http\Requests\CsvUploadRequest;
    use App\Jobs\SplitCsvJob;
    use App\Models\CsvRecord;
    use App\Models\CsvUpload;
    use App\Services\FilterService;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;

    class CsvUploadController extends Controller
    {

        public function index(Request $request)
        {
            if ($request->ajax()) {
                return datatables()->of(CsvUpload::query())
                    ->editColumn('status_id', function($row) {
                        return '<span class="badge bg-success">' . $row->status_name . '</span>';
                    })
                    ->addColumn('actions', function($row) {

                        if ($row->status_name == 'completed') {

                            return '<a href="' . route('csv.show', $row->id) . '" class="btn btn-primary btn-sm">Ver</a>';

                        }

                        return '<a href="javascript:void(0)" class="btn btn-warning btn-sm">No disponible</a>';
                    })
                    ->rawColumns(['status_id', 'actions'])
                    ->make(true);
            }

            return view('uploadCsv.index');
        }

        public function create()
        {
            return view('uploadCsv.create');
        }

        public function store(CsvUploadRequest $request): \Illuminate\Http\RedirectResponse
        {

            $path = $request->file('file')->store('csv_files');
            $csvUpload = CsvUpload::create([
                'name' => $request->file('file')->getClientOriginalName(),
            ]);

            $csvUpload->files()->create([
                'filename' => $request->file('file')->getClientOriginalName(),
                'path' => $path,
                'size' => $request->file('file')->getSize(),
                'mime_type' => $request->file('file')->getMimeType(),
            ]);

             SplitCsvJob::dispatch($csvUpload);


            return redirect()->route('csv.index')->with('success', 'File uploaded successfully.');
        }

        public function show(CsvUpload $csv, Request $request, FilterService $filterService)
        {

            if ($request->ajax()) {


                if ($request->has('filter') && $request->filled('filter')) {

                    $query = $filterService->buildQuery($request->input('filter'), $csv->id);

                } else {

                    $query = CsvRecord::select(
                        'csv_id',
                        DB::raw('fecha_desde AS start_date'),
                        DB::raw('fecha_hasta AS end_date')
                    )->where('csv_uploads_id', $csv->id);

                }


                return datatables()->of($query)->make(true);
            }


            return view('uploadCsv.show', compact('csv'));
        }

    }
