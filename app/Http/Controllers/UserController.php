<?php

namespace App\Http\Controllers;

use App\Models\ChucVu;
use App\Models\DonVi;
use App\Models\GioiTinh;
use App\Models\Ngach;
use App\Models\Phong;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //Hiển thị danh sách User
    public function index()
    {
        if (Auth::user()->isAdmin == 1) {
            $users = User::leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select(
                    'users.so_hieu_cong_chuc',
                    'users.name',
                    'users.ngay_sinh',
                    'chuc_vu.ten_chuc_vu',
                    'phong.ten_phong',
                    'don_vi.ten_don_vi',
                    'users.name',
                    'users.ma_trang_thai'
                )
                ->get();

            return view('congchuc.index', ['cong_chuc' => $users]);
        } else {
            return view('404');
        }
    }


    //Tạo mới User
    public function create()
    {
        if (Auth::user()->isAdmin == 1) {
            $gioi_tinh = GioiTinh::all();
            $ngach = Ngach::where('ma_trang_thai', 1)->get();
            $chuc_vu = ChucVu::where('ma_trang_thai', 1)->get();
            $don_vi = DonVi::where('ma_trang_thai', 1)->get();

            return view('congchuc.create', [
                'gioi_tinh' => $gioi_tinh,
                'ngach' => $ngach,
                'chuc_vu' => $chuc_vu,
                'don_vi' => $don_vi,
            ]);
        } else {
            return view('404');
        }
    }


    //Lưu trữ thông tin User
    public function store(Request $request)
    {
        if (Auth::user()->isAdmin == 1) {
            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                'so_hieu_cong_chuc' => 'required|unique:App\Models\User,so_hieu_cong_chuc',
                'name' => 'required',
                'ngay_sinh' => 'required',
                'gioi_tinh' => 'required',
                'don_vi' => 'required',
                'phong' => 'required'
            ]);

            $user = new User();
            $user->so_hieu_cong_chuc = $request->so_hieu_cong_chuc;
            $user->name = $request->name;
            $user->ngay_sinh = $request->ngay_sinh;
            $user->ma_gioi_tinh = $request->gioi_tinh;
            $user->ma_ngach = $request->ngach;
            $user->ma_don_vi = $request->don_vi;
            $user->ma_phong = $request->phong;
            $user->email = $request->email;
            $user->password = Hash::make('123456');
            $user->ma_trang_thai = 1;
            $user->save();

            return redirect()->route('congchuc.edit', ['id' => $request->so_hieu_cong_chuc])->with('message', 'Đã tạo mới Người dùng thành công');
        } else {
            return view('404');
        }
    }


    //Xem thông tin User
    public function show($id)
    {
        if ((Auth::user()->isAdmin == 1) || (Auth::user()->so_hieu_cong_chuc == $id)) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $gioi_tinh = GioiTinh::all();
            $ngach = Ngach::where('ma_trang_thai', 1)->get();
            $chuc_vu = ChucVu::where('ma_trang_thai', 1)->get();
            $don_vi = DonVi::where('ma_trang_thai', 1)->get();
            $phong = Phong::where('ma_trang_thai', 1)
                ->where('ma_don_vi_cap_tren', $user->ma_don_vi)
                ->get();

            return view('congchuc.show', [
                'cong_chuc' => $user,
                'gioi_tinh' => $gioi_tinh,
                'ngach' => $ngach,
                'chuc_vu' => $chuc_vu,
                'don_vi' => $don_vi,
                'phong' => $phong
            ]);
        } else {
            return view('404');
        }
    }


    //Sửa thông tin User
    public function edit($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $gioi_tinh = GioiTinh::all();
            $ngach = Ngach::where('ma_trang_thai', 1)->get();
            $chuc_vu = ChucVu::where('ma_trang_thai', 1)->get();
            $don_vi = DonVi::where('ma_trang_thai', 1)->get();
            $phong = Phong::where('ma_trang_thai', 1)
                ->where('ma_don_vi_cap_tren', $user->ma_don_vi)
                ->get();

            return view('congchuc.edit', [
                'cong_chuc' => $user,
                'gioi_tinh' => $gioi_tinh,
                'ngach' => $ngach,
                'chuc_vu' => $chuc_vu,
                'don_vi' => $don_vi,
                'phong' => $phong
            ]);
        } else {
            return view('404');
        }
    }


    //Cập nhật thông tin User
    public function update(Request $request, $id)
    {
        if (Auth::user()->isAdmin == 1) {

            //Kiểm tra thông tin đầu vào
            $validated = $request->validate([
                //'ma_user' => 'required|unique:App\Models\User,ma_user',
                'name' => 'required',
                'ngay_sinh' => 'required',
                'gioi_tinh' => 'required',
                'don_vi' => 'required',
                'phong' => 'required',
                'email' => 'required',
            ]);

            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $user->name = $request->name;
            $user->ngay_sinh = $request->ngay_sinh;
            $user->ma_gioi_tinh = $request->gioi_tinh;
            $user->ma_ngach = $request->ngach;
            $user->ma_chuc_vu = $request->chuc_vu;
            $user->ma_don_vi = $request->don_vi;
            $user->ma_phong = $request->phong;
            $user->email = $request->email;
            $user->save();
            return redirect()->route('congchuc.edit', ['id' => $user->so_hieu_cong_chuc])->with('message', 'Đã cập nhật Người dùng thành công');
        } else {
            return view('404');
        }
    }


    //Khóa User
    public function destroy($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $user->ma_trang_thai = 0;
            $user->save();

            return back()->with('message', 'Đã khóa Người dùng');
        } else {
            return view('404');
        }
    }


    //Mở khóa User
    public function restore($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $user->ma_trang_thai = 1;
            $user->save();

            return back()->with('message', 'Đã mở khóa Người dùng');
        } else {
            return view('404');
        }
    }


    //Change Password
    public function changePass($id, Request $request)
    {
        if (Auth::user()->so_hieu_cong_chuc == $id) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            if (!(Hash::check($request->old_password, $user->password))) {
                return back()->with('msg_error', 'Nhập sai mật mã cũ');
            } else {
                $user->password = Hash::make($request->new_password);
                $user->save();
                return back()->with('msg_success', 'Đổi mật mã thành công');
            }
        } else {
            return view('404');
        }
    }


    //Reset Password
    public function resetPass($id)
    {
        if (Auth::user()->isAdmin == 1) {
            $user = User::where('so_hieu_cong_chuc', $id)->first();
            $user->password = Hash::make('123456');
            $user->save();
            return back()->with('msg_success', 'Reset mật mã thành công');
        } else {
            return view('404');
        }
    }
}
