<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\KetQuaMucA;
use App\Models\KetQuaMucACucTruong;
use App\Models\KetQuaMucB;
use App\Models\KQXLQuy;
use App\Models\LyDoDiemCong;
use App\Models\LyDoDiemTru;
use App\Models\Mau01A;
use App\Models\Mau01B;
use App\Models\Mau01C;
use App\Models\PhieuDanhGia;
use App\Models\User;
use App\Models\XepLoai;
use App\Traits\PhieuDanhGiaTrait;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\KQXLThang;
use App\Models\PhieuDanhGiaCucTruong;
use App\Models\Phong;
use Termwind\Components\Raw;

class PhieuDanhGiaController extends Controller
{
    use PhieuDanhGiaTrait;
    ///////////////////// Cá nhân tự đánh giá, xếp loại ////////////////////
    // Tạo Phiếu đánh giá cá nhân tự chấm
    public function canhanCreate()
    {
        //$thoi_diem_danh_gia = Carbon::now()->subMonth();
        $thoi_diem_danh_gia = Carbon::now();
        $ngay_thuc_hien_danh_gia = Carbon::now();
        $xep_loai = XepLoai::all();

        $user = User::where('so_hieu_cong_chuc', Auth::user()->so_hieu_cong_chuc)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'phong.ten_phong', 'chuc_vu.ten_chuc_vu', 'don_vi.ten_don_vi')
            ->first();

        // Xác định mẫu phiếu và thông tin về mẫu phiếu
        list($mau_phieu_danh_gia, $thong_tin_mau_phieu) = $this->xacDinhMauPhieu();

