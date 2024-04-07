<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ngach extends Model
{
    use HasFactory;
    protected $table = 'ngach';
    protected $primaryKey = 'ma_ngach';
    public $incrementing = false;
}
