<?php

namespace App\Http\Controllers;

use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\Storage\FileUpload;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PaperworkController extends Controller
{
    use GetSelectionData, GetSimpleSearchData, FileUpload;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'template' => ['sometimes', 'string'],
        ]);
    }

    private function createdEditParams()
    {
        return [
            "mode" => ['Simple', 'Advanced'],
            "types" => [null => '', 'carrier' => 'Carriers', 'driver' => 'Drivers', 'trailer' => 'Trailers', 'truck' => 'Trucks'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('paperwork.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createdEditParams();
        return view('paperwork.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('paperwork.index');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Paperwork
     */
    private function storeUpdate(Request $request, $id = null)
    {
        if ($id)
            $paperwork = Paperwork::findOrFail($id);
        else
            $paperwork = new Paperwork();

        $paperwork->name = $request->name;
        $paperwork->type = $request->type;
        $paperwork->required = $request->required ?? null;
        $paperwork->template = $request->template ?? null;
        $paperwork->save();

        return $paperwork;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $paperwork = Paperwork::findOrFail($id);
        $params = compact('paperwork') + $this->createdEditParams();
        return view('paperwork.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('paperwork.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $paperwork = Paperwork::findOrFail($id);

        if ($paperwork)
            return ['success' => $paperwork->delete()];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Paperwork::select([
            "paperwork.id",
            "paperwork.name",
            "paperwork.type",
            "paperwork.required",
        ])
            ->where('type', $type);

        return $this->simpleSearchData($query, $request, "where", true);
    }

    public function storeFiles(Request $request)
    {
        return DB::transaction(function () use ($request) {
            $result = [];
            if ($request->file) {
                foreach ($request->file as $i => $file) {
                    $paperwork = PaperworkFile::where('paperwork_id', $i)
                        ->where('related_id', $request->related_id)
                        ->first();
                    if (!$paperwork)
                        $paperwork = new PaperworkFile();
                    $paperwork->paperwork_id = $i;
                    $paperwork->related_id = $request->related_id;
                    $paperwork->expiration_date = $request->expiration_date[$i];
                    $paperwork->url = $this->uploadFile($file, "paperwork/$request->type/$request->related_id/$i");
                    $paperwork->save();
                    $paperwork->file_name = $paperwork->getFileNameAttribute();

                    $result[] = $paperwork;
                }
            }

            return ['success' => true, 'data' => $result];
        });
    }

    public function storeTemplate(Request $request)
    {

    }
}
