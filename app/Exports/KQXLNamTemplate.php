<?php

namespace App\Exports;

use App\Invoice;
use App\Models\User;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class KQXLNamTemplate implements FromCollection, WithColumnWidths, WithStyles
{
    public function collection()
    {
        $i = 1;
        $user_list = collect();
        $user_list->push([
            'stt' => 'STT',
            'so_hieu_cong_chuc' => 'Số hiệu công chức',
            'name' => 'Họ và tên',
            'ma_chuc_vu' => 'Mã Chức vụ',
            'chuc_vu' => 'Chức vụ',
            'ma_phong' => 'Mã Phòng',
            'phong' => 'Phòng',
            'ma_don_vi' => 'Mã Đơn vị',
            'don_vi' => 'Đơn vị',
            'xep_loai' => 'Kết quả xếp loại',
            'nam_danh_gia' => 'Năm đánh giá',
        ]);

        $users = User::where('users.ma_trang_thai', 1)->orderBy('users.ma_don_vi', 'ASC')
            ->leftjoin('chuc_vu', 'chuc_vu.ma_chuc_vu', 'users.ma_chuc_vu')
            ->leftjoin('phong', 'phong.ma_phong', 'users.ma_phong')
            ->leftjoin('don_vi', 'don_vi.ma_don_vi', 'users.ma_don_vi')
            ->select('users.so_hieu_cong_chuc', 'users.name', 'users.ma_chuc_vu', 'chuc_vu.ten_chuc_vu', 'users.ma_phong', 'phong.ten_phong', 'users.ma_don_vi', 'don_vi.ten_don_vi')
            ->orderBy('users.ma_phong', 'ASC')
            ->orderByRaw('ISNULL(users.ma_chuc_vu), users.ma_chuc_vu ASC')
            ->get();

        foreach ($users as $user) {
            $user_list->push([
                'stt' => $i++,
                'so_hieu_cong_chuc' => $user->so_hieu_cong_chuc,
                'name' => $user->name,
                'ma_chuc_vu' => $user->ma_chuc_vu,
                'chuc_vu' => $user->ten_chuc_vu,
                'ma_phong' => $user->ma_phong,
                'phong' => $user->ten_phong,
                'ma_don_vi' => $user->ma_don_vi,
                'don_vi' => $user->ten_don_vi,
                'xep_loai' => '',
                'nam_danh_gia' => Carbon::now()->subyear()->year,
            ]);
        }
        return $user_list;
    }

    public function columnWidths(): array
    {
        return [
            'A' => 5,
            'B' => 20,
            'C' => 30,
            'D' => 15,
            'E' => 20,
            'F' => 10,
            'G' => 30,
            'H' => 10,
            'I' => 30,
            'J' => 15,
            'K' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row as bold text.
            1    => ['font' => ['bold' => true]],
            'A:Z' => [
                'font' => [
                    'name' => 'Times New Roman', // Tên font chữ
                    'size' => 12,      // Cỡ chữ
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    //'wrapText' => true,
                ],
            ],
            'C' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'E' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'I' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_LEFT,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
            'A1:L1' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }
}
