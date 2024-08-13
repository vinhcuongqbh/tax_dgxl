@extends('dashboard')

@section('title', 'Cập nhật permissions cho Role')

@section('heading')
    <table class="table table-borderless m-0">
        <tr>
            <td class="m-0 p-0">
                Cập nhật permissions cho Role: {{ $role->name }}
            </td>
            <td class="text-right m-0 p-0">
                <button type="submit" class="btn bg-olive text-nowrap" id="submit">Cập nhật</button>
                <a class="btn bg-warning text-nowrap" href="{{ route('roles.index') }}">Quay lại</a>
            </td>
        </tr>
    </table>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('roles.give-permissions', $role->id) }}" method="post" id="form">
            @csrf
            @method('PUT')
            <div class="row">
                @foreach ($permission_groups as $permission_group)
                    @if ($permissions->where('permission_group', $permission_group->id)->count() != 0)
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
                                                    <label class="font-weight-normal">
                                                        <input type="checkbox" name="permission[]"
                                                            value="{{ $permission->name }}"
                                                            {{ in_array($permission->id, $rolePermissions) ? 'checked' : '' }} />
                                                        {{ $permission->name }}
                                                    </label>
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
        </form>
    </div>
    <!-- /.container-fluid -->
@stop

@section('js')
    <script>
        $('#submit').click(function() {
            $('#form').submit();
        });
    </script>
@stop
