@extends('dashboard')

@section('title', 'Thông báo kết quả xếp loại năm')

@section('heading')
    <form action="{{ route('phieudanhgia.thongbaonam') }}" method="post" id="mauphieudanhgia">
        @csrf
        <div class="d-flex">
            <div class="col-4">
                Thông báo KQXL năm
            </div>
            <div class="d-flex justify-content-end col-8">
                <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
                    @foreach ($ds_don_vi as $ds_don_vi)
                        <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                            {{ $ds_don_vi->ten_don_vi }}</option>
                    @endforeach
                </select>
                <label class="h6 mt-2 mx-2">Năm</label><input type="number" id="nam_danh_gia" name="nam_danh_gia"
                    value="{{ $nam_danh_gia }}" class="form-control text-center">
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
                                <col style="width:25%;">
                                <col style="width:20%;">
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
                                    <th class="text-center align-middle" colspan="4">Mức độ đánh giá xếp loại</th>
                                    <th class="text-center align-middle" rowspan="2">Bản tự đánh giá xếp loại (ký số)</th>
                                </tr>
                                <tr>
                                    @foreach ($xep_loai as $xl)
                                        <th class="text-center align-middle">{{ $xl->ten_xep_loai }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1 @endphp
                                @foreach ($don_vi as $dv)
                                    @if ($phieu_danh_gia->where('ma_don_vi', $dv->ma_don_vi)->count() > 0)
                                        <tr>
                                            <td class="text-center text-bold bg-olive">{{ $i++ }}</td>
                                            <td class="text-bold bg-olive" colspan="7">{{ $dv->ten_don_vi }}</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                    @endif
                                    @foreach ($phong->where('ma_don_vi_cap_tren', $dv->ma_don_vi) as $ph)
                                        @if ($phieu_danh_gia->where('ma_phong', $ph->ma_phong)->count() > 0)
                                            <tr>
                                                <td class="text-center"></td>
                                                <td class="text-bold" colspan="7">{{ $ph->ten_phong }}</td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                            </tr>
                                        @endif
                                        @php $j = 1 @endphp
                                        @foreach ($phieu_danh_gia->where('ma_phong', $ph->ma_phong) as $phieu)
                                            <tr>
                                                <td class="text-center">{{ $j++ }}</td>
                                                <td>{{ $phieu->name }}</td>
                                                <td class="text-center">{{ $phieu->ten_chuc_vu }}</td>
                                                @foreach ($xep_loai as $xl)
                                                    <td class="text-center">
                                                        @if ($phieu->kqxl == $xl->ma_xep_loai)
                                                            x
                                                        @endif
                                                    </td>
                                                @endforeach
                                                <td class="text-center">
                                                    @if (isset($phieu->file_tu_dgxl))
                                                        <a href="{{ $phieu->file_tu_dgxl }}" target="_blank" rel="noopener noreferrer"><img
                                                                src="/img/pdf.png" width="30" height="30"></a>
                                                    @endif
                                                </td>
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
