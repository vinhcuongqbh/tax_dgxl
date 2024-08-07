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
        // Gán quyền cho danh sách người dùng
        $danh_sach = User::where('ma_trang_thai', 1)->get();
        foreach ($danh_sach as $ds) {
            $ds->assignRole('Cấp tự đánh giá');
            if (in_array($ds->ma_chuc_vu, ['01', '03', '04', '05', '09'])) $ds->assignRole('Cấp đánh giá');
            if (in_array($ds->ma_chuc_vu, ['01', '03'])) $ds->assignRole('Cấp phê duyệt');
        }

        // Tạo user Super Admin
        $user = User::create([
            'so_hieu_cong_chuc' => 'sadmin',
            'name' => 'Super Admin',
            'ngay_sinh' => '9999-12-31',
            'ma_gioi_tinh' => 0,
            'ma_ngach' => '',
            'ma_chuc_vu' => '',
            'ma_phong' => '',
            'ma_don_vi' => '',
            'email' => 'sadmin.qbi@gdt.gov.vn',
            'password' => Hash::make('@15h1t3rU'),
            'ma_trang_thai' => 2,
        ]);

        // Gán quyền cho user Super Admin
        $user = User::where('so_hieu_cong_chuc', 'sadmin')->first();
        $superAdminRole = Role::where('name' , 'Super Admin')->first();
        $allPermissionNames = Permission::pluck('name')->toArray();
        $superAdminRole->givePermissionTo($allPermissionNames);
        $user->assignRole($superAdminRole);
    }
}
