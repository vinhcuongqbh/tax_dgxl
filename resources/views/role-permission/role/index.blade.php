@extends('dashboard')

@section('title', 'Danh sách Role')

@section('heading')
    Danh sách Role
@stop

@section('content')
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="container-fluid">
        <div class="row">
            <div class="col-8">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:10%;">
                                <col style="width:30%;">
                                <col style="width:60%;">
                            </colgroup>
                            <thead style="text-align: center">
                                <tr>
                                    <th class="text-center align-middle">Id</th>
                                    <th class="text-center align-middle">Tên Role</th>
                                    <th class="text-center align-middle">Chức năng</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($roles as $role)
                                    <tr>
                                        <td class="text-center">{{ $role->id }}</td>
                                        <td class=""><a
                                                href="{{ route('roles.edit', $role->id) }}">{{ $role->name }}</a></td>
                                        <td>
                                            <a href="{{ url('roles/' . $role->id . '/give-permissions') }}"
                                                class="btn btn-warning">
                                                Cập nhật Permission
                                            </a>

                                            @can('update role')
                                                <a href="{{ url('roles/' . $role->id . '/edit') }}" class="btn btn-success">
                                                    Edit
                                                </a>
                                            @endcan

                                            @can('delete role')
                                                <a href="{{ url('roles/' . $role->id . '/delete') }}"
                                                    class="btn btn-danger mx-2">
                                                    Delete
                                                </a>
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
                lengthChange: false,
                info: false,
                pageLength: 20,
                searching: true,
                autoWidth: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tạo mới',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('roles.create') }}';
                        },
                    },
                    // {
                    //     extend: 'spacer',
                    //     style: 'bar',
                    //     text: 'Xuất:'
                    // },
                    // //'csv',
                    // 'excel',
                    // 'pdf',
                ],
                language: {
                    url: '/plugins/datatables/vi.json'
                },
            }).buttons().container().appendTo('#table_wrapper .col-md-6:eq(0)');
        });
    </script>
@stop
