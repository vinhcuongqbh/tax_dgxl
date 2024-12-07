<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KQXLNamTapThe extends Model
{
    use HasFactory;
    protected $table ='kqxl_nam_tap_the';

    protected $fillable = [
        'nam_danh_gia',
        'ma_phong',
        'ket_qua_xep_loai',
        'ma_trang_thai'
    ];

    public function phong(): BelongsTo
    {
        return $this->BelongsTo(Phong::class, 'ma_phong', 'ma_phong')->withDefault();
    }
}
