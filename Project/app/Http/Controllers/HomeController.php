<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
session_start();
class HomeController extends Controller
{
    public function index()
    {
        $cate_product = DB::table("tbl_category_product")->where('category_status','0') -> orderBy('category_id','desc') -> get();
        $brand_product = DB::table('tbl_brand')->where('brand_status','1') -> orderBy('brand_id','desc') -> get();
        // ->join('tbl_category_product','tbl_category_product.category_id','=','tbl_product.category_id')
        // ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
        // ->orderBy('tbl_product.product_id','desc') -> get();
        $all_product = DB::table('tbl_product')->where('product_status','1')->orderby('product_id','desc')->get();


        return view('pages.home')->with('category',$cate_product)->with('brand',$brand_product)->with('all_product', $all_product);
    }
}
