<?php

namespace App\Http\Controllers;

use App\Models\Leased;
use App\Models\Trailer;
use App\Models\TrailerType;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class TrailerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $trailerTypes = TrailerType::pluck( 'Name','id')->toArray();
        $params['trailerTypes'] = $trailerTypes;

        return view('trailers.create', $params);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function inspectionStore(Request $request)
    {
//        dd($request);
        if (isset(session()->get('modules')[3])) {
            $id = $request->leasedId;
            $inspection_items = [];
            $categories = $this->getInspectionCategories();
            foreach ($categories as $category) {
                foreach ($category->items as $item) {
                    $optionsJSON = json_decode($category->inspection_category_options);
                    if ($optionsJSON->type == 'options') {
                        $option_value = $request->input('option_' . $item->id);
                        if ($option_value != $optionsJSON->default) {
                            $attributes = [
                                'option_value' => $option_value,
                                'updated_at' => Carbon::now(),
                                'created_at' => Carbon::now(),
                            ];
                            $inspection_items[$item->id] = $attributes;
                        }
                    }
                    if ($optionsJSON->type == 'inputs') {
                        $option_value = $request->input('option_' . $item->inspection_item_id);
                        if (!empty($option_value)) {
                            $attributes = [
                                'option_value' => $option_value,
                                'updated_at' => Carbon::now(),
                                'created_at' => Carbon::now(),
                            ];
                            $inspection_items[$item->inspection_item_id] = $attributes;
                        }
                    }
                }
            }

            $conditionData = $request->input('condition-data');
            $dataArray = json_decode($conditionData);
            $dataArray[] = ['car_type' => $request->input('condition-background')];
            $conditionData = json_encode($dataArray);
            if ($conditionData != null) {
                // THE INTERNAL ITEM ID OF THE CAR CONDITION DATA IS 39
                $inspection_items[42] = ['option_value' => $conditionData,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
            }

            $signature_client = $request->input('signature-7');

            if (!empty($signature_client)) {
                list($type, $signature_client) = explode(';', $signature_client);
                list(, $signature_client) = explode(',', $signature_client);
                $signature_client = base64_decode($signature_client);

                $path = 'photos/shopper_' . $shop->shop_id;
                Storage::makeDirectory($path);
                $file_name = 'signature_' . $id;
                $extension = '.png';
                $path_file = storage_path('app/' . $path . '/');
                file_put_contents($path_file . '/' . $file_name . $extension, $signature_client);

                $img = Image::make($path_file . $file_name . $extension);
                $img->resize(538, 302, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
                $img->save($path_file . $file_name . $extension, 100);

                $disk = Storage::disk(env('STORAGE_S3', 's3-test'));

                try {
                    $disk->put(
                        'shops_app/' . $path . '/order_' . $id . '/' . $file_name . $extension,
                        (string)file_get_contents($path_file . $file_name . $extension),
                        'public'
                    );
                    $amazon_path = 'https://' . env('STORAGE_BUCKET', 'kipup-test') .
                        '.s3.amazonaws.com/shops_app/' . $path . '/order_' . $id . '/' . $file_name . $extension;
                    // THE INTERNAL ITEM ID OF THE SIGNATURE IS 40
                    $inspection_items[40] = ['option_value' => $amazon_path,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
                } catch (S3Exception $exception) {
                    if ($exception->getResponse()->getStatusCode() === 404) {
                        return [
                            'access' => false,
                            'errors' => ['Hubo un problema al guardar la firma.'],
                        ];
                    }
                    throw $exception;
                }

                Storage::delete($path . '/' . $file_name . $extension);
            }

            $signature_shop = $request->input('signature-10');

            if (!empty($signature_shop)) {
                list($type, $signature_shop) = explode(';', $signature_shop);
                list(, $signature_shop) = explode(',', $signature_shop);
                $signature_shop = base64_decode($signature_shop);

                $path = 'photos/shopper_' . $shop->shop_id;
                Storage::makeDirectory($path);
                $file_name_10 = 'signature_shop_' . $id;
                $extension = '.png';
                $path_file = storage_path('app/' . $path . '/');
                file_put_contents($path_file . '/' . $file_name_10 . $extension, $signature_shop);

                $img = Image::make($path_file . $file_name_10 . $extension);
                $img->resize(538, 302, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
                $img->save($path_file . $file_name_10 . $extension, 100);

                $disk = Storage::disk(env('STORAGE_S3', 's3-test'));

                try {
                    $disk->put(
                        'shops_app/' . $path . '/order_' . $id . '/' . $file_name_10 . $extension,
                        (string)file_get_contents($path_file . $file_name_10 . $extension),
                        'public'
                    );
                    $amazon_path = 'https://' . env('STORAGE_BUCKET', 'kipup-test') .
                        '.s3.amazonaws.com/shops_app/' . $path . '/order_' . $id . '/' . $file_name_10 . $extension;
                    // THE INTERNAL ITEM ID OF THE SIGNATURE IS 74
                    $inspection_items[74] = ['option_value' => $amazon_path,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
                } catch (S3Exception $exception) {
                    if ($exception->getResponse()->getStatusCode() === 404) {
                        return [
                            'access' => false,
                            'errors' => ['Hubo un problema al guardar la firma.'],
                        ];
                    }
                    throw $exception;
                }

                Storage::delete($path . '/' . $file_name_10 . $extension);
            }

            $comment = $request->commentInspection;

            $inspection_items[75] = ['option_value' => $comment, 'updated_at' => Carbon::now(),'created_at' => Carbon::now()];

            $order = Order::find($id);
            $order->inspectionItems()->sync($inspection_items);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $trailer = new Trailer();
        $trailer->trailer_number = $request->input('trailer_number', 0);
        $trailer->trailer_type = $request->input('trailer_type', 0);
        $trailer->trailer_plate = $request->input('trailer_plate', 0);
        $trailer->registration_expiration_date = date("Y-m-d",strtotime($request->input('expiration_date')));
        $trailer->status = 'available';
        $trailer->updated_at = Carbon::now();
        $trailer->created_at = Carbon::now();
        $trailer->save();

        $jsonData = [
            'id' => $trailer->id,
            'success' => false,
            'msg' => "Trailer saved successfully",
        ];

        return response()->json($jsonData);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function show(Trailer $trailer)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function edit(Trailer $trailer)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Trailer $trailer)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Trailer  $trailer
     * @return \Illuminate\Http\Response
     */
    public function destroy(Trailer $trailer)
    {
        //
    }

    public function getTrailers(Request $request){
        $resultsPerPage = $request->input('resultsPerPage', 15);
        $currentPage = $request->input('page', 0);
        $skip = $resultsPerPage * $currentPage;
        $leased = Trailer::select('trailers.id','trailer_number', 'trailer_types.name', 'status')
            ->join('trailer_types', 'trailer_types.id', '=', 'trailer_type');
        if (!empty($request->search))
            $leased->orWhere("trailer_number", "LIKE", "%$request->search%")
                ->orWhere("trailer_types.name", "LIKE", "%$request->search%")
                ->orWhere("status", "LIKE", "%$request->search%");
        $total = $leased->count();
        $result = $leased->skip(0)->take($resultsPerPage)->get();
        $data = [
            'data' => $result,
            'total' => $total
        ];
        return response()->json($data);
    }

}
