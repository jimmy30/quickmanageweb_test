<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
//use App\Http\Requests;
use Illuminate\Http\Request;
//use App\Http\Requests\Request;
use Illuminate\Support\Facades\View;
use Validator;

use Session;

use App\Car;
use App\Helpers\ApplicationHelpers;

use Illuminate\Support\Facades\Input;
use App\Http\Requests\CarAddRequest;
use App\Http\Requests\CarEditRequest;
use DB;


class CarController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('auth');
    }
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    
    public function manage()
    {
        return view('car.manage');
    }

    public function getAjaxCars(Request $request)
    {
	$aColumns = array
        (
            "cars.id",
            "cars.name",
        );

                
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "cars";
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
        
        $total_cars = Car::count();

        $cars=Car::where(function($query)  use ($aColumns, $request)
                    {
                        if ( $request->sSearch != "" )
                        {
                            foreach ($aColumns as $aColumn)
                            {
                                $query->orWhere($aColumn, 'like', '%'.$request->sSearch.'%');
                            }
                        }
                    })
            ->orderBy($aColumns[ intval( $request->iSortCol_0 ) ] , $request->sSortDir_0)
            ->take($request->iDisplayLength)->skip($request->iDisplayStart)
            ->get();


//      echo "<pre>";
//	print_r($cars);
//      exit;
	/*
	 * Output
	 */

	$output = array(
		"sEcho" => intval($request->sEcho),
		"iTotalRecords" => count($cars),
		"iTotalDisplayRecords" => $total_cars,
		"aaData" => array()
	);
        
	foreach ($cars as $car)
	{
            $row = array();
            foreach($aColumns as $aColumn)
            {
                $aColumn = explode('.',$aColumn)[1];
                
                if ( $aColumn == "version" )
                    $row[] = ($car->{$aColumn}=="0") ? '-' : $car->{$aColumn};
                else if ( $aColumn != ' ' )
                {
                    $row[] = $car->{$aColumn};
                }
                
            }

            $row[]='<a href="'.route('edit-car',['id' => $car->id]).'" title="Edit"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="confirmDelete(\''.route('delete-car',['id' => $car->id]).'\',\'Are you sure, you want to delete?\')" title="Delete"><i class="fa fa-trash"></i></a>';
            $output['aaData'][] = $row;
	}
        
	
	echo json_encode( $output );
        
    }
    
    public function add()
    {
        return view('car.add');
    }
    
    public function postAdd(CarAddRequest $request)
    {
            
        $data = array();
        $data = $request->all();

        $car_data["name"] = $data["name"];
        $car_data["description"] = $data["description"];
        $car_data["is_active"] = $data["is_active"];

        $car=Car::create($car_data);

        if ($car) {

            if($data["image"]!="")
            {
                list($type, $data["image"]) = explode(';', $data["image"]);
                list(,$extenstion) = explode('/',$type);
                list(,$data["image"])      = explode(',', $data["image"]);

                $image_string = $data["image"];
                $image_name=ApplicationHelpers::uploadMedia($image_string, $extenstion, $car->id);
                Car::where('id',$car->id)->update(['image'=>$image_name]);
            }

            return redirect('add-car')->with('success', 'Car created successfully.');
        } else {
            return redirect('add-car')->with('fail', 'Sorry, Car not created.');
        }

    }
    
    public function edit(Request $request)
    {
        $car = Car::where('id',$request->id)->first();
       
        return view('car.edit')
                ->with("car", $car);
    }
    
    public function postEdit(CarEditRequest $request)
    {
        // Create a new validator instance.
        $id =  $request->id;

        $data = array();
        $data = $request->all();

        $car_data["name"] = $data["name"];
        $car_data["description"] = $data["description"];
        $car_data["is_active"] = $data["is_active"];
        
        Car::where('id',$id)->update($car_data);
        $car = Car::find($id);

        if(Input::has('image') && Input::get('image')!="")
        {
            $gallery_image = Input::get('image');

            list($type, $imageData) = explode(';', $gallery_image);
            list(,$extenstion) = explode('/',$type);
            list(,$gallery_image)      = explode(',', $gallery_image);

            $image_string = $gallery_image;
            $image_name = ApplicationHelpers::uploadMedia($image_string, $extenstion, $id, $car->image);
            Car::where('id',$id)->update(['image'=>$image_name]);

        }

        return redirect()->route('edit-car', [$id])->with('success', 'Car updated successfully.');

    }


    public function delete(Request $request)
    {
        $id = $request->id;
        $car = Car::find($id);
        ApplicationHelpers::deleteMedia($car->image);
        
        $car = Car::where('id',$id)->delete();
        
        if ($car) {
            return redirect('cars')->with('success', 'Car deleted successfully.');
        } else {
            return redirect('cars')->with('fail', 'Sorry, Car not found.');
        }
    }


}
