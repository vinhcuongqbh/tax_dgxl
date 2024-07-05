<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::create([
            'id' => 999,
            'so_hieu_cong_chuc' => 'sadmin',
            'name' => 'Super Admin',
            'ngay_sinh' => '9999-12-31',
            'ma_gioi_tinh' => 1,
            'ma_ngach' => '',
            'ma_chuc_vu' => '',
            'ma_phong' => '',
            'ma_don_vi' => '',
            'email' => 'sadmin.qbi@gdt.gov.vn',
            'password' => Hash::make('@15h1t3rU'),
            'hoi_dong_phe_duyet' => 0,
            'isAdmin' => 0,
            'ma_trang_thai' => 2,
        ]);

        $superAdminRole = Role::where('name' , 'Super Admin')->first();

        $user->assignRole($superAdminRole);
    }
}
