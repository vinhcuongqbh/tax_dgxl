<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LyDoKhongTuDanhGia extends Model
{
    use HasFactory;
    protected $table = 'ly_do_khong_tu_danh_gia';

    public function phieu_danh_gia(): HasMany
    {
        return $this->hasMany(PhieuDanhGia::class, 'id', 'ly_do_khong_tu_danh_gia')->withDefault();
    }
}
