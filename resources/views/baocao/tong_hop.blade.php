@extends('dashboard')

@section('title', 'Báo cáo tiến độ tháng')

@section('heading')
    <form action="{{ route('baocao.tonghop') }}" method="post" id="form">
        @csrf
        <div class="d-flex">
            <div class="col-4">
                Báo cáo tiến độ tháng
            </div>
            <div class="d-flex justify-content-end col-8">
                <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
                    @foreach ($ds_don_vi as $ds_don_vi)
                        <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                            {{ $ds_don_vi->ten_don_vi }}</option>
                    @endforeach
                </select>
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
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:30%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                                <col style="width:5%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle" rowspan="2">STT</th>
                                    <th class="text-center align-middle" rowspan="2">Tên đơn vị</th>
                                    <th class="text-center align-middle" colspan="3">Công chức</th>
                                    <th class="text-center align-middle" colspan="2">Cá nhân <br>đánh giá</th>
                                    <th class="text-center align-middle" colspan="2">Cấp trên <br>đánh giá</th>
                                    <th class="text-center align-middle" colspan="2">Chi cục trưởng duyệt/<br>phê duyệt
                                    </th>
                                    <th class="text-center align-middle" colspan="2">Cục trưởng<br>phê duyệt</th>
                                    <th class="text-center align-middle" colspan="2">Hội đồng TĐKT<br>phê duyệt</th>
                                </tr>
                                <tr>
                                    <th class="text-center align-middle">Tổng số</th>
                                    <th class="text-center align-middle">Đi học, nghỉ sinh</th>
                                    <th class="text-center align-middle">Tự đánh giá</th>
                                    <th class="text-center align-middle">Chưa lập phiếu</th>
                                    <th class="text-center align-middle">Chưa gửi phiếu</th>
                                    <th class="text-center align-middle">Chưa đánh giá</th>
                                    <th class="text-center align-middle">Đã đánh giá</th>
                                    <th class="text-center align-middle">Chưa Duyệt<br>/phê duyệt</th>
                                    <th class="text-center align-middle">Đã phê duyệt</th>
                                    <th class="text-center align-middle">Chưa phê duyệt</th>
                                    <th class="text-center align-middle">Đã phê duyệt</th>
                                    <th class="text-center align-middle">Chưa phê duyệt</th>
                                    <th class="text-center align-middle">Đã phê duyệt</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($danh_sach as $ds)
                                    <tr>
                                        <td class="text-center">{{ $ds['stt'] }}</td>
                                        <td>{{ $ds['ten_don_vi'] }}</td>
                                        <td class="text-center">{{ $ds['tong_so_cong_chuc'] }}</td>
                                        <td class="text-center">{{ $ds['ca_nhan_khong_tu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_tu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_chua_lap_phieu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_chua_gui_phieu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_cap_tren_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['cap_tren_da_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_chi_cuc_truong_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['chi_cuc_truong_da_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_cuc_truong_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['cuc_truong_da_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_hoi_dong_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['hoi_dong_da_phe_duyet'] }} </td>
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
