<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Phong extends Model
{
    use HasFactory;
    protected $table = 'phong';
    protected $primaryKey = 'ma_phong';
    public $incrementing = false;

    public function user(): HasMany
    {
        return $this->hasMany(User::class, 'ma_phong', 'ma_phong');
    }

    public function phieu_danh_gia(): HasMany
    {
        return $this->hasMany(PhieuDanhGia::class, 'ma_phong', 'ma_phong')->withDefault();
    }
    
    public function don_vi(): BelongsTo
    {
        return $this->BelongsTo(DonVi::class, 'ma_don_vi_cap_tren', 'ma_don_vi')->withDefault();
    }

    
}
