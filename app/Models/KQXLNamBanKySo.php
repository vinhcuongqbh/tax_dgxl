<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KQXLNamBanKySo extends Model
{
    use HasFactory;

    protected $table ='kqxl_nam_ban_ky_so';

    protected $fillable = [
        'nam_danh_gia',
        'duong_dan_file',
        'ma_can_bo_cap_nhat',
        'ma_trang_thai'
    ];
}
