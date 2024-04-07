@extends('dashboard')

@section('title', 'Tạo mới Phòng/Đội')

@section('heading')
    Tạo mới Phòng/Đội
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
            <div class="col-xl-6">
                <div class="card card-default">
                    {{-- <div class="card-header">
                        <h3 class="card-title text-bold">Tạo mới phòng/đội</h3>
                    </div> --}}
                    <form action="{{ route('phong.store') }}" method="post" id="phong-create">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_phong">Mã Phòng/Đội</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ma_phong" name="ma_phong" value="{{ old('ma_phong') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ten_phong">Tên Phòng/Đội</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ten_phong" name="ten_phong" value="{{ old('ten_phong') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_don_vi_cap_tren">Đơn vị cấp trên</label>                            
                                <div class="col-sm-9">
                                    <select id="ma_don_vi_cap_tren" name="ma_don_vi_cap_tren"
                                        class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($don_vi as $don_vi)
                                            <option value="{{ $don_vi->ma_don_vi }}">{{ $don_vi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Tạo</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('phong') }}">Quay lại</a>
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
            $('#phong-create').validate({
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
