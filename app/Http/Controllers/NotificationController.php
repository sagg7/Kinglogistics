<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\Driver\DriverParams;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\QuillEditor\QuillFormatter;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
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
     * @return Notification
     */
    private function storeUpdate(Request $request, $id = null): Notification
    {
        $message = json_decode($request->message);

        if ($id)
            $notification = Notification::findOrFail($id);
        else
            $notification = new Notification();
        $notification->title = $request->title;
        $notification->carrier_id = $request->carrier;
        $notification->zone_id = $request->zone;
        $notification->turn_id = $request->turn;
        $notification->save();

        $html = $this->formatQuillHtml($message, "notification/$notification->id");

        $notification->message = $html;
        $notification->message_json = $message->ops;
        $notification->save();

        $notification->drivers()->sync($request->drivers);

        return $notification;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('notifications.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('notifications.create', $params);
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
            return redirect()->route('notification.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /*$notification = Notification::findOrFail($id);
        return view('exports.paperwork.template', ['title' => $notification->title, 'html' => $notification->message]);*/
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $notification = Notification::with([
            'drivers:id,name',
            'carrier:id,name',
            'zone:id,name',
        ])
            ->findOrFail($id);
        $params = compact('notification') + $this->createEditParams();
        return view('notifications.edit', $params);
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
            return redirect()->route('notification.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $notification = Notification::findOrFail($id);

        if ($notification && $this->deleteDirectory("notification/$notification->id")) {
            return ['success' => $notification->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request)
    {
        $query = Notification::select([
            "notifications.id",
            "notifications.title",
        ]);

        return $this->simpleSearchData($query, $request);
    }
}
