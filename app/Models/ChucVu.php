<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChucVu extends Model
{
    use HasFactory;
    protected $table = 'chuc_vu';
    protected $primaryKey = 'ma_chuc_vu';
    public $incrementing = false;

    public function user(): HasMany
    {
        return $this->HasMany(User::class);
    }
}
