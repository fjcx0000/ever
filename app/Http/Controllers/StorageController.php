<?php

namespace App\Http\Controllers;

use Datatables;
use Illuminate\Http\Request;
use App\Repositories\Storage\StorageRepositoryContract;
use App\Models\StorageLocation;
use App\Models\StorageItem;
use App\Models\Color;
use App\Exceptions\StorageException;
use Exception;
use Carbon\Carbon;

class StorageController extends Controller
{
    protected $storage;

    public function __construct(
        StorageRepositoryContract $storage
    )
    {
        $this->storage = $storage;
    }
    /**
     * Display location management page
     */
    public function locIndex(Request $request)
    {
        return view('storages.locmanage');
    }

    /**
     * Add location
     */
    public function addLocation(Request $request)
    {
        $this->validate($request,[
            'area'=>'required',
            'line'=>'required',
            'unit'=>'required',
        ]);

        // duplication check
        if (empty($request->level)) {
            $request->level = "";
        }
        if (StorageLocation::where([
            ['area', '=', $request->area],
            ['line', '=', $request->line],
            ['unit', '=', $request->unit],
            ['level', '=', $request->level],
        ])->exists()) {
            return [
                'result' => false,
                'message' => 'location already exists',
            ];
        }

        $location = new StorageLocation;
        $location->area = $request->area;
        $location->line = $request->line;
        $location->unit = $request->unit;
        $location->level = (empty($request->level)? "" : $request->level);
        $location->save();
        return [
            'result' => true,
            'message' => $request->area."-".
                            $request->line."-".
                            $request->unit.
                            (empty($request->level)? " ": "-".$request->level).
                            " added successfully.",
        ];
    }

