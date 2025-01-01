@extends('dashboard')

@section('title', 'Nhập kết quả xếp loại năm của tập thể')

@section('heading')
    <div class="d-flex">
        <div class="col-4">
            Nhập KQXL năm của tập thể
        </div>
        <div class="d-flex justify-content-end col-8">
            {{-- <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
        <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
            @foreach ($ds_don_vi as $ds_don_vi)
                <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                    {{ $ds_don_vi->ten_don_vi }}</option>
            @endforeach
        </select> --}}
            <label class="h6 mt-2 mx-2">Năm</label>
            <input type="number" id="nam_danh_gia" name="nam_danh_gia" value="{{ $thoi_diem_danh_gia->year }}"
                onchange="setNamDangGia()" class="form-control text-center col-2">
        </div>
    </div>
@stop

@section('content')
    <form action="{{ route('tapthe.luuketqua') }}" method="post" id="luuketqua">
        @csrf
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-12">

                </div>
            </div>
        </div><!-- /.container-fluid -->
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card card-default">
                        <div class="card-body">
                            <table id="table" class="table table-bordered table-striped">
                                <colgroup>
                                    <col style="width:5%;">
                                    <col style="width:15%;">
                                    <col style="width:50%;">
                                    <col style="width:15%;">
                                    <col style="width:15%;">
                                </colgroup>
                                <thead>
                                    <tr>
                                        <th class="text-center align-middle">STT</th>
                                        <th class="text-center align-middle">Mã phòng/đội</th>
                                        <th class="text-center align-middle">Tên phòng/đội</th>
                                        <th class="text-center align-middle">Xếp loại</th>
                                        <th class="text-center align-middle">Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php $i = 1 @endphp
                                    @foreach ($don_vi as $dv)
                                        <tr>
                                            <td class="text-center text-bold bg-olive">{{ $i }}</td>
                                            <td class="text-bold bg-olive" colspan="4">{{ $dv->ten_don_vi }}</td>
                                            <td style="display: none"></td>
                                            <td style="display: none"></td>
                                            <td style="display: none"></td>
                                        </tr>
                                        @php $j = 1 @endphp
                                        @foreach ($phong->where('ma_don_vi_cap_tren', $dv->ma_don_vi) as $ph)
                                            <tr>
                                                <td class="text-center">{{ $i }}.{{ $j++ }}</td>
                                                <td class="text-center">{{ $ph->ma_phong }}</td>
                                                <td>{{ $ph->ten_phong }}</td>
                                                <td class="text-center">
                                                    <select id="e{{ $ph->ma_phong }}" name="{{ $ph->ma_phong }}"
                                                        class="form-control custom-select">
                                                        <option selected></option>
                                                        @foreach ($xep_loai as $xl)
                                                            <option value="{{ $xl->ma_xep_loai }}"
                                                                @if ($ph->ket_qua_xep_loai == '{{ $xl->ma_xep_loai }}') selected @endif>
                                                                {{ $xl->ma_xep_loai }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td></td>
                                            </tr>
                                        @endforeach
                                        @php $i++ @endphp
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <input type="hidden" id="nam_danh_gia_2" name="nam_danh_gia_2" value="{{ $thoi_diem_danh_gia->year }}">
                    </div>
                    <!-- /.card -->
                </div>
                <!-- /.col -->
            </div>
            <!-- /.row -->
        </div>
        <!-- /.container-fluid -->
    </form>
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
                        text: 'Lưu',
                        className: 'bg-olive',
                        action: function(e, dt, node, config) {
                            document.getElementById("luuketqua").submit();
                        },
                    },
                    {
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
    <script>
        function setNamDangGia() {
            document.getElementById("nam_danh_gia_2").value = document.getElementById("nam_danh_gia").value;
        }
    </script>    
@stop
