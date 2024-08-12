@extends('dashboard')

@section('title', 'Danh sách')

@section('heading')
    <form action="{{ route('baocao.chitiet', $chuc_nang) }}" method="post" id="form">
        @csrf
        <div class="d-flex">
            <div class="col-4">
                @switch($chuc_nang)
                    @case('ds_chualapphieu')
                        DS chưa lập phiếu
                    @break

                    @case('ds_dalap_chuagui')
                        DS đã lập PĐG nhưng chưa gửi
                    @break

                    @case('ds_captren_danhgia')
                        DS chờ cấp trên đánh giá
                    @break

                    @case('ds_chicuctruong_pheduyet')
                        DS chờ Chi cục trưởng duyệt/phê duyệt
                    @break

                    @case('ds_cuctruong_pheduyet')
                        DS chờ Cục trưởng phê duyệt
                    @break

                    @default
                @endswitch
            </div>
            <div class="d-flex justify-content-end col-8">
                <label for="ma_don_vi" class="h6 mt-2 mx-2">ĐV: </label>
                <select id="ma_don_vi_da_chon" name="ma_don_vi_da_chon" class="form-control custom-select col-6">
                    @foreach ($don_vi as $dv)
                        <option value="{{ $dv->ma_don_vi }}" @if ($ma_don_vi_da_chon == $dv->ma_don_vi) selected @endif>
                            {{ $dv->ten_don_vi }}</option>
                    @endforeach
                </select>
                <label for="thang_danh_gia" class="h6 mt-2 mx-2">Tháng: </label>
                <input type="number" id="thang_danh_gia" name="thang_danh_gia" min="1" max="12"
                    value="{{ $thoi_diem_danh_gia->month }}" class="form-control text-center">
                <label class="h6 mt-2 mx-2">/</label>
                <input type="number" id="nam_danh_gia"name="nam_danh_gia" value="{{ $thoi_diem_danh_gia->year }}"
                    class="form-control text-center">
                <button type="submit" class="btn bg-olive form-control ml-2">Xem</button>
            </div>
        </div>
    </form>
@stop

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card card-default">
                    <div class="card-body">
                        <table id="table" class="table table-bordered table-striped">
                            <colgroup>
                                <col style="width:5%;">
                                <col style="width:35%;">
                                <col style="width:35%;">
                                <col style="width:25%;">
                            </colgroup>
                            <thead>
                                <tr>
                                    <th class="text-center align-middle">STT</th>
                                    <th class="text-center align-middle">Họ và tên</th>
                                    <th class="text-center align-middle">Chức vụ</th>
                                    <th class="text-center align-middle">Ghi chú</th>
                                    <th class="text-center align-middle" style="display: none;">Phòng</th>
                                    <th class="text-center align-middle" style="display: none;">Đơn vị</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php $i = 1 @endphp
                                @foreach ($don_vi as $dv)
                                    @if ($phieu_danh_gia->where('ma_don_vi', $dv->ma_don_vi)->count() > 0)
                                        <tr>
                                            <td class="text-center text-bold bg-olive">{{ $i++ }}</td>
                                            <td class="text-bold bg-olive" colspan="5">{{ $dv->ten_don_vi }}</td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                            <td style="display: none;"></td>
                                        </tr>
                                    @endif
                                    @foreach ($phong->where('ma_don_vi_cap_tren', $dv->ma_don_vi) as $ph)
                                        @if ($phieu_danh_gia->where('ma_phong', $ph->ma_phong)->count() > 0)
                                            <tr>
                                                <td class="text-center"></td>
                                                <td class="text-bold" colspan="5">{{ $ph->ten_phong }}</td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;"></td>
                                                <td style="display: none;">{{ $dv->ten_phong }}</td>
                                                <td style="display: none;">{{ $dv->ten_don_vi }}</td>
                                            </tr>
                                        @endif
                                        @php $j = 1 @endphp
                                        @foreach ($phieu_danh_gia->where('ma_phong', $ph->ma_phong) as $phieu)
                                            <tr>
                                                <td class="text-center">{{ $j++ }}</td>
                                                <td>
                                                    @if ($chuc_nang == 'ds_chualapphieu')
                                                        {{ $phieu->name }}
                                                    @else
                                                        {{ $phieu->user->name }}
                                                    @endif
                                                </td>
                                                <td class="text-center">{{ $phieu->chuc_vu->ten_chuc_vu }}</td>
                                                <td class="text-center">
                                                    @if ($chuc_nang != 'ds_chualapphieu')
                                                        {{ $phieu->ly_do->ly_do }}
                                                    @endif
                                                </td>
                                                <td style="display: none;">{{ $phieu->phong->ten_phong }}</td>
                                                <td style="display: none;">{{ $phieu->don_vi->ten_don_vi }}</td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
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
                info: false,
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
@stop
