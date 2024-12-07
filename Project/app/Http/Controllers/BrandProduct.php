<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
session_start();
class BrandProduct extends Controller
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
    public function add_brand_product()
    {
        $this->AuthLogin();
        return view('admin.add_brand_product');
    }

    public function all_brand_product()
    {
        $this->AuthLogin();
        $all_brand_product = DB::table('tbl_brand')->get();
        $manager_brand_product = view('admin.all_brand_product')->with('all_brand_product',$all_brand_product);

        return view('admin_layout')->with('admin.all_brand_product',$manager_brand_product);
    }

    public function save_brand_product(Request $request)
{
    $this->AuthLogin();
    // Kiểm tra dữ liệu đầu vào
    $request->validate([
        'brand_product_name' => 'required|max:191', // Tên thương hiệu bắt buộc
        'brand_product_desc' => 'required', // Mô tả thương hiệu bắt buộc
        'brand_product_status' => 'required|integer', // Trạng thái thương hiệu bắt buộc
    ], [
        'brand_product_name.required' => 'Tên thương hiệu không được để trống',
        'brand_product_desc.required' => 'Mô tả thương hiệu không được để trống',
        'brand_product_status.required' => 'Trạng thái thương hiệu không được để trống',
    ]);

    // Nếu validation thành công, lưu dữ liệu
    $data = [
        'brand_name' => $request->brand_product_name,
        'brand_desc' => $request->brand_product_desc,
        'brand_status' => $request->brand_product_status,
    ];

    DB::table('tbl_brand')->insert($data);

    Session::put('message', 'Thêm thương hiệu sản phẩm thành công');
    return Redirect::to('add-brand-product');
}

public function unactive_brand_product($brand_product_id)
{
    $this->AuthLogin();
    DB::table('tbl_brand')
        ->where('brand_id', $brand_product_id)
        ->update(['brand_status' => 0]); // Cập nhật thành array

    Session::put('message', 'Không kích hoạt thương hiệu sản phẩm thành công');
    return Redirect::to('all-brand-product');
}

public function active_brand_product($brand_product_id)
{
    $this->AuthLogin();
    DB::table('tbl_brand')
        ->where('brand_id', $brand_product_id)
        ->update(['brand_status' => 1]); // Cập nhật thành array

    Session::put('message', 'Kích hoạt thương hiệu sản phẩm thành công');
    return Redirect::to('all-brand-product');
}

public function edit_brand_product($brand_product_id)
{
    $this->AuthLogin();
    $edit_brand_product = DB::table('tbl_brand')->where('brand_id', $brand_product_id)->get();

    if (!$edit_brand_product) {
        return redirect('all-brand-product')->with('message', 'Thương hiệu không tồn tại!');
    }

    return view('admin.edit_brand_product')->with('edit_brand_product', $edit_brand_product);
}
public function update_brand_product(Request $request, $brand_product_id)
{
    $this->AuthLogin();
    $data = array();
    $data['brand_name'] = $request->brand_product_name;
    $data['brand_desc'] = $request->brand_product_desc;
    DB::table('tbl_brand')->where('brand_id',$brand_product_id)->update($data);
    Session::put('message', 'Cập nhật thương hiệu sản phẩm thành công');
   
    return Redirect::to('all-brand-product');

}
public function delete_brand_product($brand_product_id)
{
    $this->AuthLogin();
    // Kiểm tra xem thương hiệu có tồn tại trước khi xóa
    $brand = DB::table('tbl_brand')->where('brand_id', $brand_product_id)->first();

    if (!$brand) {
        Session::put('message', 'thương hiệu sản phẩm không tồn tại');
        return Redirect::to('all-brand-product');
    }

    // Xóa thương hiệu
    DB::table('tbl_brand')->where('brand_id', $brand_product_id)->delete();

    // Thông báo xóa thành công
    Session::put('message', 'Xóa thương hiệu sản phẩm thành công');
    return Redirect::to('all-brand-product');
}
//End Function Admin Page
public function show_brand_home($brand_id)
{
    // Lấy danh sách danh mục sản phẩm với trạng thái hoạt động
    $cate_product = DB::table("tbl_category_product")
        ->where('category_status', '0')
        ->orderBy('category_id', 'desc')
        ->get();

    // Lấy danh sách thương hiệu với trạng thái hoạt động
    $brand_product = DB::table('tbl_brand')
        ->where('brand_status', '1')
        ->orderBy('brand_id', 'desc')
        ->get();

    // Lấy danh sách sản phẩm theo thương hiệu được chọn
    $brand_by_id = DB::table('tbl_product')
        ->join('tbl_brand', 'tbl_product.brand_id', '=', 'tbl_brand.brand_id')
        ->where('tbl_product.brand_id', $brand_id)
        ->get();
        $brand_name = DB::table('tbl_brand')->where('tbl_brand.brand_id',$brand_id)->limit(1)->get();

    // Trả về view với dữ liệu
    return view('pages.brand.show_brand', [
        'category' => $cate_product,
        'brand' => $brand_product,
        'brand_by_id' => $brand_by_id,
        'brand_name'=>$brand_name,

    ]);
}

}
