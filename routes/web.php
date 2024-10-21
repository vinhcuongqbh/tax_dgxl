<?php

use App\Http\Controllers\BaocaoController;
use App\Http\Controllers\DonViController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\PhieuDanhGiaController;
use App\Http\Controllers\PhongController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\XepLoaiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware('auth')->group(function () {
    Route::get('/', function () {
        return view('dashboard');
    });

    Route::get('dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('/dangxaydung', function () {
        return view('dangxaydung');
    });

    Route::group(['prefix' => 'danhmuc'], function () {
        Route::group(['prefix' => 'donvi'], function () {
            Route::get('', [DonViController::class, 'index'])->name('donvi');
            Route::get('create', [DonViController::class, 'create'])->name('donvi.create');
            Route::post('store', [DonViController::class, 'store'])->name('donvi.store');
            Route::get('{id}/edit', [DonViController::class, 'edit'])->name('donvi.edit');
            Route::post('{id}/update', [DonViController::class, 'update'])->name('donvi.update');
            Route::get('{id}/delete', [DonViController::class, 'destroy'])->name('donvi.delete');
            Route::get('{id}/restore', [DonViController::class, 'restore'])->name('donvi.restore');
        });

        Route::group(['prefix' => 'phong'], function () {
            Route::get('', [PhongController::class, 'index'])->name('phong');
            Route::get('create', [PhongController::class, 'create'])->name('phong.create');
            Route::post('store', [PhongController::class, 'store'])->name('phong.store');
            Route::get('{id}/edit', [PhongController::class, 'edit'])->name('phong.edit');
            Route::post('{id}/update', [PhongController::class, 'update'])->name('phong.update');
            Route::get('{id}/delete', [PhongController::class, 'destroy'])->name('phong.delete');
            Route::get('{id}/restore', [PhongController::class, 'restore'])->name('phong.restore');
            Route::post('dm-phong', [PhongController::class, 'dmPhong'])->name('phong.dmphong');
        });

        Route::group(['prefix' => 'congchuc'], function () {
            Route::get('', [UserController::class, 'index'])->name('congchuc');
            Route::get('create', [UserController::class, 'create'])->name('congchuc.create');
            Route::post('store', [UserController::class, 'store'])->name('congchuc.store');
            Route::get('{id}/show', [UserController::class, 'show'])->name('congchuc.show');
            Route::get('{id}/edit', [UserController::class, 'edit'])->name('congchuc.edit');
            Route::post('{id}/update', [UserController::class, 'update'])->name('congchuc.update');
            Route::get('{id}/delete', [UserController::class, 'destroy'])->name('congchuc.delete');
            Route::get('{id}/restore', [UserController::class, 'restore'])->name('congchuc.restore');
            Route::post('{id}/changePass', [UserController::class, 'changePass'])->name('congchuc.changePass');
            Route::get('{id}/resetPass', [UserController::class, 'resetPass'])->name('congchuc.resetPass');
            Route::post('userList', [UserController::class, 'userList'])->name('congchuc.userList');
        });

        Route::group(['prefix' => 'xeploai'], function () {
            Route::get('', [XepLoaiController::class, 'index'])->name('xeploai');
            Route::get('create', [XeploaiController::class, 'create'])->name('xeploai.create');
            Route::post('store', [XeploaiController::class, 'store'])->name('xeploai.store');
            Route::get('{id}/edit', [XeploaiController::class, 'edit'])->name('xeploai.edit');
            Route::post('{id}/update', [XeploaiController::class, 'update'])->name('xeploai.update');
            Route::get('{id}/delete', [XeploaiController::class, 'destroy'])->name('xeploai.delete');
            Route::get('{id}/restore', [XeploaiController::class, 'restore'])->name('xeploai.restore');
        });
        
        Route::resource('roles', RoleController::class);
        Route::get('roles/{roleId}/delete', [RoleController::class, 'destroy'])->name('roles.delete');
        Route::get('roles/{roleId}/give-permissions', [RoleController::class, 'addPermissionToRole']);
        Route::put('roles/{roleId}/give-permissions', [RoleController::class, 'givePermissionToRole'])->name('roles.give-permissions');

        Route::resource('permissions', PermissionController::class);
        Route::get('permissions/{permissionId}/delete', [PermissionController::class, 'destroy'])->name('permissions.delete');
    });
    
    Route::group(['prefix' => 'phieudanhgia'], function () {
        Route::get('canhanList', [PhieuDanhGiaController::class, 'canhanList'])->name('phieudanhgia.canhan.list');
        Route::get('canhanCreate', [PhieuDanhGiaController::class, 'canhanCreate'])->name('phieudanhgia.canhan.create');
        Route::post('canhanStore', [PhieuDanhGiaController::class, 'canhanStore'])->name('phieudanhgia.canhan.store');
        Route::get('{id}/canhanEdit', [PhieuDanhGiaController::class, 'canhanEdit'])->name('phieudanhgia.canhan.edit');
        Route::post('{id}/canhanUpdate', [PhieuDanhGiaController::class, 'canhanUpdate'])->name('phieudanhgia.canhan.update');
        Route::get('{id}/canhanShow', [PhieuDanhGiaController::class, 'canhanShow'])->name('phieudanhgia.canhan.show');
        Route::get('{id}/canhanSend', [PhieuDanhGiaController::class, 'canhanSend'])->name('phieudanhgia.canhan.send');

        Route::get('captrenList', [PhieuDanhGiaController::class, 'captrenList'])->name('phieudanhgia.captren.list');
        Route::get('{id}/captrenCreate', [PhieuDanhGiaController::class, 'captrenCreate'])->name('phieudanhgia.captren.create');
        Route::post('{id}/captrenStore', [PhieuDanhGiaController::class, 'captrenStore'])->name('phieudanhgia.captren.store');
        Route::get('{id}/captrenEdit', [PhieuDanhGiaController::class, 'captrenEdit'])->name('phieudanhgia.captren.edit');
        Route::post('{id}/captrenUpdate', [PhieuDanhGiaController::class, 'captrenUpdate'])->name('phieudanhgia.captren.update');
        Route::get('{id}/captrenShow', [PhieuDanhGiaController::class, 'captrenShow'])->name('phieudanhgia.captren.show');
        Route::get('captrenSend', [PhieuDanhGiaController::class, 'captrenSend'])->name('phieudanhgia.captren.send');
        Route::get('{id}/captrenSendBack', [PhieuDanhGiaController::class, 'captrenSendBack'])->name('phieudanhgia.captren.sendback');

        Route::get('capqdList', [PhieuDanhGiaController::class, 'capqdList'])->name('phieudanhgia.capqd.list');
        Route::get('capqdpheduyethang', [PhieuDanhGiaController::class, 'capQDPheDuyetThang'])->name('phieudanhgia.capqd.pheduyetthang');
        Route::get('{id}/capqdSendBack', [PhieuDanhGiaController::class, 'capqdSendBack'])->name('phieudanhgia.capqd.sendback');
        Route::get('thongbaothang', [PhieuDanhGiaController::class, 'thongBaoThang'])->name('phieudanhgia.thongbaothang');
        Route::get('baocaothang', [PhieuDanhGiaController::class, 'baoCaoThang']);
        Route::post('baocaothang', [PhieuDanhGiaController::class, 'baoCaoThang'])->name('phieudanhgia.baocaothang');

        Route::get('capqddsquy', [PhieuDanhGiaController::class, 'capQDDSQuy'])->name('phieudanhgia.capqd.dsquy');
        Route::get('capqdpheduyetdsquy', [PhieuDanhGiaController::class, 'capQDPheDuyetDSQuy'])->name('phieudanhgia.capqd.pheduyetdsquy');
        Route::get('thongbaoquy', [PhieuDanhGiaController::class, 'thongBaoQuy'])->name('phieudanhgia.thongbaoquy');
        Route::get('baocaoquy', [PhieuDanhGiaController::class, 'baoCaoQuy']);
        Route::post('baocaoquy', [PhieuDanhGiaController::class, 'baoCaoQuy'])->name('phieudanhgia.baocaoquy');

        Route::get('tracuu', [PhieuDanhGiaController::class, 'traCuu']);
        Route::post('tracuu', [PhieuDanhGiaController::class, 'traCuu'])->name('phieudanhgia.tracuu');
    });

    Route::group(['prefix' => 'phieuKTDG'], function () {
        Route::get('create', [PhieuDanhGiaController::class, 'phieuKTDGCreate'])->name('phieuKTDG.create');
        Route::post('store', [PhieuDanhGiaController::class, 'phieuKTDGStore'])->name('phieuKTDG.store');
        Route::get('list', [PhieuDanhGiaController::class, 'phieuKTDGList']);
        Route::post('list', [PhieuDanhGiaController::class, 'phieuKTDGList'])->name('phieuKTDG.list');
    });

    Route::group(['prefix' => 'cuctruong'], function () {
        Route::get('hoidongList', [PhieuDanhGiaController::class, 'hoiDongList']);
        Route::post('hoidongList', [PhieuDanhGiaController::class, 'hoiDongList'])->name('phieudanhgia.hoidong.list');
        Route::get('{id}/hoidongCreate', [PhieuDanhGiaController::class, 'hoiDongCreate'])->name('phieudanhgia.hoidong.create');
        Route::post('{id}/hoidongStore', [PhieuDanhGiaController::class, 'hoiDongStore'])->name('phieudanhgia.hoidong.store');
        Route::get('tonghopdukienthang', [PhieuDanhGiaController::class, 'hoiDongTongHopDuKien']);
        Route::post('tonghopdukienthang', [PhieuDanhGiaController::class, 'hoiDongTongHopDuKien'])->name('phieudanhgia.hoidong.tonghopdukien');
        Route::post('tonghopdanhgia', [PhieuDanhGiaController::class, 'hoiDongTongHopDanhGia'])->name('phieudanhgia.hoidong.tonghopdanhgia');
        Route::get('tonghopdukienquy', [PhieuDanhGiaController::class, 'hoiDongTongHopDuKienQuy']);
        Route::post('tonghopdukienquy', [PhieuDanhGiaController::class, 'hoiDongTongHopDuKienQuy'])->name('phieudanhgia.hoidong.tonghopdukienquy');
        Route::post('pheduyetquy', [PhieuDanhGiaController::class, 'hoiDongPheDuyetQuy'])->name('phieudanhgia.hoidong.pheduyetquy');
    });

    Route::group(['prefix' => 'baocao'], function () {
        Route::get('tonghop', [BaocaoController::class, 'tonghop']);
        Route::post('tonghop', [BaocaoController::class, 'tonghop'])->name('baocao.tonghop');
        Route::get('chitiet/{chuc_nang}', [BaocaoController::class, 'chitiet']);
        Route::post('chitiet/{chuc_nang}', [BaocaoController::class, 'chitiet'])->name('baocao.chitiet');
    });

    Route::group(['prefix' => 'ungdung'], function () {
        Route::get('nangcap', [PhieuDanhGiaController::class, 'dangxaydung']);
        Route::get('huongdansudung', [PhieuDanhGiaController::class, 'dangxaydung']);
    });
});

require __DIR__ . '/auth.php';
