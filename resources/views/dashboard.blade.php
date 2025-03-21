<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title')</title>
    <link rel="icon" type="image/x-icon" href="/img/logo.png">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="/plugins/fontawesome-free/css/all.min.css">
    <!-- IonIcons -->
    <link rel="stylesheet" href="/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/css/adminlte.min.css">
    <!-- SweetAlert2 -->
    <script src="/plugins/sweetalert2/sweetalert2.all.min.js"></script>

    <!-- REQUIRED SCRIPTS -->
    <!-- jQuery -->
    <script src="/plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap -->
    <script src="/plugins/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- AdminLTE -->
    <script src="/js/adminlte.js"></script>

    <script src="/js/jquery-3.7.0.js"></script>
    <script src="/js/jquery.dataTables.min.js"></script>
    <script src="/js/dataTables.buttons.min.js"></script>
    <script src="/js/jszip.min.js"></script>
    <script src="/js/pdfmake.min.js"></script>
    <script src="/js/vfs_fonts.js"></script>
    <script src="/js/buttons.html5.min.js"></script>
    <script src="/js/buttons.print.min.js"></script>
    <script src="/js/dataTables.rowReorder.min.js"></script>
    <script src="/js/dataTables.responsive.min.js"></script>

    <link rel="stylesheet" href="/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="/css/buttons.dataTables.min.css">
    <link rel="stylesheet" href="/css/rowReorder.dataTables.min.css">
    <link rel="stylesheet" href="/css/responsive.dataTables.min.css">
    <link rel="stylesheet" href="/plugins/overlayScrollbars/css/OverlayScrollbars.min.css">
</head>

