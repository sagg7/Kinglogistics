<?php

namespace App\Http\Controllers\Shippers;

use App\Http\Controllers\Controller;
use App\Models\Equipment;
use App\Models\Service;
use App\Traits\Storage\S3Functions;
use DOMDocument;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use S3Functions;

    protected $broker_id;

    public function __construct()
    {
        $this->broker_id = 1;
    }

    public function profile()
    {
        $shipper = auth()->user();
        $params = compact('shipper');
        return view('subdomains.shippers.profile.edit', $params);
    }

    public function equipment()
    {
        $equipment = Equipment::where('broker_id', $this->broker_id)
            ->first([
                'title',
                'message'
            ]);
        if (!$equipment) {
            $equipment = [];
        } else {
            $dom = new DOMDocument();
            $dom->loadHTML($equipment->message);

            foreach ($dom->getElementsByTagName('img') as $img) {
                $img->setAttribute('src', $this->getTemporaryFile(substr($img->getAttribute('src'),1)));
                $img->setAttribute('class', 'img-fluid');
            }

            foreach ($dom->getElementsByTagName('blockquote') as $item) {
                $item->setAttribute('class', 'blockquote pl-1 border-left-primary border-left-3');
            }
            $equipment->html = $dom->saveHTML();
            $equipment = $equipment->toArray();
        }
        $params = ['section' => 'Equipment'] + $equipment;
        return view('layouts.renderHtml', $params);
    }

    public function services()
    {
        $service = Service::where('broker_id', $this->broker_id)
            ->first([
                'title',
                'message',
            ]);
        if (!$service) {
            $service = [];
        } else {
            $dom = new DOMDocument();
            $dom->loadHTML($service->message);

            foreach ($dom->getElementsByTagName('img') as $img) {
                $img->setAttribute('src', $this->getTemporaryFile(substr($img->getAttribute('src'),1)));
                $img->setAttribute('class', 'img-fluid');
            }

            foreach ($dom->getElementsByTagName('blockquote') as $item) {
                $item->setAttribute('class', 'blockquote pl-1 border-left-primary border-left-3');
            }
            $service->html = $dom->saveHTML();
            $service = $service->toArray();
        }
        $params = ['section' => 'Services'] + $service;
        return view('layouts.renderHtml', $params);
    }
}
