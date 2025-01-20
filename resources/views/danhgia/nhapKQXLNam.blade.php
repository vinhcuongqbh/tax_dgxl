@extends('dashboard')

@section('title', 'Nhập kết quả xếp loại năm')

@section('heading')
    <div class="d-flex">
        <div class="col-4">
            Nhập kết quả xếp loại năm
        </div>
        <div class="d-flex jFustify-content-end col-8">
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="{{ route('canhan.readExcel') }}" method="POST" enctype="multipart/form-data" class="col-6 py-3">
            @csrf
            <div class="row pb-3">
                <div class="col-auto">
                    <label>File mẫu KQXL Năm </label>
                </div>
                <div class="col-auto">
                    <a class="btn btn-primary" href="/canhan/kqxlNamTemplate">Tải file</a>
                </div>
            </div>
            <div class="row">
                <div class="col-auto">
                    <label>Upload file</label>
                </div>
                <div class="col-auto">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="KQXLNam" name="KQXLNam"
                            accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                        <label class="custom-file-label" for="KQXLNam"></label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:10%;">
                                <col style="width:20%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:7%;">
                                <col style="width:10%;">
                                <col style="width:10%;">
                                <col style="width:25%;">
                                <col style="width:0%;">
                                <col style="width:0%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Số hiệu công chức</th>
                                    <th class="text-center align-middle">Họ và tên</th>
                                    <th class="text-center align-middle">Mã chức vụ</th>
                                    <th class="text-center align-middle">Mã phòng</th>
                                    <th class="text-center align-middle">Mã đơn vị</th>
                                    <th class="text-center align-middle">Xếp loại</th>
                                    <th class="text-center align-middle">Năm đánh giá</th>
                                    <th class="text-center align-middle">Ghi chú</th>
                                    <th class="text-center align-middle" style="display: none;">Phòng</th>
                                    <th class="text-center align-middle" style="display: none;">Đơn vị</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if ($error_list !== null)
                                    @php $i = 1 @endphp
                                    @foreach ($error_list as $phieu)
                                        <tr>
                                            <td class="text-center">{{ $i++ }}</td>
                                            <td class="text-center">{{ $phieu['so_hieu_cong_chuc'] }}</td>
                                            <td>{{ $phieu['name'] }}</td>
                                            <td class="text-center">{{ $phieu['ma_chuc_vu'] }}</td>
                                            <td class="text-center">{{ $phieu['ma_phong'] }}</td>
                                            <td class="text-center">{{ $phieu['ma_don_vi'] }}</td>
                                            <td class="text-center">{{ $phieu['xep_loai'] }}</td>
                                            <td class="text-center">{{ $phieu['nam_danh_gia'] }}</td>
                                            <td class="text-justify">{{ $phieu['ghi_chu'] }}</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                        </tr>
                                    @endforeach
                                @endif
                            </tbody>
                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
            <!-- /.col -->
        </div>
        <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
@stop

@section('css')
@stop

@section('js')
    <!-- Datatable -->
    <script>
        $(function() {
            $("#table").DataTable({
                lengthChange: false,
                searching: true,
                autoWidth: false,
                ordering: false,
                paging: false,
                scrollCollapse: true,
                scrollX: true,
                scrollY: 1000,
                dom: 'Bfrtip',
                buttons: [{
                        extend: 'spacer',
                        style: 'bar',
                        text: 'Xuất:'
                    },
                    //'csv',
                    'excel',
                    'pdf',
                ],
                language: {
                    url: '/plugins/datatables/vi.json'
                },
            }).buttons().container().appendTo('#table_wrapper .col-md-6:eq(0)');
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
