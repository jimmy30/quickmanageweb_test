<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
//use App\Http\Requests;
use Illuminate\Http\Request;
//use App\Http\Requests\Request;
use Illuminate\Support\Facades\View;
use Validator;

use Session;

use App\Ad;
use App\Helpers\ApplicationHelpers;

use Illuminate\Support\Facades\Input;
use App\Http\Requests\AdsAddRequest;
use App\Http\Requests\AdsEditRequest;
use DB;


class AdsController extends Controller
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
        return view('ads.manage');
    }

    public function getAjaxAds(Request $request)
    {
	$aColumns = array
        (
            "ads.id",
            "ads.title",
            "ads.start_time",
            "ads.end_time",
        );

                
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "ads";
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
        
        $total_ads = Ad::count();

        $ads=Ad::where(function($query)  use ($aColumns, $request)
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
//	print_r($ads);
//      exit;
	/*
	 * Output
	 */

	$output = array(
		"sEcho" => intval($request->sEcho),
		"iTotalRecords" => count($ads),
		"iTotalDisplayRecords" => $total_ads,
		"aaData" => array()
	);
        
	foreach ($ads as $ads)
	{
            $row = array();
            foreach($aColumns as $aColumn)
            {
                $aColumn = explode('.',$aColumn)[1];
                
                if ( $aColumn == "version" )
                    $row[] = ($ads->{$aColumn}=="0") ? '-' : $ads->{$aColumn};
                else if ( $aColumn != ' ' )
                {
                    $row[] = $ads->{$aColumn};
                }
                
            }

            $row[]='<a href="'.route('edit-ad',['id' => $ads->id]).'" title="Edit"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="confirmDelete(\''.route('delete-ad',['id' => $ads->id]).'\',\'Are you sure, you want to delete?\')" title="Delete"><i class="fa fa-trash"></i></a>';
            $output['aaData'][] = $row;
	}
        
	
	echo json_encode( $output );
        
    }
    
    public function add()
    {
        return view('ads.add');
    }
    
    public function postAdd(AdsAddRequest $request)
    {
            
        $data = array();
        $data = $request->all();

        $ads_data["title"] = $data["title"];
        $ads_data["url"] = $data["url"];
        $ads_data["start_time"] = date('Y-m-d h;i:s',strtotime($data["start_time"]));
        $ads_data["end_time"] = date('Y-m-d h;i:s',strtotime($data["end_time"]));
        $ads_data["is_active"] = $data["is_active"];

        $ads=Ad::create($ads_data);

        if ($ads) {

            if($data["image"]!="")
            {
                list($type, $data["image"]) = explode(';', $data["image"]);
                list(,$extenstion) = explode('/',$type);
                list(,$data["image"])      = explode(',', $data["image"]);

                $image_string = $data["image"];
                $image_name=ApplicationHelpers::uploadMedia($image_string, $extenstion, $ads->id);
                Ad::where('id',$ads->id)->update(['image'=>$image_name]);
            }

            return redirect('add-ad')->with('success', 'Ad created successfully.');
        } else {
            return redirect('add-ad')->with('fail', 'Sorry, Ad not created.');
        }

    }
    
    public function edit(Request $request)
    {
        $ad = Ad::where('id',$request->id)->first();
        $ad->start_time = date('m/d/Y', strtotime($ad->start_time));
        $ad->end_time = date('m/d/Y', strtotime($ad->end_time));
       
        return view('ads.edit')
                ->with("ad", $ad);
    }
    
    public function postEdit(AdsEditRequest $request)
    {
        // Create a new validator instance.
        $id =  $request->id;

        $data = array();
        $data = $request->all();

        $ads_data["title"] = $data["title"];
        $ads_data["url"] = $data["url"];
        $ads_data["start_time"] = date('Y-m-d h;i:s',strtotime($data["start_time"]));
        $ads_data["end_time"] = date('Y-m-d h;i:s',strtotime($data["end_time"]));
        $ads_data["is_active"] = $data["is_active"];
        
        Ad::where('id',$id)->update($ads_data);
        $ads = Ad::find($id);

        if(Input::has('image') && Input::get('image')!="")
        {
            $gallery_image = Input::get('image');

            list($type, $imageData) = explode(';', $gallery_image);
            list(,$extenstion) = explode('/',$type);
            list(,$gallery_image)      = explode(',', $gallery_image);

            $image_string = $gallery_image;
            $image_name = ApplicationHelpers::uploadMedia($image_string, $extenstion, $id, $ads->image);
            Ad::where('id',$id)->update(['image'=>$image_name]);

        }

        return redirect()->route('edit-ad', [$id])->with('success', 'Ad updated successfully.');

    }


    public function delete(Request $request)
    {
        $id = $request->id;
        $ads = Ad::find($id);
        ApplicationHelpers::deleteMedia($ads->image);
        
        $ads = Ad::where('id',$id)->delete();
        
        if ($ads) {
            return redirect('ads')->with('success', 'Ad deleted successfully.');
        } else {
            return redirect('ads')->with('fail', 'Sorry, Ad not found.');
        }
    }


}
