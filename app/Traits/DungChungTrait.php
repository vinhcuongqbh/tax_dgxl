<?php

namespace App\Traits;

use App\Models\DonVi;
use App\Models\Phong;
use Carbon\Carbon;

trait DungChungTrait
{
    public function thoi_diem_danh_gia($request)
    {
        // Trường hợp không chọn thời điểm
        if (!isset($request->nam_danh_gia)) $thoi_diem_danh_gia = Carbon::now()->subMonthNoOverflow()->endOfMonth();
        else $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();

        return $thoi_diem_danh_gia;
    }

    public function don_vi_da_chon($request)
    {
        // Trường hợp không chọn đơn vị
        if ((!isset($request->ma_don_vi_da_chon)) or ($request->ma_don_vi_da_chon == '4400')) $ma_don_vi = '%';
        else $ma_don_vi = $request->ma_don_vi_da_chon;

        return $ma_don_vi;
    }

    public function dm_don_vi()
    {
        $don_vi = DonVi::where('ma_trang_thai', 1)->get();

        return $don_vi;
    }

    public function dm_phong()
    {
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return $phong;
    }
}
