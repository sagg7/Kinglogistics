<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use App\Traits\QuillEditor\QuillFormatter;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    use GetSimpleSearchData, FileUpload, QuillFormatter;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'name' => ['title', 'string', 'max:255'],
            'message' => ['title', 'string', 'max:512'],
        ]);
    }

    /**
     * @param Request $request
     * @param null $id
     * @return Notification
     */
    private function storeUpdate(Request $request, $id = null): Notification//: Notification
    {
        $message = json_decode($request->message);

        if ($id)
            $notification = Notification::findOrFail($id);
        else
            $notification = new Notification();
        $notification->title = $request->title;
        $notification->save();

        $html = $this->formatQuillHtml($message, "notification/$notification->id");

        $notification->message = $html;
        $notification->message_json = $message->ops;
        $notification->save();

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
        return view('notifications.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->storeUpdate($request);

        if ($request->ajax())
            return ['success' => true];
        else
            return redirect()->route('notification.index');
    }

    private function replaceText(string $string)
    {
        $matches = ["/\n/",];
        $replacements = ["<br>",];

        return preg_replace($matches, $replacements, $string);
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
        $notification = Notification::findOrFail($id);
        $params = compact('notification');
        return view('notifications.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
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
