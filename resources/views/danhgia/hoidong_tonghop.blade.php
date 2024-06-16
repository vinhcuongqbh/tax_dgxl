@extends('dashboard')

@section('title', 'Danh sách Phiếu đánh giá')

@section('heading')
    <form action="{{ route('phieudanhgia.hoidong.tonghopdukien') }}" method="post" id="mauphieudanhgia">
        @csrf
        <div class="d-flex">
            <div class="col-8">
                Hội đồng đánh giá, xếp loại Cục trưởng
            </div>
            <div class="d-flex justify-content-end col-4">
                <label for="thang_danh_gia" class="h6 mt-2 mx-2">Tháng: </label>
                <input id="thang_danh_gia" name="thang_danh_gia" type="number" min="1" max="12"
                    value="{{ $thoi_diem_danh_gia->month }}" class="form-control text-center"><label
                    class="h6 mt-2 mx-2">/</label><input type="number" name="nam_danh_gia"
                    value="{{ $thoi_diem_danh_gia->year }}" class="form-control  text-center">
                <button type="submit" class="btn bg-olive form-control ml-2">Xem</button>
            </div>
        </div>
    </form>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('phieudanhgia.hoidong.tonghopdanhgia') }}" method="post" id="formSubmit">
                    @csrf
                    <div class="card card-default">
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <colgroup>
                                    <col style="width:5%;">
                                    <col style="width:10%;">
                                    <col style="width:15%;">
                                    <col style="width:10%;">
                                    <col style="width:15%;">
                                    <col style="width:15%;">
                                    <col style="width:10%;">
                                    <col style="width:10%;">
                                    <col style="width:10%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">STT</th>
                                        <th class="text-center align-middle">Kỳ đánh giá</th>
                                        <th class="text-center align-middle">Họ và tên</th>
                                        <th class="text-center align-middle">Chức vụ</th>
                                        <th class="text-center align-middle">Phòng/Đội</th>
                                        <th class="text-center align-middle">Đơn vị</th>
                                        <th class="text-center align-middle">Điểm Cục trưởng tự chấm</th>
                                        <th class="text-center align-middle">Điểm đánh giá cho Cục trưởng</th>
                                        <th class="text-center align-middle">Kết quả xếp loại</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @if ($danh_sach->count() != 0)
                                        @foreach ($danh_sach as $danh_sach)
                                            <tr>
                                                <td class="text-center">
                                                    <div class="">{{ $i++ }}</div>
                                                </td>
                                                <td class="text-center">
                                                    {{ date('m', strtotime($danh_sach->thoi_diem_danh_gia)) }}/{{ date('Y', strtotime($danh_sach->thoi_diem_danh_gia)) }}
                                                </td>
                                                <td><a
                                                        href="{{ route('phieudanhgia.hoidong.create', $danh_sach->ma_phieu_danh_gia) }}">
                                                        {{ $danh_sach->name }}</a></td>
                                                <td class="text-center">{{ $danh_sach->ten_chuc_vu }}</td>
                                                <td class="text-center">{{ $danh_sach->ten_phong }}</td>
                                                <td class="text-center">{{ $danh_sach->ten_don_vi }}</td>
                                                <td class="text-center">{{ $danh_sach->tong_diem_tu_cham }}</td>
                                                <td class="text-center">{{ $danh_sach->tong_diem_danh_gia }}</td>
                                                <td class="text-center">{{ $danh_sach->ket_qua_xep_loai }}</td>
                                            </tr>
                                        @endforeach
                                        @if ($danh_sach->count() != 0)
                                            <tr>
                                                <td></td>
                                                <td class="text-bold" colspan="6">Tổng hợp kết quả</td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td class="text-center"><input type="number" max="100"
                                                        id="diem_trung_binh" name="diem_trung_binh"
                                                        class="text-center col-10" value="{{ $diem_trung_binh }}"
                                                        onchange="xep_loai()"></td>
                                                <td class="text-center"><label
                                                        id="xep_loai_trung_binh">{{ $xep_loai_trung_binh }}</label></td>
                                            </tr>
                                        @endif
                                    @endif
                                </tbody>
                            </table>
                            @if ($danh_sach->count() != 0)
                                <input type="hidden" name="ma_phieu_danh_gia" id="ma_phieu_danh_gia"
                                    value="{{ $danh_sach->first()->ma_phieu_danh_gia }}">
                            @endif
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </form>
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
@stop

@section('css')
@stop

@section('js')
    <!-- Datatable -->
    <script>
        $(function() {
            $("#table").DataTable({
                lengthChange: false,
                searching: true,
                autoWidth: false,
                ordering: false,
                paging: false,
                scrollCollapse: true,
                scrollX: true,
                scrollY: 1000,
                info: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tổng hợp',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            //document.querySelector('#formSubmit').submit();  
                            document.forms["formSubmit"].submit();
                        },
                    },
                    {
                        extend: 'spacer',
                        style: 'bar',
                        text: 'Xuất:'
                    },
                    //'csv',
                    'excel',
                    'pdf',
                ],
                language: {
                    url: '/plugins/datatables/vi.json'
                },
            }).buttons().container().appendTo('#table_wrapper .col-md-6:eq(0)');
        });
    </script>

    {{-- Tự động xếp loại dựa trên Tổng điểm --}}
    <script>
        let diem_trung_binh;
        xep_loai();

        function xep_loai() {
            let diem_trung_binh = document.querySelector('#diem_trung_binh').value;
            let xep_loai_trung_binh = document.querySelector("#xep_loai_trung_binh");

            if (diem_trung_binh >= {{ $xep_loai->where('ma_xep_loai', 'A')->first()->diem_toi_thieu }}) {
                xep_loai_trung_binh.innerHTML = "A";
            } else if (diem_trung_binh >= {{ $xep_loai->where('ma_xep_loai', 'B')->first()->diem_toi_thieu }}) {
                xep_loai_trung_binh.innerHTML = "B";
            } else if (diem_trung_binh >= {{ $xep_loai->where('ma_xep_loai', 'C')->first()->diem_toi_thieu }}) {
                xep_loai_trung_binh.innerHTML = "C";
            } else {
                xep_loai_trung_binh.innerHTML = "D";
            }
        }
    </script>
@stop
