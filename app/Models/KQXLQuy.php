<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KQXLQuy extends Model
{
    use HasFactory;
    protected $table ='kqxl_quy';

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
}
