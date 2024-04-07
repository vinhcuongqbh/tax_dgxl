@extends('dashboard')

@section('title', 'Sửa thông tin Đơn vị')

@section('heading')
    Thông tin Đơn vị
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
            <div class="col-xl-6">
                <div class="card card-default">
                    {{-- <div class="card-header">
                        <h3 class="card-title text-bold">Sửa thông tin đơn vị</h3>
                    </div> --}}
                    <form action="{{ route('donvi.update', $don_vi->ma_don_vi) }}" method="post" id="donvi-edit">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_don_vi">Mã đơn vị</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ma_don_vi" name="ma_don_vi" value="{{ $don_vi->ma_don_vi }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ten_don_vi">Tên đơn vị</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ten_don_vi" name="ten_don_vi" value="{{ $don_vi->ten_don_vi }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_don_vi_cap_tren">Đơn vị cấp trên</label>
                                <div class="col-sm-9">
                                    <select id="ma_don_vi_cap_tren" name="ma_don_vi_cap_tren" class="form-control custom-select">
                                        <option value=""></option>
                                        @foreach ($dm_don_vi as $dm_don_vi)
                                            <option value="{{ $dm_don_vi->ma_don_vi }}"
                                                @if ($don_vi->ma_don_vi_cap_tren == $dm_don_vi->ma_don_vi) {{ 'selected' }} @endif>
                                                {{ $dm_don_vi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Cập nhật</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('donvi') }}">Quay
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
            $('#donvi-edit').validate({
                rules: {
                    ma_don_vi: {
                        required: true,
                    },
                    ten_don_vi: {
                        required: true,
                    },
                },
                messages: {
                    ma_don_vi: {
                        required: "Vui lòng nhập thông tin",
                    },
                    ten_don_vi: {
                        required: "Vui lòng nhập thông tin",
                    },
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
