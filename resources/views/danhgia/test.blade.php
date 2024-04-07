@extends('dashboard')

@section('title', 'Thông báo kết quả xếp loại')

@section('heading')
    <form action="{{ route('phieudanhgia.tbKQXLThang') }}" method="get" id="mauphieudanhgia">
        <div class="d-flex">
            <div class="col-9">
                Thông báo kết quả xếp loại
            </div>
            <div class="d-flex justify-content-end col-3">
                <label for="thang_danh_gia" class="h6 mt-2 mx-2">Tháng: </label>
                {{-- <input id="thang_danh_gia" name="thang_danh_gia" type="number" min="1" max="12"
                    value="{{ $thoi_diem_danh_gia->month }}" class="form-control col-3"><label
                    class="h6 mt-2 mx-2">/</label><input type="number" name="nam_danh_gia"
                    max="{{ $thoi_diem_danh_gia->year }}" value="{{ $thoi_diem_danh_gia->year }}"
                    class="form-control col-3"> --}}
                <button type="submit" class="btn bg-olive form-control ml-2">Xem</button>
            </div>
        </div>
    </form>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:15%;">
                                <col style="width:20%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">STT</th>
                                    <th class="text-center align-middle" rowspan="2">Họ và tên</th>
                                    <th class="text-center align-middle" rowspan="2">Chức vụ</th>
                                    <th class="text-center align-middle" colspan="3">Điểm cá nhân tự chấm</th>
                                    <th class="text-center align-middle" colspan="3">Cấp có thẩm quyền đánh giá</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">Điểm tự chấm</th>
                                    <th class="text-center align-middle">Điểm cộng</th>
                                    <th class="text-center align-middle">Điểm trừ</th>
                                    <th class="text-center align-middle">Điểm phê duyệt</th>
                                    <th class="text-center align-middle">Điểm cộng</th>
                                    <th class="text-center align-middle">Điểm trừ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1 @endphp
                                @foreach ($don_vi as $dv)
                                    <tr>
                                        <td class="text-center text-bold bg-olive">{{ $i++ }}</td>
                                        <td class="text-bold bg-olive" colspan="8">{{ $dv->ten_don_vi }}</td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                        <td style="display: none;"></td>
                                    </tr>
                                    @foreach ($phong->where('ma_don_vi_cap_tren', $dv->ma_don_vi) as $ph)
                                        <tr>
                                            <td class="text-center"></td>
                                            <td class="text-bold" colspan="8">{{ $ph->ten_phong }}</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                        </tr>
                                        @php $j = 1 @endphp
                                        @foreach ($phieu_danh_gia->where('ma_phong', $ph->ma_phong) as $phieu)
                                            <tr>
                                                <td class="text-center">{{ $j++ }}</td>
                                                <td>{{ $phieu->name }}</td>
                                                <td class="text-center">{{ $phieu->ten_chuc_vu }}</td>
                                                <td class="text-center">{{ $phieu->diem_tu_cham }}</td>
                                                <td class="text-center">{{ $phieu->diem_cong_tu_cham }}</td>
                                                <td class="text-center">{{ $phieu->diem_tru_tu_cham }}</td>
                                                <td class="text-center">{{ $phieu->diem_danh_gia }}</td>
                                                <td class="text-center">{{ $phieu->diem_cong_danh_gia }}</td>
                                                <td class="text-center">{{ $phieu->diem_tru_danh_gia }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
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
                dom: 'Bfrtip',
                buttons: [{
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
@stop
