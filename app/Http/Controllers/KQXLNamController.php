<?php

namespace App\Http\Controllers;

use App\Exports\KQXLNamTemplate;
use Illuminate\Http\Request;
use App\Imports\KQXLNamImport;
use App\Models\DonVi;
use App\Models\KQXLNam;
use App\Models\KQXLNamBanKySo;
use App\Models\KQXLQuy;
use App\Models\Phong;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\KQXLNamTapThe;
use App\Models\XepLoai;

class KQXLNamController extends Controller
{
    public function __construct()
    {
        // Cấp tự đánh giá
        $this->middleware('permission:nhập KQXL năm của tập thể', ['only' => ['nhapKetQuaTapThe','luuKetQuaTapThe']]);
        $this->middleware('permission:thông báo KQXL năm của tập thể', ['only' => ['traCuuKetQuaTapThe']]);
        $this->middleware('permission:dự kiến KQXL năm của cá nhân', ['only' => ['dukienkqxlnam']]);
        $this->middleware('permission:nhập KQXL năm của cá nhân', ['only' => ['nhapKQXLNam']]);
        $this->middleware('permission:nhập bản ký số KQXL năm của cá nhân', ['only' => ['nhapKQXLNamBanKySo']]);
        $this->middleware('permission:thông báo KQXL năm của cá nhân', ['only' => ['']]);
    }


