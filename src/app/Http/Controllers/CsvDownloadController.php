<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\Attendance;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class CsvDownloadController extends Controller
{
    public function downloadCsv(Request $request)
    {
        //URLパラメータから取得
        $userId = $request->input('user_id');
        $month = $request->input('month', now()->format('Y-m'));

        //スタッフの情報を取得
        $user = User::findOrFail($userId);

        $startOfMonth = Carbon::parse($month)->startOfMonth();
        $endOfMonth = Carbon::parse($month)->endOfMonth();

        $attendanceData = Attendance::with('breakTimes')
            ->where('user_id', $userId)
            ->whereBetween('work_date', [$startOfMonth, $endOfMonth])
            ->get()
            ->keyBy(function ($item) {
                return $item->work_date->format('Y-m-d');
            });

        //CSVのヘッダー
        $csvData = [];
        $csvData[] = ['日付', '出勤', '退勤', '休憩', '合計'];

        $period = CarbonPeriod::create($startOfMonth, $endOfMonth);

        foreach ($period as $date) {
            $dateString = $date->format('Y-m-d');
            $attendance = $attendanceData->get($dateString);

            if ($attendance) {
                //勤怠データがある日
                $csvData[] = [
                    $date->format('Y/m/d'),
                    $attendance->start_time ? $attendance->start_time->format('H:i') : '',
                    $attendance->end_time ? $attendance->end_time->format('H:i') : '',
                    $attendance->break_duration_format ?? '',
                    $attendance->work_duration_format ?? '',

                ];
            } else {
                //勤怠データが無い日
                $csvDate[] = [
                    $date->format('Y/m/d'),
                    '',
                    '',
                    '',
                    '',
                ];
            }
        }

        //CSVファイル名
        $filename = $user->name . '_勤怠データ_' . $month . '.csv';

        //CSVを生成してダウンロード
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($csvData) {
            $file = fopen('php://output', 'w');

            //BOM付きで出力(Excel対応)
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        return response()->stream($callback, 200, $headers);
    }
}
