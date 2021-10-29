<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Models\Equipment;
use App\Models\Service;
use App\Traits\QuillEditor\QuillFormatter;
use App\Traits\QuillEditor\QuillHtmlRendering;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BrokerController extends Controller
{
    use FileUpload, QuillFormatter, QuillHtmlRendering;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $company = Broker::find($this->broker_id);
        $equipment = Equipment::where('broker_id', $this->broker_id)->first();
        !$equipment ?: $equipment->message_json = $this->renderForJsonMessage($equipment->message_json);
        $service = Service::where('broker_id', $this->broker_id)->first();
        !$service ?: $service->message_json = $this->renderForJsonMessage($service->message_json);
        $params = compact('company', 'equipment', 'service');
        return view('brokers.profile', $params);
    }

    private function quillValidator(array $data)
    {
        return Validator::make($data, [
            'title' => ['required', 'max:255'],
            'message' => ['required'],
        ]);
    }

    public function update(Request $request)
    {
        $company = Broker::find($this->broker_id) ?: new Broker();
        $company->name = $request->name;
        $company->contact_phone = $request->contact_phone;
        $company->email = $request->email;
        $company->dot_number = $request->dot_number;
        $company->mc_number = $request->mc_number;
        if ($request->insurance)
            $company->insurance_url = $this->uploadFile($request->insurance, "brokers/$company->id/insurance");
        $company->address = $request->address;
        $company->location = $request->coords;
        if ($request->signature) {
            $company->signature = $this->uploadImage($request->signature, "brokers/$company->id/signature");
            if ($company->signature)
                $this->deleteDirectory($company->signature);
        }
        $company->save();

        return redirect()->route('company.profile');
    }

    public function equipment(Request $request)
    {
        $this->quillValidator($request->all())->validate();
        DB::transaction(function () use ($request) {
            $content = json_decode($request->message);

            $equipment = Equipment::where('broker_id', $this->broker_id)->first() ?: new Equipment();
            $equipment->broker_id = $this->broker_id;
            $equipment->title = $request->title;
            $equipment->save();

            $html = $this->formatQuillHtml($content, "equipment/$this->broker_id");

            $equipment->message = $html;
            $equipment->message_json = $content->ops;
            $equipment->save();

            return $equipment;
        });
        return ['success' => true];
    }

    public function service(Request $request)
    {
        $this->quillValidator($request->all())->validate();
        DB::transaction(function () use ($request) {
            $content = json_decode($request->message);

            $service = Service::where('broker_id', $this->broker_id)->first() ?: new service();
            $service->broker_id = $this->broker_id;
            $service->title = $request->title;
            $service->save();

            $html = $this->formatQuillHtml($content, "service/$this->broker_id");

            $service->message = $html;
            $service->message_json = $content->ops;
            $service->save();

            return $service;
        });
        return ['success' => true];
    }
}
