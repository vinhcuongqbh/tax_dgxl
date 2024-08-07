<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\PhieuDanhGia;
use App\Models\Phong;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaocaoController extends Controller
{
    public function __construct()
    {
        // Xem Báo cáo hỗ trợ
        $this->middleware('permission:xem báo cáo hỗ trợ', ['only' =>
        [
            'baocao_tiendo', 'ds_chualapphieu', 'ds_dalap_chuagui', 'ds_captren_danhgia', 'ds_chicuctruong_pheduyet', 'ds_cuctruong_pheduyet'
        ]]);
    }

    // Báo cáo tiến độ trong tháng
    public function baocao_tiendo(Request $request)
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

        if ($ma_don_vi == 4400) $dm_don_vi = DonVi::where('ma_don_vi', '<>', '4400')->get();
        else $dm_don_vi = DonVi::where('ma_don_vi', $ma_don_vi)->get();

        $danh_sach = collect();
        $i = 0;
        foreach ($dm_don_vi as $don_vi) {
            $i++;
            $ten_don_vi = $don_vi->ten_don_vi;
            $dv = $don_vi->ma_don_vi;
            $tong_so_cong_chuc = User::where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ma_trang_thai', 1)
                ->where('created_at', '<', $thoi_diem_danh_gia->copy()->addDays(10))
                ->count();
            $ca_nhan_khong_tu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ly_do_khong_tu_danh_gia', '<>', NULL)
                ->count();
            $ca_nhan_tu_danh_gia = $tong_so_cong_chuc - $ca_nhan_khong_tu_danh_gia;
            $ca_nhan_da_lap_phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ly_do_khong_tu_danh_gia', NULL)
                ->count();
            $ca_nhan_chua_lap_phieu_danh_gia = $ca_nhan_tu_danh_gia - $ca_nhan_da_lap_phieu_danh_gia;
            $ca_nhan_chua_gui_phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ma_trang_thai', 11)
                ->count();
            $ca_nhan_da_gui_phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ma_trang_thai', '>=', 13)
                ->count();
            $ca_nhan_cho_cap_tren_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ma_trang_thai', 13)
                ->count();
            $cap_tren_da_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $don_vi->ma_don_vi)
                ->where('ma_trang_thai', 15)
                ->count();
            if ($don_vi->ma_don_vi == '4401') {
                $ca_nhan_cho_chi_cuc_truong_phe_duyet = 0;
                $chi_cuc_truong_da_phe_duyet = 0;
            } else {
                $ca_nhan_cho_chi_cuc_truong_phe_duyet = PhieuDanhGia::where('ma_don_vi', $don_vi->ma_don_vi)
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_trang_thai', '17')
                    ->where('ma_chuc_vu', null)
                    ->orwhere('ma_don_vi', $don_vi->ma_don_vi)
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_trang_thai', '16')
                    ->where('ma_chuc_vu', '<>', null)
                    ->count();
                $chi_cuc_truong_da_phe_duyet = PhieuDanhGia::where('ma_don_vi', $don_vi->ma_don_vi)
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_trang_thai', '>=', '19')
                    ->where('ma_chuc_vu', null)
                    ->count();
            }
            if ($don_vi->ma_don_vi == '4401') {
                $ca_nhan_cho_cuc_truong_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '17')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['02', '04', '05', '07', '08'])
                            ->where('ma_don_vi', $dv)
                            ->orwhere('ma_don_vi', '4401')
                            ->where('ma_chuc_vu', null);
                    })
                    ->count();
                $cuc_truong_da_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '>=', '19')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['02', '04', '05', '07', '08'])
                            ->where('ma_don_vi', $dv)
                            ->orwhere('ma_don_vi', '4401')
                            ->where('ma_chuc_vu', null);
                    })
                    ->count();
            } else {
                $ca_nhan_cho_cuc_truong_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '17')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['03', '06', '09', '10'])
                            ->where('ma_don_vi', $dv);
                    })
                    ->count();
                $cuc_truong_da_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '>=', '19')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['03', '06', '09', '10'])
                            ->where('ma_don_vi', $dv);
                    })
                    ->count();
            }

            if ($don_vi->ma_don_vi == '4401') {
                $ca_nhan_cho_hoi_dong_phe_duyet = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_chuc_vu', '01')
                    ->where('ma_trang_thai', '17')
                    ->count();
                $hoi_dong_da_phe_duyet = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_chuc_vu', '01')
                    ->where('ma_trang_thai', '>=', '19')
                    ->count();
            } else {
                $ca_nhan_cho_hoi_dong_phe_duyet = 0;
                $hoi_dong_da_phe_duyet = 0;
            }

            $ca_nhan_cho_phe_duyet =  $ca_nhan_cho_chi_cuc_truong_phe_duyet +  $ca_nhan_cho_cuc_truong_phe_duyet;
            $danh_sach->push([
                'stt' => $i,
                'ten_don_vi' => $ten_don_vi,
                'tong_so_cong_chuc' => $tong_so_cong_chuc,
                'ca_nhan_khong_tu_danh_gia' => $ca_nhan_khong_tu_danh_gia,
                'ca_nhan_tu_danh_gia' => $ca_nhan_tu_danh_gia,
                'ca_nhan_chua_lap_phieu_danh_gia' => $ca_nhan_chua_lap_phieu_danh_gia,
                'ca_nhan_da_lap_phieu_danh_gia' => $ca_nhan_da_lap_phieu_danh_gia,
                'ca_nhan_chua_gui_phieu_danh_gia' => $ca_nhan_chua_gui_phieu_danh_gia,
                'ca_nhan_da_gui_phieu_danh_gia' => $ca_nhan_da_gui_phieu_danh_gia,
                'ca_nhan_cho_cap_tren_danh_gia' => $ca_nhan_cho_cap_tren_danh_gia,
                'cap_tren_da_danh_gia' => $cap_tren_da_danh_gia,
                'ca_nhan_cho_chi_cuc_truong_phe_duyet' => $ca_nhan_cho_chi_cuc_truong_phe_duyet,
                'chi_cuc_truong_da_phe_duyet' => $chi_cuc_truong_da_phe_duyet,
                'ca_nhan_cho_cuc_truong_phe_duyet' => $ca_nhan_cho_cuc_truong_phe_duyet,
                'cuc_truong_da_phe_duyet' => $cuc_truong_da_phe_duyet,
                'ca_nhan_cho_hoi_dong_phe_duyet' => $ca_nhan_cho_hoi_dong_phe_duyet,
                'hoi_dong_da_phe_duyet' => $hoi_dong_da_phe_duyet
            ]);

            $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
            $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
            $phong = Phong::where('ma_trang_thai', 1)->get();
        }

        return view('baocao.baocao_tiendo', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'danh_sach' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }

    // Danh sách chưa lập phiếu đánh giá
    public function ds_chualapphieu(Request $request)
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
            $ds_canbo = User::where('users.ma_trang_thai', 1)
                ->where('users.created_at', '<', $thoi_diem_danh_gia)
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
            $ds_canbo_dalapphieu = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        } else {
            $ds_canbo = User::where('users.ma_trang_thai', 1)
                ->where('users.ma_don_vi', $ma_don_vi)
                ->where('users.created_at', '<', $thoi_diem_danh_gia)
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
            $ds_canbo_dalapphieu = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_don_vi', $ma_don_vi)
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        }

        foreach ($ds_canbo_dalapphieu as $ds2) {
            foreach ($ds_canbo as $key => $ds1) {
                if ($ds2->so_hieu_cong_chuc == $ds1->so_hieu_cong_chuc) {
                    $ds_canbo->forget($key);
                }
            }
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('baocao.ds_chualapphieu', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $ds_canbo,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


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
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
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

        if (($ma_don_vi == 4400) or ($ma_don_vi == 4401)) {
            $danh_sach = NULL;
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', '17')
                ->where('phieu_danh_gia.ma_chuc_vu', null)
                ->orwhere('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
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


    // Danh sách chờ Cục trưởng phê duyệt
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
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where(function ($query) use ($ma_don_vi) {
                    $query->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05', '06', '07', '08', '09', '10'])
                        ->orwhere('phieu_danh_gia.ma_don_vi', '4401');
                })
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
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where(function ($query) use ($ma_don_vi) {
                    $query->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05', '06', '07', '08', '09', '10'])
                        ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                        ->orwhere('phieu_danh_gia.ma_don_vi', '4401');
                })
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


        return view('baocao.ds_cuctruong_pheduyet', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }
}
