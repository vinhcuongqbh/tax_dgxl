@extends('dashboard')

@section('title', 'Nhập bản tự đánh giá xếp loại năm của cá nhân')

@section('heading')
    Nhập bản tự đánh giá xếp loại năm của cá nhân
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
                    <form action="{{ route('canhan.nhapbanTuDGXLcanhan') }}" method="post" enctype="multipart/form-data" id="bantuDGXL-create" >
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="nam_danh_gia">Năm đánh giá</label>
                                <div class="col-sm-9">
                                    <input type="number" id="nam_danh_gia" name="nam_danh_gia" value="{{ $nam_danh_gia }}"
                                        class="form-control text-center">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="don_vi">Đơn vị</label>
                                <div class="col-sm-9">
                                    <select id="don_vi" name="don_vi" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($don_vi as $don_vi)
                                            <option value="{{ $don_vi->ma_don_vi }}"
                                                @if ($don_vi_da_chon == $don_vi->ma_don_vi) selected @endif>{{ $don_vi->ten_don_vi }}
                                            </option>
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
                                                @if ($phong_da_chon == $phong->ma_phong) selected @endif>
                                                {{ $phong->ten_phong }}</option>
                                        @endforeach
                                    </select>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="user">Cán bộ</label>
                                <div class="col-sm-9">
                                    <select id="user" name="user" class="form-control custom-select">
                                        <option selected></option>
                                        @foreach ($cong_chuc as $user)
                                            <option value="{{ $user->so_hieu_cong_chuc }}"
                                                @if ($cong_chuc_da_chon == $user->so_hieu_cong_chuc) selected @endif>
                                                {{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="banTuDGXLcanhan">File</label>
                                <div class="col-sm-9">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" id="banTuDGXLcanhan"
                                            name="banTuDGXLcanhan" accept="application/pdf">
                                        <label class="custom-file-label" for="banTuDGXLcanhan"></label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn bg-olive text-nowrap col-2">Upload</button>
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
            $('#bantuDGXL-create').validate({
                rules: {
                    nam_danh_gia: {
                        required: true,
                    },
                    don_vi: {
                        required: true,
                    },
                    phong: {
                        required: true,
                    },
                    user: {
                        required: true,
                    },
                    banTuDGXLcanhan: {
                        required: true,
                    },
                },
                messages: {
                    nam_danh_gia: {
                        required: "Vui lòng nhập thông tin",
                    },
                    don_vi: {
                        required: "Vui lòng chọn thông tin",
                    },
                    phong: {
                        required: "Vui lòng chọn thông tin",
                    },
                    user: {
                        required: "Vui lòng chọn thông tin",
                    },
                    banTuDGXLcanhan: {
                        required: "Vui lòng chọn file",
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
                    url: "{{ url('danhmuc/phong/dm-phong') }}",
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

            $('#phong').on('change', function() {
                var ma_phong = this.value;
                $("#user").html('');
                $.ajax({
                    url: "{{ url('danhmuc/congchuc/userList') }}",
                    type: "POST",
                    data: {
                        ma_phong: ma_phong,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: 'json',
                    success: function(result) {
                        $('#user').html('<option value=""></option>');
                        $.each(result.user, function(key, value) {
                            $("#user").append('<option value="' + value
                                .so_hieu_cong_chuc + '">' + value.name + '</option>'
                                );
                        });
                    }
                });
            });
        });
    </script>
    {{-- CHỌN FILE UPLOAD --}}
    <script src="/plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
    <script>
        $(function() {
            bsCustomFileInput.init();
        });
    </script>
@stop
