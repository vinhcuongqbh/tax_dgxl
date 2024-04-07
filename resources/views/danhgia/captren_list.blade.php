@extends('dashboard')

@section('title', 'Danh sách Phiếu đánh giá')

@section('heading')
    Cấp trên đánh giá
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
                                    <th class="text-center align-middle">Điểm tự chấm</th>
                                    <th class="text-center align-middle">Điểm cấp trên đánh giá</th>
                                    <th class="text-center align-middle">Kết quả xếp loại</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @if (isset($danh_sach))
                                    @foreach ($danh_sach as $danh_sach)
                                        <tr>
                                            <td class="text-center">
                                                <div class="">{{ $i++ }}</div>
                                            </td>
                                            <td class="text-center">
                                                {{ date('m', strtotime($danh_sach->thoi_diem_danh_gia)) }}/{{ date('Y', strtotime($danh_sach->thoi_diem_danh_gia)) }}
                                            </td>
                                            <td><a
                                                    href="{{ route('phieudanhgia.captren.create', $danh_sach->ma_phieu_danh_gia) }}">
                                                    {{ $danh_sach->name }}</a></td>
                                            <td class="text-center">{{ $danh_sach->ten_chuc_vu }}</td>
                                            <td class="text-center">{{ $danh_sach->ten_phong }}</td>
                                            <td class="text-center">{{ $danh_sach->ten_don_vi }}</td>
                                            <td class="text-center">{{ $danh_sach->tong_diem_tu_cham }}</td>
                                            <td class="text-center">{{ $danh_sach->tong_diem_danh_gia }}</td>
                                            <td class="text-center">{{ $danh_sach->ket_qua_xep_loai }}</td>
                                        </tr>
                                    @endforeach
                                @endif
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
                        text: 'Trình Phê duyệt',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('phieudanhgia.captren.send') }}';
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
