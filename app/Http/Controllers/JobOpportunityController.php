<?php

namespace App\Http\Controllers;

use App\Models\JobOpportunity;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\QuillEditor\QuillFormatter;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobOpportunityController extends Controller
{
    use GetSimpleSearchData, FileUpload, QuillFormatter;

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'max:255'],
            'message' => ['required'],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('jobOpportunities.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('jobOpportunities.create');
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

        if ($request->ajax())
            return ['success' => true];
        else
            return redirect()->route('notification.index');
    }

    /**
     * @param Request $request
     * @param null $id
     * @return JobOpportunity
     */
    private function storeUpdate(Request $request, $id = null): JobOpportunity
    {
        $message = json_decode($request->message);

        if ($id)
            $opportunity = JobOpportunity::findOrFail($id);
        else
            $opportunity = new JobOpportunity();
        $opportunity->title = $request->title;
        $opportunity->save();

        $html = $this->formatQuillHtml($message, "jobOpportunity/$opportunity->id");

        $opportunity->message = $html;
        $opportunity->message_json = $message->ops;
        $opportunity->save();

        $opportunity->carriers()->sync($request->carriers);

        return $opportunity;
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
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
        $opportunity = JobOpportunity::with('carriers:id,name')
            ->findOrFail($id);
        $params = compact('opportunity');
        return view('jobOpportunities.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\JobOpportunity  $jobOpportunity
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, int $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        if ($request->ajax())
            return ['success' => true];
        else
            return redirect()->route('jobOpportunity.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(int $id)
    {
        $opportunity = JobOpportunity::findOrFail($id);

        if ($opportunity && $this->deleteDirectory("jobOpportunity/$opportunity->id")) {
            return ['success' => $opportunity->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = JobOpportunity::select([
            "job_opportunities.id",
            "job_opportunities.title",
        ]);

        return $this->simpleSearchData($query, $request);
    }
}
