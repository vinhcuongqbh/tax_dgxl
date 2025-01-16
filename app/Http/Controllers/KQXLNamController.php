<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Imports\ExcelReader;
use Maatwebsite\Excel\Facades\Excel;

class KQXLNam extends Controller
{
    public function readExcel(Request $request)
    {
        // Kiểm tra tệp đã được tải lên
        if ($request->hasFile('KQXLNam')) {
            // Đọc tệp Excel và chuyển đổi thành mảng
            $data = Excel::toArray(new ExcelReader, $request->file('KQXLNam'));

            // Xử lý dữ liệu (chỉ đọc, không lưu)
            return response()->json([
                'status' => 'success',
                'data' => $data
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'No file uploaded'
        ]);
    }
}
