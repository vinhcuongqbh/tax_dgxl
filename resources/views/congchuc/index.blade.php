@extends('dashboard')

@section('title', 'Danh sách Công chức')

@section('heading')
    Danh sách Công chức
@stop

@section('content')
    @if (session()->has('message'))
        <script>
            Swal.fire({
                icon: 'success',
                text: `{{ session()->get('message') }}`,
                showConfirmButton: false,
                timer: 2000
            })
        </script>
    @endif
    <script>
        Swal.fire({
            icon: 'warning',
            text: 'Lưu ý: Mọi sự thay đổi về nhân sự chỉ thực hiện sau khi kết thúc phê duyệt kết quả đánh giá trong tháng',
            showConfirmButton: false,
            timer: 10000
        })
    </script>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:10%;">
                                <col style="width:20%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:20%;">
                                <col style="width:20%;">
                                <col style="width:5%;">
                            </colgroup>
                            <thead style="text-align: center">
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Số hiệu</th>
                                    <th class="text-center align-middle">Họ và tên</th>
                                    <th class="text-center align-middle">Ngày sinh</th>
                                    <th class="text-center align-middle">Chức vụ</th>
                                    <th class="text-center align-middle">Phòng/Đội</th>
                                    <th class="text-center align-middle">Đơn vị</th>
                                    <th class="text-center align-middle">Mở/Khóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1 ?>
                                @foreach ($cong_chuc as $cong_chuc)
                                    <tr>
                                        <td class="text-center">{{ $i++ }}</td>
                                        <td class="text-center">{{ $cong_chuc->so_hieu_cong_chuc }}</td>                                        
                                        <td><a
                                                href="{{ route('congchuc.edit', $cong_chuc->so_hieu_cong_chuc) }}">{{ $cong_chuc->name }}</a>
                                        </td>
                                        <td class="text-center">{{ $cong_chuc->ngay_sinh }}</td>
                                        <td class="text-center">{{ $cong_chuc->ten_chuc_vu }}</td>
                                        <td class="text-center">{{ $cong_chuc->ten_phong}}</td>
                                        <td class="text-center">{{ $cong_chuc->ten_don_vi}}</td>
                                        <td class="text-center" data-title="Mở/Khóa">
                                            @if ($cong_chuc->ma_trang_thai == 1)
                                                <a class="btn bg-danger text-nowrap w-100"
                                                    href="{{ route('congchuc.delete', $cong_chuc->so_hieu_cong_chuc) }}">
                                                    Khóa
                                                </a>
                                            @else
                                                <a class="btn bg-olive text-nowrap w-100"
                                                    href="{{ route('congchuc.restore', $cong_chuc->so_hieu_cong_chuc) }}">
                                                    Mở
                                                </a>
                                            @endif
                                        </td>
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
                // responsive: {
                //     details: {
                //         display: DataTable.Responsive.display.modal({
                //             header: function(row) {
                //                 var data = row.data();
                //                 return data[2];
                //             }
                //         }),
                //         renderer: DataTable.Responsive.renderer.tableAll({
                //             tableClass: 'table'
                //         })
                //     }
                // },
                // rowReorder: {
                //     selector: 'td:nth-child(2)'
                // },
                lengthChange: false,
                pageLength: 20,
                searching: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tạo mới',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('congchuc.create') }}';
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
