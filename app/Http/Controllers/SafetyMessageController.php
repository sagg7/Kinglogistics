<?php

namespace App\Http\Controllers;

use App\Models\SafetyMessage;
use App\Traits\Driver\DriverParams;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\QuillEditor\QuillFormatter;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SafetyMessageController extends Controller
{
    use GetSimpleSearchData, FileUpload, QuillFormatter, DriverParams;

    /**
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data)
    {
        return Validator::make($data, [
            'carrier' => ['required', 'exists:carriers,id'],
            'zone' => ['required', 'exists:zones,id'],
            'driver' => ['nullable', 'exists:drivers,id'],
            'title' => ['required', 'max:255'],
            'message' => ['required'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return $this->getTurnsArray();
    }

    /**
     * @param Request $request
     * @param null $id
     * @return SafetyMessage
     */
    private function storeUpdate(Request $request, $id = null): SafetyMessage
    {
        return DB::transaction(function () use ($request, $id) {
            $content = json_decode($request->message);

            if ($id)
                $message = SafetyMessage::findOrFail($id);
            else
                $message = new SafetyMessage();
            $message->title = $request->title;
            $message->carrier_id = $request->carrier;
            $message->zone_id = $request->zone;
            $message->turn_id = $request->turn;
            $message->save();

            $html = $this->formatQuillHtml($content, "safety_message/$message->id");

            $message->message = $html;
            $message->message_json = $content->ops;
            $message->save();

            $message->drivers()->sync($request->drivers);

            return $message;
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('safetyMessages.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('safetyMessages.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        if ($request->ajax())
            return ['success' => true];
        else
            return redirect()->route('safety_message.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*$message = SafetyMessage::findOrFail($id);
        return view('exports.paperwork.template', ['title' => $message->title, 'html' => $message->message]);*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $message = SafetyMessage::with([
            'drivers:id,name',
            'carrier:id,name',
            'zone:id,name',
        ])
            ->findOrFail($id);
        $params = compact('message') + $this->createEditParams();
        return view('safetyMessages.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     * @throws \Illuminate\Validation\ValidationException
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        if ($request->ajax())
            return ['success' => true];
        else
            return redirect()->route('safety_message.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $message = SafetyMessage::findOrFail($id);

        if ($message && $this->deleteDirectory("safety_message/$message->id")) {
            return ['success' => $message->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = SafetyMessage::select([
            "safety_messages.id",
            "safety_messages.title",
        ]);

        return $this->multiTabSearchData($query, $request);
    }
}
