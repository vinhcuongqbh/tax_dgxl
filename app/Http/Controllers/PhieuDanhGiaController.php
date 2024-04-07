<?php

namespace App\Http\Controllers;

use App\Models\DonVi;
use App\Models\KetQuaMucA;
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
use App\Models\Phong;

class PhieuDanhGiaController extends Controller
{
    use PhieuDanhGiaTrait;
    ///////////////////// Cá nhân tự đánh giá, xếp loại ////////////////////
    // Tạo Phiếu đánh giá cá nhân tự chấm
    public function canhanCreate()
    {
        $thoi_diem_danh_gia = Carbon::now()->subMonth();
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
        if (Auth::user()->ma_chuc_vu == null) {
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
                    ->orderBy('phieu_danh_gia.created_at', 'DESC')
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
                    ->orderBy('phieu_danh_gia.created_at', 'DESC')
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
                    ->orderBy('phieu_danh_gia.created_at', 'DESC')
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

        // Kết quả cá nhân tự xếp loại
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
            $list->ma_trang_thai = 17;
            $list->save();
        }

        return redirect()->route('phieudanhgia.captren.list')->with('msg_success', 'Đã gửi thành công Danh sách phiếu đánh giá');
    }


    public function captrenSendBack ($ma_phieu_danh_gia) {
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
        if ((Auth::user()->hoi_dong_phe_duyet <> 1) and (!in_array(Auth::user()->ma_chuc_vu, ['01', '03']))) {
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
                    ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                    ->get();
            } elseif (Auth::user()->ma_chuc_vu == "03") {
                // Nếu Người dùng có chức vụ Chi cục Trưởng
                // Đánh giá cho Công chức không giữ chức vụ lãnh đạo thuộc Chi cục     
                $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                    ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                    ->where('phieu_danh_gia.ma_chuc_vu', null)
                    ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                    ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                    ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                    ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                    ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                    ->orderBy('phieu_danh_gia.created_at', 'DESC')
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
                ->orderBy('phieu_danh_gia.ma_don_vi', 'ASC')
                ->get();
        } elseif (Auth::user()->ma_chuc_vu == "03") {
            // Nếu Người dùng có chức vụ Chi cục Trưởng
            // Đánh giá cho Công chức không giữ chức vụ lãnh đạo thuộc Chi cục     
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.ma_trang_thai', '17')
                ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->where('phieu_danh_gia.ma_chuc_vu', null)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('phieu_danh_gia.created_at', 'DESC')
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

        // Lưu kết quả vào Bảng kết quả xếp loại tháng
        if ($danh_sach_phe_duyet != null) $this->kQXLThang($danh_sach_phe_duyet);

        if ($danh_sach_phe_duyet != null) {
            foreach ($danh_sach_phe_duyet as $danh_sach_phe_duyet) {
                if ($danh_sach_phe_duyet->ma_chuc_vu == '01') {
                    $danh_sach_phe_duyet->tong_diem_danh_gia = $danh_sach_phe_duyet->tong_diem_tu_cham;
                    $danh_sach_phe_duyet->ket_qua_xep_loai = $danh_sach_phe_duyet->ca_nhan_tu_xep_loai;
                }
                $danh_sach_phe_duyet->ma_cap_tren_phe_duyet = Auth::user()->so_hieu_cong_chuc;
                $danh_sach_phe_duyet->ma_trang_thai = 19;
                $danh_sach_phe_duyet->save();
            }
            return redirect()->route('phieudanhgia.capqd.list')->with('msg_success', 'Đã phê duyệt thành công Danh sách phiếu đánh giá');
        } else {
            return redirect()->route('phieudanhgia.capqd.list')->with('msg_error', 'Danh sách phê duyệt trống');
        }
    }


    public function capqdSendBack ($ma_phieu_danh_gia) {
        $phieu_danh_gia = PhieuDanhGia::where('ma_phieu_danh_gia', $ma_phieu_danh_gia)->first();
        $phieu_danh_gia->ma_trang_thai = 15;
        $phieu_danh_gia->save();

        return redirect()->route('phieudanhgia.capqd.list')->with('msg_success', 'Đã gửi trả Phiếu đánh giá');
    }


    // Danh sách Thông báo Kết quả xếp loại theo tháng
    public function thongBaoThang(Request $request)
    {
        if (!isset($request->nam_danh_gia)) {
            $thoi_diem_danh_gia = Carbon::now()->subMonth()->endOfMonth();
        } else {
            $thoi_diem_danh_gia = Carbon::createFromDate($request->nam_danh_gia, $request->thang_danh_gia)->endOfMonth();
        }

        if ((in_array(Auth::user()->ma_chuc_vu, ['01', '02', '04'])) || ((Auth::user()->ma_chuc_vu == '05') && (Auth::user()->ma_phong == '440103'))) {
            // Nếu Người dùng có chức vụ Cục Trưởng, Cục Phó, Chánh Văn phòng, Trưởng phòng Tổ chức cán bộ
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_trang_thai', '>=', 19)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
        } else {
            // Những người còn lại            
            $danh_sach = PhieuDanhGia::where('phieu_danh_gia.thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
                ->where('phieu_danh_gia.ma_don_vi', Auth::user()->ma_don_vi)
                ->where('phieu_danh_gia.ma_trang_thai', '>=', 19)
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->get();
        }

        // $don_vi = DonVi::where('ma_don_vi', Auth::user()->ma_don_vi)->where('ma_trang_thai', 1)->get();
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();
        return view('danhgia.thongbaothang', [
            'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $danh_sach,
            'don_vi' => $don_vi,
            'phong' => $phong,
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

        // Danh sách phiếu đánh giá trong tháng
        $phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia->toDateString())
            ->where('ma_trang_thai', '>=', 19)
            // ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
            // ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            // ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            // ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            // ->select('phieu_danh_gia.*', 'users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
            ->get();

        $list = collect();
        $danh_sach_lanh_dao_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["01", "02"]);
        $danh_sach_lanh_dao_phong = $phieu_danh_gia->wherein('ma_chuc_vu', ["04", "05", "07", "08"]);
        $danh_sach_lanh_dao_chi_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["03", "06"]);
        $danh_sach_lanh_dao_doi_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["09", "10"]);
        $danh_sach_cong_chuc = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01B');
        $danh_sach_hop_dong = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01C');

        $list->push($danh_sach_lanh_dao_cuc_thue);
        $list->push($danh_sach_lanh_dao_phong);
        $list->push($danh_sach_lanh_dao_chi_cuc_thue);
        $list->push($danh_sach_lanh_dao_doi_thue);
        $list->push($danh_sach_cong_chuc);
        $list->push($danh_sach_hop_dong);

        $danh_sach = collect();
        $ten = array(
            "Lãnh đạo Cục thuế",
            "Lãnh đạo Phòng, Văn phòng",
            "Lãnh đạo Chi cục Thuế",
            "Lãnh đạo Đội thuế",
            "Công chức",
            "Hợp đồng lao động"
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

                $so_luong_khong_xep_loai =  $ls->where("ket_qua_xep_loai", null)->count();
                if ($so_luong <> 0) {
                    $ti_le_khong_xep_loai = round($so_luong_khong_xep_loai / $so_luong * 100, 2);
                } else {
                    $ti_le_khong_xep_loai = 0;
                }
            }

            $i++;

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

        return view('danhgia.baocaothang', ['danh_sach' => $danh_sach, 'thoi_diem_danh_gia' => $thoi_diem_danh_gia]);
    }


