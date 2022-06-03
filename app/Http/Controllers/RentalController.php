<?php

namespace App\Http\Controllers;

use App\Enums\PeriodEnum;
use App\Enums\TrailerEnum;
use App\Exports\TemplateExport;
use App\Models\InspectionCategory;
use App\Models\InspectionRentalDelivery;
use App\Models\InspectionRentalReturned;
use App\Models\Rental;
use App\Models\RentalDeliveryPhotos;
use App\Models\RentalReturnPhotos;
use App\Models\Trailer;
use App\Traits\EloquentQueryBuilder\GetSimpleSearchData;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use App\Traits\Storage\FileUpload;
use App\Traits\Storage\S3Functions;
use Mpdf\Mpdf;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class RentalController extends Controller
{
    use GetSimpleSearchData, FileUpload, S3Functions;

    /**
     * @param array $data
     * @param int|null $id
     * @return \Illuminate\Contracts\Validation\Validator
     */
    private function validator(array $data, int $id = null)
    {
        return Validator::make($data, [
            'carrier_id' => ['required', 'exists:carriers,id'],
            'driver_id' => ['exists:drivers,id'],
            'trailer_id' => ['required', 'exists:trailers,id'],
            'date_submit' => ['required', 'date'],
            'cost' => ['required', 'numeric'],
            'deposit' => ['nullable', 'numeric'],
        ]);
    }

    /**
     * @return array
     */
    private function createEditParams(): array
    {
        return [
            'periods' => [null => '', PeriodEnum::WEEKLY => 'Weekly', PeriodEnum::MONTHLY => 'Monthly', PeriodEnum::ANNUAL => 'Annual'],
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('rentals.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $params = $this->createEditParams();
        return view('rentals.create', $params);
    }

    private function storeUpdate(Request $request, $id = null): Rental
    {
        return DB::transaction(function () use ($request, $id) {
            if ($id)
                $rental = Rental::whereHas('broker', function ($q) {
                    $q->where('id', session('broker'));
                })
                    ->findOrFail($id);
            else {
                $rental = new Rental();
                $rental->broker_id = session('broker');
                $rental->status = 'uninspected';
            }
            $rental->trailer_id = $request->trailer_id;
            $rental->carrier_id = $request->carrier_id;
            $rental->driver_id = $request->driver_id;
            $rental->date = Carbon::parse($request->date_submit);
            $rental->cost = $request->cost;
            $rental->deposit = $request->deposit;
            $rental->deposit_is_paid = $request->is_paid ?? null;
            $rental->period = $request->period;
            $rental->user_id = auth()->user()->id;
            $rental->save();

            if ($rental->status !== 'finished') {
                // Assign trailer to driver's truck
                if ($rental->driver && $rental->driver->truck) {
                    $rental->driver->truck->trailer_id = $request->trailer_id;
                    $rental->driver->truck->save();
                }
                // Assign trailer status to rented
                $rental->trailer->status = TrailerEnum::RENTED;
                $rental->trailer->save();
            } else {
                // Assign trailer status to available
                $rental->trailer->status = TrailerEnum::AVAILABLE;
                $rental->trailer->save();
            }

            return $rental;
        });
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request);

        return redirect()->route('rental.index');
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with(['carrier:id,name', 'driver:id,name', 'trailer:id,number'])
            ->findOrFail($id);
        $params = compact('rental') + $this->createEditParams();
        return view('rentals.edit', $params);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->validator($request->all())->validate();

        $this->storeUpdate($request, $id);

        return redirect()->route('rental.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with('trailer')
            ->findOrFail($id);

        if ($rental) {
            $trailer = $rental->trailer;
            $trailer->status = TrailerEnum::AVAILABLE;
            $trailer->save();
            return ['success' => $rental->delete()];
        } else
            return ['success' => false];
    }

    /**
     * @param $item
     * @return array|string[]|null
     */
    private function getRelationArray($item): ?array
    {
        switch ($item) {
            case 'carrier':
            case 'driver':
                $array = [
                    'relation' => $item,
                    'column' => 'name',
                ];
                break;
            case 'trailer':
                $array = [
                    'relation' => $item,
                    'column' => 'number',
                ];
                break;
            default:
                $array = null;
                break;
        }

        return $array;
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request, $type)
    {
        $query = Rental::select([
            "rentals.id",
            "rentals.date",
            "rentals.carrier_id",
            "rentals.driver_id",
            "rentals.trailer_id",
            "rentals.period",
            "rentals.cost",
            "rentals.status",
            "rentals.deposit",
            "rentals.status",
            "rentals.delivered_at",
            "rentals.finished_at",
        ])
            ->with(['carrier:id,name', 'driver:id,name', 'trailer:id,number'])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where('status', $type)
            ->where(function ($q) use ($type, $request) {
                switch ($type) {
                    case 'finished':
                        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
                        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
                        $q->whereDate('finished_at', '>=', $start)
                            ->whereDate('finished_at', '<=', $end);
                        break;
                }
            });

        return $this->multiTabSearchData($query, $request, 'getRelationArray');
    }

    private function createEditInspectionParams()
    {
        return [
            'coordsTemplates' => [
                1 => ["text" => "Sandbox", "img_src" => asset('images/app/trailers/sandbox.png')],
                2 => ["text" => "HiCrush", "img_src" => asset('images/app/trailers/sandbox.png')],
            ],
        ];
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function createInspection(Request $request)
    {
        $rental_id = $request->id;
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with(['broker' => function ($q) {
                $q->with('config:broker_id,rental_inspection_check_out_annex');
            }])
            ->findOrFail($rental_id);
        $inspection_categories = InspectionCategory::select([
            'id',
            'name',
            'options',
            'position',
        ])
            ->with('items')
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();

        $pictures = DB::table('rental_delivery_photos')
            ->where('rental_id', '=', $rental_id)->get();
        $inspectionItems = InspectionRentalDelivery::where('rental_id', $rental_id)->pluck( 'option_value','inspection_item_id')->toArray();

        $params = [
            'title' => 'Deliver',
            'inspection_categories' => $inspection_categories,
            'rental' => $rental,
            'leased' => $rental->leased,
            'pictures' => $pictures,
            'inspection_items' => $inspectionItems,
            'action' => 'inspection.store',
            'type' => 'deliver',
        ] + $this->createEditInspectionParams();
        return view('rentals.createInspection', $params);

    }

    public function storeInspection(Request $request){
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($request->rental_id);
        $inspection_items = $this->createInspectionArray($request, $rental);
        $rental->inspectionItems()->sync($inspection_items);
        $rental->delivered_at = Carbon::now();
        $rental->status = 'delivered';
        $rental->delivery_user_id = auth()->user()->id;
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
                $optionsJSON = json_decode($category->options);
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
            $inspection_items[41] = ['option_value' => $this->uploadImage($signature_client, "rentals/rental_".$rental->id."/".$stage,80),'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
        }

        $signature_shop = $request->input('signature-7');

        if (!empty($signature_shop)) {
            $inspection_items[40] = ['option_value' => $this->uploadImage($signature_shop, "rentals/rental_".$rental->id."/".$stage,80),'updated_at' => Carbon::now(),'created_at' => Carbon::now()];
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
            'name',
            'options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();

        return $inspection_categories;
    }

    public function createEndRental(Request $request)
    {
        $rental_id = $request->id;
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->with(['broker' => function ($q) {
                $q->with('config:broker_id,rental_inspection_check_in_annex');
            }])
            ->findOrFail($rental_id);
        $inspection_categories = InspectionCategory::select([
            'id',
            'name',
            'options',
            'position',
        ])
            ->whereNull('deleted_at')
            ->orderBy('position')
            ->get();
        $pictures = DB::table('rental_return_photos')
            ->where('rental_id', '=', $rental_id)->get();
        $tempPics = [];
        foreach ($pictures as $key => $picture){
            $tempPics[$key]["id"] = $picture->id;
            $tempPics[$key]["url"] = $this->getTemporaryFile($picture->url);
            $tempPics[$key]["created_at"] = $picture->created_at;
        }
        $inspectionItems = InspectionRentalReturned::where('rental_id', $rental_id)->pluck( 'option_value','inspection_item_id')->toArray();// add flag to deliver trailer
        foreach ($inspectionItems as $key => $inspectionItem){
            if ($key == 41 || $key == 40)
                $inspectionItems[$key] = $this->getTemporaryFile($inspectionItems[$key]);
        }
        $params = [
            'title' => 'Return - End Rental',
            'inspection_categories' => $inspection_categories,
            'rental' => $rental,
            'leased' => $rental->leased,
            'pictures' => $tempPics,
            'inspection_items' => $inspectionItems,
            'action' => 'rental.end',
            'type' => 'return',
        ]  + $this->createEditInspectionParams();
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
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($request->rental_id);

        $inspection_items = $this->createInspectionArray($request, $rental, "return");
        $rental->inspectionItemsReturned()->sync($inspection_items);
        $rental->finished_at = Carbon::now();
        $rental->status = 'finished';
        $rental->returned_user_id = auth()->user()->id;
        $rental->save();
        $rental->trailer->status = TrailerEnum::AVAILABLE;
        $rental->trailer->save();

        return response()->json([
            'msg' => 'Rental terminated correctly',
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

        $countImg = RentalDeliveryPhotos::where('rental_id', $request->header('rentalid'))->count() + 1;
        $max_photos = 20;
        if ($countImg >= $max_photos) {
            $msg = 'You have reached the maximum number of images.';
            $jsonData = [
                'success' => false,
                'msg' => $msg,
            ];
            return response()->json($jsonData);
        }
        $rental = Rental::whereHas('broker', function ($q) {
            $q->where('id', session('broker'));
        })
            ->findOrFail($request->header('rentalId'));
        if ($rental->status == 'uninspected'){
            $stage = 'deliver';
            $inspection_photos = new RentalDeliveryPhotos();
        } else {
            $stage = 'return';
            $inspection_photos = new RentalReturnPhotos();

        }
        $file_name = 'pic_' . $countImg . '.jpg';
        $file_name_slider = 'pic_' . $countImg . '_slider.jpg';
        $inspection_photos->rental_id = $request->header('rentalId');
        $inspection_photos->url = $this->uploadImage($request->get('newImage'), "rentals/rental_".$rental->id."/".$stage."/photos",80);
        $inspection_photos->created_at = Carbon::now();
        $inspection_photos->updated_at = Carbon::now();
        $inspection_photos->save();
        $inspection_photos->url = $this->getTemporaryFile($inspection_photos->url);
        return response()->json(['success' => true, 'picture' => $inspection_photos]);
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

    private function getConsecutiveDeliver($rentalId)
    {
        $consecutive = DB::table('rental_delivery_photos')
            ->where('rental_id', '=', $rentalId)
            ->max('id');
        return $consecutive;
    }

    private function getConsecutiveEnd($rentalId)
    {
        $consecutive = DB::table('rental_return_photos')
            ->where('rental_id', '=', $rentalId)
            ->max('id');
        return $consecutive;
    }

    public function getRented(Request $request){
        $resultsPerPage = $request->input('resultsPerPage', 15);
        $currentPage = $request->input('page', 0);
        $skip = $resultsPerPage * $currentPage;
        $leased = Rental::select('leased.name as leased_name', 'trailer_number', 'drivers.name as driver_name', 'date', 'rentals.id', 'status')
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
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

    public function downloadXLS(Request $request, $type)
    {
        $rentals = Rental::with([
            'carrier:id,name',
            'trailer:id,number',
        ])
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->where('status', $type)
            ->where(function ($q) use ($type, $request) {
                switch ($type) {
                    case 'finished':
                        $start = $request->start ? Carbon::parse($request->start) : Carbon::now()->startOfMonth();
                        $end = $request->end ? Carbon::parse($request->end)->endOfDay() : Carbon::now()->endOfMonth()->endOfDay();
                        $q->whereDate('finished_at', '>=', $start)
                            ->whereDate('finished_at', '<=', $end);
                        break;
                }
            })
            ->get();

        if (count($rentals) === 0)
            return redirect()->back()->withErrors('There are no rentals to generate the document');

        $data = [];
        foreach ($rentals as $rental) {
            $data[] = [
                'date' => $rental->date->format('m/d/Y'),
                'carrier' => $rental->carrier->name,
                'driver' => (isset($rental->driver)) ? $rental->driver->name : null,
                'trailer' => $rental->trailer->number,
                'period' => ucfirst($rental->period),
                'cost' => $rental->cost,
                'deposit' => $rental->deposit,
                'delivered_at' => $rental->delivered_at,
                'finished_at' => $rental->finished_at,
            ];
        }

        return (new TemplateExport([
            "data" => $data,
            "headers" => ["Date", session('renames') ? session('renames')->carrier : 'Carrier', "Driver", "Trailer", "Period", "Cost", "Deposit", "Delivered At", "Finished At"],
            "formats" => [
                'F' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
                'G' => NumberFormat::FORMAT_CURRENCY_USD_SIMPLE,
            ],
        ]))->download("Rentals " . ucfirst($type) . " - " . Carbon::now()->format('m-d-Y') . ".xlsx");
    }

    private function generatePDF($idx, $returnedFlag = false)
    {
        $withRelations = [
            'carrier:id,name,owner',
            'trailer:id,number',
            'driver:id,name',
            'inspectionItems',
            'user:id,name',
        ];
        if ($returnedFlag) {
            $withRelations[] = 'inspectionItemsReturned';
            $withRelations['broker'] = function ($q) {
                $q->with('config:broker_id,rental_inspection_check_out_annex');
            };
            $withRelations[] = 'returnedUser:id,name';
        } else {
            $withRelations['broker'] = function ($q) {
                $q->with('config:broker_id,rental_inspection_check_in_annex');
            };
            $withRelations[] = 'deliveryUser:id,name';
        }
        $rental = Rental::with($withRelations)
            ->whereHas('broker', function ($q) {
                $q->where('id', session('broker'));
            })
            ->findOrFail($idx);
        $categories = InspectionCategory::orderBy('position')
            ->get(['id', 'name', 'position', 'options'])
            ->keyBy('id')
            ->toArray();

        $createEdit = $this->createEditInspectionParams();
        $itemsDelivery = $rental->inspectionItems->toArray();
        $itemsReturned = $rental->inspectionItemsReturned->toArray() ?? [];
        $items = $returnedFlag ? $itemsReturned : $itemsDelivery;
        if (count($items) === 0) {
            return redirect()->back()->withErrors('No data found to generate the PDF');
        }
        foreach ($items as $item) {
            if ($returnedFlag) {
                $idx = array_search($item['id'], array_column($itemsDelivery, 'id'), true);
                if ($idx !== false) {
                    $original_value = $itemsDelivery[$idx]['pivot']['option_value'];
                    $category_type = json_decode($categories[$item["inspection_category_id"]]['options'])->type;
                    switch ($category_type) {
                        case 'options':
                            $original_value = $original_value === "0" ? null : $original_value;
                            $value_changed = $original_value !== $item['pivot']['option_value'];
                            break;
                        case 'inputs':
                            $original_value = json_decode($original_value);
                            $current_value = json_decode($item['pivot']['option_value']);
                            $value_changed = [];
                            foreach ($original_value as $i => $original) {
                                $value_changed[] = $original !== $current_value[$i];
                            }
                            break;
                        default:
                            $value_changed = false;
                            break;
                    }
                    $itemsDelivery[$idx]['pivot']['value_changed'] = $value_changed;
                    $item['return_data'] = $itemsDelivery[$idx]['pivot'];
                }
            }

            switch ($item["id"]) {
                case 38:
                    $car_type = array_column(json_decode($item["pivot"]["option_value"]), 'car_type')[0];
                    $item["pivot"]['coords_template'] = $createEdit["coordsTemplates"][$car_type];

                    break;
                default:
                    break;
            }
            $categories[$item["inspection_category_id"]]['rental_items'][] = $item;
        }
        $categories = array_values($categories);

        $fmt = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
        $rental->cost = numfmt_format_currency($fmt, $rental->cost, 'USD');
        $rental->deposit = numfmt_format_currency($fmt, $rental->deposit, 'USD');
        $companyLogo = asset('images/app/logos/logo.png');
        $params = compact('companyLogo', 'rental', 'categories', 'returnedFlag');
        return view('exports.rentals.inspection', $params);
    }

    public function downloadInspectionDeliveryPDF($id)
    {
        return $this->generatePDF($id);
    }

    public function downloadInspectionReturnedPDF($id)
    {
        return $this->generatePDF($id, true);
    }
}
