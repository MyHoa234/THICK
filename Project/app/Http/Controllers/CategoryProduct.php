<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;
session_start();
 



class CategoryProduct extends Controller
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
    public function add_category_product()
    {
        $this->AuthLogin();
        return view('admin.add_category_product');
    }

    public function all_category_product()
    {
        $this->AuthLogin();
        $all_category_product = DB::table('tbl_category_product')->get();
        $manager_category_product = view('admin.all_category_product')->with('all_category_product',$all_category_product);

        return view('admin_layout')->with('admin.all_category_product',$manager_category_product);
    }

    public function save_category_product(Request $request)
    {
        $this->AuthLogin();
        $data = array();
        $data['category_name'] = $request->category_product_name;
        $data['category_desc'] = $request->category_product_desc;
        $data['category_status'] = $request->category_product_status;
        DB::table('tbl_category_product')->insert($data);
        Session::put('message','Thêm thành công');
        return Redirect::to ('add-category-product');
    }
    public function unactive_category_product($category_product_id)
{
    $this->AuthLogin();
    DB::table('tbl_category_product')
        ->where('category_id', $category_product_id)
        ->update(['category_status' => 0]); // Cập nhật thành array

    Session::put('message', 'Không kích hoạt danh mục sản phẩm thành công');
    return Redirect::to('all-category-product');
}

public function active_category_product($category_product_id)
{
    $this->AuthLogin();
    DB::table('tbl_category_product')
        ->where('category_id', $category_product_id)
        ->update(['category_status' => 1]); // Cập nhật thành array

    Session::put('message', 'Kích hoạt danh mục sản phẩm thành công');
    return Redirect::to('all-category-product');
}

public function edit_category_product($category_product_id)
{
    $this->AuthLogin();
    $edit_category_product = DB::table('tbl_category_product')->where('category_id', $category_product_id)->get();

    if (!$edit_category_product) {
        return redirect('all-category-product')->with('message', 'Danh mục không tồn tại!');
    }

    return view('admin.edit_category_product')->with('edit_category_product', $edit_category_product);
}
public function update_category_product(Request $request, $category_product_id)
{
    $this->AuthLogin();
    $data = array();
    $data['category_name'] = $request->category_product_name;
    $data['category_desc'] = $request->category_product_desc;
    DB::table('tbl_category_product')->where('category_id',$category_product_id)->update($data);
    Session::put('message', 'Cập nhật danh mục sản phẩm thành công');
   
    return Redirect::to('all-category-product');

}
public function delete_category_product($category_product_id)
{
    $this->AuthLogin();
    // Kiểm tra xem danh mục có tồn tại trước khi xóa
    $category = DB::table('tbl_category_product')->where('category_id', $category_product_id)->first();

    if (!$category) {
        Session::put('message', 'Danh mục sản phẩm không tồn tại');
        return Redirect::to('all-category-product');
    }

    // Xóa danh mục
    DB::table('tbl_category_product')->where('category_id', $category_product_id)->delete();

    // Thông báo xóa thành công
    Session::put('message', 'Xóa danh mục sản phẩm thành công');
    return Redirect::to('all-category-product');
}

    //End Function Admin Page
    public function show_category_home($category_id)
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

    // Lấy danh sách sản phẩm theo danh mục được chọn
    $category_by_id = DB::table('tbl_product')
        ->join('tbl_category_product', 'tbl_product.category_id', '=', 'tbl_category_product.category_id')
        ->where('tbl_product.category_id', $category_id)
        ->get();

    $category_name = DB::table('tbl_category_product')->where('tbl_category_product.category_id',$category_id)->limit(1)->get();
    // Trả về view với dữ liệu
    return view('pages.category.show_category', [
        'category' => $cate_product,
        'brand' => $brand_product,
        'category_by_id' => $category_by_id,
        'category_name'=>$category_name,
    ]);
}
    

}
