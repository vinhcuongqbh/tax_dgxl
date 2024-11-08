<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\PhieuDanhGia;
use App\Models\Phong;
use App\Models\User;
use App\Traits\DungChungTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BaocaoController extends Controller
{
    use DungChungTrait;

    public function __construct()
    {
        // Xem Báo cáo hỗ trợ
        $this->middleware('permission:xem báo cáo hỗ trợ', ['only' => ['tonghop', 'chitiet']]);
    }

    // Báo cáo tổng hợp
    public function tonghop(Request $request)
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
                ->where('ma_trang_thai', '>=', 15)
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
                        $query->wherein('ma_chuc_vu', ['02', '04', '05', '07', '08', '07A', '08A'])
                            ->where('ma_don_vi', $dv)
                            ->orwhere('ma_don_vi', '4401')
                            ->where('ma_chuc_vu', null);
                    })
                    ->count();
                $cuc_truong_da_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '>=', '19')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['02', '04', '05', '07', '08', '07A', '08A'])
                            ->where('ma_don_vi', $dv)
                            ->orwhere('ma_don_vi', '4401')
                            ->where('ma_chuc_vu', null);
                    })
                    ->count();
            } else {
                $ca_nhan_cho_cuc_truong_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '17')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['03', '06', '09', '10', '10A'])
                            ->where('ma_don_vi', $dv);
                    })
                    ->count();
                $cuc_truong_da_phe_duyet = PhieuDanhGia::where('ma_trang_thai', '>=', '19')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) use ($dv) {
                        $query->wherein('ma_chuc_vu', ['03', '06', '09', '10', '10A'])
                            ->where('ma_don_vi', $dv);
                    })
                    ->count();
            }

            if ($don_vi->ma_don_vi == '4401') {
                $ca_nhan_cho_hoi_dong_phe_duyet = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_chuc_vu', '01', '02A')
                    ->where('ma_trang_thai', '17')
                    ->count();
                $hoi_dong_da_phe_duyet = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_chuc_vu', '01', '02A')
                    ->where('ma_trang_thai', '>=', '19')
                    ->count();
            } else {
                $ca_nhan_cho_hoi_dong_phe_duyet = 0;
                $hoi_dong_da_phe_duyet = 0;
            }

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

        return view('baocao.tong_hop', [
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
        // Xác định thời điểm đánh giá
        $thoi_diem_danh_gia = $this->thoi_diem_danh_gia($request);
        // Xác định đơn vị được chọn
        $ma_don_vi = $this->don_vi_da_chon($request);
        // Lấy danh mục Đơn vị
        $don_vi = $this->dm_don_vi();
        // Lấy danh mục Phòng
        $phong = $this->dm_phong();

        $ds_canbo = User::where('ma_trang_thai', 1)
            ->where('ma_don_vi', 'LIKE', $ma_don_vi)
            ->where('created_at', '<', $thoi_diem_danh_gia)
            ->orderBy('ma_don_vi', 'ASC')
            ->orderBy('ma_phong', 'ASC')
            ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
            ->get();
        $ds_canbo_dalapphieu = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
            ->where('ma_don_vi', 'LIKE', $ma_don_vi)
            ->orderBy('ma_don_vi', 'ASC')
            ->orderBy('ma_phong', 'ASC')
            ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
            ->get();

        foreach ($ds_canbo_dalapphieu as $ds2) {
            foreach ($ds_canbo as $key => $ds1) {
                if ($ds2->so_hieu_cong_chuc == $ds1->so_hieu_cong_chuc) {
                    $ds_canbo->forget($key);
                }
            }
        }

        return view('baocao.chi_tiet', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $ds_canbo,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    // Báo cáo Chi tiết
    public function chitiet($chuc_nang, Request $request)
    {
        // Xác định thời điểm đánh giá
        $thoi_diem_danh_gia = $this->thoi_diem_danh_gia($request);
        // Xác định đơn vị được chọn
        $ma_don_vi = $this->don_vi_da_chon($request);
        // Lấy danh mục Đơn vị
        $don_vi = $this->dm_don_vi();
        // Lấy danh mục Phòng
        $phong = $this->dm_phong();

        // Xác định danh sách phù hợp với yêu cầu
        switch ($chuc_nang) {
            case 'ds_chualapphieu':
                // Danh sách cá nhân chưa lập phiếu
                $danh_sach = User::where('ma_trang_thai', 1)
                    ->where('ma_don_vi', 'LIKE', $ma_don_vi)
                    ->where('created_at', '<', $thoi_diem_danh_gia)
                    ->orderBy('ma_don_vi', 'ASC')
                    ->orderBy('ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                    ->get();
                $ds_canbo_dalapphieu = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_don_vi', 'LIKE', $ma_don_vi)
                    ->orderBy('ma_don_vi', 'ASC')
                    ->orderBy('ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                    ->get();

                foreach ($ds_canbo_dalapphieu as $ds2) {
                    foreach ($danh_sach as $key => $ds1) {
                        if ($ds2->so_hieu_cong_chuc == $ds1->so_hieu_cong_chuc) {
                            $danh_sach->forget($key);
                        }
                    }
                }
                break;
            case 'ds_dalap_chuagui':
                // Danh sách cá nhân đã lập nhưng chưa gửi phiếu
                $danh_sach = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where('ma_trang_thai', 11)
                    ->where('ma_don_vi', 'LIKE', $ma_don_vi)
                    ->orderBy('ma_don_vi', 'ASC')
                    ->orderBy('ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                    ->get();
                break;
            case 'ds_captren_danhgia':
                // Danh sách cá nhân chờ cấp trên đánh giá
                $danh_sach = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->wherein('ma_trang_thai', [13, 15])
                    ->where('ma_don_vi', 'LIKE', $ma_don_vi)
                    ->orderBy('ma_don_vi', 'ASC')
                    ->orderBy('ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                    ->get();
                break;
            case 'ds_chicuctruong_pheduyet':
                // Danh sách cá nhân chờ Chi cục trưởng duyệt/phê duyệt
                $danh_sach = PhieuDanhGia::where('ma_don_vi', 'LIKE', $ma_don_vi)
                    ->where('ma_don_vi', '<>', '4401')
                    ->where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->where(function ($query) {
                        $query->where('ma_trang_thai', '17')
                            ->where('ma_chuc_vu', null)
                            ->orwhere('ma_trang_thai', '16')
                            ->where('ma_chuc_vu', '<>', null);
                    })
                    ->orderBy('ma_don_vi', 'ASC')
                    ->orderBy('ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                    ->get();
                break;
            case 'ds_cuctruong_pheduyet':
                // Danh sách cá nhân chờ Cục trưởng phê duyệt      
                if (!isset($request->ma_don_vi_da_chon) or ($request->ma_don_vi_da_chon == '4400'))
                    $danh_sach = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                        ->where('ma_trang_thai', '17')
                        ->where(function ($query) {
                            $query->where('ma_don_vi',  '4401')
                                ->orwhere('ma_don_vi', '<>', '4401')
                                ->wherein('ma_chuc_vu', ['03', '06', '09', '10', '10A']);
                        })
                        ->orderBy('ma_don_vi', 'ASC')
                        ->orderBy('ma_phong', 'ASC')
                        ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                        ->get();
                elseif ($request->ma_don_vi_da_chon == '4401')
                    $danh_sach = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                        ->where('ma_trang_thai', '17')
                        ->where('ma_don_vi', '4401')
                        ->orderBy('ma_don_vi', 'ASC')
                        ->orderBy('ma_phong', 'ASC')
                        ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                        ->get();
                else {
                    $danh_sach = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                        ->where('ma_trang_thai', '17')
                        ->where('ma_don_vi', 'LIKE', $ma_don_vi)
                        ->wherein('ma_chuc_vu', ['03', '06', '09', '10', '10A'])
                        ->orderBy('ma_don_vi', 'ASC')
                        ->orderBy('ma_phong', 'ASC')
                        ->orderByRaw('ISNULL(ma_chuc_vu), ma_chuc_vu ASC')
                        ->get();
                }
                break;
        }

        return view('baocao.chi_tiet', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
            'chuc_nang' => $chuc_nang,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'phieu_danh_gia' => $danh_sach,
        ]);
    }
}
