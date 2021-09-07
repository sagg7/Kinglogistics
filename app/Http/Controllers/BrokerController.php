<?php

namespace App\Http\Controllers;

use App\Models\Broker;
use App\Traits\Storage\FileUpload;
use Illuminate\Http\Request;

class BrokerController extends Controller
{
    use FileUpload;
    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function profile()
    {
        $company = Broker::find(1);
        $params = compact('company');
        return view('brokers.profile', $params);
    }

    public function update(Request $request, int $id)
    {
        $company = Broker::find($id ?? 1) ?: new Broker();
        $company->name = $request->name;
        $company->contact_phone = $request->contact_phone;
        $company->email = $request->email;
        $company->dot_number = $request->dot_number;
        $company->mc_number = $request->mc_number;
        if ($request->insurance)
            $company->insurance_url = $this->uploadFile($request->insurance, "brokers/$company->id/insurance");
        $company->address = $request->address;
        $company->location = $request->coords;
        $company->save();

        return redirect()->route('company.profile');
    }
}
