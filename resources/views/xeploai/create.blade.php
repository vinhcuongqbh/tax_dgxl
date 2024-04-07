@extends('dashboard')

@section('title', 'Tạo mới Xếp loại')

@section('heading')
    Tạo mới Xếp loại
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
                        <h3 class="card-title text-bold">Tạo mới xếp loại</h3>
                    </div> --}}
                    <form action="{{ route('xeploai.store') }}" method="post" id="xeploai-create">
                        @csrf
                        <div class="card-body">
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ma_xep_loai">Mã xếp loại</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ma_xep_loai" name="ma_xep_loai"
                                        value="{{ old('ma_xep_loai') }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="ten_xep_loai">Xếp loại</label>
                                <div class="col-sm-9">
                                    <input type="text" id="ten_xep_loai" name="ten_xep_loai"
                                        value="{{ old('ten_xep_loai') }}" class="form-control">
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-3 col-form-label" for="diem_toi_thieu">Điểm tối thiểu</label>
                                <div class="col-sm-9">
                                    <input type="number" id="diem_toi_thieu" name="diem_toi_thieu"
                                        value="{{ old('diem_toi_thieu') }}" min="0" max="100"
                                        class="form-control">
                                </div>
                            </div>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer d-flex justify-content-center">
                            <button type="submit" class="btn bg-olive text-nowrap col-3 mx-2">Tạo</button>
                            <a class="btn bg-warning text-nowrap col-3 mx-2" href="{{ route('xeploai') }}">Quay lại</a>
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
            $('#xeploai-create').validate({
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
