<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\PhieuDanhGia;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaocaoController extends Controller
{
    // Danh sách đã lập phiếu nhưng chưa gửi
    public function ds_dalap_chuagui(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', 11)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', 11)
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('baocao.ds_dalap_chuagui', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    // Danh sách chờ cấp trên đánh giá
    public function ds_captren_danhgia(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', 13)
                ->orwhere('phieu_danh_gia.ma_trang_thai', 15)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', 13)
                ->orwhere('phieu_danh_gia.ma_trang_thai', 15)
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('baocao.ds_captren_danhgia', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    // Danh sách chờ Chi cục trưởng phê duyệt
    public function ds_chicuctruong_pheduyet(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $danh_sach = NULL;
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
            ->where('phieu_danh_gia.thoi_diem_danh_gia', '<=', Carbon::now())
            ->where('phieu_danh_gia.ma_trang_thai', '17')
            ->where('phieu_danh_gia.ma_chuc_vu', null)
            ->orwhere('phieu_danh_gia.ma_don_vi', $ma_don_vi)
            ->where('phieu_danh_gia.thoi_diem_danh_gia', '<=', Carbon::now())
            ->where('phieu_danh_gia.ma_trang_thai', '16')
            ->where('phieu_danh_gia.ma_chuc_vu', '<>', null)
            ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
            ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
            ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
            ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('baocao.ds_chicuctruong_pheduyet', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    // Danh sách chờ Chi cục trưởng phê duyệt
    public function ds_cuctruong_pheduyet(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $danh_sach = NULL;
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
            ->where('phieu_danh_gia.thoi_diem_danh_gia', '<=', Carbon::now())
            ->where('phieu_danh_gia.ma_trang_thai', '17')
            ->where('phieu_danh_gia.ma_chuc_vu', null)
            ->orwhere('phieu_danh_gia.ma_don_vi', $ma_don_vi)
            ->where('phieu_danh_gia.thoi_diem_danh_gia', '<=', Carbon::now())
            ->where('phieu_danh_gia.ma_trang_thai', '16')
            ->where('phieu_danh_gia.ma_chuc_vu', '<>', null)
            ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
            ->leftjoin('ly_do_khong_tu_danh_gia', 'ly_do_khong_tu_danh_gia.id', 'phieu_danh_gia.ly_do_khong_tu_danh_gia')
            ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi', 'ly_do_khong_tu_danh_gia.ly_do')
            ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('baocao.ds_captren_danhgia', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }
}