    public function nhapKetQuaTapThe(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')
            ->where('ma_trang_thai', 1)
            ->get();

        $kqxl = Phong::where('ma_trang_thai', 1)
            ->whereRaw('ma_phong % 100 <> ?', [1])
            ->get();

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $xep_loai = XepLoai::where('ma_xep_loai', '<>', 'K')->get();

        return view('danhgiatapthe.create', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'don_vi' => $don_vi,
            'phong' => $kqxl,
            'ds_don_vi' => $ds_don_vi,
            'xep_loai' => $xep_loai,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    public function luuKetQuaTapThe(Request $request)
    {
        $phong = Phong::where('ma_trang_thai', 1)->get();

        foreach ($phong as $ph) {
            if ($request->input($ph->ma_phong) !== NULL) {
                $kqxl = KQXLNamTapThe::updateOrCreate(
                    [
                        'nam_danh_gia' => $request->nam_danh_gia_2,
                        'ma_phong' => $ph->ma_phong,
                    ],
                    [
                        'ket_qua_xep_loai' => $request->input($ph->ma_phong),
                        'ma_can_bo_cap_nhat' => Auth::user()->so_hieu_cong_chuc,
                        'ma_trang_thai' => 1,
                    ]
                );
            }
        }
        return redirect()->route('tapthe.nhapketqua')->with('msg_success', 'Đã cập nhật thành công.');
    }


    public function traCuuKetQuaTapThe(Request $request)
    {
        // Trường hợp không chọn năm đánh giá
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subYear();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia);
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $kqxl = KQXLNamTapThe::where('kqxl_nam_tap_the.nam_danh_gia', $thoi_diem_danh_gia->year)
                ->leftjoin('phong', 'phong.ma_phong', 'kqxl_nam_tap_the.ma_phong')
                ->select('kqxl_nam_tap_the.*', 'phong.ten_phong', 'phong.ma_don_vi_cap_tren')
                ->get();
        } else {
            $kqxl = KQXLNamTapThe::where('kqxl_nam_tap_the.nam_danh_gia', $thoi_diem_danh_gia->year)
                ->leftjoin('phong', 'phong.ma_phong', 'kqxl_nam_tap_the.ma_phong')
                ->where('phong.ma_don_vi_cap_tren', $ma_don_vi)
                ->select('kqxl_nam_tap_the.*', 'phong.ten_phong', 'phong.ma_don_vi_cap_tren')
                ->get();
        }

        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')
            ->where('ma_trang_thai', 1)
            ->get();
        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();

        return view('danhgiatapthe.show', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'don_vi' => $don_vi,
            'kqxl' => $kqxl,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }

    // Dự kiến KQXL năm của cá nhân
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


    // Nhập KQXL năm của cá nhân
    public function nhapKQXLNam(Request $request)
    {
        if (Session::get('error') > 0) $error_list = Session::get('error_list');
        else  $error_list = null;

        session()->forget(['error', 'error_list']);

        return view('danhgia.nhapKQXLNam', ['error_list' => $error_list]);
    }

    // Nhập bản ký số KQXL năm của cá nhân
    public function nhapKQXLNamBanKySo(Request $request)
    {
        if (isset($request->nam_danh_gia)) $nam_danh_gia = $request->nam_danh_gia;
        else $nam_danh_gia = Carbon::now()->subYear()->year;

        if (!empty($request->file('KQXLNamBanKySo'))) {
            //$file = Storage::putFile('/File', $request->file('KQXLNamBanKySo'));
            //$path = $request->file('KQXLNamBanKySo')->storeAs('KQXLNam', $nam_danh_gia);    
            $path = '/storage/'.Storage::disk('public')->put('File', $request->file('KQXLNamBanKySo'));

            KQXLNamBanKySo::updateOrCreate(
                ['nam_danh_gia' => $nam_danh_gia],
                [
                    'duong_dan_file' => $path,
                    'ma_can_bo_cap_nhat' => Auth::user()->so_hieu_cong_chuc,
                    'ma_trang_thai' => 1
                ]
            );
        }

        $kqxl_nam_ban_ky_so = KQXLNamBanKySo::orderby('nam_danh_gia', 'desc')->get();

        return view('danhgia.nhapKQXLNamBanKySo', ['kqxl_nam_ban_ky_so' => $kqxl_nam_ban_ky_so, 'nam_danh_gia' => $nam_danh_gia]);
    }



    // Thông báo KQXL theo năm của cá nhân
    public function thongBaoNam(Request $request)
    {
        //Xác định quý đánh giá, năm đánh giá
        if (isset($request->nam_danh_gia)) {
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $nam_danh_gia = Carbon::now()->subYear()->year;
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        $thang_cuoi_cung = Carbon::create($nam_danh_gia)->endOfYear();

        if ($ma_don_vi == 4400) {
            $danh_sach = KQXLNam::where('kqxl_nam.nam_danh_gia', $nam_danh_gia)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'kqxl_nam.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'kqxl_nam.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'kqxl_nam.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'kqxl_nam.ma_don_vi')
                ->select('kqxl_nam.*', 'users.name', 'chuc_vu.ma_chuc_vu', 'chuc_vu.ten_chuc_vu', 'phong.ma_phong', 'don_vi.ma_don_vi')
                ->orderBy('kqxl_nam.ma_don_vi', 'ASC')
                ->orderBy('kqxl_nam.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(kqxl_nam.ma_chuc_vu), kqxl_nam.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = KQXLNam::where('kqxl_nam.nam_danh_gia', $nam_danh_gia)
                ->where('kqxl_nam.ma_don_vi', $ma_don_vi)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'kqxl_nam.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'kqxl_nam.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'kqxl_nam.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'kqxl_nam.ma_don_vi')
                ->select('kqxl_nam.*', 'users.name', 'chuc_vu.ma_chuc_vu', 'chuc_vu.ten_chuc_vu', 'phong.ma_phong', 'don_vi.ma_don_vi')
                ->orderBy('kqxl_nam.ma_don_vi', 'ASC')
                ->orderBy('kqxl_nam.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(kqxl_nam.ma_chuc_vu), kqxl_nam.ma_chuc_vu ASC')
                ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('danhgia.thongbaonam', [
            //'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'ds_don_vi' => $ds_don_vi,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'nam_danh_gia' => $nam_danh_gia,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon
        ]);
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


    public function downloadKQXLNamTemplate()
    {
        return Excel::download(new KQXLNamTemplate, 'KQLXNam.xlsx');
    }
}
