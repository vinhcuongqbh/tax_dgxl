@extends('dashboard')

@section('title', 'Danh sách Permission')

@section('heading')
    <table class="table table-borderless m-0">
        <tr>
            <td class="m-0 p-0">
                Danh sách Permission
            </td>
            <td class="text-right m-0 p-0">
                <a href="/permissions/create" class="btn bg-olive text-nowrap">Tạo mới</a>
            </td>
        </tr>
    </table>

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
            @foreach ($permission_groups as $permission_group)
                @if ($permissions->where('permission_group', $permission_group->id)->count() <> 0)
                        <div class="col-xl-3">
                            <div class="card card-default">
                                <div class="card-header py-2">
                                    <label>{{ $permission_group->name }}</label>
                                </div>
                                <div class="card-body py-2">
                                    @error('permission')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                    <div class="form-group row">
                                        @foreach ($permissions as $permission)
                                            @if ($permission->permission_group == $permission_group->id)
                                                <div class="col-12">
                                                    - <a
                                                        href="{{ url('permissions/' . $permission->id . '/edit') }}">{{ $permission->name }}</a>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                                <!-- /.card-body -->
                            </div>
                            <!-- /.card -->
                        </div>
                @endif
            @endforeach
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
                sorting: false,
                dom: 'Bfrtip',
                buttons: [{
                        text: 'Tạo mới',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            window.location = '{{ route('permissions.create') }}';
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
