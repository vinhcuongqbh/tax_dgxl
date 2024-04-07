<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GioiTinh extends Model
{
    use HasFactory;
    protected $table = 'gioi_tinh';
    protected $primaryKey = 'ma_gioi_tinh';
    public $incrementing = false;
}
