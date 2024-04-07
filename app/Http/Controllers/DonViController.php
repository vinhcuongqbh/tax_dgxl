<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DonViController extends Controller
{
    //Hiển thị danh sách Đơn vị
    public function index()
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::leftjoin('don_vi as dv2', 'dv2.ma_don_vi', 'don_vi.ma_don_vi_cap_tren')
                ->select('don_vi.id', 'don_vi.ma_don_vi', 'don_vi.ten_don_vi', 'dv2.ten_don_vi as ten_don_vi_cap_tren', 'don_vi.ma_trang_thai')
                ->get();

            return view('donvi.index', ['don_vi' => $don_vi]);
        } else {
            return view('404');
        }
    }


    //Tạo mới Đơn vị
    public function create()
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::where('ma_trang_thai', 1)->get();

            return view('donvi.create', ['don_vi' => $don_vi]);
        } else {
            return view('404');
        }
    }


    //Lưu trữ thông tin Đơn vị
    public function store(Request $request)
    {
        if (Auth::user()->isAdmin == 1) {
            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                'ma_don_vi' => 'required|unique:App\Models\DonVi,ma_don_vi',
                'ten_don_vi' => 'required',
            ]);

            $don_vi = new DonVi();
            $don_vi->ma_don_vi = $request->ma_don_vi;
            $don_vi->ten_don_vi = $request->ten_don_vi;
            $don_vi->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
            $don_vi->ma_trang_thai = 1;
            $don_vi->save();

            return redirect()->route('donvi.edit', ['id' => $request->ma_don_vi])->with('message', 'Đã tạo mới Đơn vị thành công');
        } else {
            return view('404');
        }
    }


    //Sửa thông tin Đơn vị
    public function edit($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::where('ma_don_vi', $id)->first();
            $dm_don_vi = DonVi::all();


            return view('donvi.edit', [
                'don_vi' => $don_vi,
                'dm_don_vi' => $dm_don_vi
            ]);
        } else {
            return view('404');
        }
    }


    //Cập nhật thông tin Đơn vị
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAdmin == 1) {
            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                //'ma_don_vi' => 'required|unique:App\Models\DonVi,ma_don_vi',
                'ten_don_vi' => 'required',
            ]);

            $don_vi = DonVi::where('ma_don_vi', $id)->first();
            $don_vi->ten_don_vi = $request->ten_don_vi;
            $don_vi->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
            $don_vi->save();
            return redirect()->route('donvi.edit', ['id' => $don_vi->ma_don_vi])->with('message', 'Đã cập nhật Đơn vị thành công');
        } else {
            return view('404');
        }
    }


    //Khóa Đơn vị
    public function destroy($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::where('ma_don_vi', $id)->first();
            $don_vi->ma_trang_thai = 0;
            $don_vi->save();

            return back()->with('message', 'Đã khóa Đơn vị');
        } else {
            return view('404');
        }
    }


    //Mở khóa Đơn vị
    public function restore($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::where('ma_don_vi', $id)->first();
            $don_vi->ma_trang_thai = 1;
            $don_vi->save();

            return back()->with('message', 'Đã mở khóa Đơn vị');
        } else {
            return view('404');
        }
    }
}
