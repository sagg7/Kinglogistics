<?php

namespace App\Http\Controllers;

use App\Models\LoadDescription;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class LoadDescriptionController extends Controller
{
    use GetSimpleSearchData;

    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'name_spanish' => ['required', 'string', 'max:255'],
            'text' => ['required', 'string', 'max:1024'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        return view('loads.descriptions.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function create()
    {
        return view('loads.descriptions.create');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return LoadDescription
     */
    private function storeUpdate(Request $request, $id = null): LoadDescription
    {
        if ($id) {
            $description = LoadDescription::whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
                ->findOrFail($id);
        } else {
            $description = new LoadDescription();
            $description->broker_id = session('broker');
        }

        $description->name = $request->name;
        $description->name_spanish = $request->name_spanish;
        $description->text = $request->text;
        $description->save();

        return $description;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('loadDescription.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\LoadDescription  $loadDescription
     * @return \Illuminate\Http\Response
     */
    public function show(LoadDescription $loadDescription)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit(int $id)
    {
        $description = LoadDescription::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);
        $params = compact('description');
        return view('loads.descriptions.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('loadDescription.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return array
     */
    public function destroy(int $id)
    {
        $rate = LoadDescription::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($id);

        return ['success' => $rate->delete()];
    }

    public function search(Request $request)
    {
        $query = LoadDescription::select([
            "load_descriptions.id",
            "load_descriptions.name",
            "load_descriptions.name_spanish",
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            });

        return $this->multiTabSearchData($query, $request);
    }
}
