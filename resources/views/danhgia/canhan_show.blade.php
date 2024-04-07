@extends('dashboard')

@section('title', 'Kết quả Đánh giá, xếp loại')

@section('heading')
    Kết quả Đánh giá, xếp loại
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        {{-- Phần Tiêu đề --}}
                        <table class="table table-borderless">
                            <h6 class="font-italic text-bold text-right">{{ $thong_tin_mau_phieu['ten_mau'] }}</h6>
                            <tbody>
                                <tr>
                                    <td class="text-center py-0">TỔNG CỤC THUẾ</td>
                                    <td class="text-center text-bold py-0">CỘNG HÒA XÃ HỘI CHỦ NGHĨA VIỆT NAM</td>
                                </tr>
                                <tr class="px-0">
                                    <td class="text-center text-bold py-0">CỤC <u>THUẾ TỈNH QUẢNG</u> BÌNH</td>
                                    <td class="text-center text-bold py-0"><u>Độc lập - Tự do - Hạnh phúc</u></td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                        <br>
                        <h4 class="text-center text-bold my-0">PHIẾU ĐÁNH GIÁ, XẾP LOẠI CHẤT LƯỢNG HẰNG THÁNG</h4>
                        <h6 class="text-center font-italic my-0">(Áp dụng đối với {{ $thong_tin_mau_phieu['doi_tuong_ap_dung'] }})
                        </h6>
                        <h6 class="text-center align-middle my-0">Tháng
                            {{ $thoi_diem_danh_gia->month }}/{{ $thoi_diem_danh_gia->year  }}
                        </h6>
                        <br>

                        {{-- Phần Thông tin cá nhân --}}
                        <h6>&emsp;&emsp;&emsp;- Họ và tên: {{ $phieu_danh_gia->name }}</h6>
                        @if ($phieu_danh_gia->mau_phieu_danh_gia == 'mau01A')
                            <h6>&emsp;&emsp;&emsp;- Chức vụ: {{ $phieu_danh_gia->ten_chuc_vu }}</h6>
                        @endif
                        <h6>&emsp;&emsp;&emsp;- Đơn vị: {{ $phieu_danh_gia->ten_phong }}, {{ $phieu_danh_gia->ten_don_vi }}</h6>
                        <br>

                        {{-- Phần A --}}
                        <h6 class="text-bold">&emsp;&emsp;&emsp;A. Điểm đánh giá</h6>
                        {{-- Bảng tiêu chí đánh giá --}}
                        <table id="danh-gia" class="table table-bordered">
                            <colgroup>
                                <col style="width:4%;">
                                <col style="width:60%;">
                                <col style="width:12%;">
                                <col style="width:12%;">
                                <col style="width:12%;">
                            </colgroup>
                            <thead class="text-center">
                                <tr>
                                    <th class="align-middle" rowspan="2">STT</th>
                                    <th class="align-middle" rowspan="2">Nội dung đánh giá</th>
                                    <th class="align-middle" rowspan="2">Điểm tối đa</th>
                                    <th class="align-middle" colspan="2">Kết quả đánh giá</th>
                                </tr>
                                <tr>
                                    <th class="align-middle">Điểm cá nhân tự chấm</th>
                                    <th class="align-middle">Cấp có thẩm quyền đánh giá</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ket_qua_muc_A as $ket_qua)
                                    @php
                                        if (
                                            $ket_qua->loai_tieu_chi == 'muc_lon' ||
                                            $ket_qua->loai_tieu_chi == 'muc_nho' ||
                                            $ket_qua->loai_tieu_chi == 'lua_chon' ||
                                            $ket_qua->loai_tieu_chi == 'tong_diem' ||
                                            $ket_qua->loai_tieu_chi == 'cong'
                                        ) {
                                            $tinh_diem = 0;
                                        } else {
                                            $tinh_diem = 1;
                                        }
                                    @endphp
                                    <tr>
                                        {{-- Cột Số thứ tự --}}
                                        <td class="text-center @if ($tinh_diem == 0) text-bold @endif">
                                            {{ $ket_qua->tt }}
                                        </td>
                                        {{-- Cột Nội dung tiêu chí --}}
                                        <td class="text-justify @if ($tinh_diem == 0) text-bold @endif">
                                            {{ $ket_qua->noi_dung }}
                                        </td>
                                        {{-- Cột Điểm tối đa của tiêu chí --}}
                                        <td
                                            class="text-center align-middle @if ($tinh_diem == 0) text-bold @endif">
                                            {{ $ket_qua->diem_toi_da }}
                                        </td>
                                        {{-- Cột Điểm cá nhân tự chấm --}}
                                        @if ($ket_qua->loai_tieu_chi != 'phuong_an')
                                            <td
                                                class="text-center align-middle @if ($tinh_diem == 0) text-bold @endif">
                                                {{ $ket_qua->diem_tu_cham }}
                                            </td>
                                        @else
                                            {{-- Ghi chú: 
                                                    - Nếu điểm của phương án bằng điểm tối đa của tiêu chí mẹ thì tự đánh dấu vào ô của phương án đó. 
                                                    - Khi điểm của tiêu chí con nào thay đổi thì thực hiện tính toán lại Tổng điểm của tiêu chí mẹ, 
                                                      Tổng điểm của các Mục lớn, Tổng điểm cuối cùng và Tự xếp loại. 
                                                --}}
                                            @php
                                                $diem_tu_cham = $ket_qua
                                                    ->where('ma_tieu_chi', $ket_qua->tieu_chi_me)
                                                    ->where('ma_phieu_danh_gia', $ket_qua->ma_phieu_danh_gia)
                                                    ->first()->diem_tu_cham;
                                            @endphp
                                            <td class="align-middle text-center">
                                                <input class="m-0" type="radio"
                                                    value="{{ $ket_qua->diem_toi_da }}"
                                                    @if ($ket_qua->diem_toi_da == $diem_tu_cham) checked @else disabled @endif></label>
                                            </td>
                                        @endif
                                        {{-- Cột Điểm cấp trên đánh giá --}}
                                        <td class="align-middle text-center">
                                        </td>
                                    </tr>
                                @endforeach
                                <tr>
                                    <td></td>
                                    <td class="align-middle text-bold">TỔNG CỘNG</td>
                                    <td></td>
                                    <td class="align-middle text-center text-bold" id="tong_diem_tu_cham">
                                        {{ $phieu_danh_gia->tong_diem_tu_cham }}
                                    </td>
                                    <td class="align-middle text-center text-bold" id="tong_diem_dang_gia">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>

                        {{-- Mục B --}}
                        <h6 class="text-bold">&emsp;&emsp;&emsp;B. Số liệu thống kê kết quả thực hiện nhiệm vụ</h6>
                        <h6>&emsp;&emsp;&emsp;- Nhiệm vụ theo chương trình, kế hoạch và nhiệm vụ phát sinh:
                            <i>(Thống kê các nhiệm vụ và đánh dấu X vào một trong 4 ô sau cùng tương ứng)</i>
                        </h6>
                        {{-- Bảng danh sách Nhiệm vụ --}}
                        <table id="nhiem-vu" class="table table-bordered">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:45%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead>
                                <tr class="text-center">
                                    <th class="align-middle text-bold">TT</th>
                                    <th class="align-middle text-bold">Nhiệm vụ</th>
                                    <th class="align-middle text-bold">Nhiệm vụ phát sinh (đánh dấu x)</th>
                                    <th class="align-middle text-bold">Trước hạn</th>
                                    <th class="align-middle text-bold">Đúng hạn</th>
                                    <th class="align-middle text-bold">Quá hạn</th>
                                    <th class="align-middle text-bold">Lùi, chưa triển khai</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ket_qua_muc_B as $ket_qua_muc_B)
                                    <tr>
                                        <td>+</td>
                                        <td class="text-justify">
                                            <p>{{ $ket_qua_muc_B->noi_dung }}</p>
                                        </td>
                                        <td><input type="checkbox" value="1"
                                                @if ($ket_qua_muc_B->nhiem_vu_phat_sinh == 1) checked @else disabled @endif></td>
                                        <td><input type="radio" value="truoc_han"
                                                @if ($ket_qua_muc_B->hoan_thanh_nhiem_vu == 'truoc_han') checked @else disabled @endif></td>
                                        <td><input type="radio" value="dung_han"
                                                @if ($ket_qua_muc_B->hoan_thanh_nhiem_vu == 'dung_han') checked @else disabled @endif></td>
                                        <td><input type="radio" value="qua_han"
                                                @if ($ket_qua_muc_B->hoan_thanh_nhiem_vu == 'qua_han') checked @else disabled @endif></td>
                                        <td><input type="radio" value="lui_han"
                                                @if ($ket_qua_muc_B->hoan_thanh_nhiem_vu == 'lui_han') checked @else disabled @endif></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <br>
                        {{-- Mục Lý do Điểm thưởng --}}
                        <h6>&emsp;&emsp;&emsp;- Các nhiệm vụ có sáng kiến, đổi mới, sáng tạo, mang lại hiệu quả được áp dụng
                            điểm thưởng: <i>(mô tả tóm tắt cách thức, hiệu quả mang lại)</i></h6>
                        <div class="form-group">
                            <textarea class="form-control" id="ly_do_diem_cong" name="ly_do_diem_cong" rows="7" readonly>{{ $ly_do_diem_cong->noi_dung }}</textarea>
                        </div>
                        {{-- Mục Lý do Điểm trừ --}}
                        <h6>&emsp;&emsp;&emsp;- Lý do áp dụng điểm trừ: <i>(mô tả tóm tắt)</i></h6>
                        <div class="form-group">
                            <textarea class="form-control" id="ly_do_diem_tru" name="ly_do_diem_tru" rows="7" readonly>{{ $ly_do_diem_tru->noi_dung }}</textarea>
                        </div>
                        {{-- Mục Cá nhân tự xếp loại --}}
                        <h6 class="text-bold">&emsp;&emsp;&emsp;C. Cá nhân tự xếp loại: <i>(Chọn 01 trong 04 ô tương ứng
                                dưới đây)</i></h6>
                        {{-- Danh sách xếp loại --}}
                        <table class="table table-borderless">
                            <colgroup>
                                <col style="width:20%;">
                                <col style="width:5%;">
                                <col style="width:20%;">
                                <col style="width:5%;">
                                <col style="width:20%;">
                                <col style="width:5%;">
                                <col style="width:20%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    {{-- Xếp loại A --}}
                                    <td class="text-center">
                                        <input type="radio" name="tu_danh_gia" value="A" id="hoan_thanh_xuat_sac"
                                            class="form-control"
                                            @if ($phieu_danh_gia->ca_nhan_tu_xep_loai == $xep_loai->where('ma_xep_loai', 'A')->first()->ma_xep_loai) 
                                            checked @else disabled @endif>
                                        <b>Hoàn thành suất sắc <br>nhiệm vụ<br>(Loại
                                            A)</b><br>{{ $xep_loai->where('ma_xep_loai', 'A')->first()->diem_toi_thieu }}
                                        điểm trở lên
                                    </td>
                                    <td></td>
                                    {{-- Xếp loại B --}}
                                    <td class="text-center">
                                        <input type="radio" name="tu_danh_gia" value="B" id="hoan_thanh_tot"
                                            class="form-control"
                                            @if ($phieu_danh_gia->ca_nhan_tu_xep_loai == $xep_loai->where('ma_xep_loai', 'B')->first()->ma_xep_loai) 
                                            checked @else disabled @endif>
                                        <b>Hoàn thành tốt <br>nhiệm vụ<br>(Loại B)</b><br>Từ
                                        {{ $xep_loai->where('ma_xep_loai', 'B')->first()->diem_toi_thieu }} điểm đến
                                        {{ $xep_loai->where('ma_xep_loai', 'A')->first()->diem_toi_thieu - 1 }}
                                        điểm
                                    </td>
                                    <td></td>
                                    {{-- Xếp loại C --}}
                                    <td class="text-center">
                                        <input type="radio" name="tu_danh_gia" value="C" id="hoan_thanh"
                                            class="form-control"
                                            @if ($phieu_danh_gia->ca_nhan_tu_xep_loai == $xep_loai->where('ma_xep_loai', 'C')->first()->ma_xep_loai) 
                                            checked @else disabled @endif>
                                        <b>Hoàn thành <br>nhiệm vụ<br>(Loại
                                            C)</b><br>{{ $xep_loai->where('ma_xep_loai', 'C')->first()->diem_toi_thieu }}
                                        điểm đến {{ $xep_loai->where('ma_xep_loai', 'B')->first()->diem_toi_thieu - 1 }}
                                        điểm
                                    </td>
                                    <td></td>
                                    {{-- Xếp loại D --}}
                                    <td class="text-center">
                                        <input type="radio" name="tu_danh_gia" value="D" id="khong_hoan_thanh"
                                            class="form-control"
                                            @if ($phieu_danh_gia->ca_nhan_tu_xep_loai == $xep_loai->where('ma_xep_loai', 'D')->first()->ma_xep_loai) 
                                            checked @else disabled @endif>
                                        <b>Không hoàn thành <br>nhiệm vụ<br>(Loại D)</b><br>Từ
                                        {{ $xep_loai->where('ma_xep_loai', 'C')->first()->diem_toi_thieu - 1 }} điểm
                                        trở xuống
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>

                        {{-- Phần Thông tin Người ký --}}
                        <table class="table table-borderless">
                            <colgroup>
                                <col style="width:40%;">
                                <col style="width:20%;">
                                <col style="width:40%;">
                            </colgroup>
                            <tbody>
                                <tr>
                                    <td class="py-0"></td>
                                    <td class="py-0"></td>
                                    <td class="text-center font-italic py-0">Ngày {{ $ngay_thuc_hien_danh_gia->day }} tháng
                                        {{ $ngay_thuc_hien_danh_gia->month }} năm {{ $ngay_thuc_hien_danh_gia->year }} </td>
                                </tr>
                                <tr>
                                    <td class="text-center text-bold py-0">LÃNH ĐẠO ĐƠN VỊ</td>
                                    <td class="py-0"></td>
                                    <td class="text-center text-bold py-0">
                                        NGƯỜI TỰ ĐÁNH GIÁ
                                        <br><br><br><br><br>
                                        {{ $phieu_danh_gia->name }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <br>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
                @if ($phieu_danh_gia->ma_trang_thai == 11)
                    <div class="text-right">
                        <a href="{{ route('phieudanhgia.canhan.edit', $phieu_danh_gia->ma_phieu_danh_gia) }}"><button
                                type="button" class="btn bg-warning text-nowrap mb-2 ml-2 col-1"
                                name="edit">Sửa</button></a>
                        <a href="{{ route('phieudanhgia.canhan.send', $phieu_danh_gia->ma_phieu_danh_gia) }}"><button
                                type="button" class="btn bg-olive text-nowrap mb-2 ml-2 col-1"
                                name="send">Gửi</button></a>
                    </div>
                @endif
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
@stop

@section('css')
    <style>
        table.dataTable tbody tr.selected>* {
            box-shadow: inset 0 0 0 9999px rgb(184, 184, 184) !important;
        }

        input[type="checkbox"] {
            pointer-events: none;
        }

        input[type="radio"] {
            pointer-events: none;
        }
    </style>
@stop

@section('js')
    <script>
        $(function() {
            const table = $("#nhiem-vu").DataTable({
                lengthChange: false,
                pageLength: 20,
                searching: false,
                autoWidth: false,
                paging: false,
                ordering: false,
                info: false,
                columnDefs: [
                    // Center align both header and body content of columns 1, 2 & 3
                    {
                        className: "dt-center",
                        targets: [0, 1, 2, 3, 4, 5, 6]
                    }
                ],
            })
        })
    </script>
@stop
