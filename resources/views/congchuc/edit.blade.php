@extends('dashboard')

@section('title', 'Sửa thông tin Công chức')

@section('heading')
    Thông tin Công chức
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
                    <form action="{{ route('congchuc.update', $cong_chuc->so_hieu_cong_chuc) }}" method="post"
                        id="congchuc-edit">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="so_hieu_cong_chuc">Số hiệu</label>
                                <div class="col-sm-9">
                                    <input type="text" id="so_hieu_cong_chuc" name="so_hieu_cong_chuc"
                                        value="{{ $cong_chuc->so_hieu_cong_chuc }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="name">Họ và tên</label>
                                <div class="col-sm-9">
                                    <input type="text" id="name" name="name" value="{{ $cong_chuc->name }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ngay_sinh">Ngày sinh</label>
                                <div class="col-sm-9">
                                    <input type="date" id="ngay_sinh" name="ngay_sinh"
                                        value="{{ $cong_chuc->ngay_sinh }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="gioi_tinh">Giới tính</label>
                                <div class="col-sm-9">
                                    <select id="gioi_tinh" name="gioi_tinh" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($gioi_tinh as $gioi_tinh)
                                            <option value="{{ $gioi_tinh->ma_gioi_tinh }}"
                                                @if ($cong_chuc->ma_gioi_tinh == $gioi_tinh->ma_gioi_tinh) selected @endif>
                                                {{ $gioi_tinh->ten_gioi_tinh }}
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
                                            <option value="{{ $ngach->ma_ngach }}"
                                                @if ($cong_chuc->ma_ngach == $ngach->ma_ngach) selected @endif>
                                                {{ $ngach->ten_ngach }}
                                            </option>
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
                                            <option value="{{ $chuc_vu->ma_chuc_vu }}"
                                                @if ($cong_chuc->ma_chuc_vu == $chuc_vu->ma_chuc_vu) selected @endif>
                                                {{ $chuc_vu->ten_chuc_vu }}
                                            </option>
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
                                            <option value="{{ $don_vi->ma_don_vi }}"
                                                @if ($cong_chuc->ma_don_vi == $don_vi->ma_don_vi) selected @endif>
                                                {{ $don_vi->ten_don_vi }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="phong">Phòng/Đội</label>
                                <div class="col-sm-9">
                                    <select id="phong" name="phong" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($phong as $phong)
                                            <option value="{{ $phong->ma_phong }}"
                                                @if ($cong_chuc->ma_phong == $phong->ma_phong) selected @endif>
                                                {{ $phong->ten_phong }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="email">Email</label>
                                <div class="col-sm-9">
                                    <input type="email" id="email" name="email" value="{{ $cong_chuc->email }}"
                                        class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="role">Vai trò</label>
                                <div class="col-sm-9">
                                    <div class="form-check">
                                        @foreach ($roles as $role)
                                            <input type="checkbox" id="{{ $role }}" class="form-check-input" name="roles[]"
                                                value="{{ $role }}" {{ in_array($role, $userRoles) ? 'checked':'' }}>
                                            <label class="form-check-label" for="{{ $role }}">{{ $role }}</label><br>
                                        @endforeach
                                    </div>
                                    {{-- <select name="roles[]" class="form-control custom-select" multiple>
                                        <option value="">---</option>
                                        @foreach ($roles as $role)
                                        <option
                                            value="{{ $role }}"
                                            {{ in_array($role, $userRoles) ? 'selected':'' }}
                                        >
                                            {{ $role }}
                                        </option>
                                        @endforeach
                                    </select> --}}
                                    @error('roles') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-end">
                            <div class="col-sm-3 p-0">
                                <a class="btn bg-primary text-white text-nowrap"
                                    href="{{ route('congchuc.resetPass', $cong_chuc->so_hieu_cong_chuc) }}">Đặt lại mật
                                    mã</a>
                            </div>
                            <div class="col-sm-9 d-flex justify-content-end p-0">
                                <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Cập nhật</button>
                                <a class="btn bg-warning text-white text-nowrap col-3"
                                    href="{{ route('congchuc') }}">Quay
                                    lại</a>
                            </div>
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
            $('#congchuc-edit').validate({
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
                    don_vi: {
                        required: true,
                    },
                    phong: {
                        required: true,
                    },
                    email: {
                        required: true,
                    }
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
                    don_vi: {
                        required: "Vui lòng chọn thông tin",
                    },
                    phong: {
                        required: "Vui lòng chọn thông tin",
                    },
                    email: {
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
    <script>
        $(document).ready(function() {
            $('#don_vi').on('change', function() {
                var ma_don_vi = this.value;
                $("#phong").html('');
                $.ajax({
                    url: "{{ url('phong/dm-phong') }}",
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
