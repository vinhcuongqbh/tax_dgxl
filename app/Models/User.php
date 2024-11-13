<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    //protected $primaryKey = 'so_hieu_cong_chuc';
    //public $incrementing = false;

    public function phieu_danh_gia(): HasMany
    {
        return $this->hasMany(PhieuDanhGia::class, 'so_hieu_cong_chuc', 'so_hieu_cong_chuc')->withDefault();
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
}
