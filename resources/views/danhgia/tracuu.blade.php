@extends('dashboard')

@section('title', 'Tra cứu Phiếu đánh giá')

@section('heading')
    <form action="{{ route('phieudanhgia.tracuu') }}" method="post" id="mauphieudanhgia">
        @csrf
        <div class="row">
            <div class="col-12">
                Tra cứu Phiếu đánh giá
            </div>
            <div class="d-flex justify-content-start col-12 mt-2">
                <label for="ma_don_vi" class="h6 mt-2 mr-2 col-auto">Đơn vị: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-4">
                    @foreach ($ds_don_vi as $ds_don_vi)
                        <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                            {{ $ds_don_vi->ten_don_vi }}</option>
                    @endforeach
                </select>
                <label for="thang_danh_gia" class="h6 mt-2 mx-2">Tháng: </label>
                <input id="thang_danh_gia" name="thang_danh_gia" type="number" min="1" max="12"
                    value="{{ $thoi_diem_danh_gia->month }}" class="form-control text-center col-1"><label
                    class="h6 mt-2 mx-2">/</label><input type="number" name="nam_danh_gia"
                    value="{{ $thoi_diem_danh_gia->year }}" class="form-control text-center col-1">
                <label for="thang_danh_gia" class="h6 mt-2 mx-2 col-auto">Xếp loại: </label>
                <select id="xep_loai" name="xep_loai" class="form-control custom-select col-1">
                    @foreach ($dm_xep_loai as $xep_loai)
                        <option value="{{ $xep_loai->ma_xep_loai }}" @if ($ma_xep_loai_da_chon == $xep_loai->ma_xep_loai) selected @endif>
                            {{ $xep_loai->ma_xep_loai }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn bg-olive form-control ml-2 col-1">Xem</button>
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
                                <col style="width:25%;">
                                <col style="width:15%;">
                                <col style="width:15%;">
                                <col style="width:15%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Họ và tên</th>
                                    <th class="text-center align-middle">Chức vụ</th>
                                    <th class="text-center align-middle">Điểm được duyệt</th>
                                    <th class="text-center align-middle">Xếp loại</th>
                                    <th class="text-center align-middle">Ghi chú</th>
                                    <th class="text-center align-middle" style="display: none;">Phòng</th>
                                    <th class="text-center align-middle" style="display: none;">Đơn vị</th>
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
                                        </tr>
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
                                                <td style="display: none;">{{ $dv->ten_phong }}</td>
                                                <td style="display: none;">{{ $dv->ten_don_vi }}</td>
                                            </tr>
                                        @endif
                                        @php $j = 1 @endphp
                                        @foreach ($phieu_danh_gia->where('ma_phong', $ph->ma_phong) as $phieu)
                                            <tr>
                                                <td class="text-center">{{ $j++ }}</td>
                                                <td>
                                                    <a href="{{ route('phieudanhgia.captren.show', $phieu->ma_phieu_danh_gia) }}">{{ $phieu->name }}</a>
                                                </td>
                                                <td class="text-center">{{ $phieu->ten_chuc_vu }}</td>
                                                <td class="text-center">{{ $phieu->tong_diem_danh_gia }}</td>
                                                <td class="text-center">{{ $phieu->ket_qua_xep_loai }}</td>
                                                <td class="text-center">{{ $phieu->ly_do }}</td>
                                                <td style="display: none;">{{ $phieu->ten_phong }}</td>
                                                <td style="display: none;">{{ $phieu->ten_don_vi }}</td>
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
