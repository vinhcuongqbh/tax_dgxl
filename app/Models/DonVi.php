<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DonVi extends Model
{
    use HasFactory;
    protected $table = 'don_vi';
    protected $primaryKey = 'ma_don_vi';
    public $incrementing = false;
}
