@extends('dashboard')

@section('title', 'Danh sách Phiếu đánh giá')

@section('heading')
    Danh sách Phiếu Đánh giá
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
                                <col style="width:10%;">
                                <col style="width:15%;">
                                <col style="width:15%;">
                                <col style="width:20%;">
                                <col style="width:15%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                            </colgroup>
                            <thead style="text-center">
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Kỳ đánh giá</th>
                                    <th class="text-center align-middle">Họ và tên</th>
                                    <th class="text-center align-middle">Chức vụ</th>
                                    <th class="text-center align-middle">Phòng/Đội</th>
                                    <th class="text-center align-middle">Đơn vị</th>
                                    <th class="text-center align-middle">Điểm tự chấm</th>
                                    <th class="text-center align-middle">Cá nhân tự xếp loại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($danh_sach as $danh_sach)
                                    <tr>
                                        <td class="text-center">{{ $i++ }}</td>
                                        <td class="text-center">
                                            {{ date('m', strtotime($danh_sach->thoi_diem_danh_gia)) }}/{{ date('Y', strtotime($danh_sach->thoi_diem_danh_gia)) }}
                                        </td>
                                        </td>
                                        <td><a
                                                href="{{ route('phieudanhgia.canhan.show', $danh_sach->ma_phieu_danh_gia) }}">
                                                {{ $danh_sach->user->name }}
                                            </a>
                                        </td>
                                        <td class="text-center">{{ $danh_sach->chuc_vu->ten_chuc_vu }}</td>
                                        <td class="text-center">{{ $danh_sach->phong->ten_phong }}</td>
                                        <td class="text-center">{{ $danh_sach->don_vi->ten_don_vi }}</td>
                                        <td class="text-center">{{ $danh_sach->tong_diem_tu_cham }}</td>
                                        <td class="text-center">{{ $danh_sach->ca_nhan_tu_xep_loai }} <br> {{ $danh_sach->ly_do->ly_do }}</td>
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
                pageLength: 20,
                searching: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tạo mới',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('phieudanhgia.canhan.create') }}';
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
@stop
