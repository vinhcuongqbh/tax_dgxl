<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PhieuDanhGia extends Model
{
    use HasFactory;
    protected $table = 'phieu_danh_gia';

    public function user(): BelongsTo
    {
        return $this->BelongsTo(User::class, 'so_hieu_cong_chuc', 'so_hieu_cong_chuc')->withDefault();
    }

    public function chuc_vu(): BelongsTo
    {
        return $this->BelongsTo(ChucVu::class, 'ma_chuc_vu')->withDefault();
    }

    public function phong(): BelongsTo
    {
        return $this->BelongsTo(Phong::class, 'ma_phong')->withDefault();
    }

    public function don_vi(): BelongsTo
    {
        return $this->BelongsTo(DonVi::class, 'ma_don_vi')->withDefault();
    }

    public function ly_do(): BelongsTo
    {
        return $this->BelongsTo(LyDoKhongTuDanhGia::class, 'ly_do_khong_tu_danh_gia', 'id')->withDefault();
    }

}
