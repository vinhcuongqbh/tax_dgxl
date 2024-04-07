@extends('dashboard')

@section('title', 'Báo cáo kết quả xếp loại quý')

@section('heading')
    <form action="{{ route('phieudanhgia.baocaoquy') }}" method="get" id="mauphieudanhgia">
        <div class="d-flex">
            <div class="col-9">
                Báo cáo kết quả xếp loại quý
            </div>
            <div class="d-flex justify-content-end col-3">
                <label for="quy_danh_gia" class="h6 mt-2 mx-2">Quý: </label>
                <input id="quy_danh_gia" name="quy_danh_gia" type="number" min="1" max="4"
                    value="{{ $quy_danh_gia }}" class="form-control col-3"><label class="h6 mt-2 mx-2">/</label><input
                    type="number" id="nam_danh_gia" name="nam_danh_gia" 
                    value="{{ $nam_danh_gia }}" class="form-control col-3">
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
                                <col style="width:18%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="3">STT</th>
                                    <th class="text-center align-middle" rowspan="3">Tên đơn vị</th>
                                    <th class="text-center align-middle" rowspan="3">Tổng số người</th>
                                    <th class="text-center align-middle" colspan="8">Mức độ phân loại đánh giá</th>
                                    <th class="text-center align-middle" colspan="2" rowspan="2">Số lượng CC, NLĐ
                                        không xếp loại</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle" colspan="2">Loại A</th>
                                    <th class="text-center align-middle" colspan="2">Loại B</th>
                                    <th class="text-center align-middle" colspan="2">Loại C</th>
                                    <th class="text-center align-middle" colspan="2">Loại D</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">Số lượng (người)</th>
                                    <th class="text-center align-middle">Tỷ lệ (%)</th>
                                    <th class="text-center align-middle">Số lượng (người)</th>
                                    <th class="text-center align-middle">Tỷ lệ (%)</th>
                                    <th class="text-center align-middle">Số lượng (người)</th>
                                    <th class="text-center align-middle">Tỷ lệ (%)</th>
                                    <th class="text-center align-middle">Số lượng (người)</th>
                                    <th class="text-center align-middle">Tỷ lệ (%)</th>
                                    <th class="text-center align-middle">Số lượng (người)</th>
                                    <th class="text-center align-middle">Tỷ lệ (%)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($danh_sach as $ds)
                                    <tr>
                                        <td class="text-center">{{ $ds['stt'] }}</td>
                                        <td>{{ $ds['ten_don_vi'] }}</td>
                                        <td class="text-center">{{ $ds['so_luong'] }}</td>
                                        <td class="text-center">{{ $ds['so_luong_loai_A'] }} </td>
                                        <td class="text-center">{{ $ds['ti_le_loai_A'] }} </td>
                                        <td class="text-center">{{ $ds['so_luong_loai_B'] }} </td>
                                        <td class="text-center">{{ $ds['ti_le_loai_B'] }} </td>
                                        <td class="text-center">{{ $ds['so_luong_loai_C'] }} </td>
                                        <td class="text-center">{{ $ds['ti_le_loai_C'] }} </td>
                                        <td class="text-center">{{ $ds['so_luong_loai_D'] }} </td>
                                        <td class="text-center">{{ $ds['ti_le_loai_D'] }} </td>
                                        <td class="text-center">{{ $ds['so_luong_khong_xep_loai'] }} </td>
                                        <td class="text-center">{{ $ds['ti_le_khong_xep_loai'] }} </td>
                                    </tr>
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
