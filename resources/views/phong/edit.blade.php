@extends('dashboard')

@section('title', 'Sửa thông tin Phòng/Đội')

@section('heading')
    Thông tin Phòng/Đội
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
                        <h3 class="card-title text-bold">Sửa thông tin phòng/đội</h3>
                    </div> --}}
                    <form action="{{ route('phong.update', $phong->ma_phong) }}" method="post" id="phong-edit">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_phong">Mã phòng/đội</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ma_phong" name="ma_phong" value="{{ $phong->ma_phong }}"
                                        class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ten_phong">Tên phòng/đội</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ten_phong" name="ten_phong" value="{{ $phong->ten_phong }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_don_vi_cap_tren">Đơn vị cấp trên</label>
                                <div class="col-sm-9">
                                    <select id="ma_don_vi_cap_tren" name="ma_don_vi_cap_tren" class="form-control custom-select">
                                        <option value=""></option>
                                        @foreach ($don_vi as $don_vi)
                                            <option value="{{ $don_vi->ma_don_vi }}"
                                                @if ($phong->ma_don_vi_cap_tren == $don_vi->ma_don_vi) {{ 'selected' }} @endif>
                                                {{ $don_vi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Cập nhật</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('phong') }}">Quay
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
            $('#phong-edit').validate({
                rules: {
                    ma_phong: {
                        required: true,
                    },
                    ten_phong: {
                        required: true,
                    },
                    ma_don_vi_cap_tren: {
                        required: true,
                    },
                },
                messages: {
                    ma_phong: {
                        required: "Vui lòng nhập thông tin",
                    },
                    ten_phong: {
                        required: "Vui lòng nhập thông tin",
                    },
                    ma_don_vi_cap_tren: {
                        required: "Vui lòng chọn thông tin",
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
