<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DonVi extends Model
{
    use HasFactory;
    protected $table = 'don_vi';
    protected $primaryKey = 'ma_don_vi';
    public $incrementing = false;

    public function phong(): HasMany
    {
        return $this->hasMany(Phong::class, 'ma_don_vi_cap_tren', 'ma_don_vi');
    }

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'ma_don_vi', 'ma_don_vi');
    }

    public function phieu_danh_gia(): HasMany
    {
        return $this->hasMany(PhieuDanhGia::class, 'ma_don_vi', 'ma_don_vi')->withDefault();
    }
}
