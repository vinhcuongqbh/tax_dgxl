<?php

namespace App\Imports;

use App\Models\ChucVu;
use App\Models\DonVi;
use App\Models\KQXLNam;
use App\Models\Phong;
use App\Models\User;
use App\Models\XepLoai;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Facades\Session;


use function Laravel\Prompts\error;

class KQXLNamImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        $error_list = collect();
        $error_sum = 0;

        foreach ($rows as $row) {
            $error = 0;
            $error_cc = "";
            $error_chuc_vu = "";
            $error_phong = "";
            $error_don_vi = "";
            $error_kqxl = "";
            $error_nam = "";

            $cong_chuc = User::where('so_hieu_cong_chuc', $row[1])->get();
            if (isset($row[3])) $ma_chuc_vu = ChucVu::where('ma_chuc_vu', $row[3])->get()->count();
            else $ma_chuc_vu = 1;
            $ma_phong = Phong::where('ma_phong', $row[5])->get();
            $ma_don_vi = DonVi::where('ma_don_vi', $row[7])->get();
            $kqxl = XepLoai::where('ma_xep_loai', $row[9])->get();
            $nam_xep_loai = $row[10];

            // if ($nam_xep_loai == null) $nam_xep_loai = " ";          

            if ($cong_chuc->count() == 0) {
                $error++;
                $error_sum++;
                $error_cc = "Lỗi CCCD không có trong cơ sở dữ liệu.";
            }

            if ($ma_chuc_vu == 0) {
                $error++;
                $error_sum++;
                $error_chuc_vu = "Lỗi Mã chức vụ không có trong cơ sở dữ liệu.";
            }

            if ($ma_phong->count() == 0) {
                $error++;
                $error_sum++;
                $error_phong = "Lỗi Mã phòng không có trong cơ sở dữ liệu.";
            }

            if ($ma_don_vi->count() == 0) {
                $error++;
                $error_sum++;
                $error_don_vi = "Lỗi Mã đơn vị không có trong cơ sở dữ liệu.";
            }

            if ($kqxl->count() == 0) {
                $error++;
                $error_sum++;
                $error_kqxl = "Lỗi Kết quả xếp loại không đúng.";
            }

            if (is_int($nam_xep_loai) == false) {
                $error++;
                $error_sum++;
                $error_nam = "Lỗi năm đánh giá không đúng.";
            }

            if ($error > 0) {
                $error_list->push([
                    'so_hieu_cong_chuc' => $row[1],
                    'name' => $row[2],
                    'ma_chuc_vu' => $row[3],
                    'ma_phong' => $row[5],
                    'ma_don_vi' => $row[7],
                    'xep_loai' => $row[9],
                    'nam_danh_gia' => $row[10],
                    'ghi_chu' => $error_cc . "\n" . $error_chuc_vu . "\n" . $error_phong
                        . "\n" . $error_don_vi . "\n" . $error_kqxl . "\n" . $error_nam
                ]);
            }
        }


        if ($error_sum == 0) {
            foreach ($rows as $row) {
                KQXLNam::updateOrCreate(
                    ['ma_kqxl' => $row[10] . "_" . $row[1]],
                    [
                        'so_hieu_cong_chuc' => $row[1],
                        'ma_chuc_vu' => $row[3],
                        'ma_phong' => $row[5],
                        'ma_don_vi' => $row[7],
                        'kqxl' => $row[9],
                        'nam_danh_gia' => $row[10],                        
                        'ma_can_bo_cap_nhat' => Auth::user()->so_hieu_cong_chuc,
                        'ma_trang_thai' => 1
                    ]
                );
            }
        }

        Session::put('error', $error_sum);
        Session::put('error_list', $error_list);
    }

    public function startRow(): int
    {
        return 2;
    }
}

// use App\Models\KQXLNam;
// use Maatwebsite\Excel\Concerns\ToModel;
// use Maatwebsite\Excel\Concerns\WithStartRow;

// class KQXLNamImport implements ToModel, WithStartRow
// {
//     public function model(array $row)
//     {
//         KQXLNam::updateOrCreate(
//             ['ma_kqxl' => $row[4]."_".$row[1]],
//             [
//                 'so_hieu_cong_chuc' => $row[1],
//                 'nam_danh_gia' => $row[4],
//                 'kqxl' => $row[3],
//                 'ma_trang_thai' => 1
//             ]
//         );
//     }    
// }