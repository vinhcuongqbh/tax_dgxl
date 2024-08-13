@extends('dashboard')

@section('title', 'Tạo mới Công chức')

@section('heading')
    Tạo mới Công chức
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
                    <form action="{{ route('congchuc.store') }}" method="post" id="congchuc-create">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="so_hieu_cong_chuc">Số hiệu</label>
                                <div class="col-sm-9">
                                    <input type="text" id="so_hieu_cong_chuc" name="so_hieu_cong_chuc"
                                        value="{{ old('so_hieu_cong_chuc') }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Họ và tên</label>
                                <div class="col-sm-9">
                                    <input type="text" id="name" name="name" value="{{ old('name') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ngay_sinh">Ngày sinh</label>
                                <div class="col-sm-9">
                                    <input type="date" id="ngay_sinh" name="ngay_sinh" value="{{ old('ngay_sinh') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="gioi_tinh">Giới tính</label>
                                <div class="col-sm-9">
                                    <select id="gioi_tinh" name="gioi_tinh" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($gioi_tinh as $gioi_tinh)
                                            <option value="{{ $gioi_tinh->ma_gioi_tinh }}">{{ $gioi_tinh->ten_gioi_tinh }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ngach">Ngạch</label>
                                <div class="col-sm-9">
                                    <select id="ngach" name="ngach" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($ngach as $ngach)
                                            <option value="{{ $ngach->ma_ngach }}">{{ $ngach->ten_ngach }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="chuc_vu">Chức vụ</label>
                                <div class="col-sm-9">
                                    <select id="chuc_vu" name="chuc_vu" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($chuc_vu as $chuc_vu)
                                            <option value="{{ $chuc_vu->ma_chuc_vu }}">{{ $chuc_vu->ten_chuc_vu }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="don_vi">Đơn vị</label>
                                <div class="col-sm-9">
                                    <select id="don_vi" name="don_vi" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($don_vi as $don_vi)
                                            <option value="{{ $don_vi->ma_don_vi }}">{{ $don_vi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="phong">Phòng/Đội</label>
                                <div class="col-sm-9">
                                    <select id="phong" name="phong" class="form-control custom-select">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="email">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" id="email" name="email" value="{{ old('email') }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="role">Vai trò</label>
                                <div class="col-sm-9 ">
                                    {{-- <select id="multiple" name="roles[]" class="form-control custom-select" multiple>
                                        <option value="">---</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role }}">{{ $role }}</option>
                                        @endforeach
                                    </select> --}}
                                    <div class="form-check">
                                        @foreach ($roles as $role)
                                            <input type="checkbox" id="{{ $role }}" class="form-check-input" name="roles[]"
                                                value="{{ $role }}">
                                            <label class="form-check-label" for="{{ $role }}">{{ $role }}</label><br>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Tạo</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('congchuc') }}">Quay lại</a>
                        </div>
                    </form>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->
@stop

@section('css')

@endsection

@section('js')
    <!-- jquery-validation -->
    <script src="/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
    <!-- Page specific script -->
    <script>
        $(function() {
            $('#congchuc-create').validate({
                rules: {
                    so_hieu_cong_chuc: {
                        required: true,
                    },
                    name: {
                        required: true,
                    },
                    ngay_sinh: {
                        required: true,
                    },
                    gioi_tinh: {
                        required: true,
                    },
                    ngach: {
                        required: true,
                    },
                    don_vi: {
                        required: true,
                    },
                    phong: {
                        required: true,
                    },
                    email: {
                        required: true,
                    },
                },
                messages: {
                    so_hieu_cong_chuc: {
                        required: "Vui lòng nhập thông tin",
                    },
                    name: {
                        required: "Vui lòng nhập thông tin",
                    },
                    ngay_sinh: {
                        required: "Vui lòng nhập thông tin",
                    },
                    gioi_tinh: {
                        required: "Vui lòng chọn thông tin",
                    },
                    ngach: {
                        required: "Vui lòng chọn thông tin",
                    },
                    don_vi: {
                        required: "Vui lòng chọn thông tin",
                    },
                    phong: {
                        required: "Vui lòng chọn thông tin",
                    },
                    email: {
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
    <script>
        $(document).ready(function() {
            $('#don_vi').on('change', function() {
                var ma_don_vi = this.value;
                $("#phong").html('');
                $.ajax({
                    url: "{{ url('/danhmuc/phong/dm-phong') }}",
                    type: "POST",
                    data: {
                        ma_don_vi: ma_don_vi,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#phong').html('<option value=""></option>');
                        $.each(result.phong, function(key, value) {
                            $("#phong").append('<option value="' + value
                                .ma_phong + '">' + value.ten_phong + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@stop
