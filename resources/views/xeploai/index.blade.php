@extends('dashboard')

@section('title', 'Danh mục Xếp loại')

@section('heading')
    Danh mục Xếp loại
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
            <div class="col-6">
                <div class="card card-default">
                    {{-- <div class="card-header">
                        <div class="row">
                            <div class="col-auto">
                                <a href="{{ route('xeploai.create') }}"><button type="button"
                                        class="btn bg-olive text-white w-100 text-nowrap"><span>Tạo mới</span></button></a>
                            </div>
                        </div>
                    </div> --}}
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:20%;">
                                <col style="width:50%;">
                                <col style="width:30%;">
                            </colgroup>
                            <thead style="text-align: center">
                                <tr>
                                    <th class="text-center align-middle">Mã Xếp loại</th>
                                    <th class="text-center align-middle">Xếp loại</th>
                                    <th class="text-center align-middle">Điểm tối thiểu</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($xep_loai as $xep_loai)
                                    <tr>
                                        <td class="text-center"><a
                                                href="{{ route('xeploai.edit', $xep_loai->ma_xep_loai) }}">{{ $xep_loai->ma_xep_loai }}</a>
                                        </td>
                                        <td class="text-left">{{ $xep_loai->ten_xep_loai }}</td>
                                        <td class="text-center">{{ $xep_loai->diem_toi_thieu }}</td>
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
                searching: false,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tạo mới',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('xeploai.create') }}';
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
