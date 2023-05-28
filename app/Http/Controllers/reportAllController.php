<?php

namespace App\Http\Controllers;

use App\Models\class_booking;
use App\Models\report_income;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class reportAllController extends Controller
{
    //////////////////////////////////////////////// LAPORAN PENDAPATAN TAHUNAN (DONE)
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexPendapatanBulanan($tahun)
    {
        $report_income = report_income::where('tahun', $tahun)->orderBy('created_at', 'desc')->get();

        // Mendapatkan data bulan yang sudah ada dalam respon JSON
        $existingMonths = collect($report_income)->pluck('bulan')->toArray();

        // Array berisi semua nama bulan
        $allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        // Mencari nama bulan yang belum ada dalam respon JSON
        $missingMonths = array_diff($allMonths, $existingMonths);

        // Membuat array data dengan nama bulan yang belum ada dan data lainnya menjadi null
        $missingData = [];
        foreach ($missingMonths as $month) {
            $missingData[] = [
                'id' => null,
                'tahun' => $tahun,
                'bulan' => $month,
                'aktivasi' => 0,
                'deposit' => 0,
                'total' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }

        // Menggabungkan data yang sudah ada dengan data yang belum ada
        $completeData = array_merge($report_income->toArray(), $missingData);

        // Mengurutkan data berdasarkan nama bulan secara ascending
        usort($completeData, function ($a, $b) use ($allMonths) {
            $monthA = array_search($a['bulan'], $allMonths);
            $monthB = array_search($b['bulan'], $allMonths);
            return $monthA - $monthB;
        });

        // Menghitung total report
        $total = 0;
        foreach ($completeData as $report) {
            $total += $report['total'];
        }

        // Mengembalikan respon JSON
        return response()->json([
            'success' => true,
            'message' => 'List Data report_income',
            'data'    => $completeData,
            'total'   => $total
        ], 200);
    }

    public function howManyYearInDB()
    {
        $year = report_income::select('tahun')->orderBy('created_at', 'desc')->get();

        $year = $year->map(function ($item) {
            $item->tahun = (int) $item->tahun;
            return $item;
        });

        return response()->json([
            'success' => true,
            'message' => 'List Data Year',
            'data'    => $year
        ], 200);
    }

    public function chartDataPendapatanBulanan($tahun)
    {

        // Fetch the report_income data for the selected year
        $report_income = report_income::where('tahun', $tahun)->orderBy('created_at', 'desc')->get();

        // Create an array with all month names
        $allMonths = [
            'January', 'February', 'March', 'April', 'May', 'June', 'July',
            'August', 'September', 'October', 'November', 'December'
        ];

        // Initialize label and value arrays with 0 values for each month
        $label = $allMonths;
        $value = array_fill(0, 12, 0);

        // Iterate over the report_income data and update the value array accordingly
        foreach ($report_income as $report) {
            $monthIndex = array_search($report->bulan, $allMonths);
            $value[$monthIndex] = $report->total;
        }

        return response()->json([
            'success' => true,
            'message' => 'List Data report_income',
            'label'   => $label,
            'value'   => $value
        ], 200);
    }

    /**
     * Download PDF Pendapatan Bulanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDFPendapatanBulanan($tahun)
    {
        if ($tahun == null) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'Report Not Found',
            ], 404);
        }
        $report_income = report_income::where('tahun', $tahun)->orderBy('created_at', 'desc')->get();

        // Mendapatkan data bulan yang sudah ada dalam respon JSON
        $existingMonths = collect($report_income)->pluck('bulan')->toArray();

        // Array berisi semua nama bulan
        $allMonths = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        // Mencari nama bulan yang belum ada dalam respon JSON
        $missingMonths = array_diff($allMonths, $existingMonths);

        // Membuat array data dengan nama bulan yang belum ada dan data lainnya menjadi null
        $missingData = [];
        foreach ($missingMonths as $month) {
            $missingData[] = [
                'id' => null,
                'tahun' => $tahun,
                'bulan' => $month,
                'aktivasi' => 0,
                'deposit' => 0,
                'total' => 0,
                'created_at' => Carbon::now()->format('Y-m-d H:i:s'),
                'updated_at' => Carbon::now()->format('Y-m-d H:i:s')
            ];
        }

        // Menggabungkan data yang sudah ada dengan data yang belum ada
        $completeData = array_merge($report_income->toArray(), $missingData);

        // Mengurutkan data berdasarkan nama bulan secara ascending
        usort($completeData, function ($a, $b) use ($allMonths) {
            $monthA = array_search($a['bulan'], $allMonths);
            $monthB = array_search($b['bulan'], $allMonths);
            return $monthA - $monthB;
        });

        // Menghitung total report
        $total = 0;
        foreach ($completeData as $report) {
            $total += $report['total'];
        }

        $dateNow = Carbon::now()->format('d F Y');

        $data = [
            'report_income' => $completeData,
            'dateNow' => $dateNow,
            'yearChoose' => $tahun,
            'total' => $total
        ];

        $pdf = Pdf::loadview('report_incomePDF', $data);

        return $pdf->output();
    }

    ////////////////////////////////////////////////// LAPORAN AKTIVITAS KELAS BULANAN (DONE)
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAktivitasKelasBulanan($bulan, $tahun)
    {
        // //membuat report berapa yang member hadir dari class_booking
        // $class_booking = class_booking::where('status', 1)->whereMonth('date_time', $bulan)->whereYear('date_time', $tahun)->with(['class_running.jadwal_umum.class_detail', 'class_running.instruktur', 'member'])->get();
        $report_class = DB::table('class_running')
            ->join('jadwal_umum', 'class_running.id_jadwal_umum', '=', 'jadwal_umum.id')
            ->join('instruktur', 'class_running.id_instruktur', '=', 'instruktur.id')
            ->join('class_detail', 'jadwal_umum.id_class_detail', '=', 'class_detail.id')
            ->leftJoin('class_booking', function ($join) use ($bulan, $tahun) {
                $join->on('class_running.id', '=', 'class_booking.id_class_running')
                    ->whereMonth('class_booking.date_time', '=', $bulan)
                    ->whereYear('class_booking.date_time', '=', $tahun)
                    ->where('class_booking.status', '=', 1);
            })
            ->select('class_detail.name AS nama_kelas', 'instruktur.name AS nama_instruktur')
            ->selectRaw('COALESCE(COUNT(CASE WHEN class_booking.status = 1 THEN class_booking.id END), 0) AS jumlah_peserta')
            ->selectRaw('CAST(COALESCE(SUM(CASE WHEN class_running.status = "libur" THEN 1 ELSE 0 END), 0) AS UNSIGNED) AS jumlah_libur_kelas')
            ->groupBy('class_detail.name', 'instruktur.name')
            ->orderBy('class_detail.name', 'ASC')
            ->distinct()
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Aktivitas Kelas',
            'data'    => $report_class
        ], 200);
    }

    public function howManyMonthYearInClassBooking()
    {
        // mencari data month dalam class_booking
        $listMonth = DB::table('class_booking')
            ->select(DB::raw('MONTH(class_booking.date_time) as value, MONTHNAME(class_booking.date_time) as text'))
            ->groupBy('value', 'text')
            ->orderBy('value', 'asc')
            ->get();

        // mencari data year dalam class_booking
        $listYear = DB::table('class_booking')
            ->select(DB::raw('YEAR(class_booking.date_time) as tahun'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();


        return response()->json([
            'success' => true,
            'message' => 'List Data Month Year',
            'bulan'    => $listMonth,
            'tahun'    => $listYear
        ], 200);
    }

    /**
     * Download PDF Pendapatan Bulanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDFAktivitasKelasBulanan($bulan, $tahun)
    {
        if ($tahun == null || $bulan == null) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'Report Not Found',
            ], 404);
        }

        // membuat report berapa yang member hadir dari class_booking
        $report_class = DB::table('class_running')
            ->join('jadwal_umum', 'class_running.id_jadwal_umum', '=', 'jadwal_umum.id')
            ->join('instruktur', 'class_running.id_instruktur', '=', 'instruktur.id')
            ->join('class_detail', 'jadwal_umum.id_class_detail', '=', 'class_detail.id')
            ->leftJoin('class_booking', function ($join) use ($bulan, $tahun) {
                $join->on('class_running.id', '=', 'class_booking.id_class_running')
                    ->whereMonth('class_booking.date_time', '=', $bulan)
                    ->whereYear('class_booking.date_time', '=', $tahun)
                    ->where('class_booking.status', '=', 1);
            })
            ->select('class_detail.name AS nama_kelas', 'instruktur.name AS nama_instruktur')
            ->selectRaw('COALESCE(COUNT(CASE WHEN class_booking.status = 1 THEN class_booking.id END), 0) AS jumlah_peserta')
            ->selectRaw('CAST(COALESCE(SUM(CASE WHEN class_running.status = "libur" THEN 1 ELSE 0 END), 0) AS UNSIGNED) AS jumlah_libur_kelas')
            ->groupBy('class_detail.name', 'instruktur.name')
            ->orderBy('class_detail.name', 'ASC')
            ->distinct()
            ->get();


        $dateNow = Carbon::now()->format('d F Y');
        $yearChoose = Carbon::parse($tahun)->format('Y');
        $monthChoose = Carbon::createFromFormat('m', $bulan)->format('F');

        $data = [
            'report_class' => $report_class,
            'dateNow' => $dateNow,
            'yearChoose' => $yearChoose,
            'monthChoose' => $monthChoose,
        ];

        $pdf = Pdf::loadview('report_classPDF', $data);

        return $pdf->output();
    }


    ////////////////////////////////////////////////// Laporan Aktivitas Gym Bulanan (DONE)
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexAktivitasGymBulanan($bulan, $tahun)
    {
        // membuat report berapa yang member hadir dari gym_history

        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $period = CarbonPeriod::create($startDate, $endDate);

        $tanggalList = [];

        foreach ($period as $date) {
            $tanggalList[$date->format('d F Y')] = 0;
        }

        $gym_history = DB::table('gym_history')
            ->join('gym_booking', 'gym_history.id_gym_booking', '=', 'gym_booking.id')
            ->select(
                DB::raw('DATE_FORMAT(gym_booking.date_booking, "%d %M %Y") as tanggal'),
                DB::raw('COUNT(gym_history.id) as jumlah_member')
            )
            ->whereMonth('gym_booking.date_booking', $bulan)
            ->whereYear('gym_booking.date_booking', $tahun)
            ->where('gym_history.status', 1)
            ->groupBy('gym_booking.date_booking')
            ->orderBy('gym_booking.date_booking', 'asc')
            ->get();

        foreach ($gym_history as $data) {
            $tanggal = $data->tanggal;
            $jumlah_member = $data->jumlah_member;

            // Pengecekan apakah tanggal sudah ada dalam $tanggalList
            if (isset($tanggalList[$tanggal])) {
                $tanggalList[$tanggal] += $jumlah_member;
            } else {
                $tanggalList[$tanggal] = $jumlah_member;
            }
        }

        $dataArray = [];

        foreach ($tanggalList as $tanggal => $jumlah_member) {
            $dataArray[] = [
                'tanggal' => date('d F Y', strtotime($tanggal)),
                'jumlah_member' => $jumlah_member
            ];
        }

        $total = 0;
        foreach ($gym_history as $report) {
            $total = $total + $report->jumlah_member;
        }

        return response()->json([
            'success' => true,
            'message' => 'List Data report Gym Bulanan',
            'data'    => $dataArray,
            'total'   => $total
        ], 200);
    }

    public function howManyMonthYearInGym()
    {
        // mencari data month dalam gym_booking
        $listMonth = DB::table('gym_booking')
            ->select(DB::raw('MONTH(gym_booking.date_booking) as value, MONTHNAME(gym_booking.date_booking) as text'))
            ->groupBy('value', 'text')
            ->orderBy('value', 'asc')
            ->get();

        // mencari data year dalam gym_booking
        $listYear = DB::table('gym_booking')
            ->select(DB::raw('YEAR(gym_booking.date_booking) as tahun'))
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();


        return response()->json([
            'success' => true,
            'message' => 'List Data Month Year',
            'bulan'    => $listMonth,
            'tahun'    => $listYear
        ], 200);
    }

    /**
     * Download PDF Pendapatan Bulanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDFAktivitasGymBulanan($bulan, $tahun)
    {
        if ($tahun == null || $bulan == null) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'Report Not Found',
            ], 404);
        }

        // membuat report berapa yang member hadir dari gym_history
        $startDate = Carbon::createFromDate($tahun, $bulan, 1);
        $endDate = $startDate->copy()->endOfMonth();

        $period = CarbonPeriod::create($startDate, $endDate);

        $tanggalList = [];

        foreach ($period as $date) {
            $tanggalList[$date->format('d F Y')] = 0;
        }

        $gym_history = DB::table('gym_history')
            ->join('gym_booking', 'gym_history.id_gym_booking', '=', 'gym_booking.id')
            ->select(
                DB::raw('DATE_FORMAT(gym_booking.date_booking, "%d %M %Y") as tanggal'),
                DB::raw('COUNT(gym_history.id) as jumlah_member')
            )
            ->whereMonth('gym_booking.date_booking', $bulan)
            ->whereYear('gym_booking.date_booking', $tahun)
            ->where('gym_history.status', 1)
            ->groupBy('gym_booking.date_booking')
            ->orderBy('gym_booking.date_booking', 'asc')
            ->get();

        foreach ($gym_history as $data) {
            $tanggal = $data->tanggal;
            $jumlah_member = $data->jumlah_member;

            // Pengecekan apakah tanggal sudah ada dalam $tanggalList
            if (isset($tanggalList[$tanggal])) {
                $tanggalList[$tanggal] += $jumlah_member;
            } else {
                $tanggalList[$tanggal] = $jumlah_member;
            }
        }

        $dataArray = [];

        foreach ($tanggalList as $tanggal => $jumlah_member) {
            $dataArray[] = [
                'tanggal' => date('d F Y', strtotime($tanggal)),
                'jumlah_member' => $jumlah_member
            ];
        }

        $total = 0;
        foreach ($gym_history as $report) {
            $total = $total + $report->jumlah_member;
        }

        $dateNow = Carbon::now()->format('d F Y');
        $yearChoose = Carbon::parse($tahun)->format('Y');
        $monthChoose = Carbon::createFromFormat('m', $bulan)->format('F');

        $data = [
            'report_gym' => $dataArray,
            'dateNow' => $dateNow,
            'yearChoose' => $yearChoose,
            'monthChoose' => $monthChoose,
            'total' => $total
        ];

        $pdf = Pdf::loadview('report_gymPDF', $data);

        return $pdf->output();
    }

    ////////////////////////////////////////////////// Laporan Laporan kinerja instruktur (done)
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function indexKinerjaInstruktur($bulan, $tahun)
    {
        // membuat report kinerja instruktur berdasarkan tabel instruktur urut berdasarkan waktu terlambat ascending
        // ambil data dari tabel instruktur, instruktur_izin, instruktur_presensi, dan class_running yang libur berdasarkan id_instruktur
        $report = DB::table('instruktur')
            ->leftJoin('instruktur_presensi', function ($join) use ($tahun, $bulan) {
                $join->on('instruktur.id', '=', 'instruktur_presensi.id_instruktur')
                    ->whereRaw('YEAR(instruktur_presensi.date_time) = ?', $tahun)
                    ->whereRaw('MONTH(instruktur_presensi.date_time) = ?', $bulan);
            })
            ->select(
                'instruktur.id',
                'instruktur.name as nama_instruktur',
                DB::raw('SUM(CASE WHEN instruktur_presensi.status_class = 1 THEN 1 ELSE 0 END) as jumlah_hadir'),
                DB::raw('(SELECT COUNT(*) FROM instruktur_izin WHERE instruktur_izin.id_instruktur = instruktur.id AND instruktur_izin.is_confirm = 1) as jumlah_libur'),
                'instruktur.total_late as waktu_terlambat'
            )
            ->groupBy('instruktur.id', 'instruktur.name', 'instruktur.total_late')
            ->orderBy('instruktur.total_late', 'asc')
            ->orderBy('instruktur.name', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data kinerja instruktur',
            'data'    => $report
        ], 200);
    }

    public function howManyMonthYearInKinerjaInstruktur()
    {
        // mencari data month dalam instruktur_presensi
        $listMonth = DB::table('instruktur_presensi')
            ->select(DB::raw('MONTH(instruktur_presensi.date_time) as value, MONTHNAME(instruktur_presensi.date_time) as text'))
            ->whereNotNull('instruktur_presensi.date_time')
            ->groupBy('value', 'text')
            ->orderBy('value', 'asc')
            ->get();

        // mencari data year dalam instruktur_presensi
        $listYear = DB::table('instruktur_presensi')
            ->select(DB::raw('YEAR(instruktur_presensi.date_time) as tahun'))
            ->whereNotNull('instruktur_presensi.date_time')
            ->groupBy('tahun')
            ->orderBy('tahun', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'message' => 'List Data Month Year',
            'bulan'    => $listMonth,
            'tahun'    => $listYear
        ], 200);
    }

    /**
     * Download PDF Pendapatan Bulanan.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function generatePDFKinerjaInstruktur($bulan, $tahun)
    {
        if ($tahun == null || $bulan == null) {
            //data aktivasi_history not found
            return response()->json([
                'success' => false,
                'message' => 'Report Not Found',
            ], 404);
        }

        $report_instruktur = DB::table('instruktur')
            ->leftJoin('instruktur_presensi', function ($join) use ($tahun, $bulan) {
                $join->on('instruktur.id', '=', 'instruktur_presensi.id_instruktur')
                    ->whereRaw('YEAR(instruktur_presensi.date_time) = ?', $tahun)
                    ->whereRaw('MONTH(instruktur_presensi.date_time) = ?', $bulan);
            })
            ->select(
                'instruktur.id',
                'instruktur.name as nama_instruktur',
                DB::raw('SUM(CASE WHEN instruktur_presensi.status_class = 1 THEN 1 ELSE 0 END) as jumlah_hadir'),
                DB::raw('(SELECT COUNT(*) FROM instruktur_izin WHERE instruktur_izin.id_instruktur = instruktur.id AND instruktur_izin.is_confirm = 1) as jumlah_libur'),
                'instruktur.total_late as waktu_terlambat'
            )
            ->groupBy('instruktur.id', 'instruktur.name', 'instruktur.total_late')
            ->orderBy('instruktur.total_late', 'asc')
            ->orderBy('instruktur.name', 'asc')
            ->get();


        $dateNow = Carbon::now()->format('d F Y');
        $yearChoose = Carbon::parse($tahun)->format('Y');
        $monthChoose = Carbon::createFromFormat('m', $bulan)->format('F');

        $data = [
            'report_instruktur' => $report_instruktur,
            'dateNow' => $dateNow,
            'yearChoose' => $yearChoose,
            'monthChoose' => $monthChoose,
        ];

        $pdf = Pdf::loadview('report_instrukturPDF', $data);

        return $pdf->output();
    }
}
