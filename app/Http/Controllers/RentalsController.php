<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\InspectionCategory;
use App\Models\InspectionItem;
use App\Models\InspectionPictures;
use App\Models\InspectionRental;
use App\Models\InspectionRentalReturned;
use App\Models\Leased;
use App\Models\Rental;
use App\Models\Trailer;
use Aws\S3\Exception\S3Exception;
use Carbon\Carbon;
use http\Exception;
use http\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class RentalsController extends Controller
{
    protected $inspectionPictures;
    /**
    * @param InspectionPictures $inspectionPictures
    */
    public function __construct(InspectionPictures $inspectionPictures){
        $this->inspectionPictures = $inspectionPictures;
    }
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
        $leased_id = $request->id;
        $leased = Leased::find($leased_id);
        $leased->drivers();
        $trailers = Trailer::pluck( 'trailer_number','id')->toArray();
        $drivers = $leased->drivers()->select(DB::raw('concat(name, " ", last_name) as name'), 'id')
            ->pluck('name', 'id')->toArray();
        $params['leased'] = $leased;
        $params['trailers'] = $trailers;
        $params['drivers'] = $drivers;

        return view('rentals.create', $params);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function createInspection(Request $request)
    {
        $rental_id = $request->id;
        $rental = Rental::find($rental_id);
        $inspection_categories = InspectionCategory::select([
            'id',
            'inspection_category_name',
            'inspection_category_options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();
        $pictures = DB::table('inspection_pictures')
            ->where('rental_id', '=', $rental_id)
            ->where('stage', 'deliver')->get();
        $rental->drivers;
        $rental->trailers;
        $inspectionItems = InspectionRental::where('rental_id', $rental_id)->pluck( 'option_value','inspection_item_id')->toArray();

        $params['title'] = 'Deliver';
        $params['inspection_categories'] = $inspection_categories;
        $params['rental'] = $rental;
        $params['leased'] = $rental->leased;
        $params['pictures'] = $pictures;
        $params['inspection_items'] = $inspectionItems;
        $params['action'] = 'inspection.store';
        //dd($inspectionItems);
        return view('rentals.createInspection', $params);

    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
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
            $rental->rental_status = 'Uninspected';
            $rental->updated_at = Carbon::now();
            $rental->created_at = Carbon::now();
            $rental->save();

            $trailer = Trailer::find($rental->trailer_id);
            $trailer->status = 'rented';
            $trailer->save();

            $jsonData = [
                'id' => $rental->id,
                'success' => false,
                'msg' => "Rental saved successfully",
            ];

            return response()->json($jsonData);
    }

    public function storeInspection(Request $request){
        $rental = Rental::find($request->rental_id);
        $inspection_items = $this->createInspectionArray($request, $rental);
        $rental->inspectionItems()->sync($inspection_items);
        $rental->delivered_at = Carbon::now();
        $rental->rental_status = 'Rented';
        $rental->save();
        $jsonData = [
            'id' => $rental->id,
            'success' => false,
            'msg' => "Inspection saved successfully",
        ];
        return response()->json($jsonData);
    }

    private function createInspectionArray(Request $request, $rental, $stage="deliver"){
        $inspection_items = [];
        $categories = $this->getInspectionCategories();
        foreach ($categories as $category) {
            foreach ($category->items as $item) {
                $optionsJSON = json_decode($category->inspection_category_options);
                if ($optionsJSON->type == 'options') {
                    $option_value = $request->input('option_' . $item->id);
                    if ($option_value != $optionsJSON->default && $option_value != null) {
                        $attributes = [
                            'option_value' => $option_value,
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now(),
                        ];
                        $inspection_items[$item->id] = $attributes;
                    }
                }
                if ($optionsJSON->type == 'inputs') {
                    $option_value = $request->input('option_' . $item->id);
                    if (!empty($option_value)) {
                        $attributes = [
                            'option_value' => json_encode($option_value),
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now(),
                        ];
                        $inspection_items[$item->id] = $attributes;
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

            $path = 'photos/leased_'.$rental->leased_id.'/rentals/'.$rental->id.'/'.$stage;
            Storage::makeDirectory('public/'.$path);
            $file_name = 'signature_' . $rental->id;
            $extension = '.png';
            $path_file = storage_path('app/public/' . $path . '/');
            file_put_contents($path_file . '/' . $file_name . $extension, $signature_client);

            $img = Image::make($path_file . $file_name . $extension);
            $img->resize(538, 302, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
            $img->save($path_file . $file_name . $extension, 100);
            $inspection_items[41] = ['option_value' => url('storage')."/".$path."/". $file_name . $extension,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];


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

            $path = 'photos/leased_'.$rental->leased_id.'/rentals/'.$rental->id.'/'.$stage;
            Storage::makeDirectory('public/'.$path);
            $file_name_7 = 'signature_inspector_' . $rental->id;
            $extension = '.png';
            $path_file = storage_path('app/public/' . $path . '/');
            file_put_contents($path_file . '/' . $file_name_7 . $extension, $signature_shop);

            $img = Image::make($path_file . $file_name_7 . $extension);
            $img->resize(538, 302, function ($constraint) {
                $constraint->aspectRatio();
            });
            $img->resizeCanvas(538, 302, 'center', false, [255, 255, 255, 0]);
            $img->save($path_file . $file_name_7 . $extension, 100);
            $inspection_items[40] = ['option_value' => url('storage')."/".$path."/". $file_name_7 . $extension,'updated_at' => Carbon::now(),'created_at' => Carbon::now()];

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
        if ($comment != null)
            $inspection_items[39] = ['option_value' => $comment, 'updated_at' => Carbon::now(),'created_at' => Carbon::now()];

        return $inspection_items;
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
     * @param  \App\Models\Rental  $rental
     * @return \Illuminate\Http\Response
     */
    public function show(Rental $rental)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Rental  $rental
     * @return \Illuminate\Http\Response
     */
    public function edit(Rental $rental)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Rental  $rental
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Rental $rental)
    {
        //
    }


    public function createEndRental(Request $request)
    {
        $rental_id = $request->id;
        $rental = Rental::find($rental_id);
        $inspection_categories = InspectionCategory::select([
            'id',
            'inspection_category_name',
            'inspection_category_options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();
        $pictures = DB::table('inspection_pictures')
            ->where('rental_id', '=', $rental_id)
            ->where('stage', 'return')->get();

        dd($pictures);
        $rental->drivers;
        $rental->trailers;
        $inspectionItems = InspectionRentalReturned::where('rental_id', $rental_id)->pluck( 'option_value','inspection_item_id')->toArray();// add flag to deliver trailer

        $params['title'] = 'Return - End Rental';
        $params['inspection_categories'] = $inspection_categories;
        $params['rental'] = $rental;
        $params['leased'] = $rental->leased;
        $params['pictures'] = $pictures;
        $params['inspection_items'] = $inspectionItems;
        $params['action'] = 'rental.end';
        //dd($inspectionItems);
        return view('rentals.createInspection', $params);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function storeEndRental(Request $request)
    {
        $rental = Rental::find($request->rental_id);

        $inspection_items = $this->createInspectionArray($request, $rental, "return");
        $rental->inspectionItemsReturned()->sync($inspection_items);
        $rental->end_rental_at = Carbon::now();
        $rental->rental_status = 'Ended';
        $rental->save();

        return response()->json([
            'msg' => 'Rental terminated correctly',
            'success' => true
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $rental = Rental::find($id);
        $rental->delete();

        return response()->json([
            'msg' => 'Rental elimiated correctly',
            'success' => true
        ]);
    }
    /**
     * upload inspection photos.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function uploadPhoto(Request $request){
        if (!strpos($request->get('newImage'),'image')) {
            return response()->json([
                'msg' => 'Only images are accepted',
                'success' => false
            ]);
        }

        $countImg = InspectionPictures::where('rental_id', $request->header('rentalid'))->count();
        $max_photos = 20;
        if ($countImg >= $max_photos) {
            $msg = 'You have reached the maximum number of images.';
            $jsonData = [
                'success' => false,
                'msg' => $msg,
            ];
            return response()->json($jsonData);
        }
        $rental = Rental::find($request->header('rentalId'));
        if ($rental->rental_status == 'Uninspected'){
            $stage = 'deliver';
        } else {
            $stage = 'return';
        }
        $path = 'photos/leased_' . $rental->leased_id . '/rentals/' . $rental->id. "/" . $stage;
        $path_file = storage_path('app/public/' . $path . '/');
        Storage::makeDirectory('public/'.$path);
        $cont = $this->getConsecutive($rental->id) + 1;
        try {
            $this->img = Image::make($request->get('newImage'));
            $this->img->resize(300, 300, function ($constraint) {
                $constraint->aspectRatio();
            });
        } catch (Exception $e) {
            return $this->catchUploadException($path);
        }
        $file_name = 'pic_' . $cont . '.jpg';
        try {
            $this->img->save($path_file . $file_name, 100);
        } catch (Exception $e) {
            return $this->catchUploadException();
        }

        try {
            $this->image_slider = Image::make($request->get('newImage'));
            $this->image_slider->resize(700, 700, function ($constraint) {
                $constraint->aspectRatio();
            });
        } catch (Exception $e) {
            return $this->catchUploadException($path);
        }

        $file_name_slider = 'pic_' . $cont . '_slider.jpg';
        try {
            $this->image_slider->save($path_file . $file_name_slider, 100);
        } catch (Exception $e) {
            return $this->catchUploadException();
        }
//upload to amazon
        /*$disk = Storage::disk(env('STORAGE_S3', 's3-test'));
        $success = true;
        try {
            $disk->put(
                'shops_app/' . $path . '/' . $file_name,
                (string)file_get_contents($path_file . $file_name),
                'public'
            );
            $disk->put(
                'shops_app/' . $path . '/' . $file_name_slider,
                (string)file_get_contents($path_file . $file_name_slider),
                'public'
            );
            $amazon_path = 'https://' . env('STORAGE_BUCKET',  'kipup-test') .
                '.s3.amazonaws.com/shops_app/' . $path . '/';
        } catch (S3Exception $exception) {
            /*if ($exception->getResponse()->getStatusCode() === 404) {
                return redirect()->back()->withErrors('Hubo un problema al enviar la Foto.');
            }
            throw $exception;*/
        /*    $success = false;
        }*/

        $this->inspectionPictures->rental_id = $request->header('rentalId');
        $this->inspectionPictures->picture_name = $file_name;
        $this->inspectionPictures->picture_slider = $file_name_slider;
        $this->inspectionPictures->picture_path = url('storage')."/".$path."/";
        $this->inspectionPictures->stage = $stage;
        $this->inspectionPictures->created_at = Carbon::now();
        $this->inspectionPictures->updated_at = Carbon::now();
        $this->inspectionPictures->save();
        return response()->json(['success' => true, 'picture' => $this->inspectionPictures]);
    }

    private function validateFileType(Request $request)
    {
        $this->validate($request, [
            'newFile' => 'max:5120|mimes:doc,docb,docm,dot,dotm,dotx,docx,xlm,xls,xlsm,xlsx,xlsb,xlt,xltm,xltx,xl,xml,pdf,jpg,png,jpeg,odm,odg,otg,odp',
        ], [
            'newFile.max' => 'The file exceeds the maximum size of 5mb',
            'newFile.mimes' => 'Invalid file type',
        ]);
    }

    /**
     * @param $path
     * @return \Illuminate\Http\JsonResponse
     */
    private function catchUploadException($path = null)
    {
        if ($path)
            Storage::disk('local')->deleteDirectory($path);
        return response()->json(['success' => false, 'msg' => ['The image could not be processed, make sure it has a correct format and is not damaged, and try again']]);
    }

    private function getConsecutive($rentalId)
    {
        $consecutive = DB::table('inspection_pictures')
            ->where('rental_id', '=', $rentalId)
            ->max('id');
        return $consecutive;
    }

    public function getRented(Request $request){
        $resultsPerPage = $request->input('resultsPerPage', 15);
        $currentPage = $request->input('page', 0);
        $skip = $resultsPerPage * $currentPage;
        $leased = Rental::select('leased.name as leased_name', 'trailer_number', 'drivers.name as driver_name', 'rental_date', 'rentals.id', 'rental_status')
            ->join('leased', 'leased.id', '=', 'leased_id')
            ->join('trailers', 'trailers.id', '=', 'trailer_id')
            ->join('drivers', 'drivers.id', '=', 'driver_id');
        if (!empty($request->search))
            $leased->orWhere("name", "LIKE", "%$request->search%")
                ->orWhere("email", "LIKE", "%$request->search%")
                ->orWhere("phone", "LIKE", "%$request->search%")
                ->orWhere("address", "LIKE", "%$request->search%");
        $total = $leased->count();
        $result = $leased->skip(0)->take($resultsPerPage)->get();
        $data = [
            'data' => $result,
            'total' => $total
        ];
        return response()->json($data);
    }
}
