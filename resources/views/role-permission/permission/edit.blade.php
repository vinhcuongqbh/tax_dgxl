@extends('dashboard')

@section('title', 'Thay đổi Permission')

@section('heading')
    Thay đổi Permission
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-default">
                    <form action="{{ route('permissions.update', $permission->id) }}" method="post" id="permision-edit">
                        @csrf
                        @method('PUT')
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Tên Permision</label>
                                <div class="col-sm-9">
                                    <input type="text" id="name" name="name" value="{{ $permission->name }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="permission_group">Nhóm Permision</label>
                                <div class="col-sm-9">
                                    <select name="permission_group" id="permission_group" class="form-control">
                                        @foreach ($permission_groups as $permission_group)
                                            <option value="{{ $permission_group->id }}"
                                                {{ $permission_group->id == $permission->permission_group ? 'selected' : '' }}>
                                                {{ $permission_group->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Cập nhật</button>
                            <a class="btn bg-danger text-nowrap col-3 mx-2" href="{{ route('permissions.delete', $permission->id) }}">Xóa</a>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('permissions.index') }}">Quay
                                lại</a>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@stop

@section('js')
    <!-- jquery-validation -->
    <script src="/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $('#permission-edit').validate({
                rules: {
                    name: {
                        required: true,
                    }
                },
                messages: {
                    name: {
                        required: "Vui lòng nhập thông tin",
                    }
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('.col-sm-9').append(error);

                },
                highlight: function(element, errorClass, validClass) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element, errorClass, validClass) {
                    $(element).removeClass('is-invalid');
                }
            });
        });
    </script>
@stop
