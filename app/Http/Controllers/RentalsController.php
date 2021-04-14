<?php

namespace App\Http\Controllers;

use App\Models\Chassis;
use App\Models\Driver;
use App\Models\InspectionCategory;
use App\Models\Leased;
use App\Models\Rental;
use App\Models\Trailer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RentalsController extends Controller
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
     * @return \Illuminate\Contracts\Foundation\Application\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create(Request $request)
    {
        $inspection_categories = InspectionCategory::select([
            'id',
            'inspection_category_name',
            'inspection_category_options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();
        $leased_id = $request->id;
        $leased = Leased::find($leased_id);
        $leased->drivers();
        $trailers = Trailer::pluck( 'trailer_number','id')->toArray();
        $drivers = $leased->drivers()->select(DB::raw('concat(name, " ", last_name) as name'), 'id')
            ->pluck('name', 'id')->toArray();
        $params['inspection_categories'] = $inspection_categories;
        $params['leased'] = $leased;
        $params['trailers'] = $trailers;
        $params['drivers'] = $drivers;

        return view('chassis.rentals.create', $params);

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function Store(Request $request)
    {
            $rental = new Rental();
            $rental->rental_date = Carbon::now();
            $rental->cost = $request->input('cost', 0);
            $rental->deposit_amount = $request->input('deposit_amount', 0);
            $rental->is_paid = $request->input('is_paid', 0);
            $rental->periodicity = $request->input('periodicity', 'weekly');
           // $rental->valid_until = $request->input('periodicity', 'weekly');
            $rental->trailer_id = $request->input('trailer_id');
            $rental->leased_id = $request->input('leased_id');
            $rental->driver_id = $request->input('driver_id');
            $rental->updated_at = Carbon::now();
            $rental->updated_at = Carbon::now();
            $rental->save();

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
                $inspection_items[38] = ['option_value' => $conditionData,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
            }

            $signature_client = $request->input('signature-8');

            if (!empty($signature_client)) {
                list($type, $signature_client) = explode(';', $signature_client);
                list(, $signature_client) = explode(',', $signature_client);
                $signature_client = base64_decode($signature_client);

                $path = 'photos/leased_'.$rental->leased_id.'/rentals/'.$rental->id;
                Storage::makeDirectory($path);
                $file_name = 'signature_' . $rental->id;
                $extension = '.png';
                $path_file = storage_path('app/' . $path . '/');
                file_put_contents($path_file . '/' . $file_name . $extension, $signature_client);

                $img = Image::make($path_file . $file_name . $extension);
                $img->resize(538, 302, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
                $img->save($path_file . $file_name . $extension, 100);

                /*$disk = Storage::disk(env('STORAGE_S3', 's3-test'));

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

                Storage::delete($path . '/' . $file_name . $extension);*/
            }

            $signature_shop = $request->input('signature-7');

            if (!empty($signature_shop)) {
                list($type, $signature_shop) = explode(';', $signature_shop);
                list(, $signature_shop) = explode(',', $signature_shop);
                $signature_shop = base64_decode($signature_shop);

                $path = 'photos/leased_'.$rental->leased_id.'/rentals/'.$rental->id;
                Storage::makeDirectory($path);
                $file_name_7 = 'signature_inspector_' . $rental->id;
                $extension = '.png';
                $path_file = storage_path('app/' . $path . '/');
                file_put_contents($path_file . '/' . $file_name_7 . $extension, $signature_shop);

                $img = Image::make($path_file . $file_name_7 . $extension);
                $img->resize(538, 302, function ($constraint) {
                    $constraint->aspectRatio();
                });
                $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
                $img->save($path_file . $file_name_7 . $extension, 100);

                /*$disk = Storage::disk(env('STORAGE_S3', 's3-test'));

                try {
                    $disk->put(
                        'shops_app/' . $path . '/order_' . $id . '/' . $file_name_7 . $extension,
                        (string)file_get_contents($path_file . $file_name_7 . $extension),
                        'public'
                    );
                    $amazon_path = 'https://' . env('STORAGE_BUCKET', 'kipup-test') .
                        '.s3.amazonaws.com/shops_app/' . $path . '/order_' . $id . '/' . $file_name_7 . $extension;
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

                Storage::delete($path . '/' . $file_name_7 . $extension);*/
            }

            $comment = $request->commentInspection;

            $inspection_items[39] = ['option_value' => $comment, 'updated_at' => Carbon::now(),'created_at' => Carbon::now()];

            $rental->inspectionItems()->sync($inspection_items);
            $jsonData = [
                'success' => false,
                'msg' => "Rental saved successfully",
            ];

            return response()->json($jsonData);
    }

    public function getInspectionCategories()
    {
        $inspection_categories = InspectionCategory::select([
            'id',
            'inspection_category_name',
            'inspection_category_options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();

        return $inspection_categories;
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function show(Chassis $chassis)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function edit(Chassis $chassis)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Chassis $chassis)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Chassis  $chassis
     * @return \Illuminate\Http\Response
     */
    public function destroy(Chassis $chassis)
    {
        //
    }
}