<body class="sidebar-mini layout-navbar">
    <div class="wrapper">
        <!-- Navbar -->
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <!-- Left navbar links -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i
                            class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-widget="fullscreen" href="#" role="button">
                        <i class="fas fa-expand-arrows-alt"></i>
                    </a>
                </li>
            </ul>

            <!-- Right navbar links -->
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="post">
                        {{ csrf_field() }}
                        <input class="btn btn-default btn-sm" type="submit" value="{{ __('Đăng xuất') }}">
                    </form>
                </li>
            </ul>
        </nav>
        <!-- /.navbar -->

        <!-- Main Sidebar Container -->
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <!-- Brand Logo -->
            <a href="/dashboard" class="brand-link text-center">
                <span class="brand-text font-weight-light"><b>Cục Thuế tỉnh Quảng Bình</b></span>
            </a>

            <!-- Sidebar -->
            <div class="sidebar">
                <!-- Sidebar user panel (optional) -->
                <div class="user-panel mt-3 pb-3 mb-3 d-flex">
                    <div class="image">
                        <img src="/img/tax_avatar.png" class="img-circle elevation-2" alt="User Image">
                    </div>
                    <div class="info">
                        <a href="{{ route('congchuc.show', Auth::user()->so_hieu_cong_chuc) }}"
                            class="d-block">{{ Auth::user()->name }}</a>
                    </div>
                </div>

                <!-- Sidebar Menu -->
                <nav class="mt-2">
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu"
                        data-accordion="false">
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-cogs"></i>
                                <p>1. Hệ thống
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/donvi" class="nav-link">
                                        <p>1.1. Danh mục đơn vị</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/phong" class="nav-link">
                                        <p>1.2. Danh mục phòng/đội</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/congchuc" class="nav-link">
                                        <p>1.3. Danh sách công chức</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/xeploai" class="nav-link">
                                        <p>1.4. Danh mục xếp loại</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/roles" class="nav-link">
                                        <p>1.5. Danh sách Role</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/danhmuc/permissions" class="nav-link">
                                        <p>1.6. Danh sách Permission</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>
                                    2. Đánh giá, xếp loại cá nhân hàng tháng
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/canhanList" class="nav-link">
                                        <p>2.1. Công chức tự đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/captrenList" class="nav-link">
                                        <p>2.2. Cấp trên đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/capqdList" class="nav-link">
                                        <p>2.3. Cấp có thẩm quyền phê duyệt</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/thongbaothang" class="nav-link">
                                        <p>2.4. Thông báo KQ xếp loại</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/baocaothang" class="nav-link">
                                        <p>2.5. Báo cáo theo tháng</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/tracuu" class="nav-link">
                                        <p>2.6. Tra cứu phiếu đánh giá</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>
                                    3. Đánh giá, xếp loại cá nhân hàng quý
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/capqddsquy" class="nav-link">
                                        <p>3.1. Cấp có thẩm quyền phê duyệt</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/thongbaoquy" class="nav-link">
                                        <p>3.2. Thông báo KQ xếp loại</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/baocaoquy" class="nav-link">
                                        <p>3.3. Báo cáo theo quý</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="/dangxaydung" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>
                                    4. Đánh giá, xếp loại theo năm
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/tapthe/nhapketqua" class="nav-link">
                                        <p>4.1. Nhập kết quả xếp loại của tập thể</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/tapthe/tracuuketqua" class="nav-link">
                                        <p>4.2. Tra cứu kết quả của tập thể</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/canhan/dukienkqxlnam" class="nav-link">
                                        <p>4.3. Dự kiến xếp loại năm của cá nhân</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/canhan/nhapketqua" class="nav-link">
                                        <p>4.4. Nhập kết quả xếp loại năm của cá nhân</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/canhan/nhapbanTuDGXLcanhan" class="nav-link">
                                        <p>4.5. Nhập bản tự đánh giá xếp loại năm của cá nhân</p>
                                    </a>
                                </li>
                                {{-- <li class="nav-item pl-3">
                                    <a href="/canhan/nhapKQXLnambankyso" class="nav-link">
                                        <p>4.5. Nhập bản ký số KQXL năm của cá nhân</p>
                                    </a>
                                </li> --}}
                                <li class="nav-item pl-3">
                                    <a href="/phieudanhgia/thongbaonam" class="nav-link">
                                        <p>4.6. Thông báo kết quả xếp loại năm của cá nhân</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>
                                    5. Quản lý Không tự đánh giá
                                    <i class="right fas fa-angle-left"></i>
                                </p>
                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/phieuKTDG/create" class="nav-link">
                                        <p>5.1. Tạo phiếu KTĐG</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/phieuKTDG/list" class="nav-link">
                                        <p>5.2. Tra cứu</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>6. Đánh giá xếp loại Cục trưởng</p>
                                <i class="right fas fa-angle-left"></i>

                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/cuctruong/hoidongList" class="nav-link">
                                        <p>6.1. Hội đồng đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/cuctruong/tonghopdukienthang" class="nav-link">
                                        <p>6.2. Tổng hợp kết quả đánh giá tháng</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/cuctruong/tonghopdukienquy" class="nav-link">
                                        <p>6.3. Tổng hợp kết quả đánh giá quý</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>7. Báo cáo hỗ trợ</p>
                                <i class="right fas fa-angle-left"></i>

                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/baocao/tonghop" class="nav-link">
                                        <p>7.1. Báo cáo tiến độ tháng</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/baocao/chitiet/ds_chualapphieu" class="nav-link">
                                        <p>7.2. Danh sách chưa lập phiếu đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/baocao/chitiet/ds_dalap_chuagui" class="nav-link">
                                        <p>7.3. Danh sách đã lập nhưng chưa gửi phiếu đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/baocao/chitiet/ds_captren_danhgia" class="nav-link">
                                        <p>7.4. Danh sách chờ cấp trên đánh giá</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/baocao/chitiet/ds_chicuctruong_pheduyet" class="nav-link">
                                        <p>7.5. Danh sách chờ Chi cục trưởng duyệt/phê duyệt</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/baocao/chitiet/ds_cuctruong_pheduyet" class="nav-link">
                                        <p>7.6. Danh sách chờ Cục trưởng phê duyệt</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a href="#" class="nav-link">
                                <i class="fas fa-chart-bar"></i>
                                <p>8. Thông tin về Ứng dụng</p>
                                <i class="right fas fa-angle-left"></i>

                            </a>
                            <ul class="nav nav-treeview">
                                <li class="nav-item pl-3">
                                    <a href="/ungdung/nangcap" class="nav-link">
                                        <p>8.1. Nội dung nâng cấp</p>
                                    </a>
                                </li>
                                <li class="nav-item pl-3">
                                    <a href="/ungdung/huongdansudung" class="nav-link">
                                        <p>8.2. Hướng dẫn sử dụng</p>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </nav>
                <!-- /.sidebar-menu -->
            </div>
            <!-- /.sidebar -->
        </aside>

        <!-- Content Wrapper. Contains page content -->
        <div class="content-wrapper">
            <section class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-12">
                            <h1>@yield('heading')</h1>
                        </div>
                    </div>
                </div><!-- /.container-fluid -->
            </section>
            <section class="content">
                @if (session()->has('msg_success'))
                    <script>
                        Swal.fire({
                            icon: 'success',
                            text: `{{ session()->get('msg_success') }}`,
                            showConfirmButton: false,
                            timer: 5000
                        })
                    </script>
                @elseif (session()->has('msg_error'))
                    <script>
                        Swal.fire({
                            icon: 'error',
                            text: `{{ session()->get('msg_error') }}`,
                            showConfirmButton: false,
                            timer: 5000
                        })
                    </script>
                @endif
                @php
                    session()->forget('msg_success');
                    session()->forget('msg_error');
                @endphp
                @yield('content')
            </section>
        </div>
        <!-- /.content-wrapper -->

        <!-- Main Footer -->
        <footer class="main-footer">
            <strong>Cục Thuế tỉnh Quảng Bình - 2024</strong>
            <div class="float-right d-none d-sm-inline-block">
                <b>Version</b> 1.0.0
            </div>
        </footer>
    </div>
    <!-- ./wrapper -->

    @yield('css')
    @yield('js')
    <script>
        /*** add active class and stay opened when selected ***/
        var url = window.location;

        // for sidebar menu entirely but not cover treeview
        $('ul.nav-sidebar a').filter(function() {
            if (this.href) {
                return this.href == url || url.href.indexOf(this.href) == 0;
            }
        }).addClass('active');

        // for the treeview
        $('ul.nav-treeview a').filter(function() {
            if (this.href) {
                return this.href == url || url.href.indexOf(this.href) == 0;
            }
        }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
    </script>
</body>

</html>
