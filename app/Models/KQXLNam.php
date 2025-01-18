<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KQXLNam extends Model
{
    use HasFactory;

    protected $table ='kqxl_nam';

    protected $fillable = [
        'ma_kqxl',
        'so_hieu_cong_chuc',
        'nam_danh_gia',
        'kqxl',
        'ma_can_bo_cap_nhat',
        'ma_trang_thai'
    ];
}
