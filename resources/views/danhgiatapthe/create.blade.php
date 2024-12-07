@extends('dashboard')

@section('title', 'Nhập kết quả xếp loại năm của tập thể')

@section('heading')
    <form action="{{ route('tapthe.nhapketqua') }}" method="post">
        @csrf
        <div class="d-flex">
            <div class="col-4">
                Nhập KQXL năm của tập thể
            </div>
            <div class="d-flex justify-content-end col-8">
                <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
                    @foreach ($ds_don_vi as $ds_don_vi)
                        <option value="{{ $ds_don_vi->ma_don_vi }}" @if ($ma_don_vi_da_chon == $ds_don_vi->ma_don_vi) selected @endif>
                            {{ $ds_don_vi->ten_don_vi }}</option>
                    @endforeach
                </select>
                <label class="h6 mt-2 mx-2">Năm</label>
                <input type="number" name="nam_danh_gia" value="{{ $thoi_diem_danh_gia->year }}"
                    class="form-control  text-center">
                <button type="submit" class="btn bg-olive form-control ml-2">Xem</button>
            </div>
        </div>
    </form>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="{{ route('tapthe.luuketqua') }}" method="post" id="luuketqua">
                    @csrf
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
                                                    <select id="{{ $ph->ma_phong }}" name="{{ $ph->ma_phong }}"
                                                        class="form-control custom-select">
                                                        <option selected></option>
                                                        <option value="A" @if ($ph->ket_qua_xep_loai == "A") selected @endif>A</option>
                                                        <option value="B" @if ($ph->ket_qua_xep_loai == "B") selected @endif>B</option>
                                                        <option value="C" @if ($ph->ket_qua_xep_loai == "C") selected @endif>C</option>
                                                        <option value="D" @if ($ph->ket_qua_xep_loai == "D") selected @endif>D</option>
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
                    </div>
                    <!-- /.card -->
                    <input type="hidden" name="nam_danh_gia_2" value="{{ $thoi_diem_danh_gia->year }}"
                    class="form-control  text-center">
                </form>
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
@stop
