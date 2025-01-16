<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class ExcelReader implements ToArray
{
    public function array(array $array)
    {
        // Hàm này nhận dữ liệu Excel dưới dạng mảng
        return $array;
    }
}
