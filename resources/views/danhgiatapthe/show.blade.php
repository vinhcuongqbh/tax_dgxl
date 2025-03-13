@extends('dashboard')

@section('title', 'Nhập kết quả xếp loại năm của tập thể')

@section('heading')
    <form action="{{ route('tapthe.tracuu') }}" method="get" id="form">
        @csrf
        <div class="d-flex">
            <div class="col-4">
                KQXL năm của tập thể
            </div>
            <div class="d-flex justify-content-end col-8">
                <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
                    @foreach ($ds_don_vi as $ds_don_vi)
                        <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                            {{ $ds_don_vi->ten_don_vi }}</option>
                    @endforeach
                </select>
                <label for="nam_danh_gia" class="h6 mt-2 mx-2">Năm: </label><input type="number" name="nam_danh_gia"
                    value="{{ $thoi_diem_danh_gia->year }}" class="form-control  text-center">
                <button type="submit" class="btn bg-olive form-control ml-2">Xem</button>
            </div>
        </div>
    </form>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-12">
            </div>
        </div>
    </div><!-- /.container-fluid -->
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:7%;">
                                <col style="width:30%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">STT</th>
                                    <th class="text-center align-middle" rowspan="2">Mã phòng/đội</th>
                                    <th class="text-center align-middle" rowspan="2">Tên phòng/đội</th>
                                    <th class="text-center align-middle" colspan="4">Mức độ hoàn thành nhiệm vụ chuyên
                                        môn</th>
                                    <th class="text-center align-middle" colspan="4">Phân loại tập thể</th>
                                    <th class="text-center align-middle" rowspan="2">Ghi chú</th>
                                </tr>
                                <tr>
                                    @foreach ($xep_loai as $xl)
                                        <th class="text-center align-middle">{{ $xl->ma_xep_loai }}</th>
                                    @endforeach
                                    @foreach ($xep_loai as $xl)
                                        <th class="text-center align-middle">{{ $xl->ten_xep_loai }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 0 @endphp
                                @foreach ($don_vi as $dv)
                                    @if ($kqxl->where('ma_don_vi_cap_tren', $dv->ma_don_vi)->count() > 0)
                                        @php $i++ @endphp
                                        <tr>
                                            <td class="text-center text-bold bg-olive">{{ $i }}</td>
                                            <td class="text-bold bg-olive" colspan="2">{{ $dv->ten_don_vi }}</td>
                                            <td style="display: none;"></td>
                                            @foreach ($xep_loai as $xl)
                                                <td class="text-center text-bold bg-olive">
                                                    @php
                                                        $item = $kqxl->where('ma_phong', $dv->ma_don_vi)->first();
                                                        if ($item) {
                                                            $ket_qua = $item['ket_qua_chuyen_mon'];
                                                            if ($ket_qua == $xl->ma_xep_loai) {
                                                                echo 'x';
                                                            }
                                                        }
                                                    @endphp
                                                </td>
                                            @endforeach
                                            @foreach ($xep_loai as $xl)
                                                <td class="text-center text-bold bg-olive">
                                                    @php
                                                        $item = $kqxl->where('ma_phong', $dv->ma_don_vi)->first();
                                                        if ($item) {
                                                            $ket_qua = $item['ket_qua_xep_loai'];
                                                            if ($ket_qua == $xl->ma_xep_loai) {
                                                                echo 'x';
                                                            }
                                                        }
                                                    @endphp
                                                </td>
                                            @endforeach
                                            <td class="text-bold bg-olive"></td>
                                        </tr>
                                    @endif
                                    @php $j = 1 @endphp
                                    @foreach ($kqxl->where('ma_don_vi_cap_tren', $dv->ma_don_vi) as $kq)
                                        <tr>
                                            <td class="text-center">{{ $i }}.{{ $j++ }}</td>
                                            <td class="text-center">{{ $kq->ma_phong }}</td>
                                            <td>{{ $kq->ten_phong }}</td>
                                            @foreach ($xep_loai as $xl)
                                                <td class="text-center">
                                                    @if ($kq->ket_qua_chuyen_mon == $xl->ma_xep_loai)
                                                        x
                                                    @endif
                                                </td>
                                            @endforeach
                                            @foreach ($xep_loai as $xl)
                                                <td class="text-center">
                                                    @if ($kq->ket_qua_xep_loai == $xl->ma_xep_loai)
                                                        x
                                                    @endif
                                                </td>
                                            @endforeach
                                            <td></td>
                                        </tr>
                                    @endforeach
                                @endforeach
                            </tbody>
                        </table>
                    </div>
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
