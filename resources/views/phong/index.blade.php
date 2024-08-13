@extends('dashboard')

@section('title', 'Danh mục Phòng/Đội')

@section('heading')
    Danh mục Phòng/Đội
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
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:15%;">
                                <col style="width:35%;">
                                <col style="width:40%;">
                                <col style="width:5%;">
                            </colgroup>
                            <thead style="text-align: center">
                                <tr>
                                    <th class="text-center">STT</th>
                                    <th class="text-center">Mã Phòng/Đội</th>
                                    <th class="text-center">Tên Phòng/Đội</th>
                                    <th class="text-center">Đơn vị cấp trên</th>
                                    <th class="text-center">Mở/Khóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i = 1; ?>
                                @foreach ($phong as $phong)
                                    <tr>
                                        <td class="text-center">{{ $i++ }}</td>
                                        <td class="text-center">{{ $phong->ma_phong }}</td>
                                        <td><a
                                                href="{{ route('phong.edit', $phong->ma_phong) }}">{{ $phong->ten_phong }}</a>
                                        </td>
                                        <td>{{ $phong->ten_don_vi }}</td>
                                        <td class="text-center">
                                            @can('khóa phòng/đội')
                                                @if ($phong->ma_trang_thai == 1)
                                                    <a class="btn bg-danger text-nowrap w-100"
                                                        href="{{ route('phong.delete', $phong->ma_phong) }}">
                                                        Khóa
                                                    </a>
                                                @else
                                                    <a class="btn bg-olive text-nowrap w-100"
                                                        href="{{ route('phong.restore', $phong->ma_phong) }}">
                                                        Mở
                                                    </a>
                                                @endif
                                            @endcan
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
                            window.location = '{{ route('phong.create') }}';
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
