<?php

namespace App\Http\Controllers;

use App\Models\KetQuaMucA;
use Illuminate\Http\Request;

class KetQuaMucAController extends Controller
{
    public function store($ma_phieu_danh_gia, $mau_phieu_danh_gia, $request)
    {
        $ket_qua_muc_A = new KetQuaMucA();
        $ket_qua_muc_A->ma_phieu_danh_gia = $ma_phieu_danh_gia;
        $ket_qua_muc_A->ma_tieu_chi = $mau_phieu_danh_gia->ma_tieu_chi;
        $ket_qua_muc_A->diem_toi_da = $mau_phieu_danh_gia->diem_toi_da;
        $ket_qua_muc_A->diem_tu_cham = $request->input($mau_phieu_danh_gia->ma_tieu_chi);
        $ket_qua_muc_A->save();
    }
}
