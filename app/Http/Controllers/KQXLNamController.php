<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\KQLXNamImport;
use App\Imports\KQXLNamImport;
use App\Models\DonVi;
use App\Models\KQXLQuy;
use App\Models\Phong;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;


class KQXLNamController extends Controller
{
    public function dukienkqxlnam(Request $request)
    {
        //Xác định năm đánh giá
        if (isset($request->nam_danh_gia)) {
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $nam_danh_gia = Carbon::now()->year;
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }
        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        $kqxl_quy = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
            ->leftjoin('users', 'users.so_hieu_cong_chuc', 'kqxl_quy.so_hieu_cong_chuc')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->select('kqxl_quy.*', 'users.name', 'users.ma_don_vi', 'users.ma_phong', 'chuc_vu.ten_chuc_vu')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();

        if (isset($kqxl_quy)) {
            foreach ($kqxl_quy as $kqxl) {
                // Tìm kết quả xếp loại các tháng trong quý
                $xep_loai_1 = $kqxl->kqxl_q1;
                if (isset($xep_loai_1)) $xep_loai_1 = "K";
                echo $xep_loai_1 . "<br>";

                $xep_loai_2 = $kqxl->kqxl_q2;
                if (isset($xep_loai_2)) $xep_loai_2 = "K";
                echo $xep_loai_2 . "<br>";

                $xep_loai_3 = $kqxl->kqxl_q3;
                if (isset($xep_loai_3)) $xep_loai_3 = "K";
                echo $xep_loai_3 . "<br>";

                $xep_loai_4 = $kqxl->kqxl_q4;
                if (isset($xep_loai_4)) $xep_loai_4 = "K";
                echo $xep_loai_4 . "<br>";


                // Tính toán xếp loại năm
                $countA = 0;
                $countB = 0;
                $countC = 0;
                $countD = 0;
                $countK = 0;

                for ($i = 1; $i <= 4; $i++) {
                    if (${"xep_loai_" . $i} == "A") $countA++;
                    elseif (${"xep_loai_" . $i} == "B") $countB++;
                    elseif (${"xep_loai_" . $i} == "C") $countC++;
                    elseif (${"xep_loai_" . $i} == "D")  $countD++;
                    elseif (${"xep_loai_" . $i} == "K")  $countK++;
                }

                $ket_qua_xep_loai = null;
                if (($xep_loai_1 == null) || ($xep_loai_2 == null) || ($xep_loai_3 == null) || ($xep_loai_4 == null)) $ket_qua_xep_loai = null;
                elseif ($countK > 0) $ket_qua_xep_loai = "K";
                elseif ($countD > 0) $ket_qua_xep_loai = "D";
                elseif (($countA >= 2) && ($countC == 0) && ($countD == 0)) $ket_qua_xep_loai = "A";
                elseif (((($countA >= 2) || ($countB >= 2)) || (($countA >= 1) && ($countB >= 1))) && ($countD == 0)) $ket_qua_xep_loai = "B";
                elseif ((($countA > 0) || ($countB > 0) || ($countC > 0)) && ($countD == 0)) $ket_qua_xep_loai = "C";
                else $ket_qua_xep_loai = null;


                // Đưa vào danh sách
                if ($ket_qua_xep_loai != null) {
                    $collection->push([
                        'name' => $kqxl->name,
                        'ten_chuc_vu' => $kqxl->ten_chuc_vu,
                        'ma_phong' => $kqxl->ma_phong,
                        'ma_don_vi' => $kqxl->ma_don_vi,
                        'kqxl_q1' => $kqxl->kqxl_q1,
                        'kqxl_q2' => $kqxl->kqxl_q2,
                        'kqxl_q3' => $kqxl->kqxl_q3,
                        'kqxl_q4' => $kqxl->kqxl_q4,
                        'kqxl_nam' => $ket_qua_xep_loai,
                    ]);
                }
            }
        }

        // return view('danhgia.canhan_dukienkqxlnam', [
        //     //'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
        //     'phieu_danh_gia' => $collection,
        //     'ds_don_vi' => $ds_don_vi,
        //     'don_vi' => $don_vi,
        //     'phong' => $phong,
        //     'nam_danh_gia' => $nam_danh_gia,
        //     'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon
        // ]);
    }


    public function nhapKQXLNam(Request $request)
    {
        if (Session::get('error') >0 ) $error_list = Session::get('error_list');
        else  $error_list = null;

        session()->forget(['error', 'error_list']);

        return view('danhgia.nhapKQXLNam', ['error_list' => $error_list]);
    }


    public function readExcel(Request $request)
    {
        // Kiểm tra tệp đã được tải lên
        if ($request->hasFile('KQXLNam')) {
            // Đọc tệp Excel và chuyển đổi thành mảng
            Excel::import(new KQXLNamImport, $request->file('KQXLNam'));

            $error = Session::get('error');
            // $error_list = Session::get('error_list');            

            if ($error > 0) {
                return redirect()->route(
                    'canhan.nhapketqua',
                )->with('msg_error', 'Có lỗi trong dữ liệu file excel');
            } else {
                return redirect()->route(
                    'canhan.nhapketqua'
                )->with('msg_success', 'Nhập dữ liệu từ file excel thành công');
            }
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No file uploaded'
            ]);
        }
    }
}
