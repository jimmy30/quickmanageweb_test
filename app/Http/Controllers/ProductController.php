<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
//use App\Http\Requests;
use Illuminate\Http\Request;
//use App\Http\Requests\Request;
use Illuminate\Support\Facades\View;
use Validator;

use Session;

use App\Product;
use App\Helpers\ApplicationHelpers;

use Illuminate\Support\Facades\Input;
use App\Http\Requests\ProductAddRequest;
use App\Http\Requests\ProductEditRequest;
use DB;


class ProductController extends Controller
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
        return view('product.manage');
    }

    public function getAjaxProducts(Request $request)
    {
	$aColumns = array
        (
            "products.id",
            "products.name",
            "products.price",
        );

                
	/* Indexed column (used for fast and accurate table cardinality) */
	$sIndexColumn = "id";
	
	/* DB table to use */
	$sTable = "products";
	
	
	/* 
	 * Filtering
	 * NOTE this does not match the built-in DataTables filtering which does it
	 * word by word on any field. It's possible to do here, but concerned about efficiency
	 * on very large tables, and MySQL's regex functionality is very limited
	 */
        
        $total_products = Product::count();

        $products=Product::where(function($query)  use ($aColumns, $request)
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
//	print_r($products);
//      exit;
	/*
	 * Output
	 */

	$output = array(
		"sEcho" => intval($request->sEcho),
		"iTotalRecords" => count($products),
		"iTotalDisplayRecords" => $total_products,
		"aaData" => array()
	);
        
	foreach ($products as $product)
	{
            $row = array();
            foreach($aColumns as $aColumn)
            {
                $aColumn = explode('.',$aColumn)[1];
                
                if ( $aColumn == "version" )
                    $row[] = ($product->{$aColumn}=="0") ? '-' : $product->{$aColumn};
                else if ( $aColumn != ' ' )
                {
                    $row[] = $product->{$aColumn};
                }
                
            }

            $row[]='<a href="'.route('edit-product',['id' => $product->id]).'" title="Edit"><i class="fa fa-edit"></i></a> &nbsp; <a href="#" onclick="confirmDelete(\''.route('delete-product',['id' => $product->id]).'\',\'Are you sure, you want to delete?\')" title="Delete"><i class="fa fa-trash"></i></a>';
            $output['aaData'][] = $row;
	}
        
	
	echo json_encode( $output );
        
    }
    
    public function add()
    {
        return view('product.add');
    }
    
    public function postAdd(ProductAddRequest $request)
    {
            
        $data = array();
        $data = $request->all();

        $product_data["name"] = $data["name"];
        $product_data["price"] = $data["price"];
        $product_data["is_active"] = $data["is_active"];

        $product=Product::create($product_data);

        if ($product) {

            if($data["image"]!="")
            {
                list($type, $data["image"]) = explode(';', $data["image"]);
                list(,$extenstion) = explode('/',$type);
                list(,$data["image"])      = explode(',', $data["image"]);

                $image_string = $data["image"];
                $image_name=ApplicationHelpers::uploadMedia($image_string, $extenstion, $product->id);
                Product::where('id',$product->id)->update(['image'=>$image_name]);
            }

            return redirect('add-product')->with('success', 'Product created successfully.');
        } else {
            return redirect('add-product')->with('fail', 'Sorry, Product not created.');
        }

    }
    
    public function edit(Request $request)
    {
        $product = Product::where('id',$request->id)->first();
       
        return view('product.edit')
                ->with("product", $product);
    }
    
    public function postEdit(ProductEditRequest $request)
    {
        // Create a new validator instance.
        $id =  $request->id;

        $data = array();
        $data = $request->all();

        $product_data["name"] = $data["name"];
        $product_data["price"] = $data["price"];
        $product_data["is_active"] = $data["is_active"];
        
        Product::where('id',$id)->update($product_data);
        $product = Product::find($id);

        if(Input::has('image') && Input::get('image')!="")
        {
            $gallery_image = Input::get('image');

            list($type, $imageData) = explode(';', $gallery_image);
            list(,$extenstion) = explode('/',$type);
            list(,$gallery_image)      = explode(',', $gallery_image);

            $image_string = $gallery_image;
            $image_name = ApplicationHelpers::uploadMedia($image_string, $extenstion, $id, $product->image);
            Product::where('id',$id)->update(['image'=>$image_name]);

        }

        return redirect()->route('edit-product', [$id])->with('success', 'Product updated successfully.');

    }


    public function delete(Request $request)
    {
        $id = $request->id;
        $product = Product::find($id);
        ApplicationHelpers::deleteMedia($product->image);
        
        $product = Product::where('id',$id)->delete();
        
        if ($product) {
            return redirect('products')->with('success', 'Product deleted successfully.');
        } else {
            return redirect('products')->with('fail', 'Sorry, Product not found.');
        }
    }


}
