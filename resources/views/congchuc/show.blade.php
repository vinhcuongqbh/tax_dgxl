@extends('dashboard')

@section('title', 'Thông tin Công chức')

@section('heading')
    <div class="d-flex justify-content-between col-6">
        <div>
            Thông tin Công chức
        </div>
        <div>
            <button type="button" class="btn btn-primary text-nowrap" 
            data-toggle="modal" data-target="#reset-pass">Đổi mật mã</button>
        </div>
    </div>
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
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="so_hieu_cong_chuc">Số hiệu</label>
                            <div class="col-sm-9">
                                <input type="text" id="so_hieu_cong_chuc" name="so_hieu_cong_chuc"
                                    value="{{ $cong_chuc->so_hieu_cong_chuc }}" class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="name">Họ và tên</label>
                            <div class="col-sm-9">
                                <input type="text" id="name" name="name" value="{{ $cong_chuc->name }}"
                                    class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="ngay_sinh">Ngày sinh</label>
                            <div class="col-sm-9">
                                <input type="date" id="ngay_sinh" name="ngay_sinh" value="{{ $cong_chuc->ngay_sinh }}"
                                    class="form-control" disabled>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label" for="gioi_tinh">Giới tính</label>
                            <div class="col-sm-9">
                                <select id="gioi_tinh" name="gioi_tinh" class="form-control custom-select" disabled>
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
                                <select id="ngach" name="ngach" class="form-control custom-select" disabled>
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
                                <select id="chuc_vu" name="chuc_vu" class="form-control custom-select" disabled>
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
                                <select id="don_vi" name="don_vi" class="form-control custom-select" disabled>
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
                                <select id="phong" name="phong" class="form-control custom-select" disabled>
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
                                    class="form-control" disabled>
                            </div>
                        </div>
                    </div>
                    <!-- /.card-body -->
                    <div class="card-footer">
                        <button type="button" class="btn btn-primary text-nowrap col-2" data-toggle="modal"
                            data-target="#reset-pass">Đổi mật mã</button>
                    </div>
                </div>
                <!-- /.card -->
            </div>
        </div>
    </div>
    <!-- /.container-fluid -->

    {{-- Cấp lại mật mã --}}
    <form action="{{ route('congchuc.changePass', $cong_chuc->so_hieu_cong_chuc) }}" method="post" id="form-resetpass">
        @csrf
        <div class="modal fade" id="reset-pass">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Đổi mật mã</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group row">
                            <div class="col-5">
                                <label for="old_password" class="col-form-label">Mật mã cũ</label>
                            </div>
                            <div class="col-7">
                                <input type="password" id="old_password" name="old_password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-5">
                                <label for="new_password" class="col-form-label">Mật mã mới</label>
                            </div>
                            <div class="col-7">
                                <input type="password" id="new_password" name="new_password" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-5">
                                <label for="confirm_password" class="col-form-label">Nhập lại Mật mã mới</label>
                            </div>
                            <div class="col-7">
                                <input type="password" id="confirm_password" name="confirm_password"
                                    class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                        <button type="submit" class="btn bg-olive">Cập nhật</button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
        <!-- /.modal -->
    </form>
@stop

@section('js')
    <!-- jquery-validation -->
    <script src="/plugins/jquery-validation/jquery.validate.min.js"></script>
    <script src="/plugins/jquery-validation/additional-methods.min.js"></script>
    <script>
        //Kiểm tra dữ liệu đầu vào
        $(function() {
            $('#form-resetpass').validate({
                rules: {
                    old_password: {
                        required: true,
                    },
                    new_password: {
                        required: true,
                    },
                    confirm_password: {
                        equalTo: "#new_password"
                    }
                },
                messages: {
                    old_password: {
                        required: "Nhập mật mã cũ"
                    },
                    new_password: {
                        required: "Nhập mật mã mới",
                    },
                    confirm_password: "Nhập lại mật mã mới chưa chính xác",
                },
                errorElement: 'span',
                errorPlacement: function(error, element) {
                    error.addClass('invalid-feedback');
                    element.closest('div').append(error);

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