    public function getLocations(Request $request)
    {
        $locations = $this->storage->getLocations($request);
        return Datatables::of($locations)
            ->addColumn('code', function ($location) {
                return empty($location->level) ?
                    $location->area."-".$location->line."-".$location->unit :
                    $location->area."-".$location->line."-".$location->unit."-".$location->level;
            })
            ->add_column('isused', '<a href="#" class="btn btn-success" >Items</a>')
            ->add_column('delete', '<input type="button" name="delete" value="delete" id="delete{{ $id }}" 
                class="btn btn-danger"i onclick="del_location({{$id}},\'{{$code}}\')">')
            ->make(true);
    }
    public function delLocation(Request $request)
    {
        $this->validate($request,[
            'id'=>'required',
            'code'=>'required',
        ]);
        try {
            $this->storage->delLocation($request);
            return [
                'result' => true,
                'message' => "Location ".$request->code." deleted successfully",
            ];
        } catch (StorageException $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
    public function uploadLocationFile(Request $request)
    {

        $this->validate($request,[
            'filetype'=>'required',
            'uploadfile'=>'required',
        ]);
        try {
            switch ($request->filetype) {
                case "location":
                    $impNumber = $this->storage->importLocationFile($request);
                    break;
                case "relation":
                    $impNumber = $this->storage->importRelationFile($request);
                    break;
                default:
                    return [
                        'result' => false,
                        'message' => "Wrong filetype!",
                    ];
            }
            return [
                'result' => true,
                'message' => "File processed successfully, ".$impNumber." records imported."
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }
    /**
     * Display item management page
     */
    public function itemIndex(Request $request)
    {
        return view('storages.itemmanage');
    }

    /**
     * Add Storage Item
     */
    public function addItem(Request $request)
    {
        $this->validate($request,[
            'product_id'=>'required',
        ]);

        // duplication check
        if (empty($request->color_id)) {
            $request->color_id = "";
        }
        if (empty($request->size_value)) {
            $request->size_value = "";
        }
        if (StorageItem::where([
            ['product_id', '=', $request->product_id],
            ['color_id', '=', $request->color_id],
            ['size_value', '=', $request->size_value],
        ])->exists()) {
            return [
                'result' => false,
                'message' => 'Item already exists',
            ];
        }

        $item = new StorageItem;
        $item->product_id = $request->product_id;
        $item->color_id = $request->color_id;
        $item->size_value = $request->size_value;
        $item->save();
        if (empty($request->color_id)) {
            $color = "all colors";
        } else {
            $color = Color::find($item->color_id)->ename;
        }
        return [
            'result' => true,
            'message' => $request->product_id."-".
                $color.
                (empty($request->size_value)? " ": "-".$request->size_value).
                " added successfully.",
        ];
    }

    public function getItems(Request $request)
    {
        $items = $this->storage->getItems($request);
        return Datatables::of($items)
            ->add_column('isused', '<a href="#" class="btn btn-success" >Locations</a>')
            ->add_column('delete', '<input type="button" name="delete" value="delete" id="delete{{ $id }}" 
                class="btn btn-danger" onclick="del_item({{$id}})">')
            ->make(true);
    }

    public function delItem(Request $request)
    {
        $this->validate($request,[
            'id'=>'required',
        ]);
        try {
            $this->storage->delItem($request);
            return [
                'result' => true,
                'message' => "Item deleted successfully",
            ];
        } catch (StorageException $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function importProductData(Request $request)
    {
        try {
            $count = $this->storage->importProductData();
            return [
                'result' => true,
                'message' => $count . " items imported successfully",
            ];
        } catch (StorageException $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }

    }

    /**
     * show location-item storage page
     */
    public function locitemIndex(Request $request)
    {
        return view('storages.locitemmanage');

    }

    /**
     * return location search results
     */
    public function searchLocations(Request $request)
    {
        if (empty($request->q))
            return [""];
        $arr = explode('-',$request->q);
        $request->area = $arr[0];
        $request->line = isset($arr[1]) ? $arr[1] : "";
        $request->unit = isset($arr[2]) ? $arr[2] : "";
        $request->level = isset($arr[3]) ? $arr[3] : "";

        $locations = $this->storage->getLocations($request);
        if ($locations->count() > 100) {
            return [""];
        }
        $result = array();
        foreach($locations as $location) {
            $result[] = empty($location->level) ?
                    $location->area."-".$location->line."-".$location->unit.", ID=".$location->id :
                    $location->area."-".$location->line."-".$location->unit."-".$location->level.", ID=".$location->id;
        }

        return $result;

    }
    /**
     * return location search results
     */
    public function searchItems(Request $request)
    {
        if (empty($request->q))
            return [""];
        $request->products = $request->q;

        $items = $this->storage->getItems($request);
        if ($items->count() > 100) {
            return [""];
        }
        $result = array();
        foreach($items as $item) {
            $result[] =
                $item->product_id." ".$item->product_ename.
                (empty($item->color_ename) ? "" : "-" . $item->color_ename ) .
                (empty($item->size_value) ? "" : "-" . $item->size_value  ) .
                ", ID=" .$item->id;
        }
        return $result;
    }

    /**
     * add relation of location & item
     */
    public function addRelation(Request $request)
    {
        $this->validate($request,[
            'location'=>'required',
            'item'=>'required',
        ]);

        // get loc_id
        $locID = end((explode("ID=",$request->location)));
        $itemID = end((explode("ID=",$request->item)));
        if (empty($locID) || empty($itemID)) {
            return [
                'result' => false,
                'message' => 'location or item is wrong',
            ];
        }
        try {
            StorageItem::findOrFail($itemID);
            $location = StorageLocation::findOrFail($locID);
            $timestamp = Carbon::now();
            $location->items()->attach($itemID,[
                'comment' => $request->comment,
                'created_at' => $timestamp,
                'updated_at' => $timestamp]);

            return [
                'result' => true,
                'message' => "Relation added successfully.",
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    public function getRelations(Request $request)
    {
        $relations = $this->storage->getRelations($request);
        return Datatables::of($relations)
            ->addColumn('code', function ($relation) {
                return empty($relation->level) ?
                    $relation->area."-".$relation->line."-".$relation->unit :
                    $relation->area."-".$relation->line."-".$relation->unit."-".$relation->level;
            })
            ->add_column('delete', '<input type="button" name="delete" value="delete" id="delete{{ $id }}" 
                class="btn btn-danger"i onclick="del_relation({{$id}})">')
            ->make(true);
    }

    public function delRelation(Request $request)
    {
        $this->validate($request,[
            'id'=>'required',
        ]);
        try {
            $this->storage->delRelation($request);
            return [
                'result' => true,
                'message' => "Relation deleted successfully",
            ];
        } catch (Exception $e) {
            return [
                'result' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
