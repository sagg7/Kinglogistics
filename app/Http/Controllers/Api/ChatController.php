<?php

namespace App\Http\Controllers\Api;

use App\Events\NewChatMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\ConversationResource;
use App\Models\BotAnswers;
use App\Models\BotQuestions;
use App\Models\Driver;
use App\Models\Message;
use App\Traits\Chat\MessagesTrait;
use App\Traits\EloquentQueryBuilder\GetSelectionData;
use Illuminate\Http\Request;
use App\Traits\Notifications\PushNotificationsTrait;
use App\Enums\DriverAppRoutes;


class ChatController extends Controller
{

    use GetSelectionData, PushNotificationsTrait, MessagesTrait;

    public function getChatHistory(Request $request)
    {
        $driver = auth()->user();

        $query = Message::where('driver_id', $driver->id)
            ->orderBy('id', 'desc');

//        $this->readMessages($driver_id, auth()->user()->id);

        $messages = $this->selectionData($query, $request->take, $request->page);

        return response(['status' => 'ok', 'data' => $messages]);
    }

    public function sendMessageAsUser(Request $request)
    {
        $driverIds = $request->get('driver_ids');
        $content = $request->get('content');
        $userId = $request->get('user_id');
        $image = $request->get('image');

        if (empty($driverIds)) {
            return response('Drivers collection is not setted', 400);
        }

        if (!empty($image)) {
            $image = $this->uploadImage($image, 'chat');
        }

        foreach ($driverIds as $driverId) {
            $driver = Driver::find($driverId);

            $message = $this->sendMessage(
                $driverId,
                $content,
                $userId,
                null,
                null,
                null,
                $image
            );

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                $content,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
        }

        return response(['status' => 'ok'], 200);
    }

    public function sendMessageAsDriver(Request $request)
    {
        $driver = auth()->user();
        $content = $request->get('content');
        $image = $request->get('image');

        if (!empty($image)) {
            $image = $this->uploadImage($image, 'chat');
        }


        $message = $this->sendMessage(
            $driver->id,
            $content,
            null,
            true,
            null,
            null,
            $image
        );

        event(new NewChatMessage($message));

        $botAnswer = BotAnswers::where('driver_id', $driver->id)->first();

        $affirmative = 2;
        if ($botAnswer != null && $botAnswer->answer != null && $botAnswer->incorrect < 6){
            if (strtolower($content)  == 'si' || strtolower($content)  == 'yes' || strtolower($content)  == 'y' || strtolower($content)  == 's')
                $affirmative = 1;
            if (strtolower($content)  == 'no' || strtolower($content)  == 'n')
                $affirmative = 0;
            if ($affirmative != 2){
                switch ($botAnswer->bot_question_id){
                    case '1':
                        if( $affirmative ){
                            $driver->status = 'ready';
                        }
                        else
                            $driver->status = 'inactive';
                        $driver->save();
                        $botAnswer->answer = $content;
                        $driver->save();
                        $responseContent = BotQuestions::find(5)->question; //Excelente!, nos vemos a las 6!
                        break;
                    case '2':
                        if( $affirmative )
                            $driver->status = 'active';
                        else
                            $driver->status = 'inactive';
                        $driver->save();
                        $botAnswer->answer = $content;
                        $driver->save();
                        $responseContent = BotQuestions::find(6)->question; //Lamento escuchar eso, ¿Cual es el motivo?
                        $botAnswer->delete();
                        break;
                    default:
                        $responseContent = BotQuestions::find(4)->question; //Lo siento no te entendí, por favor contesta: SI NO
                        break;
                }
            }
            $message = $this->sendMessage(
                $driver->id,
                $responseContent,
                null,
                null,
                null,
                null,
                $image,
                1
            );
            $botAnswer->incorrect++;
            $botAnswer->save();

            $driverDevices = $this->getUserDevices($driver);

            $this->sendNotification(
                'Message from King',
                BotQuestions::find(4)->question,
                $driverDevices,
                DriverAppRoutes::CHAT,
                $message,
                DriverAppRoutes::CHAT_ID,
            );
        }

        return response(['status' => 'ok'], 200);
    }

}