    // // Danh sách kết quả xếp loại theo quý
    // public function capQDDSQuy(Request $request)
    // {
    //     // Lấy danh sách Hội đồng phê duyệt
    //     if (Auth::user()->hoi_dong_phe_duyet == 1) {
    //         // Nếu Người dùng có chức năng phê duyệt của Hội đồng TĐKT
    //         $user_list_hd = $this->danhSachHoiDongPheDuyet();
    //     } else {
    //         $user_list_hd = null;
    //     }

    //     // Lấy danh sách Cục trưởng/Chi cục trưởng phê duyệt
    //     if (Auth::user()->ma_chuc_vu == "01") {
    //         // Nếu Người dùng có chức vụ Cục Trưởng 
    //         $user_list_cm = $this->danhSachCucTruongPheDuyet();
    //     } elseif (Auth::user()->ma_chuc_vu == "03") {
    //         // Nếu Người dùng có chức vụ Chi cục Trưởng 
    //         $user_list_cm = $this->danhSachChiCucTruongPheDuyet();
    //     } else {
    //         $user_list_cm = null;
    //     }

    //     // Nối 2 danh sách Hội đồng + Cục trưởng/Chi cục trưởng
    //     if (($user_list_hd != null) && ($user_list_cm != null)) {
    //         $user_list = $user_list_hd->merge($user_list_cm);
    //     } elseif (($user_list_hd != null) && ($user_list_cm == null)) {
    //         $user_list = $user_list_hd;
    //     } elseif (($user_list_hd == null) && ($user_list_cm != null)) {
    //         $user_list = $user_list_cm;
    //     } else {
    //         $user_list = null;
    //     }

