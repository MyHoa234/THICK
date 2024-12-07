<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
session_start();
class ProductController extends Controller
{
    public function AuthLogin()
    {
        $admin_id = Session::get('admin_id');
        if($admin_id)
        {
            return Redirect::to('dashboard');
        }else{
            return Redirect::to('admin')->send();
        }
    }
    public function add_product()
    {
        $this->AuthLogin();
        $cate_product = DB::table("tbl_category_product") -> orderBy('category_id','desc') -> get();
        $brand_product = DB::table('tbl_brand') -> orderBy('brand_id','desc') -> get();
       
        return view('admin.add_product')->with('cate_product', $cate_product) -> with('brand_product', $brand_product);
    }

    public function all_product()
    {
        $this->AuthLogin();
        $all_product = DB::table('tbl_product')
        ->join('tbl_category_product','tbl_category_product.category_id','=','tbl_product.category_id')
        ->join('tbl_brand','tbl_brand.brand_id','=','tbl_product.brand_id')
        ->orderBy('tbl_product.product_id','desc') -> get();
        $manager_product = view('admin.all_product')->with('all_product',$all_product);

        return view('admin_layout')->with('admin.all_product',$manager_product);
    }

    public function save_product(Request $request)
    {
        $this->AuthLogin();
    $data = array();
    $data['product_name'] = $request->product_name;
    $data['product_price'] = $request->product_price;
    $data['product_desc'] = $request->product_desc;
    $data['product_content'] = $request->product_content;
    $data['product_status'] = $request->product_status;
    $data['category_id'] = $request->product_cate;
    $data['brand_id'] = $request->product_brand;
    $get_image = $request->file('product_image');
    if($get_image){
        $get_name_image = $get_image->getClientOriginalName();
        $name_image = current(explode('.',$get_name_image)); //lấy tên hình ảnh phân tách bởi dấu chấm 
        $new_image = $name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();//upload tên file không bị trùng và nối với đuôi mở rộng
        $get_image->move('uploads/product', $new_image);
        $data['product_image'] = $new_image;
        DB::table('tbl_product')->insert($data);
        Session::put('message', 'Thêm sản phẩm thành công');
        return Redirect::to('add-product');
    }
    $data['product_image'] = '';
    DB::table('tbl_product')->insert($data);

    Session::put('message', 'Thêm sản phẩm thành công');
    return Redirect::to('all-product');
    }

public function unactive_product($product_id)
{
    $this->AuthLogin();
    DB::table('tbl_product')
        ->where('product_id', $product_id)
        ->update(['product_status' => 0]); // Cập nhật thành array

    Session::put('message', 'Không kích hoạt sản phẩm thành công');
    return Redirect::to('all-product');
}

public function active_product($product_id)
{
    $this->AuthLogin();
    DB::table('tbl_product')
        ->where('product_id', $product_id)
        ->update(['product_status' => 1]); // Cập nhật thành array

    Session::put('message', 'Kích hoạt sản phẩm thành công');
    return Redirect::to('all-product');
}

public function edit_product($product_id)
{
    $this->AuthLogin();
    $cate_product = DB::table("tbl_category_product") -> orderBy('category_id','desc') -> get();
    $brand_product = DB::table('tbl_brand') -> orderBy('brand_id','desc') -> get();

    $edit_product = DB::table('tbl_product')->where('product_id', $product_id)->get();
    $manager_product = view('admin.edit_product')
    ->with('edit_product',$edit_product)
    ->with('cate_product', $cate_product)
    ->with('brand_product',$brand_product);
    return view('admin_layout')->with('admin.edit_product', $manager_product);
}
public function update_product(Request $request, $product_id)
{
    $this->AuthLogin();
    $data = array();
    $data['product_name'] = $request->product_name;
    $data['product_price'] = $request->product_price;
    $data['product_desc'] = $request->product_desc;
    $data['product_content'] = $request->product_content;
    $data['product_status'] = $request->product_status;
    $data['category_id'] = $request->product_cate;
    $data['brand_id'] = $request->product_brand;
    $get_image = $request->file('product_image');
    if($get_image){
        $get_name_image = $get_image->getClientOriginalName();
        $name_image = current(explode('.',$get_name_image)); //lấy tên hình ảnh phân tách bởi dấu chấm 
        $new_image = $name_image.rand(0,99).'.'.$get_image->getClientOriginalExtension();//upload tên file không bị trùng và nối với đuôi mở rộng
        $get_image->move('uploads/product', $new_image);
        $data['product_image'] = $new_image;
        DB::table('tbl_product')->where('product_id',$product_id)->update($data);
        Session::put('message', 'Cập nhật sản phẩm thành công');
        return Redirect::to('add-product');
    }

    DB::table('tbl_product')->where('product_id',$product_id)->update($data);

    Session::put('message', 'Cập nhật sản phẩm sản phẩm thành công');
    return Redirect::to('all-product');
    }

public function delete_product($product_id)
{
    $this->AuthLogin();
    // Kiểm tra xem có tồn tại trước khi xóa
    $product = DB::table('tbl_product')->where('product_id', $product_id)->first();

    if (!$product) {
        Session::put('message', 'Sản phẩm không tồn tại');
        return Redirect::to('all-product');
    }

    // Xóa thương hiệu
    DB::table('tbl_product')->where('product_id', $product_id)->delete();

    // Thông báo xóa thành công
    Session::put('message', 'Xóa sản phẩm thành công');
    return Redirect::to('all-product');
}

//
public function details_product($product_id)
{
    $cate_product = DB::table("tbl_category_product")
    ->where('category_status', '0')
    ->orderBy('category_id', 'desc')
    ->get();

// Lấy danh sách thương hiệu với trạng thái hoạt động
$brand_product = DB::table('tbl_brand')
    ->where('brand_status', '1')
    ->orderBy('brand_id', 'desc')
    ->get();
    return view('pages.sanpham.show_details',[
        'category' => $cate_product,
        'brand' => $brand_product,
    ]);
}
}