        return view(
            'danhgia.canhan_create',
            [
                'mau_phieu' => $mau_phieu_danh_gia,
                'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                'xep_loai' => $xep_loai,
                'user' => $user,
            ]
        );
    }


    // Lưu Phiếu đánh giá tự chấm
    public function canhanStore(Request $request)
    {
        // Tạo Mã phiếu đánh giá 
        $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia,)->endOfMonth();
        $ma_phieu_danh_gia = $thoi_diem_danh_gia->format("Y") . $thoi_diem_danh_gia->format("m") . "_" . Auth::user()->so_hieu_cong_chuc;

        // Kiểm tra Mã phiếu đánh giá đã tồn tại
        if (PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->exists()) {
            return back()->with('msg_error', 'Đã tồn tại kết quả đánh giá của tháng này');
        } else {
            // Tính Tổng điểm cá nhân tự chấm
            $diem_tu_cham = $request->tc_300;
            $diem_cong_tu_cham = $request->tc_400;
            $diem_tru_tu_cham = $request->tc_500;
            $tong_diem_tu_cham = $this->tinhTongDiem($request);
            // Kết quả cá nhân tự xếp loại
            $ca_nhan_tu_xep_loai = $this->xepLoai($tong_diem_tu_cham);
            // Xác định mẫu phiếu và thông tin về mẫu phiếu
            list($mau_phieu_danh_gia, $thong_tin_mau_phieu) = $this->xacDinhMauPhieu();

            // Lưu kết quả tự chấm Mục A   
            foreach ($mau_phieu_danh_gia as $mau_phieu_danh_gia) {
                $this->ketQuaMucAStore($ma_phieu_danh_gia, $mau_phieu_danh_gia, $request);
            }

            // Lưu kết quả tự chấm Mục B    
            for ($i = 1; $i <= 50; $i++) {
                if ($request->input($i . '_noi_dung_nhiem_vu') != null) {
                    $this->ketQuaMucBStore($ma_phieu_danh_gia, $request, $i);
                };
            }

            // Lưu kết quả Lý do điểm Cộng
            $this->lyDoDiemCongStore($ma_phieu_danh_gia, $request);

            // Lưu kết quả Lý do điểm trừ
            $this->lyDoDiemTruStore($ma_phieu_danh_gia, $request);

            // Lưu kết quả Phiếu đánh giá cá nhân tự đánh giá
            $phieu_danh_gia = new PhieuDanhGia();
            $phieu_danh_gia->mau_phieu_danh_gia = $thong_tin_mau_phieu['mau'];
            $phieu_danh_gia->ma_phieu_danh_gia = $ma_phieu_danh_gia;
            $phieu_danh_gia->thoi_diem_danh_gia = $thoi_diem_danh_gia;
            $phieu_danh_gia->so_hieu_cong_chuc = Auth::user()->so_hieu_cong_chuc;
            $phieu_danh_gia->ma_chuc_vu = Auth::user()->ma_chuc_vu;
            $phieu_danh_gia->ma_phong = Auth::user()->ma_phong;
            $phieu_danh_gia->ma_don_vi = Auth::user()->ma_don_vi;
            $phieu_danh_gia->diem_tu_cham = $diem_tu_cham;
            $phieu_danh_gia->diem_cong_tu_cham = $diem_cong_tu_cham;
            $phieu_danh_gia->diem_tru_tu_cham = $diem_tru_tu_cham;
            $phieu_danh_gia->tong_diem_tu_cham = $tong_diem_tu_cham;
            $phieu_danh_gia->ca_nhan_tu_xep_loai = $ca_nhan_tu_xep_loai;
            $phieu_danh_gia->ma_trang_thai = 11;
            //if (Auth::user()->ma_chuc_vu == "01") $phieu_danh_gia->ket_qua_xep_loai = $ca_nhan_tu_xep_loai;
            $phieu_danh_gia->save();

            return redirect()->route(
                'phieudanhgia.canhan.show',
                [
                    'id' => $phieu_danh_gia->ma_phieu_danh_gia
                ]
            )->with('msg_success', 'Đã lưu thành công Phiếu đánh giá. Bạn vui lòng kiểm tra lại kỹ trước khi gửi Phiếu.');
        }
    }


    public function canhanEdit($id)
    {
        // Tìm Phiếu đánh giá
        $phieu_danh_gia = $this->timPhieuDanhGia($id);
        $thoi_diem_danh_gia = Carbon::create($phieu_danh_gia->thoi_diem_danh_gia);

        // Nếu Mã trạng thái khác 11-Cá nhân tạo Phiếu đánh giá
        if (($phieu_danh_gia->ma_trang_thai <> 11) || ($phieu_danh_gia->so_hieu_cong_chuc <> Auth::user()->so_hieu_cong_chuc)) {
            return back()->with('msg_error', "Không được phép sửa Phiếu đánh giá này");
        }

        // Lấy dữ liệu mục A
        $ket_qua_muc_A = $this->timKetQuaMucA($phieu_danh_gia);
        // Lấy dữ liệu mục B
        $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $id)->get();
        // Lấy dữ liệu Lý do điểm cộng
        $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $id)->first();
        // Lấy dữ liệu Lý do điểm trừ
        $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $id)->first();

        // Lấy thông tin Mẫu phiếu đánh giá
        $thong_tin_mau_phieu = $this->thongTinMauPhieu($phieu_danh_gia->mau_phieu_danh_gia);

        $ngay_thuc_hien_danh_gia = Carbon::create($phieu_danh_gia->created_at);
        $xep_loai = XepLoai::all();

        return view(
            'danhgia.canhan_edit',
            [
                'phieu_danh_gia' => $phieu_danh_gia,
                'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                'xep_loai' => $xep_loai,
                'ket_qua_muc_A' => $ket_qua_muc_A,
                'ket_qua_muc_B' => $ket_qua_muc_B,
                'ly_do_diem_cong' => $ly_do_diem_cong,
                'ly_do_diem_tru' => $ly_do_diem_tru,
            ]
        );
    }


    //Cập nhật Phiếu đánh giá cá nhân tự chấm
    public function canhanUpdate($ma_phieu_danh_gia, Request $request)
    {
        // Tính Tổng điểm cá nhân tự chấm
        $diem_tu_cham = $request->tc_300;
        $diem_cong_tu_cham = $request->tc_400;
        $diem_tru_tu_cham = $request->tc_500;
        $tong_diem_tu_cham = $this->tinhTongDiem($request);
        // Kết quả cá nhân tự xếp loại
        $ca_nhan_tu_xep_loai = $this->xepLoai($tong_diem_tu_cham);

        // Lưu kết quả tự chấm Mục A        
        $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();
        foreach ($ket_qua_muc_A as $ket_qua) {
            $data = $ket_qua_muc_A->where('ma_tieu_chi', $ket_qua->ma_tieu_chi)->first();
            $data->diem_tu_cham = $request->input($ket_qua->ma_tieu_chi);
            $data->save();
        }

        // Lưu kết quả tự chấm Mục B    
        $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();
        foreach ($ket_qua_muc_B as $ket_qua) {
            $ket_qua->delete();
        }
        for ($i = 1; $i <= 50; $i++) {
            if ($request->input($i . '_noi_dung_nhiem_vu') != null) {
                $ket_qua_muc_B = new KetQuaMucB();
                $ket_qua_muc_B->ma_phieu_danh_gia = $ma_phieu_danh_gia;
                $ket_qua_muc_B->noi_dung = $request->input($i . '_noi_dung_nhiem_vu');
                $ket_qua_muc_B->nhiem_vu_phat_sinh = $request->input($i . '_nhiem_vu_phat_sinh');
                $ket_qua_muc_B->hoan_thanh_nhiem_vu = $request->input($i . '_hoan_thanh_nhiem_vu');
                $ket_qua_muc_B->save();
            };
        }

        // Lưu kết quả Lý do điểm Cộng
        $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $ly_do_diem_cong->noi_dung = $request->ly_do_diem_cong;
        $ly_do_diem_cong->save();

        // Lưu kết quả Lý do điểm trừ
        $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $ly_do_diem_tru->noi_dung = $request->ly_do_diem_tru;
        $ly_do_diem_tru->save();

        // Lưu kết quả Phiếu đánh giá cá nhân tự đánh giá
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $phieu_danh_gia->diem_tu_cham = $diem_tu_cham;
        $phieu_danh_gia->diem_cong_tu_cham = $diem_cong_tu_cham;
        $phieu_danh_gia->diem_tru_tu_cham = $diem_tru_tu_cham;
        $phieu_danh_gia->tong_diem_tu_cham = $tong_diem_tu_cham;
        $phieu_danh_gia->ca_nhan_tu_xep_loai = $ca_nhan_tu_xep_loai;
        //if (Auth::user()->ma_chuc_vu == "01") $phieu_danh_gia->ket_qua_xep_loai = $ca_nhan_tu_xep_loai;
        $phieu_danh_gia->save();

        return redirect()->route(
            'phieudanhgia.canhan.show',
            [
                'id' => $phieu_danh_gia->ma_phieu_danh_gia
            ]
        )->with('msg_success', 'Đã cập nhật thành công Phiếu đánh giá. Bạn vui lòng kiểm tra lại kỹ trước khi gửi Phiếu.');
    }


    public function canhanShow($ma_phieu_danh_gia)
    {
        //Tìm Phiếu đánh giá
        $phieu_danh_gia = $this->timPhieuDanhGia($ma_phieu_danh_gia);

        //Lấy dữ liệu mục A
        $ket_qua_muc_A = $this->timKetQuaMucA($phieu_danh_gia);

        //Lấy dữ liệu mục B
        $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();

        //Lấy dữ liệu Lý do điểm cộng
        $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

        //Lấy dữ liệu Lý do điểm trừ
        $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

        //Lấy thông tin Mẫu phiếu đánh giá
        $thong_tin_mau_phieu = $this->thongTinMauPhieu($phieu_danh_gia->mau_phieu_danh_gia);

        $thoi_diem_danh_gia = Carbon::create($phieu_danh_gia->thoi_diem_danh_gia);
        $ngay_thuc_hien_danh_gia = Carbon::create($phieu_danh_gia->created_at);
        $xep_loai = XepLoai::all();

        return view(
            'danhgia.canhan_show',
            [
                'phieu_danh_gia' => $phieu_danh_gia,
                'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                'xep_loai' => $xep_loai,
                'ket_qua_muc_A' => $ket_qua_muc_A,
                'ket_qua_muc_B' => $ket_qua_muc_B,
                'ly_do_diem_cong' => $ly_do_diem_cong,
                'ly_do_diem_tru' => $ly_do_diem_tru,
            ]
        );
    }


    public function canhanSend($ma_phieu_danh_gia)
    {
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)
            ->first();
        $phieu_danh_gia->ma_trang_thai = 13;
        $phieu_danh_gia->save();

        return redirect()->route(
            'phieudanhgia.canhan.show',
            ['id' => $ma_phieu_danh_gia]
        )->with('msg_success', 'Đã gửi Phiếu đánh giá thành công');
    }


    public function canhanList()
    {
        $danh_sach_tu_danh_gia = PhieuDanhGia::where('phieu_danh_gia.so_hieu_cong_chuc', Auth::user()->so_hieu_cong_chuc)
            ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
            ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
            ->get();

        return view('danhgia.canhan_list', ['danh_sach' => $danh_sach_tu_danh_gia]);
    }

    //////////////// Cấp tham mưu đánh giá, xếp loại //////////////////////

    //Danh sách Phiếu đánh giá cấp trên cần thực hiện đánh giá
    public function captrenList()
    {
        // Nếu người dùng không có thẩm quyền tham mưu đánh giá Phiếu đánh giá thì chuyển hướng về trang 404
        if (in_array(Auth::user()->ma_chuc_vu, [null, '02', '06', '07', '08', '10'])) {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        } else {
            if (Auth::user()->ma_chuc_vu == "01") {
                // Nếu Người dùng có chức vụ Cục Trưởng
                // Đánh giá cho: 02-Phó Cục trưởng; 03-Chi Cục trưởng; 04-Chánh Văn phòng; 05-Trưởng phòng
                $danh_sach_cap_tren_danh_gia = PhieuDanhGia::wherein('phieu_danh_gia.ma_trang_thai', [13, 15])
                    ->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05'])
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                    ->orderBy('users.ma_don_vi', 'ASC')
                    ->orderBy('users.ma_phong', 'ASC')
                    ->get();
            } elseif (Auth::user()->ma_chuc_vu == "03") {
                // Nếu Người dùng có chức vụ Chi cục Trưởng
                // Đánh giá cho: 06-Phó chi Cục trưởng; 09-Đội trưởng
                $danh_sach_cap_tren_danh_gia = PhieuDanhGia::wherein('phieu_danh_gia.ma_trang_thai', [13, 15])
                    ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                    ->wherein('phieu_danh_gia.ma_chuc_vu', ['06', '09'])
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                    ->get();
            } elseif ((Auth::user()->ma_chuc_vu == "04") || (Auth::user()->ma_chuc_vu == "05") || (Auth::user()->ma_chuc_vu == "09")) {
                //Nếu Người dùng có chức vụ Chánh Văn phòng, Trưởng phòng hoặc Đội trưởng
                $danh_sach_cap_tren_danh_gia = PhieuDanhGia::wherein('phieu_danh_gia.ma_trang_thai', [13, 15])
                    ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                    ->where('phieu_danh_gia.ma_phong', Auth::user()->ma_phong)
                    ->where('phieu_danh_gia.so_hieu_cong_chuc', '<>', Auth::user()->so_hieu_cong_chuc)
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                    ->get();
            } else {
                $danh_sach_cap_tren_danh_gia = Null;
            }

            return view('danhgia.captren_list', ['danh_sach' => $danh_sach_cap_tren_danh_gia]);
        }
    }


    // Cấp trên đánh giá cho cấp dưới
    public function captrenCreate($ma_phieu_danh_gia)
    {
        // Nếu người dùng không có thẩm quyền tham mưu đánh giá Phiếu đánh giá thì chuyển hướng về trang 404
        if (Auth::user()->ma_chuc_vu == null) {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        } else {
            // Tìm Phiếu đánh giá
            $phieu_danh_gia = $this->timPhieuDanhGia($ma_phieu_danh_gia);

            // Lấy dữ liệu mục A
            $ket_qua_muc_A = $this->timKetQuaMucA($phieu_danh_gia);

            // Lấy dữ liệu mục B
            $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();

            // Lấy dữ liệu Lý do điểm cộng
            $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

            // Lấy dữ liệu Lý do điểm trừ
            $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

            // Lấy thông tin Mẫu phiếu đánh giá
            $thong_tin_mau_phieu = $this->thongTinMauPhieu($phieu_danh_gia->mau_phieu_danh_gia);
            $thoi_diem_danh_gia = Carbon::create($phieu_danh_gia->thoi_diem_danh_gia);
            $ngay_thuc_hien_danh_gia = Carbon::create($phieu_danh_gia->created_at);
            $xep_loai = XepLoai::all();

            return view(
                'danhgia.captren_create',
                [
                    'phieu_danh_gia' => $phieu_danh_gia,
                    'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                    'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                    'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                    'xep_loai' => $xep_loai,
                    'ket_qua_muc_A' => $ket_qua_muc_A,
                    'ket_qua_muc_B' => $ket_qua_muc_B,
                    'ly_do_diem_cong' => $ly_do_diem_cong,
                    'ly_do_diem_tru' => $ly_do_diem_tru,
                ]
            );
        }
    }


    // Lưu Kết quả cấp trên đánh giá
    public function captrenStore($ma_phieu_danh_gia, Request $request)
    {
        // Tính Tổng điểm cấp trên đánh giá
        $diem_danh_gia = $request->tc_300;
        $diem_cong_danh_gia = $request->tc_400;
        $diem_tru_danh_gia = $request->tc_500;
        $tong_diem_danh_gia = $this->tinhTongDiem($request);

        // Kết quả cấp trên xếp loại
        $ket_qua_xep_loai = $this->xepLoai($tong_diem_danh_gia);

        // Cập nhật kết quả cấp trên đánh giá cho Mục A
        $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();
        foreach ($ket_qua_muc_A as $ket_qua) {
            $data = $ket_qua_muc_A->where('ma_tieu_chi', $ket_qua->ma_tieu_chi)->first();
            $data->diem_danh_gia = $request->input($ket_qua->ma_tieu_chi);
            $data->save();
        }

        // Lưu kết quả Phiếu đánh giá cấp trên đánh giá     
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $phieu_danh_gia->diem_danh_gia = $diem_danh_gia;
        $phieu_danh_gia->diem_cong_danh_gia = $diem_cong_danh_gia;
        $phieu_danh_gia->diem_tru_danh_gia = $diem_tru_danh_gia;
        $phieu_danh_gia->tong_diem_danh_gia = $tong_diem_danh_gia;
        $phieu_danh_gia->ket_qua_xep_loai = $ket_qua_xep_loai;
        $phieu_danh_gia->ma_cap_tren_danh_gia = Auth::user()->so_hieu_cong_chuc;
        $phieu_danh_gia->ma_trang_thai = 15;
        $phieu_danh_gia->save();

        return redirect()->route(
            'phieudanhgia.captren.show',
            ['id' => $phieu_danh_gia->ma_phieu_danh_gia]
        )->with('msg_success', 'Cấp trên đã thực hiện đánh giá thành công');
    }


    public function captrenShow($ma_phieu_danh_gia)
    {
        //Tìm Phiếu đánh giá
        $phieu_danh_gia = $this->timPhieuDanhGia($ma_phieu_danh_gia);

        //Lấy dữ liệu mục A
        $ket_qua_muc_A = $this->timKetQuaMucA($phieu_danh_gia);
        //Lấy dữ liệu mục B
        $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();
        //Lấy dữ liệu Lý do điểm cộng
        $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        //Lấy dữ liệu Lý do điểm trừ
        $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

        //Lấy thông tin Mẫu phiếu đánh giá
        $thong_tin_mau_phieu = $this->thongTinMauPhieu($phieu_danh_gia->mau_phieu_danh_gia);
        $thoi_diem_danh_gia = Carbon::create($phieu_danh_gia->thoi_diem_danh_gia);
        $ngay_thuc_hien_danh_gia = Carbon::create($phieu_danh_gia->created_at);
        $xep_loai = XepLoai::all();

        return view(
            'danhgia.captren_show',
            [
                'phieu_danh_gia' => $phieu_danh_gia,
                'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                'xep_loai' => $xep_loai,
                'ket_qua_muc_A' => $ket_qua_muc_A,
                'ket_qua_muc_B' => $ket_qua_muc_B,
                'ly_do_diem_cong' => $ly_do_diem_cong,
                'ly_do_diem_tru' => $ly_do_diem_tru,
            ]
        );
    }


    public function captrenSend()
    {
        if (Auth::user()->ma_chuc_vu == "01") {
            // Nếu Người dùng có chức vụ Cục Trưởng
            // Gửi Đánh giá của: 02-Phó Cục trưởng; 03-Chi Cục trưởng; 04-Chánh Văn phòng; 05-Trưởng phòng
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', 15)
                ->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05'])
                ->get();
        } elseif (Auth::user()->ma_chuc_vu == "03") {
            // Nếu Người dùng có chức vụ Chi cục Trưởng
            // Gửi Đánh giá cho: 06-Phó chi Cục trưởng; 09-Đội trưởng
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', 15)
                ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->wherein('phieu_danh_gia.ma_chuc_vu', ['06', '09'])
                ->get();
        } elseif ((Auth::user()->ma_chuc_vu == "04") || (Auth::user()->ma_chuc_vu == "05") || (Auth::user()->ma_chuc_vu == "09")) {
            // Nếu Người dùng có chức vụ Chánh Văn phòng, Trưởng phòng hoặc Đội trưởng
            // Gửi Đánh giá của cấp phó và công chức không giữ chức vụ quản lý thuộc phòng
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', 15)
                ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->where('phieu_danh_gia.ma_phong', Auth::user()->ma_phong)
                ->where('phieu_danh_gia.so_hieu_cong_chuc', '<>', Auth::user()->so_hieu_cong_chuc)
                ->get();
        } else {
            $danh_sach = null;
        }

        if ($danh_sach->isEmpty() || $danh_sach == null)
            return redirect()->route('phieudanhgia.captren.list')->with('msg_error', 'Danh sách trống / Có phiếu chưa được cấp tham mưu đánh giá');

        foreach ($danh_sach as $list) {
            if (in_array($list->ma_chuc_vu, ['06', '09', '10'])) $list->ma_trang_thai = 16;
            else $list->ma_trang_thai = 17;
            $list->save();
        }

        return redirect()->route('phieudanhgia.captren.list')->with('msg_success', 'Đã gửi thành công Danh sách phiếu đánh giá');
    }


    public function captrenSendBack($ma_phieu_danh_gia)
    {
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $phieu_danh_gia->ma_trang_thai = 11;
        $phieu_danh_gia->save();

        return redirect()->route('phieudanhgia.captren.list')->with('msg_success', 'Đã gửi trả Phiếu đánh giá');
    }


    //////////////////////// Cấp quyết định đánh giá, xếp loại ////////////////////////////////
    // Danh sách Phiếu đánh giá cần phê duyệt
    public function capqdList()
    {
        // Nếu người dùng không phải thành viên Hội đồng hoặc Cục trưởng hoặc Chi cục trưởng thì điều hướng về trang 404
        if (!in_array(Auth::user()->ma_chuc_vu, ['01', '03'])) {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        } else {
            if (Auth::user()->hoi_dong_phe_duyet == 1) {
                // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
                $danh_sach_hoi_dong = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '13')
                    ->where('phieu_danh_gia.ma_chuc_vu', '01')
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                    ->get();
            } else {
                $danh_sach_hoi_dong = null;
            }

            if (Auth::user()->ma_chuc_vu == "01") {
                // Nếu Người dùng có chức vụ Cục Trưởng
                // Phê duyệt đánh giá cho: 02-Phó Cục trưởng; 03-Chi cục trưởng; 04-Chánh Văn phòng; 05-Trưởng phòng; 06-Phó Chi cục Trưởng; 
                // 07-Phó Chánh Văn phòng; 08-Phó Trưởng phòng; 09-Đội trưởng; 10-Phó Đội Trưởng; Công chức không giữ chức vụ lãnh đạo thuộc Văn phòng, Phòng của Cục thuế            
                $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                    ->where(function ($query) {
                        $query->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05', '06', '07', '08', '09', '10'])
                            ->orwhere('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi);
                    })
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                    ->orderBy('users.ma_don_vi', 'ASC')
                    ->orderBy('users.ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                    ->get();
            } elseif (Auth::user()->ma_chuc_vu == "03") {
                // Nếu Người dùng có chức vụ Chi cục Trưởng
                // Phê duyệt cho Công chức thuộc Chi cục     
                $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                    ->where('phieu_danh_gia.ma_trang_thai', '17')
                    ->where('phieu_danh_gia.ma_chuc_vu', null)
                    ->orwhere('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                    ->where('phieu_danh_gia.ma_trang_thai', '16')
                    ->where('phieu_danh_gia.ma_chuc_vu', '<>', null)
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                    ->orderBy('users.ma_don_vi', 'ASC')
                    ->orderBy('users.ma_phong', 'ASC')
                    ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                    ->get();
            } else {
                $danh_sach = Null;
            }

            if (($danh_sach_hoi_dong != null) && ($danh_sach != null)) {
                $danh_sach_phe_duyet = $danh_sach_hoi_dong->merge($danh_sach);
            } elseif (($danh_sach_hoi_dong != null) && ($danh_sach == null)) {
                $danh_sach_phe_duyet = $danh_sach_hoi_dong;
            } elseif (($danh_sach_hoi_dong == null) && ($danh_sach != null)) {
                $danh_sach_phe_duyet = $danh_sach;
            } else {
                $danh_sach_phe_duyet = null;
            }

            return view('danhgia.capqd_list', ['danh_sach' => $danh_sach_phe_duyet]);
        }
    }



    public function capQDPheDuyetThang()
    {
        if (Auth::user()->hoi_dong_phe_duyet == 1) {
            // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
            $danh_sach_hoi_dong = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '13')
                ->where('phieu_danh_gia.ma_chuc_vu', '01')
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->get();
        } else {
            $danh_sach_hoi_dong = null;
        }

        if (Auth::user()->ma_chuc_vu == "01") {
            // Nếu Người dùng có chức vụ Cục Trưởng
            // Phê duyệt đánh giá cho: 02-Phó Cục trưởng; 03-Chi cục trưởng; 04-Chánh Văn phòng; 05-Trưởng phòng; 06-Phó Chi cục Trưởng; 
            // 07-Phó Chánh Văn phòng; 08-Phó Trưởng phòng; 09-Đội trưởng; 10-Phó Đội Trưởng; Công chức không giữ chức vụ lãnh đạo thuộc Văn phòng, Phòng của Cục thuế            
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                ->where(function ($query) {
                    $query->wherein('phieu_danh_gia.ma_chuc_vu', ['02', '03', '04', '05', '06', '07', '08', '09', '10'])
                        ->orwhere('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi);
                })
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
        } elseif (Auth::user()->ma_chuc_vu == "03") {
            // Nếu Người dùng có chức vụ Chi cục Trưởng
            // Đánh giá cho Công chức không giữ chức vụ lãnh đạo thuộc Chi cục     
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->where('phieu_danh_gia.ma_trang_thai', '17')
                ->where('phieu_danh_gia.ma_chuc_vu', null)
                ->orwhere('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->where('phieu_danh_gia.ma_trang_thai', '16')
                ->where('phieu_danh_gia.ma_chuc_vu', '<>', null)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.thoi_diem_danh_gia', 'DESC')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = Null;
        }

        if (($danh_sach_hoi_dong != null) && ($danh_sach != null)) {
            $danh_sach_phe_duyet = $danh_sach_hoi_dong->merge($danh_sach);
        } elseif (($danh_sach_hoi_dong != null) && ($danh_sach == null)) {
            $danh_sach_phe_duyet = $danh_sach_hoi_dong;
        } elseif (($danh_sach_hoi_dong == null) && ($danh_sach != null)) {
            $danh_sach_phe_duyet = $danh_sach;
        } else {
            $danh_sach_phe_duyet = null;
        }

        // if ($danh_sach_phe_duyet != null) $this->kQXLThang($danh_sach_phe_duyet);

        if ($danh_sach_phe_duyet != null) {
            foreach ($danh_sach_phe_duyet as $ds) {
                if ($ds->ma_chuc_vu == '01') {
                    $ds->tong_diem_danh_gia = $ds->tong_diem_tu_cham;
                    $ds->ket_qua_xep_loai = $ds->ca_nhan_tu_xep_loai;
                }
                $ds->ma_cap_tren_phe_duyet = Auth::user()->so_hieu_cong_chuc;
                if (in_array($ds->ma_chuc_vu, ['06', '09', '10'])) {
                    if ($ds->ma_trang_thai == 16) $ds->ma_trang_thai = 17;
                    elseif ($ds->ma_trang_thai == 17) $ds->ma_trang_thai = 19;
                } else $ds->ma_trang_thai = 19;
                $ds->save();
            }

            // Lưu kết quả vào Bảng kết quả xếp loại tháng
            $this->kQXLThang($danh_sach_phe_duyet); //if (!in_array($danh_sach->ma_chuc_vu, ['06', '09', '10'])) {}

            return redirect()->route('phieudanhgia.capqd.list')->with('msg_success', 'Đã phê duyệt thành công Danh sách phiếu đánh giá');
        } else {
            return redirect()->route('phieudanhgia.capqd.list')->with('msg_error', 'Danh sách phê duyệt trống');
        }
    }


    public function capqdSendBack($ma_phieu_danh_gia)
    {
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        if ($phieu_danh_gia->ma_chuc_vu == '01') {
            $phieu_danh_gia->ma_trang_thai = 11;
        } else {
            $phieu_danh_gia->ma_trang_thai = 15;
        }
        $phieu_danh_gia->save();

        return redirect()->route('phieudanhgia.capqd.list')->with('msg_success', 'Đã gửi trả Phiếu đánh giá');
    }


    // Danh sách Thông báo Kết quả xếp loại theo tháng
    public function thongBaoThang(Request $request)
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
                ->where('phieu_danh_gia.ma_trang_thai', '>=', 19)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', '>=', 19)
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('danhgia.thongbaothang', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'ds_don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
        ]);
    }


    // Báo cáo tổng hợp kết quả tháng
    public function baoCaoThang(Request $request)
    {
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        $xep_loai = XepLoai::all();

        if (!isset($request->ma_don_vi_da_chon)) {
            $ds_don_vi = DonVi::all();
            $ma_don_vi = 4400;
        } else {
            $ds_don_vi = DonVi::all();
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        // Danh sách phiếu đánh giá trong tháng
        if ($ma_don_vi == 4400) {
            $phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_trang_thai', '>=', 19)
                // ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                // ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                // ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                // ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                // ->select('phieu_danh_gia.*', 'users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->get();
        } else {
            $phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('ma_trang_thai', '>=', 19)
                ->where('ma_don_vi', $ma_don_vi)
                ->get();
        }

        $list = collect();
        $danh_sach_lanh_dao_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["01", "02"]);
        $danh_sach_lanh_dao_phong = $phieu_danh_gia->wherein('ma_chuc_vu', ["04", "05", "07", "08"]);
        $danh_sach_lanh_dao_chi_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["03", "06"]);
        $danh_sach_lanh_dao_doi_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["09", "10"]);
        $danh_sach_cong_chuc = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01B');
        $danh_sach_hop_dong = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01C');
        $danh_sach_tong = $phieu_danh_gia;

        $list->push($danh_sach_lanh_dao_cuc_thue);
        $list->push($danh_sach_lanh_dao_phong);
        $list->push($danh_sach_lanh_dao_chi_cuc_thue);
        $list->push($danh_sach_lanh_dao_doi_thue);
        $list->push($danh_sach_cong_chuc);
        $list->push($danh_sach_hop_dong);
        $list->push($danh_sach_tong);

        $danh_sach = collect();
        $ten = array(
            "Lãnh đạo Cục thuế",
            "Lãnh đạo Phòng, Văn phòng",
            "Lãnh đạo Chi cục Thuế",
            "Lãnh đạo Đội thuế",
            "Công chức",
            "Hợp đồng lao động",
            "Tổng",
        );
        $i = 0;

        foreach ($list as $ls) {
            // Số lượng Lãnh đạo Cục thuế            
            $ten_don_vi = $ten[$i];
            $so_luong = $ls->count();

            foreach ($xep_loai as $xl) {
                // Số lượng xếp loại 
                ${"so_luong_loai_" . $xl->ma_xep_loai} = $ls->where('ket_qua_xep_loai', $xl->ma_xep_loai)->count();
                // Tỉ lệ xếp loại
                if ($so_luong <> 0) {
                    ${"ti_le_loai_" . $xl->ma_xep_loai} = round(${"so_luong_loai_" . $xl->ma_xep_loai} / $so_luong * 100, 2);
                } else {
                    ${"ti_le_loai_" . $xl->ma_xep_loai} = 0;
                }

                $so_luong_khong_xep_loai =  $ls->where("ket_qua_xep_loai", "K")->count();
                if ($so_luong <> 0) {
                    $ti_le_khong_xep_loai = round($so_luong_khong_xep_loai / $so_luong * 100, 2);
                } else {
                    $ti_le_khong_xep_loai = 0;
                }
            }

            $i++;
            if ($i == 7) $i = "";
            //Đưa vào danh sách
            $danh_sach->push([
                'stt' => $i,
                'ten_don_vi' => $ten_don_vi,
                'so_luong' => $so_luong,
                'so_luong_loai_A' => $so_luong_loai_A,
                'ti_le_loai_A' => $ti_le_loai_A,
                'so_luong_loai_B' => $so_luong_loai_B,
                'ti_le_loai_B' => $ti_le_loai_B,
                'so_luong_loai_C' => $so_luong_loai_C,
                'ti_le_loai_C' => $ti_le_loai_C,
                'so_luong_loai_D' => $so_luong_loai_D,
                'ti_le_loai_D' => $ti_le_loai_D,
                'so_luong_khong_xep_loai' => $so_luong_khong_xep_loai,
                'ti_le_khong_xep_loai' => $ti_le_khong_xep_loai
            ]);
        }

        return view(
            'danhgia.baocaothang',
            [
                'danh_sach' => $danh_sach,
                'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                'don_vi' => $ds_don_vi,
                'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon,
            ]
        );
    }


    // Danh sách kết quả xếp loại theo quý
    public function capQDDSQuy(Request $request)
    {
        // Nếu người dùng không phải thành viên Hội đồng hoặc Cục trưởng hoặc Chi cục trưởng thì điều hướng về trang 404
        if ((Auth::user()->hoi_dong_phe_duyet <> 1) and (!in_array(Auth::user()->ma_chuc_vu, ['01', '03']))) {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        } else {
            // Lấy danh sách Hội đồng phê duyệt
            if (Auth::user()->hoi_dong_phe_duyet == 1) {
                // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
                $user_list_hd = $this->danhSachHoiDongPheDuyet();
            } else {
                $user_list_hd = null;
            }

            // Lấy danh sách Cục trưởng/Chi cục trưởng phê duyệt
            if (Auth::user()->ma_chuc_vu == "01") {
                // Nếu Người dùng có chức vụ Cục Trưởng 
                $user_list_cm = $this->danhSachCucTruongPheDuyet();
            } elseif (Auth::user()->ma_chuc_vu == "03") {
                // Nếu Người dùng có chức vụ Chi cục Trưởng 
                $user_list_cm = $this->danhSachChiCucTruongPheDuyet();
            } else {
                $user_list_cm = null;
            }

            // Nối 2 danh sách Hội đồng + Cục trưởng/Chi cục trưởng
            if (($user_list_hd != null) && ($user_list_cm != null)) {
                $user_list = $user_list_hd->merge($user_list_cm);
            } elseif (($user_list_hd != null) && ($user_list_cm == null)) {
                $user_list = $user_list_hd;
            } elseif (($user_list_hd == null) && ($user_list_cm != null)) {
                $user_list = $user_list_cm;
            } else {
                $user_list = null;
            }

            // Xác định tháng đánh giá, quý đánh giá
            if (isset($request->quy_danh_gia)) {
                $thang_dau_tien = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->endOfMonth();
                $thang_thu_hai = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->addMonth()->endOfMonth();
                $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->lastOfQuarter()->endOfMonth();
                $quy_danh_gia = $request->quy_danh_gia;
                $nam_danh_gia = $request->nam_danh_gia;
            } else {
                $thang_dau_tien = Carbon::now()->subQuarter()->firstOfQuarter()->endOfMonth();
                $thang_thu_hai = Carbon::now()->subQuarter()->firstOfQuarter()->addMonth()->endOfMonth();
                $thang_cuoi_cung = Carbon::now()->subQuarter()->lastOfQuarter()->endOfMonth();
                $quy_danh_gia = Carbon::now()->subQuarter()->quarter;
                $nam_danh_gia = Carbon::now()->subQuarter()->year;
            }

            if ($quy_danh_gia == "1") {
                $list_thang = ["1", "2", "3"];
            } elseif ($quy_danh_gia == "2") {
                $list_thang = ["4", "5", "6"];
            } elseif ($quy_danh_gia == "3") {
                $list_thang = ["7", "8", "9"];
            } elseif ($quy_danh_gia == "4") {
                $list_thang = ["10", "11", "12"];
            }


            // Thực hiện xếp loại theo quý
            $collection = collect();
            $xep_loai_1 = null;
            $xep_loai_2 = null;
            $xep_loai_3 = null;

            if (isset($user_list)) {
                foreach ($user_list as $user) {
                    // Tìm kết quả xếp loại các tháng trong quý
                    $xep_loai_1 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                        ->where('thoi_diem_danh_gia', $thang_dau_tien->toDateString())
                        ->where('ma_trang_thai', 19)
                        ->first();
                    if (isset($xep_loai_1)) $xep_loai_1 = $xep_loai_1->ket_qua_xep_loai;


                    $xep_loai_2 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                        ->where('thoi_diem_danh_gia', $thang_thu_hai->toDateString())
                        ->where('ma_trang_thai', 19)
                        ->first();
                    if (isset($xep_loai_2)) $xep_loai_2 = $xep_loai_2->ket_qua_xep_loai;

                    $xep_loai_3 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                        ->where('thoi_diem_danh_gia', $thang_cuoi_cung->toDateString())
                        ->where('ma_trang_thai', 19)
                        ->first();
                    if (isset($xep_loai_3)) $xep_loai_3 = $xep_loai_3->ket_qua_xep_loai;

                    // Tính toán xếp loại quý
                    $countA = 0;
                    $countB = 0;
                    $countC = 0;
                    $countD = 0;

                    for ($i = 1; $i <= 3; $i++) {
                        if (${"xep_loai_" . $i} == "A") $countA++;
                        elseif (${"xep_loai_" . $i} == "B") $countB++;
                        elseif (${"xep_loai_" . $i} == "C") $countC++;
                        elseif (${"xep_loai_" . $i} == "D")  $countD++;
                    }

                    $ket_qua_xep_loai = null;
                    if (($xep_loai_1 == null) || ($xep_loai_2 == null) || ($xep_loai_3 == null)) $ket_qua_xep_loai = null;
                    elseif (($countA >= 2) && ($countC == 0) && ($countD == 0)) $ket_qua_xep_loai = "A";
                    elseif (((($countA >= 2) || ($countB >= 2)) || (($countA >= 1) && ($countB >= 1))) && ($countD == 0)) $ket_qua_xep_loai = "B";
                    elseif ((($countA > 0) || ($countB > 0) || ($countC > 0)) && ($countD == 0)) $ket_qua_xep_loai = "C";
                    elseif ($countD > 0) $ket_qua_xep_loai = "D";
                    else $ket_qua_xep_loai = null;


                    // Đưa vào danh sách
                    if ($ket_qua_xep_loai != null) {
                        $collection->push([
                            'name' => $user->name,
                            'ten_chuc_vu' => $user->ten_chuc_vu,
                            'ten_phong' => $user->ten_phong,
                            'ten_don_vi' => $user->ten_don_vi,
                            'xep_loai_1' => $xep_loai_1,
                            'xep_loai_2' => $xep_loai_2,
                            'xep_loai_3' => $xep_loai_3,
                            'ket_qua_xep_loai' => $ket_qua_xep_loai,
                        ]);
                    }
                }
            }

            return view(
                'danhgia.capqd_list_quy',
                [
                    'danh_sach' => $collection,
                    'thang' => $list_thang,
                    'quy_danh_gia' => $quy_danh_gia,
                    'nam_danh_gia' => $nam_danh_gia,
                ]
            );
        }
    }


    // Phê duyệt Danh sách kết quả xếp loại theo quý
    public function capQDPheDuyetDSQuy(Request $request)
    {
        // Lấy danh sách Hội đồng phê duyệt
        if (Auth::user()->hoi_dong_phe_duyet == 1) {
            // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
            $user_list_hd = $this->danhSachHoiDongPheDuyet();
        } else {
            $user_list_hd = null;
        }

        // Lấy danh sách Cục trưởng/Chi cục trưởng phê duyệt
        if (Auth::user()->ma_chuc_vu == "01") {
            // Nếu Người dùng có chức vụ Cục Trưởng 
            $user_list_cm = $this->danhSachCucTruongPheDuyet();
        } elseif (Auth::user()->ma_chuc_vu == "03") {
            // Nếu Người dùng có chức vụ Chi cục Trưởng 
            $user_list_cm = $this->danhSachChiCucTruongPheDuyet();
        } else {
            $user_list_cm = null;
        }

        // Nối 2 danh sách Hội đồng + Cục trưởng/Chi cục trưởng
        if (($user_list_hd != null) && ($user_list_cm != null)) {
            $user_list = $user_list_hd->merge($user_list_cm);
        } elseif (($user_list_hd != null) && ($user_list_cm == null)) {
            $user_list = $user_list_hd;
        } elseif (($user_list_hd == null) && ($user_list_cm != null)) {
            $user_list = $user_list_cm;
        } else {
            $user_list = null;
        }

        // Xác định tháng đánh giá, quý đánh giá
        if (isset($request->quy_danh_gia)) {
            $thang_dau_tien = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = $request->quy_danh_gia;
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $thang_dau_tien = Carbon::now()->subQuarter()->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::now()->subQuarter()->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::now()->subQuarter()->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = Carbon::now()->subQuarter()->quarter;
            $nam_danh_gia = Carbon::now()->subQuarter()->year;
        }

        if ($quy_danh_gia == "1") {
            $list_thang = ["1", "2", "3"];
        } elseif ($quy_danh_gia == "2") {
            $list_thang = ["4", "5", "6"];
        } elseif ($quy_danh_gia == "3") {
            $list_thang = ["7", "8", "9"];
        } elseif ($quy_danh_gia == "4") {
            $list_thang = ["10", "11", "12"];
        }

        // Thực hiện xếp loại theo quý
        $xep_loai_1 = null;
        $xep_loai_2 = null;
        $xep_loai_3 = null;

        foreach ($user_list as $user) {
            // Tìm kết quả xếp loại các tháng trong quý
            $xep_loai_1 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                ->where('thoi_diem_danh_gia', $thang_dau_tien->toDateString())
                ->where('ma_trang_thai', 19)
                ->first();
            if (isset($xep_loai_1)) {
                $xep_loai_1->ma_trang_thai = 21;
                $xep_loai_1->save();
                $xep_loai_1 = $xep_loai_1->ket_qua_xep_loai;
            }

            $xep_loai_2 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                ->where('thoi_diem_danh_gia', $thang_thu_hai->toDateString())
                ->where('ma_trang_thai', 19)
                ->first();
            if (isset($xep_loai_2)) {
                $xep_loai_2->ma_trang_thai = 21;
                $xep_loai_2->save();
                $xep_loai_2 = $xep_loai_2->ket_qua_xep_loai;
            }

            $xep_loai_3 = PhieuDanhGia::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
                ->where('thoi_diem_danh_gia', $thang_cuoi_cung->toDateString())
                ->where('ma_trang_thai', 19)
                ->first();
            if (isset($xep_loai_3)) {
                $xep_loai_3->ma_trang_thai = 21;
                $xep_loai_3->save();
                $xep_loai_3 = $xep_loai_3->ket_qua_xep_loai;
            }

            // Tính toán xếp loại quý
            $countA = 0;
            $countB = 0;
            $countC = 0;
            $countD = 0;

            for ($i = 1; $i <= 3; $i++) {
                if (${"xep_loai_" . $i} == "A") $countA++;
                elseif (${"xep_loai_" . $i} == "B") $countB++;
                elseif (${"xep_loai_" . $i} == "C") $countC++;
                elseif (${"xep_loai_" . $i} == "D")  $countD++;
            }

            $ket_qua_xep_loai = null;
            if (($xep_loai_1 == null) || ($xep_loai_2 == null) || ($xep_loai_3 == null)) $ket_qua_xep_loai = null;
            elseif (($countA >= 2) && ($countC == 0) && ($countD == 0)) $ket_qua_xep_loai = "A";
            elseif (((($countA >= 2) || ($countB >= 2)) || (($countA >= 1) && ($countB >= 1))) && ($countD == 0)) $ket_qua_xep_loai = "B";
            elseif ((($countA > 0) || ($countB > 0) || ($countC > 0)) && ($countD == 0)) $ket_qua_xep_loai = "C";
            elseif ($countD > 0) $ket_qua_xep_loai = "D";

            if ($ket_qua_xep_loai != null) {
                $this->kQXLQuy($user, $ket_qua_xep_loai, $nam_danh_gia, $quy_danh_gia);
            }
        }

        return view(
            'danhgia.capqd_list_quy',
            [
                'thang' => $list_thang,
                'quy_danh_gia' => $quy_danh_gia,
                'nam_danh_gia' => $nam_danh_gia,
            ]
        )->with('msg_success', 'Đã phê duyệt thành công Danh sách phiếu đánh giá');
    }


    // Danh sách Thông báo Kết quả xếp loại theo Quý
    public function thongBaoQuy(Request $request)
    {
        //Xác định quý đánh giá, năm đánh giá
        if (isset($request->quy_danh_gia)) {
            $thang_dau_tien = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = $request->quy_danh_gia;
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $thang_dau_tien = Carbon::now()->subQuarter()->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::now()->subQuarter()->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::now()->subQuarter()->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = Carbon::now()->subQuarter()->quarter;
            $nam_danh_gia = Carbon::now()->subQuarter()->year;
        }

        // Trường hợp không chọn đơn vị
        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        if ($ma_don_vi == 4400) {
            $danh_sach = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
                ->leftjoin('kqxl_thang', 'kqxl_quy.so_hieu_cong_chuc', 'kqxl_thang.so_hieu_cong_chuc')
                ->leftjoin('phieu_danh_gia', 'kqxl_quy.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung->toDateString())
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('kqxl_quy.*', 'kqxl_thang.*', 'users.name', 'chuc_vu.ma_chuc_vu', 'chuc_vu.ten_chuc_vu', 'phong.ma_phong', 'don_vi.ma_don_vi')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        } else {
            $danh_sach = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
                ->leftjoin('kqxl_thang', 'kqxl_quy.so_hieu_cong_chuc', 'kqxl_thang.so_hieu_cong_chuc')
                ->leftjoin('phieu_danh_gia', 'kqxl_quy.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung->toDateString())
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('kqxl_quy.*', 'kqxl_thang.*', 'users.name', 'chuc_vu.ma_chuc_vu', 'chuc_vu.ten_chuc_vu', 'phong.ma_phong', 'don_vi.ma_don_vi')
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->orderBy('phieu_danh_gia.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(phieu_danh_gia.ma_chuc_vu), phieu_danh_gia.ma_chuc_vu ASC')
                ->get();
        }

        $ds_don_vi = DonVi::where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('danhgia.thongbaoquy', [
            //'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'ds_don_vi' => $ds_don_vi,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'quy_danh_gia' => $quy_danh_gia,
            'nam_danh_gia' => $nam_danh_gia,
            'thang_dau_tien' => $thang_dau_tien->month,
            'thang_thu_hai' => $thang_thu_hai->month,
            'thang_cuoi_cung' => $thang_cuoi_cung->month,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon
        ]);
    }


    // Báo cáo tổng hợp kết quả quý
    public function baoCaoQuy(Request $request)
    {
        if (isset($request->quy_danh_gia)) {
            $quy_danh_gia = $request->quy_danh_gia;
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $quy_danh_gia = Carbon::now()->subQuarter()->quarter;
            $nam_danh_gia = Carbon::now()->subQuarter()->year;
        }

        $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $quy_danh_gia * 3)->endOfMonth()->format("Ymd");

        $xep_loai = XepLoai::all();
        $ds_don_vi = DonVi::all();

        if (!isset($request->ma_don_vi_da_chon)) {
            $ma_don_vi = 4400;
        } else {
            $ma_don_vi = $request->ma_don_vi_da_chon;
        }

        // Danh sách phiếu đánh giá trong quý
        if ($ma_don_vi == 4400) {
            // Nếu Người dùng có chức vụ Cục Trưởng, Cục Phó, Chánh Văn phòng, Trưởng phòng Tổ chức cán bộ
            $phieu_danh_gia = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
                ->leftjoin('phieu_danh_gia', 'phieu_danh_gia.so_hieu_cong_chuc', 'kqxl_quy.so_hieu_cong_chuc')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung)
                ->select('kqxl_quy.*', 'phieu_danh_gia.mau_phieu_danh_gia', 'phieu_danh_gia.ma_chuc_vu')
                ->get();
        } else {
            $phieu_danh_gia = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
                ->leftjoin('phieu_danh_gia', 'phieu_danh_gia.so_hieu_cong_chuc', 'kqxl_quy.so_hieu_cong_chuc')
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung)
                ->where('phieu_danh_gia.ma_don_vi', $ma_don_vi)
                ->select('kqxl_quy.*', 'phieu_danh_gia.mau_phieu_danh_gia', 'phieu_danh_gia.ma_chuc_vu')
                ->get();
        }

        $list = collect();
        $danh_sach_lanh_dao_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["01", "02"]);
        $danh_sach_lanh_dao_phong = $phieu_danh_gia->wherein('ma_chuc_vu', ["04", "05", "07", "08"]);
        $danh_sach_lanh_dao_chi_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["03", "06"]);
        $danh_sach_lanh_dao_doi_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["09", "10"]);
        $danh_sach_cong_chuc = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01B');
        $danh_sach_hop_dong = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01C');
        $danh_sach_tong = $phieu_danh_gia;

        $list->push($danh_sach_lanh_dao_cuc_thue);
        $list->push($danh_sach_lanh_dao_phong);
        $list->push($danh_sach_lanh_dao_chi_cuc_thue);
        $list->push($danh_sach_lanh_dao_doi_thue);
        $list->push($danh_sach_cong_chuc);
        $list->push($danh_sach_hop_dong);
        $list->push($danh_sach_tong);

        $danh_sach = collect();
        $ten = array(
            "Lãnh đạo Cục thuế",
            "Lãnh đạo Phòng, Văn phòng",
            "Lãnh đạo Chi cục Thuế",
            "Lãnh đạo Đội thuế",
            "Công chức",
            "Hợp đồng lao động",
            "Tổng",
        );
        $i = 0;

        foreach ($list as $ls) {
            // Số lượng Lãnh đạo Cục thuế            
            $ten_don_vi = $ten[$i];
            $so_luong = $ls->count();

            foreach ($xep_loai as $xl) {
                // Số lượng xếp loại 
                ${"so_luong_loai_" . $xl->ma_xep_loai} = $ls->where('kqxl_q' . $quy_danh_gia, $xl->ma_xep_loai)->count();
                // Tỉ lệ xếp loại
                if ($so_luong <> 0) {
                    ${"ti_le_loai_" . $xl->ma_xep_loai} = round(${"so_luong_loai_" . $xl->ma_xep_loai} / $so_luong * 100, 2);
                } else {
                    ${"ti_le_loai_" . $xl->ma_xep_loai} = 0;
                }

                $so_luong_khong_xep_loai =  $ls->where('kqxl_q' . $quy_danh_gia, "K")->count();
                if ($so_luong <> 0) {
                    $ti_le_khong_xep_loai = round($so_luong_khong_xep_loai / $so_luong * 100, 2);
                } else {
                    $ti_le_khong_xep_loai = 0;
                }
            }

            $i++;
            if ($i == 7) $i = "";

            //Đưa vào danh sách
            $danh_sach->push([
                'stt' => $i,
                'ten_don_vi' => $ten_don_vi,
                'so_luong' => $so_luong,
                'so_luong_loai_A' => $so_luong_loai_A,
                'ti_le_loai_A' => $ti_le_loai_A,
                'so_luong_loai_B' => $so_luong_loai_B,
                'ti_le_loai_B' => $ti_le_loai_B,
                'so_luong_loai_C' => $so_luong_loai_C,
                'ti_le_loai_C' => $ti_le_loai_C,
                'so_luong_loai_D' => $so_luong_loai_D,
                'ti_le_loai_D' => $ti_le_loai_D,
                'so_luong_khong_xep_loai' => $so_luong_khong_xep_loai,
                'ti_le_khong_xep_loai' => $ti_le_khong_xep_loai
            ]);
        }

        return view('danhgia.baocaoquy', [
            'danh_sach' => $danh_sach,
            'quy_danh_gia' => $quy_danh_gia,
            'nam_danh_gia' => $nam_danh_gia,
            'don_vi' => $ds_don_vi,
            'ma_don_vi_da_chon' => $request->ma_don_vi_da_chon
        ]);
    }

    ////////////////////////////////// Hàm dùng chung ////////////////////////////////////////////////////////////////////////////////////////////


    // Xác định mẫu phiếu
    public function xacDinhMauPhieu()
    {
        $thong_tin_mau_phieu = collect();
        if (in_array(Auth::user()->ma_ngach, ['01.007', '01.009', '01.010', '01.011'])) {
            $mau_phieu_danh_gia = Mau01C::all();
            $thong_tin_mau_phieu['mau'] = "mau01C";
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu số 01C";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "người lao động";
        } elseif (Auth::user()->ma_chuc_vu != NULL) {
            $mau_phieu_danh_gia = Mau01A::all();
            $thong_tin_mau_phieu['mau'] = "mau01A";
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu số 01A";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "công chức giữ chức vụ lãnh đạo, quản lý";
        } elseif (Auth::user()->ma_chuc_vu == NULL) {
            $mau_phieu_danh_gia = Mau01B::all();
            $thong_tin_mau_phieu['mau'] = "mau01B";
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu số 01B";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "công chức không giữ chức vụ lãnh đạo, quản lý";
        }

        return [$mau_phieu_danh_gia, $thong_tin_mau_phieu];
    }


    // Lấy thông tin mẫu phiếu
    public function thongTinMauPhieu($mau_phieu_danh_gia)
    {
        $thong_tin_mau_phieu = collect();
        if ($mau_phieu_danh_gia == "mau01A") {
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu 01A";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "công chức giữ chức vụ lãnh đạo, quản lý";
        } elseif ($mau_phieu_danh_gia == "mau01B") {
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu 01B";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "công chức không giữ chức vụ lãnh đạo, quản lý";
        } elseif ($mau_phieu_danh_gia == "mau01C") {
            $thong_tin_mau_phieu['ten_mau'] = "Mẫu 01C";
            $thong_tin_mau_phieu['doi_tuong_ap_dung'] = "người lao động";
        }
        return $thong_tin_mau_phieu;
    }


    // Tính Tổng điểm
    public function tinhTongDiem($request)
    {
        $tc_110 = $request->tc_111 + $request->tc_112 + $request->tc_113 + $request->tc_114;
        $tc_130 = $request->tc_131 + $request->tc_132 + $request->tc_133 + $request->tc_134;
        $tc_150 = $request->tc_151 + $request->tc_152 + $request->tc_153 + $request->tc_154;
        $tc_170 = $request->tc_171 + $request->tc_172 + $request->tc_173;
        $tc_100 = $tc_110 + $tc_130 + $tc_150 + $tc_170;
        $tc_210 = $request->tc_211 + $request->tc_212 + $request->tc_213 + $request->tc_214 + $request->tc_215
            + $request->tc_216 + $request->tc_217 + $request->tc_218 + $request->tc_219 + $request->tc_220;
        $tc_230 =  $request->tc_230;
        $tc_200 = $tc_210 + $tc_230;
        $tc_300 = $tc_100 + $tc_200;
        $tc_400 = $request->tc_400;
        $tc_500 = $request->tc_500;
        $tong_diem_tu_cham = $tc_300 + $tc_400 - $tc_500;
        return $tong_diem_tu_cham;
    }


    // Xếp loại
    public function xepLoai($tong_diem_tu_cham)
    {
        $xep_loai = Xeploai::orderby('diem_toi_thieu', 'ASC')->get();
        foreach ($xep_loai as $data) {
            if ($tong_diem_tu_cham >= $data->diem_toi_thieu) {
                $ket_qua_xep_loai = $data->ma_xep_loai;
            }
        }
        return $ket_qua_xep_loai;
    }


    // Danh sách phê duyệt kết quả đánh giá của Hội đồng Kiểm tra
    public function danhSachHoiDongPheDuyet()
    {
        // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
        $user_list = User::where('users.ma_chuc_vu', '01')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();

        return $user_list;
    }


    // Danh sách phê duyệt kết quả đánh giá của Cục trưởng
    public function danhSachCucTruongPheDuyet()
    {
        $user_list = User::wherein('users.ma_chuc_vu', ['02', '03', '04', '05', '06', '07', '08', '09', '10'])
            ->orwhere('users.ma_don_vi', Auth::user()->ma_don_vi)
            ->where('users.ma_chuc_vu', null)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();
        return $user_list;
    }


    // Danh sách phê duyệt kết quả đánh giá của Chi Cục trưởng
    public function danhSachChiCucTruongPheDuyet()
    {
        $user_list = User::where('users.ma_don_vi', Auth::user()->ma_don_vi)
            ->where('users.ma_chuc_vu', null)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->orderBy('users.ma_don_vi', 'ASC')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();
        return $user_list;
    }



    // Danh sách Lãnh đạo cục Thuế
    public function danhSachLanhDaoCucThue()
    {
        $user_list = User::wherein('users.ma_chuc_vu', ["01", "02"])
            ->where('users.ma_trang_thai', 1)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }

    // Danh sách Lãnh đạo Phòng, Văn phòng
    public function danhSachLanhDaoPhong()
    {
        $user_list = User::wherein('users.ma_chuc_vu', ["04", "05", "07", "08"])
            ->where('users.ma_trang_thai', 1)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }

    // Danh sách Lãnh đạo Chi cục Thuế
    public function danhSachLanhDaoChiCucThue()
    {
        $user_list = User::wherein('users.ma_chuc_vu', ["03", "06"])
            ->where('users.ma_trang_thai', 1)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }


    // Danh sách Lãnh đạo Chi cục Đội Thuế
    public function danhSachLanhDaoDoiThue()
    {
        $user_list = User::wherein('users.ma_chuc_vu', ["09", "10"])
            ->where('users.ma_trang_thai', 1)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }


    // Danh sách công chức
    public function danhSachCongChuc()
    {
        $user_list = User::where('users.ma_chuc_vu', null)
            ->where('users.ma_trang_thai', 1)
            ->wherenotin('ma_ngach', ['01.007', '01.009', '01.010', '01.011'])
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }


    // Danh sách công chức
    public function danhSachHopDong()
    {
        $user_list = User::where('users.ma_chuc_vu', null)
            ->wherein('users.ma_ngach', ['01.007', '01.009', '01.010', '01.011'])
            ->where('users.ma_trang_thai', 1)
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        return $user_list;
    }


    public function dangxaydung()
    {
        return view('dangxaydung');
    }


    public function hoiDongList(Request $request)
    {
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        if (Auth::user()->hoi_dong_phe_duyet == 1) {
            // Nếu Người dùng là Hội đồng
            // Đánh giá cho: 01-Cục trưởng
            $danh_sach = PhieuDanhGiaCucTruong::where('phieu_danh_gia_cuc_truong.ma_cap_tren_danh_gia', Auth::user()->so_hieu_cong_chuc)
                ->where('phieu_danh_gia_cuc_truong.ma_trang_thai', 19)
                ->where('phieu_danh_gia_cuc_truong.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia_cuc_truong.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select('phieu_danh_gia_cuc_truong.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->first();

            if (!$danh_sach) {
                $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', 13)
                    ->where('phieu_danh_gia.ma_chuc_vu', '01')
                    ->where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->first();
            }

            return view('danhgia.hoidong_list', ['danh_sach' => $danh_sach, 'thoi_diem_danh_gia' => $thoi_diem_danh_gia]);
        } else {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        }
    }


    // Hội đồng đánh giá, xếp loại cho Cục trưởng
    public function hoidongCreate($ma_phieu_danh_gia)
    {
        if (Auth::user()->hoi_dong_phe_duyet == 1) {
            // Nếu Người dùng là Hội đồng
            // Đánh giá cho: 01-Cục trưởng

            // Tìm Phiếu đánh giá
            $phieu_danh_gia = PhieuDanhGiaCucTruong::where('phieu_danh_gia_cuc_truong.ma_phieu_danh_gia', $ma_phieu_danh_gia)
                ->leftjoin('phieu_danh_gia', 'phieu_danh_gia.ma_phieu_danh_gia', 'phieu_danh_gia_cuc_truong.ma_phieu_danh_gia')
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia_cuc_truong.*', 'phieu_danh_gia.mau_phieu_danh_gia', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->first();
            if (!$phieu_danh_gia) $phieu_danh_gia = $this->timPhieuDanhGia($ma_phieu_danh_gia);

            // Lấy dữ liệu mục A
            $ket_qua_muc_A = KetQuaMucACucTruong::where('ma_phieu_danh_gia', $phieu_danh_gia->ma_phieu_danh_gia)
                ->leftjoin('mau01A', 'mau01A.ma_tieu_chi', 'ket_qua_muc_A_cuc_truong.ma_tieu_chi')
                ->select('ket_qua_muc_A_cuc_truong.*', 'mau01A.tieu_chi_me', 'mau01A.loai_tieu_chi', 'mau01A.tt', 'mau01A.noi_dung')
                ->get();
            if ($ket_qua_muc_A->isEmpty()) $ket_qua_muc_A = $this->timKetQuaMucA($phieu_danh_gia);

            // Lấy dữ liệu mục B
            $ket_qua_muc_B = KetQuaMucB::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();

            // Lấy dữ liệu Lý do điểm cộng
            $ly_do_diem_cong = LyDoDiemCong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

            // Lấy dữ liệu Lý do điểm trừ
            $ly_do_diem_tru = LyDoDiemTru::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();

            // Lấy thông tin Mẫu phiếu đánh giá
            $thong_tin_mau_phieu = $this->thongTinMauPhieu("mau01A");
            $thoi_diem_danh_gia = Carbon::create($phieu_danh_gia->thoi_diem_danh_gia);
            $ngay_thuc_hien_danh_gia = Carbon::create($phieu_danh_gia->created_at);
            $xep_loai = XepLoai::all();

            return view(
                'danhgia.hoidong_create',
                [
                    'phieu_danh_gia' => $phieu_danh_gia,
                    'thong_tin_mau_phieu' => $thong_tin_mau_phieu,
                    'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                    'ngay_thuc_hien_danh_gia' => $ngay_thuc_hien_danh_gia,
                    'xep_loai' => $xep_loai,
                    'ket_qua_muc_A' => $ket_qua_muc_A,
                    'ket_qua_muc_B' => $ket_qua_muc_B,
                    'ly_do_diem_cong' => $ly_do_diem_cong,
                    'ly_do_diem_tru' => $ly_do_diem_tru,
                ]
            );
        } else {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        }
    }


    // Lưu Kết quả thành viên Hội đồng đánh giá
    public function hoiDongStore($ma_phieu_danh_gia, Request $request)
    {
        if (Auth::user()->hoi_dong_phe_duyet == 1) {
            // Tính Tổng điểm thành viên Hội đồng đánh giá
            $diem_danh_gia = $request->tc_300;
            $diem_cong_danh_gia = $request->tc_400;
            $diem_tru_danh_gia = $request->tc_500;
            $tong_diem_danh_gia = $this->tinhTongDiem($request);

            // Kết quả thành viên Hội đồng xếp loại
            $ket_qua_xep_loai = $this->xepLoai($tong_diem_danh_gia);

            // Kiểm tra đã có Phiếu đánh giá thành viên Hội đồng đánh giá  
            $phieu_danh_gia = PhieuDanhGiaCucTruong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)
                ->where('ma_cap_tren_danh_gia', Auth::user()->so_hieu_cong_chuc)
                ->first();
            $phieu = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
            if ($phieu->ma_trang_thai == 13) {

                // Lưu kết quả thành viên Hội đồng đánh giá cho Mục A
                $ket_qua_muc_A = KetQuaMucA::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->get();

                if (!isset($phieu_danh_gia)) {
                    foreach ($ket_qua_muc_A as $ket_qua) {
                        $data = new KetQuaMucACucTruong();
                        $data->ma_phieu_danh_gia = $ma_phieu_danh_gia;
                        $data->ma_cap_tren_danh_gia = Auth::user()->so_hieu_cong_chuc;
                        $data->ma_tieu_chi = $ket_qua->ma_tieu_chi;
                        $data->diem_toi_da = $ket_qua->diem_toi_da;
                        $data->diem_tu_cham = $ket_qua->diem_tu_cham;
                        $data->diem_danh_gia = $request->input($ket_qua->ma_tieu_chi);
                        $data->save();
                    }
                } else {
                    foreach ($ket_qua_muc_A as $ket_qua) {
                        $data = KetQuaMucACucTruong::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)
                            ->where('ma_cap_tren_danh_gia', Auth::user()->so_hieu_cong_chuc)
                            ->where('ma_tieu_chi', $ket_qua->ma_tieu_chi)
                            ->first();
                        $data->diem_danh_gia = $request->input($ket_qua->ma_tieu_chi);
                        $data->save();
                    }
                }

                if (!isset($phieu_danh_gia)) {
                    $phieu_danh_gia = new PhieuDanhGiaCucTruong();
                }

                $phieu_danh_gia->so_hieu_cong_chuc = $phieu->so_hieu_cong_chuc;
                $phieu_danh_gia->thoi_diem_danh_gia = $phieu->thoi_diem_danh_gia;
                $phieu_danh_gia->ma_phieu_danh_gia = $phieu->ma_phieu_danh_gia;
                $phieu_danh_gia->ma_cap_tren_danh_gia = Auth::user()->so_hieu_cong_chuc;
                $phieu_danh_gia->ma_chuc_vu_cap_tren = Auth::user()->ma_chuc_vu;
                $phieu_danh_gia->ma_phong_cap_tren = Auth::user()->ma_phong;
                $phieu_danh_gia->ma_don_vi_cap_tren = Auth::user()->ma_don_vi;
                $phieu_danh_gia->diem_tu_cham = $phieu->diem_tu_cham;
                $phieu_danh_gia->diem_cong_tu_cham = $phieu->diem_cong_tu_cham;
                $phieu_danh_gia->diem_tru_tu_cham = $phieu->diem_tru_tu_cham;
                $phieu_danh_gia->tong_diem_tu_cham = $phieu->tong_diem_tu_cham;
                $phieu_danh_gia->diem_danh_gia = $diem_danh_gia;
                $phieu_danh_gia->diem_cong_danh_gia = $diem_cong_danh_gia;
                $phieu_danh_gia->diem_tru_danh_gia = $diem_tru_danh_gia;
                $phieu_danh_gia->tong_diem_danh_gia = $tong_diem_danh_gia;
                $phieu_danh_gia->ca_nhan_tu_xep_loai = $phieu->ca_nhan_tu_xep_loai;
                $phieu_danh_gia->ket_qua_xep_loai = $ket_qua_xep_loai;
                $phieu_danh_gia->ma_trang_thai = 19;
                $phieu_danh_gia->save();

                return redirect()->route(
                    'phieudanhgia.hoidong.create',
                    ['id' => $phieu_danh_gia->ma_phieu_danh_gia]
                )->with('msg_success', 'Thành viên Hội đồng đã thực hiện đánh giá thành công');
            } else {
                return redirect()->route(
                    'phieudanhgia.hoidong.create',
                    ['id' => $phieu_danh_gia->ma_phieu_danh_gia]
                )->with('msg_error', 'Đã tổng hợp đánh giá, xếp loại cho Cục trưởng. Không thể thay đổi điểm, xếp loại đã chấm.');
            }
        } else {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        }
    }

    public function hoiDongTongHopDuKien(Request $request)
    {
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        if (Auth::user()->ma_phong == '440103') {
            $phieu_danh_gia = PhieuDanhGiaCucTruong::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia_cuc_truong.ma_cap_tren_danh_gia')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia_cuc_truong.ma_chuc_vu_cap_tren')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia_cuc_truong.ma_phong_cap_tren')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia_cuc_truong.ma_don_vi_cap_tren')
                ->select('phieu_danh_gia_cuc_truong.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->get();

            $tong_diem = 0;
            $diem_trung_binh = NULL;
            $xep_loai_trung_binh = NULL;
            $xep_loai = XepLoai::all();

            if (!$phieu_danh_gia->isEmpty()) {
                foreach ($phieu_danh_gia as $phieu) {
                    $tong_diem = $tong_diem + $phieu->tong_diem_danh_gia;
                }

                $diem_trung_binh = floor($tong_diem / $phieu_danh_gia->count());
                $xep_loai_trung_binh = $this->xepLoai($diem_trung_binh);
            }

            return view(
                'danhgia.hoidong_tonghop',
                [
                    'danh_sach' => $phieu_danh_gia,
                    'diem_trung_binh' => $diem_trung_binh,
                    'xep_loai_trung_binh' => $xep_loai_trung_binh,
                    'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
                    'xep_loai' => $xep_loai
                ]
            );
        } else {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        }
    }


    public function hoiDongTongHopDanhGia(Request $request)
    {
        if (Auth::user()->ma_phong == '440103') {
            $tong_diem_danh_gia = $request->diem_trung_binh;
            $xep_loai = $this->xepLoai($tong_diem_danh_gia);

            $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $request->ma_phieu_danh_gia)->first();
            if ($phieu_danh_gia->ma_trang_thai == 13) {
                $phieu_danh_gia->tong_diem_danh_gia = $tong_diem_danh_gia;
                $phieu_danh_gia->ket_qua_xep_loai = $xep_loai;
                $phieu_danh_gia->ma_cap_tren_danh_gia = Auth::user()->so_hieu_cong_chuc;
                $phieu_danh_gia->ma_trang_thai = 19;
                $phieu_danh_gia->save();
                return redirect()->back()->with('msg_success', 'Tổng hợp đánh giá, xếp loại Cục trưởng thành công');
            } else {
                return redirect()->back()->with('msg_error', 'Đã có kết quả đánh giá, xếp loại Cục trưởng');
            }
        } else {
            return view("404")->with('msg_error', 'Bạn không có thẩm quyền xem Trang này');
        }
    }
}
