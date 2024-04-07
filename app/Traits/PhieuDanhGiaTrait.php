<?php

namespace App\Traits;

use App\Models\KetQuaMucA;
use App\Models\KetQuaMucB;
use App\Models\KQXLQuy;
use App\Models\KQXLThang;
use App\Models\LyDoDiemCong;
use App\Models\LyDoDiemTru;
use App\Models\PhieuDanhGia;
use Illuminate\Http\Request;

trait PhieuDanhGiaTrait
{
    // Tìm phiếu đánh giá
    public function timPhieuDanhGia($ma_phieu_danh_gia)
    {
        $phieu_danh_gia = PhieuDanhGia::where('phieu_danh_gia.ma_phieu_danh_gia', $ma_phieu_danh_gia)
            ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
            ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->first();
        return $phieu_danh_gia;
    }

    // Tìm Kết quả mục A
    public  function timKetQuaMucA($phieu_danh_gia)
    {
        if ($phieu_danh_gia->mau_phieu_danh_gia == 'mau01A') {
            $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $phieu_danh_gia->ma_phieu_danh_gia)
                ->leftjoin('mau01A', 'mau01A.ma_tieu_chi', 'ket_qua_muc_A.ma_tieu_chi')
                ->select('ket_qua_muc_A.*', 'mau01A.tieu_chi_me', 'mau01A.loai_tieu_chi', 'mau01A.tt', 'mau01A.noi_dung')
                ->get();
        } elseif ($phieu_danh_gia->mau_phieu_danh_gia == 'mau01B') {
            $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $phieu_danh_gia->ma_phieu_danh_gia)
                ->leftjoin('mau01B', 'mau01B.ma_tieu_chi', 'ket_qua_muc_A.ma_tieu_chi')
                ->select('ket_qua_muc_A.*', 'mau01B.tieu_chi_me', 'mau01B.loai_tieu_chi', 'mau01B.tt', 'mau01B.noi_dung')
                ->get();
        } elseif ($phieu_danh_gia->mau_phieu_danh_gia == 'mau01C') {
            $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $phieu_danh_gia->ma_phieu_danh_gia)
                ->leftjoin('mau01C', 'mau01C.ma_tieu_chi', 'ket_qua_muc_A.ma_tieu_chi')
                ->select('ket_qua_muc_A.*', 'mau01C.tieu_chi_me', 'mau01C.loai_tieu_chi', 'mau01C.tt', 'mau01C.noi_dung')
                ->get();
        }
        return $ket_qua_muc_A;
    }

    // Lưu kết quả mục A
    public function ketQuaMucAStore($ma_phieu_danh_gia, $mau_phieu_danh_gia, $request)
    {
        $ket_qua_muc_A = new KetQuaMucA();
        $ket_qua_muc_A->ma_phieu_danh_gia = $ma_phieu_danh_gia;
        $ket_qua_muc_A->ma_tieu_chi = $mau_phieu_danh_gia->ma_tieu_chi;
        $ket_qua_muc_A->diem_toi_da = $mau_phieu_danh_gia->diem_toi_da;
        $ket_qua_muc_A->diem_tu_cham = $request->input($mau_phieu_danh_gia->ma_tieu_chi);
        $ket_qua_muc_A->save();
    }

    // Lưu kết quả mục B
    public function ketQuaMucBStore($ma_phieu_danh_gia, $request, $i)
    {
        $ket_qua_muc_B = new KetQuaMucB();
        $ket_qua_muc_B->ma_phieu_danh_gia = $ma_phieu_danh_gia;
        $ket_qua_muc_B->noi_dung = $request->input($i . '_noi_dung_nhiem_vu');
        $ket_qua_muc_B->nhiem_vu_phat_sinh = $request->input($i . '_nhiem_vu_phat_sinh');
        $ket_qua_muc_B->hoan_thanh_nhiem_vu = $request->input($i . '_hoan_thanh_nhiem_vu');
        $ket_qua_muc_B->save();
    }

    // Lưu lý do điểm cộng
    public function lyDoDiemCongStore($ma_phieu_danh_gia, $request)
    {
        $ly_do_diem_cong = new LyDoDiemCong();
        $ly_do_diem_cong->ma_phieu_danh_gia = $ma_phieu_danh_gia;
        $ly_do_diem_cong->noi_dung = $request->ly_do_diem_cong;
        $ly_do_diem_cong->save();
    }

    // Lưu lý do điểm trừ
    public function lyDoDiemTruStore($ma_phieu_danh_gia, $request)
    {
        $ly_do_diem_tru = new LyDoDiemTru();
        $ly_do_diem_tru->ma_phieu_danh_gia = $ma_phieu_danh_gia;
        $ly_do_diem_tru->noi_dung = $request->ly_do_diem_tru;
        $ly_do_diem_tru->save();
    }

    // Phê duyệt danh sách tháng
    public function kQXLThang($danh_sach_phe_duyet)
    {
        foreach ($danh_sach_phe_duyet as $danh_sach) {
            // Xác định năm đánh giá            
            $nam_danh_gia = date("Y", strtotime($danh_sach->thoi_diem_danh_gia));
            // Xác định tháng đánh giá
            $thang_danh_gia = date("m", strtotime($danh_sach->thoi_diem_danh_gia));
            // Tạo Mã xếp loại
            $ma_kqxl = $nam_danh_gia . "_" . $danh_sach->so_hieu_cong_chuc;
            $ket_qua = KQXLThang::where('ma_kqxl', $ma_kqxl)->first();
            if (!isset($ket_qua)) {
                $ket_qua = new KQXLThang();
                $ket_qua->ma_kqxl = $ma_kqxl;
                $ket_qua->so_hieu_cong_chuc = $danh_sach->so_hieu_cong_chuc;
                $ket_qua->nam_danh_gia = $nam_danh_gia;
            }
            $ket_qua->{"diem_tu_cham_t" . $thang_danh_gia} = $danh_sach->tong_diem_tu_cham;
            $ket_qua->{"diem_phe_duyet_t" . $thang_danh_gia} = $danh_sach->tong_diem_danh_gia;
            $ket_qua->{"kqxl_t" . $thang_danh_gia} = $danh_sach->ket_qua_xep_loai;
            $ket_qua->save();
        }
    }


    // Phê duyệt danh sách quý
    public function kQXLQuy($user, $ket_qua_xep_loai, $nam_danh_gia, $quy_danh_gia)
    {
        // Tạo Mã xếp loại
        $ma_kqxl = $nam_danh_gia . "_" . $user->so_hieu_cong_chuc;
        $ket_qua = KQXLQuy::where('ma_kqxl', $ma_kqxl)->first();
        if (!isset($ket_qua)) {
            $ket_qua = new KQXLQuy();
            $ket_qua->ma_kqxl = $ma_kqxl;
            $ket_qua->so_hieu_cong_chuc = $user->so_hieu_cong_chuc;
            $ket_qua->nam_danh_gia = $nam_danh_gia;
        }
        $ket_qua->{"kqxl_q" . $quy_danh_gia} = $ket_qua_xep_loai;
        $ket_qua->save();
    }
}
