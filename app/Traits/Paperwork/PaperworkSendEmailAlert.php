<?php

namespace App\Traits\Paperwork;

use App\Mail\SendNotificationPaperwork;
use App\Models\Carrier;
use App\Models\Driver;
use App\Models\Paperwork;
use App\Models\PaperworkFile;
use App\Models\PaperworkTemplate;
use App\Models\Trailer;
use App\Models\Truck;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

trait PaperworkSendEmailAlert
{
    private function NotificationPaperworkAlert($data): void
    {   
        
        // $paperwork = Paperwork::find(17);
        // $paperworkName = $paperwork->name;
        // $days = 30;
        // $element = 'carrier';
        $paperwork = Paperwork::find($data['paperwork_id']);
        $days = $data['day'];
        $element = $paperwork->type;
        if($element == 'carrier'){
            $carrier = Carrier::find($data['related_id']);
            $broker_id = $carrier->broker_id;
            $users = User::where('broker_id', $broker_id)
            ->wherehas('roles',function($q){
                $q->where('name','Seller');
            })->get();
            foreach($users as $user){
             $userName = $user->name;
             $email = $user->email;
             $params = compact('paperworkName','days','element','userName','email');
             Mail::to('ecorral@kinglogisticoil.com')->send(new SendNotificationPaperwork($params));
            }
        }
         else if($element == 'driver'){
            $driver = Driver::find($data['related_id']);
            $broker_id = $driver->broker_id;
            $users = User::where('broker_id', $broker_id)
            ->wherehas('roles',function($q){
                $q->where('name','Seller');
            })->get();
            foreach($users as $user){
             $userName = $user->name;
             $email = $user->email;
             $params = compact('paperworkName','days','element','userName','email');
             Mail::to('ecorral@kinglogisticoil.com')->send(new SendNotificationPaperwork($params));
            }
        }
        else if($element == 'truck'){
            $truck = Truck::find($data['related_id']);
            $broker_id = $truck->broker_id;
            $users = User::where('broker_id', $broker_id)
            ->wherehas('roles',function($q){
                $q->where('name','Seller');
            })->get();
            foreach($users as $user){
             $userName = $user->name;
             $email = $user->email;
             $params = compact('paperworkName','days','element','userName','email');
             Mail::to('ecorral@kinglogisticoil.com')->send(new SendNotificationPaperwork($params));
            }
        }
        else if($element == 'trailer'){
            $trailer = Trailer::find($data['related_id']);
            $broker_id = $trailer->broker_id;
            $users = User::where('broker_id', $broker_id)
            ->wherehas('roles',function($q){
                $q->where('name','Seller');
            })->get();
            foreach($users as $user){
             $userName = $user->name;
             $email = $user->email;
             $params = compact('paperworkName','days','element','userName','email');
             Mail::to('ecorral@kinglogisticoil.com')->send(new SendNotificationPaperwork($params));
            }
        }
    }
} 