    //     // Xác định tháng đánh giá, quý đánh giá
    //     $thoi_diem_danh_gia = Carbon::now();
    //     if (isset($request->quy_danh_gia)) {
    //         $quy_danh_gia = $request->quy_danh_gia;
    //     } else {
    //         $quy_danh_gia = $thoi_diem_danh_gia->quarter;
    //     }
    //     $nam_danh_gia = $thoi_diem_danh_gia->year;

    //     if ($quy_danh_gia == "1") {
    //         $list_thang = ["01", "02", "03"];
    //     } elseif ($quy_danh_gia == "2") {
    //         $list_thang = ["04", "05", "06"];
    //     } elseif ($quy_danh_gia == "3") {
    //         $list_thang = ["07", "08", "09"];
    //     } elseif ($quy_danh_gia == "4") {
    //         $list_thang = ["10", "11", "12"];
    //     }


    //     // Thực hiện xếp loại theo quý
    //     $collection = collect();
    //     $xep_loai_1 = null;
    //     $xep_loai_2 = null;
    //     $xep_loai_3 = null;

    //     foreach ($user_list as $user) {
    //         // Tìm kết quả xếp loại các tháng trong quý
    //         $xep_loai_thang = KQXLThang::where('so_hieu_cong_chuc', $user->so_hieu_cong_chuc)
    //             ->where('nam_danh_gia', $nam_danh_gia)
    //             ->first();
    //         if (isset($xep_loai_thang)) {
    //             $xep_loai_1 = $xep_loai_thang->{"kqxl_t" . $list_thang[0]};
    //             $xep_loai_2 = $xep_loai_thang->{"kqxl_t" . $list_thang[1]};
    //             $xep_loai_3 = $xep_loai_thang->{"kqxl_t" . $list_thang[2]};
    //         }

    //         // Tính toán xếp loại quý
    //         $countA = 0;
    //         $countB = 0;
    //         $countC = 0;
    //         $countD = 0;

    //         for ($i = 1; $i <= 3; $i++) {
    //             if (${"xep_loai_" . $i} == "A") $countA++;
    //             elseif (${"xep_loai_" . $i} == "B") $countB++;
    //             elseif (${"xep_loai_" . $i} == "C") $countC++;
    //             elseif (${"xep_loai_" . $i} == "D")  $countD++;
    //         }

    //         $ket_qua_xep_loai = null;
    //         if (($xep_loai_1 == null) || ($xep_loai_2 == null) || ($xep_loai_3 == null)) $ket_qua_xep_loai = null;
    //         elseif (($countA >= 2) && ($countC == 0) && ($countD == 0)) $ket_qua_xep_loai = "A";
    //         elseif (((($countA >= 2) || ($countB >= 2)) || (($countA >= 1) && ($countB >= 1))) && ($countD == 0)) $ket_qua_xep_loai = "B";
    //         elseif ((($countA > 0) || ($countB > 0) || ($countC > 0)) && ($countD == 0)) $ket_qua_xep_loai = "C";
    //         elseif ($countD > 0) $ket_qua_xep_loai = "D";
    //         else $ket_qua_xep_loai = null;


