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

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $company = Broker::find(session('broker'));
        $equipment = Equipment::where('broker_id', session('broker'))->first();
        !$equipment ?: $equipment->message_json = $this->renderForJsonMessage($equipment->message_json);
        $service = Service::where('broker_id', session('broker'))->first();
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
        $company = Broker::find(session('broker')) ?: new Broker();
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

            $equipment = Equipment::where('broker_id', session('broker'))->first() ?: new Equipment();
            $equipment->broker_id = session('broker');
            $equipment->title = $request->title;
            $equipment->save();

            $html = $this->formatQuillHtml($content, "equipment/session('broker')");

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

            $service = Service::where('broker_id', session('broker'))->first() ?: new service();
            $service->broker_id = session('broker');
            $service->title = $request->title;
            $service->save();

            $html = $this->formatQuillHtml($content, "service/session('broker')");

            $service->message = $html;
            $service->message_json = $content->ops;
            $service->save();

            return $service;
        });
        return ['success' => true];
    }
}
