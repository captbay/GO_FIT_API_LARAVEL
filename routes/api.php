<?php

use App\Http\Controllers\aktivasi_historyController;
use App\Http\Controllers\authController;
use App\Http\Controllers\class_bookingController;
use App\Http\Controllers\class_detailController;
use App\Http\Controllers\class_historyController;
use App\Http\Controllers\class_package_historyController;
use App\Http\Controllers\class_runningController;
use App\Http\Controllers\deposit_package_historyController;
use App\Http\Controllers\deposit_packageController;
use App\Http\Controllers\deposit_reguler_historyController;
use App\Http\Controllers\gym_bookingController;
use App\Http\Controllers\gym_historyController;
use App\Http\Controllers\gymController;
use App\Http\Controllers\instruktur_activityController;
use App\Http\Controllers\instruktur_izinController;
use App\Http\Controllers\instruktur_presensiController;
use App\Http\Controllers\instrukturController;
use App\Http\Controllers\jadwal_umumController;
use App\Http\Controllers\member_activityController;
use App\Http\Controllers\memberController;
use App\Http\Controllers\pegawaiController;
use App\Http\Controllers\promo_cashController;
use App\Http\Controllers\promo_classController;
use App\Http\Controllers\reportAllController;
use App\Models\instruktur_izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


//auth
Route::post('login', [authController::class, 'login']);
Route::post('users/store/admin', [authController::class, 'registerAdmin']);


//sementara taruh di luar nanti pindahin di dalem group biar bisa cek login
//class_running RSD
Route::apiResource(
    'class_running',
    class_runningController::class
);

