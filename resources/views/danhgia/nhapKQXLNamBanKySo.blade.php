@extends('dashboard')

@section('title', 'Nhập KQXL năm của cá nhân (Bản ký số)')

@section('heading')
    <div class="d-flex">
        <div class="col-12">
            Nhập KQXL năm của cá nhân (Bản ký số)
        </div>
    </div>
@stop

@section('content')
    <div class="container-fluid">
        <form action="" method="POST" enctype="multipart/form-data" class="col-6 py-3">
            @csrf
            <div class="row">
                <div class="col-auto">
                    <label>Năm đánh giá</label>
                </div>
                <div class="col-auto">
                    <input type="number" id="nam_danh_gia" name="nam_danh_gia" value="{{ $nam_danh_gia }}"
                        class="form-control text-center">
                </div>
                <div class="col-auto">
                    <label>File</label>
                </div>
                <div class="col-auto">
                    <div class="custom-file">
                        <input type="file" class="custom-file-input" id="KQXLNamBanKySo" name="KQXLNamBanKySo"
                            accept="application/pdf">
                        <label class="custom-file-label" for="KQXLNamBanKySo"></label>
                    </div>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary">Upload</button>
                </div>
            </div>
        </form>
        <div class="row">
            <div class="col-8">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:20%;">
                                <col style="width:40%;">
                                <col style="width:15%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Năm đánh giá</th>
                                    <th class="text-center align-middle">File ký số</th>
                                    <th class="text-center align-middle">Ghi chú</th>
                                </tr>
                            </thead>
                            <tbody>
                                @if (isset($kqxl_nam_ban_ky_so))
                                    @php $i = 1 @endphp
                                    @foreach ($kqxl_nam_ban_ky_so as $kqxl)
                                        <tr>
                                            <td class="text-center">{{ $i++ }}</td>
                                            <td class="text-center">{{ $kqxl->nam_danh_gia }}</td>
                                            <td><a href="{{ $kqxl->duong_dan_file }}">Kết quả xếp loại năm {{ $kqxl->nam_danh_gia }}</a></td>                                            
                                            <td></td>
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
