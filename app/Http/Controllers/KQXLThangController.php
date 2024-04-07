<?php

namespace App\Http\Controllers;

use App\Models\KQXLThang;
use Illuminate\Http\Request;

class KQXLThangController extends Controller
{
    public function store($so_hieu_cong_chuc, $nam) {
        $kqxl = new KQXLThang();
        $kqxl->ma_xep_loai_thang = $nam."_".$so_hieu_cong_chuc;
        $kqxl->so_hieu_cong_chuc = $so_hieu_cong_chuc;
        $kqxl->nam_danh_gia = $nam;
        $kqxl->ma_trang_thai = 1;
        $kqxl->save();
    }

    public function update($so_hieu_cong_chuc, $nam, $thang, $ket_qua) {
        $diem_tu_cham = "diem_tu_cham_t_".$thang;   
        $diem_phe_duyet = "diem_phe_duyet_t".$thang;
        $kqxl = "kqxl_t".$thang;

        $ket_qua_xep_loai = KQXLThang::where('so_hieu_cong_chuc', $so_hieu_cong_chuc)
            ->where('nam_danh_gia', $nam)
            ->first();
        
        $ket_qua_xep_loai->$diem_tu_cham = $ket_qua->diem_tu_cham;
        $ket_qua_xep_loai->$diem_phe_duyet = $ket_qua->diem_danh_gia;
        $ket_qua_xep_loai->$kqxl = $ket_qua->ket_qua_xep_loai;
        $ket_qua_xep_loai->save();
    }
}