//harus login baru bisa akses
Route::group(['middleware' => 'auth:api'], function () {
    //users
    Route::get('users', [authController::class, 'getCurrentLoggedInUser']);
    Route::get('users/{id}', [authController::class, 'show']);
    Route::post('logout', [authController::class, 'logout']);
    //auth extention after login baru bisa akses
    Route::post('users/updatePassword', [authController::class, 'updatePassword']);
    Route::post('users/resetPassword', [authController::class, 'resetPassword']);

    //member
    Route::apiResource(
        'member',
        memberController::class
    );
    Route::get('member/generatePdf/{id}', [memberController::class, 'generateMemberCard']);
    Route::get('showMemberExpired', [memberController::class, 'indexExpiredMember']);
    Route::get('showMemberNotActive', [memberController::class, 'indexMembershipNotActiveMember']);
    Route::post('deaktivasiMember/{id}', [memberController::class, 'deaktivasiMember']);


    //instruktur
    Route::apiResource(
        'instruktur',
        instrukturController::class
    );
    Route::post('resetTotalLate', [instrukturController::class, 'resetTotalLate']);

    //instruktur_izin web dan mobile
    Route::apiResource(
        'instruktur_izin',
        instruktur_izinController::class
    );
    //store izin di mobile cek lagi kondisinya
    Route::get('instrukturIzin/notConfirm', [instruktur_izinController::class, 'indexNotConfirm']);
    Route::get('instrukturIzin/alreadyConfirm', [instruktur_izinController::class, 'indexAlredyConfirm']);
    //nampilin tapi berdasarkan id instruktur dan username
    Route::get('instrukturIzin/byId/{id}', [instruktur_izinController::class, 'indexByIdInstruktur']);
    Route::get('instrukturIzin/byUsername/{username}', [instruktur_izinController::class, 'indexByUsernameInstruktur']);
    Route::post('instruktur_izin/confirmIzin/{id}', [instruktur_izinController::class, 'confirmIzin']);

    //pegawai
    Route::apiResource(
        'pegawai',
        pegawaiController::class
    );
    Route::get('getPegawaiKasir', [pegawaiController::class, 'showOnlyKasir']);

    //class_detail
    Route::apiResource(
        'class_detail',
        class_detailController::class
    );

    //jadwal_umum
    Route::apiResource(
        'jadwal_umum',
        jadwal_umumController::class
    );


    Route::post('class_running/generate', [class_runningController::class, 'generateDateAWeek']);
    Route::post('class_running/statusUpdate/{id}', [class_runningController::class, 'updateClassNotAvailable']);
    // get by id instruktur yang login
    Route::get('class_running/byIdInstruktur/{id}', [class_runningController::class, 'indexClassRunningByIdInstruktur']);


    //aktivasi_history
    //put / update ga ada soalnya ini recipt
    Route::apiResource(
        'aktivasi_history',
        aktivasi_historyController::class
    );
    Route::get('aktivasi_history/generatePdf/{id}', [aktivasi_historyController::class, 'generate_aktivasi_historyCard']);

    //deposit_reguler_history
    //put / update ga ada soalnya ini recipt
    Route::apiResource(
        'deposit_reguler_history',
        deposit_reguler_historyController::class
    );
    Route::get('deposit_reguler_history/generatePdf/{id}', [deposit_reguler_historyController::class, 'generate_deposit_reguler_historyCard']);

    //deposit_package
    Route::apiResource(
        'deposit_package',
        deposit_packageController::class
    );
    Route::get('showDepositPackageExpired', [deposit_packageController::class, 'indexExpiredPackage']);
    //nampilin sesuai member yang login
    Route::get('deposit_package/byIdMember/{id}', [deposit_packageController::class, 'indexByIdMember']);
    //nampilin jumlah deposit package yang ada di member by id
    Route::get('deposit_package/countPackageByIdMember/{id}', [deposit_packageController::class, 'countByIdMember']);


    //deposit_package_history
    //put / update ga ada soalnya ini recipt
    Route::apiResource(
        'deposit_package_history',
        deposit_package_historyController::class
    );
    Route::get('deposit_package_history/generatePdf/{id}', [deposit_package_historyController::class, 'generate_deposit_package_historyCard']);

    //promo_cash
    Route::apiResource(
        'promo_cash',
        promo_cashController::class
    );

    //promo_class
    Route::apiResource(
        'promo_class',
        promo_classController::class
    );

    //class_booking
    Route::apiResource(
        'class_booking',
        class_bookingController::class
    );
    //nampil data sesuai bookingan member siapa yang login mobile
    Route::get('class_booking/byIdMember/{id}', [class_bookingController::class, 'indexByIdMember']);
    //nampilin data sesuai instruktur login + class mana yang di pilih
    Route::get('class_booking/instrukturLogin/{id_instruktur}/{id_class_running}', [class_bookingController::class, 'seachClassByIdInstrukturLogin']);
    //instruktur presensi member
    Route::post('class_booking/presensiMember/{id}', [class_bookingController::class, 'presensiMember']);


    //gym
    Route::apiResource(
        'gym',
        gymController::class
    );

    //gym_booking
    Route::apiResource(
        'gym_booking',
        gym_bookingController::class
    );
    Route::get('gym_booking/byIdMember/{id}', [gym_bookingController::class, 'indexByIdMember']);

    //gym_history
    Route::apiResource(
        'gym_history',
        gym_historyController::class
    );
    Route::get('gym_history/generatePdf/{id}', [gym_historyController::class, 'generate_gym_historyCard']);
    Route::post('gym_history/updateStatus/{id}', [gym_historyController::class, 'updateStatus']);

    //class_history
    Route::apiResource(
        'class_history',
        class_historyController::class
    );
    Route::get('class_history/generatePdf/{id}', [class_historyController::class, 'generate_class_historyCard']);
    // Route::post('class_history/updateStatus/{id}', [class_historyController::class, 'updateStatus']);

    //class_package_history
    Route::apiResource(
        'class_package_history',
        class_package_historyController::class
    );
    Route::get('class_package_history/generatePdf/{id}', [class_package_historyController::class, 'generate_class_package_historyCard']);
    // Route::post('class_package_history/updateStatus/{id}', [class_package_historyController::class, 'updateStatus']);

    //instruktur_presensi
    Route::apiResource(
        'instruktur_presensi',
        instruktur_presensiController::class
    );
    Route::post('instruktur_presensi/startClassUpdate/{id}', [instruktur_presensiController::class, 'updateClassStartClass']);
    Route::post('instruktur_presensi/endClassUpdate/{id}', [instruktur_presensiController::class, 'updateClassEndClass']);
    // Route::post('instruktur_presensi/updateClassStatus/{id}', [instruktur_presensiController::class, 'updateClassStatus']);

    //member_activity
    Route::get('member_activity', [member_activityController::class, 'index']);
    Route::get('member_activity/byIdMember/{id}', [member_activityController::class, 'indexByIdMember']);
    //instruktur_activity
    Route::get('instruktur_activity', [instruktur_activityController::class, 'index']);
    Route::get('instruktur_activity/byIdInstruktur/{id}', [instruktur_activityController::class, 'indexByIdInstruktur']);

    //report 
    //pendapatan per tahun
    Route::get('howManyYearInDB', [reportAllController::class, 'howManyYearInDB']);
    Route::get('chartDataPendapatanBulanan/{tahun}', [reportAllController::class, 'chartDataPendapatanBulanan']);
    Route::get('pendapatanPerTahun/{tahun}', [reportAllController::class, 'indexPendapatanBulanan']);
    Route::get('pendapatanPerTahunPDF/{tahun}', [reportAllController::class, 'generatePDFPendapatanBulanan']);

    //aktivitas kelas
    Route::get('aktivitasKelasPerTahun/{bulan}/{tahun}', [reportAllController::class, 'indexAktivitasKelasBulanan']);
    Route::get('howManyMonthYearInClassBooking', [reportAllController::class, 'howManyMonthYearInClassBooking']);
    Route::get('aktivitasKelasPerTahunPDF/{bulan}/{tahun}', [reportAllController::class, 'generatePDFAktivitasKelasBulanan']);

    //aktivitas gym
    Route::get('howManyMonthYearInGym', [reportAllController::class, 'howManyMonthYearInGym']);
    Route::get('aktivitasGymPerTahun/{bulan}/{tahun}', [reportAllController::class, 'indexAktivitasGymBulanan']);
    Route::get('aktivitasGymPerTahunPDF/{bulan}/{tahun}', [reportAllController::class, 'generatePDFAktivitasGymBulanan']);

    //kinerja instruktur
    Route::get('kinerjaInstrukturPerTahun/{bulan}/{tahun}', [reportAllController::class, 'indexKinerjaInstruktur']);
    Route::get('howManyMonthYearInKinerjaInstruktur', [reportAllController::class, 'howManyMonthYearInKinerjaInstruktur']);
    Route::get('kinerjaInstrukturPerTahunPDF/{bulan}/{tahun}', [reportAllController::class, 'generatePDFKinerjaInstruktur']);
});

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });