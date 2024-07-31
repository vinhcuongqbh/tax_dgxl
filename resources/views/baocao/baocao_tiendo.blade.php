@extends('dashboard')

@section('title', 'Báo cáo tiến độ tháng')

@section('heading')
    <form action="{{ route('baocao.baocao_tiendo') }}" method="post" id="form">
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
                            {{-- <colgroup>
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
                            </colgroup> --}}
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Tên đơn vị</th>
                                    <th class="text-center align-middle">Tổng số công chức</th>
                                    <th class="text-center align-middle">Đi học, nghỉ sinh</th>
                                    <th class="text-center align-middle">Cá nhân tự đánh giá</th>
                                    <th class="text-center align-middle">Cá nhân chưa lập phiếu</th>
                                    <th class="text-center align-middle">Cá nhân đã lập phiếu</th>
                                    <th class="text-center align-middle">Cá nhân chưa gửi phiếu</th>
                                    <th class="text-center align-middle">Cá nhân đã gửi phiếu</th>
                                    <th class="text-center align-middle">Chờ cấp trên đánh giá</th>
                                    <th class="text-center align-middle">Chờ cấp thẩm quyền phê duyệt</th>
                                    <th class="text-center align-middle">Chờ Chi cục trưởng phê duyệt</th>
                                    <th class="text-center align-middle">Chờ Cục trưởng phê duyệt</th>
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
                                        <td class="text-center">{{ $ds['ca_nhan_da_lap_phieu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_chua_gui_phieu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_da_gui_phieu_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_cap_tren_danh_gia'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_chi_cuc_truong_phe_duyet'] }} </td>
                                        <td class="text-center">{{ $ds['ca_nhan_cho_cuc_truong_phe_duyet'] }} </td>
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
