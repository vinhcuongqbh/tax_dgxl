@extends('dashboard')

@section('title', 'Tạo phiếu Không tự đánh giá')

@section('heading')
    Tạo phiếu Không tự đánh giá
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
                    <form action="{{ route('phieuKTDG.store') }}" method="post" id="phieuKTDG-create">
                        @csrf
                        <div class="card-body">
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
                                <label class="col-sm-3 col-form-label" for="user">Cán bộ</label>
                                <div class="col-sm-9">
                                    <select id="user" name="user" class="form-control custom-select">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ly_do">Lý do</label>
                                <div class="col-sm-9">
                                    <select id="ly_do" name="ly_do" class="form-control custom-select">
                                        <option selected></option>
                                        <option value="1">Được cử đi học</option>
                                        <option value="2">Nghỉ sinh</option>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="start_date">Từ tháng</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="start_date">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="end_date">Đến tháng</label>
                                <div class="col-sm-9">
                                    <input type="date" class="form-control" name="end_date">
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn bg-olive text-nowrap col-3">Tạo</button>                            
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
            $('#phieuKTDG-create').validate({
                rules: {                   
                    don_vi: {
                        required: true,
                    },
                    phong: {
                        required: true,
                    },
                    user: {
                        required: true,
                    },
                    ly_do: {
                        required: true,
                    },
                    start_date: {
                        required: true,
                    },
                    end_date: {
                        required: true,
                    },
                },
                messages: {                    
                    don_vi: {
                        required: "Vui lòng chọn thông tin",
                    },
                    phong: {
                        required: "Vui lòng chọn thông tin",
                    },
                    user: {
                        required: "Vui lòng chọn thông tin",
                    },
                    ly_do: {
                        required: "Vui lòng chọn thông tin",
                    },
                    start_date: {
                        required: "Vui lòng chọn thông tin",
                    },
                    end_date: {
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

            $('#phong').on('change', function() {
                var ma_phong = this.value;
                $("#user").html('');
                $.ajax({
                    url: "{{ url('congchuc/userList') }}",
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
                                .so_hieu_cong_chuc + '">' + value.name + '</option>');
                        });
                    }
                });
            });
        });
    </script>
@stop
