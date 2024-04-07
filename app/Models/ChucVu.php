<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChucVu extends Model
{
    use HasFactory;
    protected $table = 'chuc_vu';
    protected $primaryKey = 'ma_chuc_vu';
    public $incrementing = false;
}
