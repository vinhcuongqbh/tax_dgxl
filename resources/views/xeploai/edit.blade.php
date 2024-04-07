@extends('dashboard')

@section('title', 'Sửa thông tin Xếp loại')

@section('heading')
    Thông tin Xếp loại
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
                        <h3 class="card-title text-bold">Sửa thông tin xếp loại</h3>
                    </div> --}}
                    <form action="{{ route('xeploai.update', $xep_loai->ma_xep_loai) }}" method="post" id="xeploai-edit">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_xep_loai">Mã xếp loại</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ma_xep_loai" name="ma_xep_loai"
                                        value="{{ $xep_loai->ma_xep_loai }}" class="form-control" readonly>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ten_xep_loai">Xếp loại</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ten_xep_loai" name="ten_xep_loai"
                                        value="{{ $xep_loai->ten_xep_loai }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="diem_toi_thieu">Điểm tối thiểu</label>
                                <div class="col-sm-9">
                                    <input type="number" id="diem_toi_thieu" name="diem_toi_thieu"
                                        value="{{ $xep_loai->diem_toi_thieu }}" min="0" max="100"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Cập nhật</button>
                            <a class="btn bg-warning text-white text-nowrap col-3 mx-2" href="{{ route('xeploai') }}">Quay
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
            $('#xeploai-edit').validate({
                rules: {
                    ma_xep_loai: {
                        required: true,
                    },
                    ten_xep_loai: {
                        required: true,
                    },
                    diem_toi_thieu: {
                        required: true,
                        digits: true,
                    },
                },
                messages: {
                    ma_xep_loai: {
                        required: "Vui lòng nhập thông tin",
                    },
                    ten_xep_loai: {
                        required: "Vui lòng nhập thông tin",
                    },
                    diem_toi_thieu: {
                        required: "Vui lòng nhập thông tin",
                        digits: "Vui lòng nhập dạng số"
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