    //         // Đưa vào danh sách
    //         $collection->push([
    //             'name' => $user->name,
    //             'ten_chuc_vu' => $user->ten_chuc_vu,
    //             'ten_phong' => $user->ten_phong,
    //             'ten_don_vi' => $user->ten_don_vi,
    //             'xep_loai_1' => $xep_loai_1,
    //             'xep_loai_2' => $xep_loai_2,
    //             'xep_loai_3' => $xep_loai_3,
    //             'ket_qua_xep_loai' => $ket_qua_xep_loai,
    //         ]);
    //     }

    //     return view(
    //         'danhgia.capqd_list_quy',
    //         [
    //             'danh_sach' => $collection,
    //             'thang' => $list_thang,
    //             'quy_danh_gia' => $quy_danh_gia,
    //             'nam_danh_gia' => $nam_danh_gia,
    //         ]
    //     );
    // }


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
                $thang_dau_tien = Carbon::now()->firstOfQuarter()->endOfMonth();
                $thang_thu_hai = Carbon::now()->firstOfQuarter()->addMonth()->endOfMonth();
                $thang_cuoi_cung = Carbon::now()->lastOfQuarter()->endOfMonth();
                $quy_danh_gia = Carbon::now()->quarter;
                $nam_danh_gia = Carbon::now()->year;
            }

            if ($quy_danh_gia == "1") {
                $list_thang = ["01", "02", "03"];
            } elseif ($quy_danh_gia == "2") {
                $list_thang = ["04", "05", "06"];
            } elseif ($quy_danh_gia == "3") {
                $list_thang = ["07", "08", "09"];
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
            $thang_dau_tien = Carbon::now()->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::now()->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::now()->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = Carbon::now()->quarter;
            $nam_danh_gia = Carbon::now()->year;
        }

        if ($quy_danh_gia == "1") {
            $list_thang = ["01", "02", "03"];
        } elseif ($quy_danh_gia == "2") {
            $list_thang = ["04", "05", "06"];
        } elseif ($quy_danh_gia == "3") {
            $list_thang = ["07", "08", "09"];
        } elseif ($quy_danh_gia == "4") {
            $list_thang = ["10", "11", "12"];
        }


        // Thực hiện xếp loại theo quý
        $collection = collect();
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
            else $ket_qua_xep_loai = null;

            $this->kQXLQuy($user, $ket_qua_xep_loai, $nam_danh_gia, $quy_danh_gia);
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
        if (isset($request->quy_danh_gia)) {
            $thang_dau_tien = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $request->quy_danh_gia * 3)->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = $request->quy_danh_gia;
            $nam_danh_gia = $request->nam_danh_gia;
        } else {
            $thang_dau_tien = Carbon::now()->firstOfQuarter()->endOfMonth();
            $thang_thu_hai = Carbon::now()->firstOfQuarter()->addMonth()->endOfMonth();
            $thang_cuoi_cung = Carbon::now()->lastOfQuarter()->endOfMonth();
            $quy_danh_gia = Carbon::now()->quarter;
            $nam_danh_gia = Carbon::now()->year;
        }

        $danh_sach = KQXLThang::where('kqxl_thang.nam_danh_gia', $nam_danh_gia)->get();

        // Thực hiện xếp loại theo quý
        $collection = collect();
        $xep_loai_1 = null;
        $xep_loai_2 = null;
        $xep_loai_3 = null;

        foreach ($danh_sach as $ds) {
            $xep_loai_thang = KQXLThang::where('so_hieu_cong_chuc', $ds->so_hieu_cong_chuc)
                ->where('nam_danh_gia', $nam_danh_gia)
                ->first();

            if (isset($xep_loai_thang)) {
                $diem_1 = $xep_loai_thang->{"diem_phe_duyet_t" . $thang_dau_tien->format("m")};
                $xep_loai_1 = $xep_loai_thang->{"kqxl_t" . $thang_dau_tien->format("m")};
                $diem_2 = $xep_loai_thang->{"diem_phe_duyet_t" . $thang_thu_hai->format("m")};
                $xep_loai_2 = $xep_loai_thang->{"kqxl_t" . $thang_thu_hai->format("m")};
                $diem_3 = $xep_loai_thang->{"diem_phe_duyet_t" . $thang_cuoi_cung->format("m")};
                $xep_loai_3 = $xep_loai_thang->{"kqxl_t" . $thang_cuoi_cung->format("m")};
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
            else $ket_qua_xep_loai = null;

            $user = PhieuDanhGia::where('phieu_danh_gia.so_hieu_cong_chuc', $ds->so_hieu_cong_chuc)
                ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung->toDateString())
                ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
                ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'phieu_danh_gia.ma_chuc_vu')
                ->leftjoin('phong', 'phong.ma_phong', 'phieu_danh_gia.ma_phong')
                ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'phieu_danh_gia.ma_don_vi')
                ->select('phieu_danh_gia.*', 'users.name', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
                ->orderBy('users.ma_don_vi', 'ASC')
                ->orderBy('users.ma_phong', 'ASC')
                ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
                ->first();

            // Đưa vào danh sách
            if (isset($user)) {
                $collection->push([
                    'name' => $user->name,
                    'ma_chuc_vu' => $user->ma_chuc_vu,
                    'ten_chuc_vu' => $user->ten_chuc_vu,
                    'ma_phong' => $user->ma_phong,
                    'ten_phong' => $user->ten_phong,
                    'ma_don_vi' => $user->ma_don_vi,
                    'ten_don_vi' => $user->ten_don_vi,
                    'diem_1' => $diem_1,
                    'xep_loai_1' => $xep_loai_1,
                    'diem_2' => $diem_2,
                    'xep_loai_2' => $xep_loai_2,
                    'diem_3' => $diem_3,
                    'xep_loai_3' => $xep_loai_3,
                    'ket_qua_xep_loai' => $ket_qua_xep_loai,
                ]);
            }
        }
        $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
        $phong = Phong::where('ma_trang_thai', 1)->get();

        return view('danhgia.thongbaoquy', [
            //'thoi_diem_danh_gia' => $thoi_diem_danh_gia,
            'phieu_danh_gia' => $collection,
            'don_vi' => $don_vi,
            'phong' => $phong,
            'quy_danh_gia' => $quy_danh_gia,
            'nam_danh_gia' => $nam_danh_gia,
            'thang_dau_tien' => $thang_dau_tien->month,
            'thang_thu_hai' => $thang_thu_hai->month,
            'thang_cuoi_cung' => $thang_cuoi_cung->month,
        ]);
    }


    // Báo cáo tổng hợp kết quả tháng
    public function baoCaoQuy(Request $request)
    {
        if (!isset($request->quy_danh_gia)) {
            $quy_danh_gia = Carbon::now()->quarter;
            $nam_danh_gia = Carbon::now()->format("Y");
        } else {
            $quy_danh_gia = $request->quy_danh_gia;
            $nam_danh_gia = $request->nam_danh_gia;
        }

        $thang_cuoi_cung = Carbon::createFromDate($request->nam_danh_gia, $quy_danh_gia * 3)->endOfMonth()->format("Ymd");

        $xep_loai = XepLoai::all();

        // Danh sách phiếu đánh giá trong tháng
        $phieu_danh_gia = KQXLQuy::where('kqxl_quy.nam_danh_gia', $nam_danh_gia)
            ->leftjoin('phieu_danh_gia', 'phieu_danh_gia.so_hieu_cong_chuc', 'kqxl_quy.so_hieu_cong_chuc')
            ->where('phieu_danh_gia.thoi_diem_danh_gia', $thang_cuoi_cung)
            ->select('kqxl_quy.*', 'phieu_danh_gia.mau_phieu_danh_gia', 'phieu_danh_gia.ma_chuc_vu')
            ->get();

        $list = collect();
        $danh_sach_lanh_dao_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["01", "02"]);
        $danh_sach_lanh_dao_phong = $phieu_danh_gia->wherein('ma_chuc_vu', ["04", "05", "07", "08"]);
        $danh_sach_lanh_dao_chi_cuc_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["03", "06"]);
        $danh_sach_lanh_dao_doi_thue = $phieu_danh_gia->wherein('ma_chuc_vu', ["09", "10"]);
        $danh_sach_cong_chuc = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01B');
        $danh_sach_hop_dong = $phieu_danh_gia->where('mau_phieu_danh_gia', 'mau01C');

        $list->push($danh_sach_lanh_dao_cuc_thue);
        $list->push($danh_sach_lanh_dao_phong);
        $list->push($danh_sach_lanh_dao_chi_cuc_thue);
        $list->push($danh_sach_lanh_dao_doi_thue);
        $list->push($danh_sach_cong_chuc);
        $list->push($danh_sach_hop_dong);

        $danh_sach = collect();
        $ten = array(
            "Lãnh đạo Cục thuế",
            "Lãnh đạo Phòng, Văn phòng",
            "Lãnh đạo Chi cục Thuế",
            "Lãnh đạo Đội thuế",
            "Công chức",
            "Hợp đồng lao động"
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

                $so_luong_khong_xep_loai =  $ls->where('kqxl_q' . $quy_danh_gia, null)->count();
                if ($so_luong <> 0) {
                    $ti_le_khong_xep_loai = round($so_luong_khong_xep_loai / $so_luong * 100, 2);
                } else {
                    $ti_le_khong_xep_loai = 0;
                }
            }

            $i++;

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
            'nam_danh_gia' => $nam_danh_gia
        ]);
    }

    ////////////////////////////////// Hàm dùng chung //////////////////////////////////////////


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






    // // Danh sách Đơn vị, phòng, ban
    // public function danhSachToanDonVi2()
    // {

    //     $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
    //     $phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', '2024-02-29')->get();
    //     foreach ($don_vi as $dv) {
    //         echo "<b>" . $dv->ten_don_vi . "</b><br>";
    //         $phong = Phong::where('ma_don_vi_cap_tren', $dv->ma_don_vi)->where('ma_trang_thai', 1)->get();
    //         foreach ($phong as $ph) {
    //             echo "<b>" . $ph->ten_phong . "</b><br>";
    //             $phieu_danh_gia = PhieuDanhGia::where('phieu_danh_gia.ma_phong', $ph->ma_phong)
    //                 ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
    //                 ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
    //                 ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
    //                 ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
    //                 ->select('phieu_danh_gia.*', 'users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
    //                 ->get();
    //             foreach ($phieu_danh_gia as $phieu) {
    //                 echo $phieu->name . " , " . $phieu->ten_chuc_vu . " , " . $phieu->tong_diem_tu_cham . " , " . $phieu->tong_diem_danh_gia . "<br>";
    //             }
    //         }
    //         echo "<br><br><br>";
    //     }
    // }


    // // Danh sách Đơn vị, phòng, ban
    // public function danhSachToanDonVi()
    // {
    //     $thoi_diem_danh_gia = '2024-01-31';
    //     $don_vi = DonVi::where('ma_don_vi', '<>', '4400')->where('ma_trang_thai', 1)->get();
    //     $phong = Phong::where('ma_trang_thai', 1)->get();
    //     $phieu_danh_gia = PhieuDanhGia::where('thoi_diem_danh_gia', $thoi_diem_danh_gia)
    //         ->leftjoin('users', 'users.so_hieu_cong_chuc', 'phieu_danh_gia.so_hieu_cong_chuc')
    //         ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
    //         ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
    //         ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
    //         ->select('phieu_danh_gia.*', 'users.*', 'chuc_vu.ten_chuc_vu', 'phong.ten_phong', 'don_vi.ten_don_vi')
    //         ->get();
    //     return view('danhgia.test', [
    //         'don_vi' => $don_vi,
    //         'phong' => $phong,
    //         'phieu_danh_gia' => $phieu_danh_gia,
    //     ]);
    // }
}
