<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\Phong;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PhongController extends Controller
{
    //Hiển thị danh sách Phòng/Đội
    public function index()
    {
        if (Auth::user()->isAdmin == 1) {
            $phong = Phong::leftjoin('don_vi', 'don_vi.ma_don_vi', 'phong.ma_don_vi_cap_tren')
                ->select('phong.id', 'phong.ma_phong', 'phong.ten_phong', 'don_vi.ten_don_vi', 'phong.ma_trang_thai')
                ->get();

            return view('phong.index', ['phong' => $phong]);
        } else {
            return view('404');
        }
    }


    //Tạo mới Phòng/Đội
    public function create()
    {
        if (Auth::user()->isAdmin == 1) {
            $don_vi = DonVi::where('ma_trang_thai', 1)->get();

            return view('phong.create', ['don_vi' => $don_vi]);
        } else {
            return view('404');
        }
    }


    //Lưu trữ thông tin Phòng/Đội
    public function store(Request $request)
    {
        if (Auth::user()->isAdmin == 1) {
            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                'ma_phong' => 'required|unique:App\Models\Phong,ma_phong',
                'ten_phong' => 'required',
            ]);

            $phong = new Phong();
            $phong->ma_phong = $request->ma_phong;
            $phong->ten_phong = $request->ten_phong;
            $phong->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
            $phong->ma_trang_thai = 1;
            $phong->save();

            return redirect()->route(
                'phong.edit',
                ['id' => $request->ma_phong]
            )->with('message', 'Đã tạo mới Phòng/Đội thành công');
        } else {
            return view('404');
        }
    }


    //Sửa thông tin Phòng/Đội
    public function edit($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $phong = Phong::where('ma_phong', $id)->first();
            $don_vi = DonVi::all();


            return view('phong.edit', [
                'phong' => $phong,
                'don_vi' => $don_vi
            ]);
        } else {
            return view('404');
        }
    }


    //Cập nhật thông tin Phòng/Đội
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAdmin == 1) {
            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                //'ma_phong' => 'required|unique:App\Models\Phong,ma_phong',
                'ten_phong' => 'required',
            ]);

            $phong = Phong::where('ma_phong', $id)->first();
            $phong->ten_phong = $request->ten_phong;
            $phong->ma_don_vi_cap_tren = $request->ma_don_vi_cap_tren;
            $phong->save();
            return redirect()->route('phong.edit', ['id' => $phong->ma_phong])->with('message', 'Đã cập nhật Phòng/Đội thành công');
        } else {
            return view('404');
        }
    }


    //Khóa Phòng/Đội
    public function destroy($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $phong = Phong::where('ma_phong', $id)->first();
            $phong->ma_trang_thai = 0;
            $phong->save();

            return back()->with('message', 'Đã khóa Phòng/Đội');
        } else {
            return view('404');
        }
    }


    //Mở khóa Phòng/Đội
    public function restore($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $phong = Phong::where('ma_phong', $id)->first();
            $phong->ma_trang_thai = 1;
            $phong->save();

            return back()->with('message', 'Đã mở khóa Phòng/Đội');
        } else {
            return view('404');
        }
    }


    //Lấy danh sách Phòng/Đội dựa trên Đơn vị
    public function dmPhong(Request $request)
    {
        $data['phong'] = Phong::where('ma_don_vi_cap_tren', $request->ma_don_vi)
            ->get(['ma_phong', 'ten_phong']);

        return response()->json($data);
    }
}
