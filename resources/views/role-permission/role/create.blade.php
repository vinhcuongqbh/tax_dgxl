@extends('dashboard')

@section('title', 'Tạo mới Role')

@section('heading')
    Tạo mới Role
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-xl-6">
                <div class="card card-default">
                    <form action="{{ route('roles.store') }}" method="post" id="role-create">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Tên Role</label>
                                <div class="col-sm-9">
                                    <input type="text" id="name" name="name"
                                        value="{{ old('name') }}" class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Tạo</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('roles.index') }}">Quay lại</a>
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
            $('#role-create').validate({
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
