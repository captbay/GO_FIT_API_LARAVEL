<!DOCTYPE html>
<html>
<head>
	<title>Laporan Aktivitas Class Bulan {{ $monthChoose }} Tahun {{ $yearChoose }}</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <!-- <meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> -->
	<!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> -->
	<script type="text/javascript" src="{{asset('assets/js/jquery.min.js')}}"></script>
	<script type="text/javascript" src="{{asset('assets/js/bootstrap.bundle.min.js')}}"></script>	
	<script type="text/javascript" src="{{asset('assets/js/echarts.min.js')}}"></script>
</head>
<body>
	{{-- <style type="text/css">
		table tr td,
		table tr th{
			font-size: 9pt;
		}
	</style> --}}

	<div>
        <h5><b>Gofit</h5>
		<h6>Jl. Centralpark No. 10 Yogyakarta</h6>
        <br/>
        <h6><b>LAPORAN AKTIVITAS KELAS BULANAN</b></h6>
        <h7>BULAN&nbsp;&nbsp;&nbsp;&nbsp;: {{$monthChoose}}</h7>
        <h7>&nbsp;&nbsp;TAHUN&nbsp;&nbsp;&nbsp;&nbsp;: {{$yearChoose}}</h7>
        <br>
        <h7>Tanggal cetak&nbsp;&nbsp;&nbsp;&nbsp;: {{$dateNow}}</h7>
        <br/><br/>
        <table style="border: 1px solid black;">
            <tr>
                <th width="165px" style="border: 1px solid black; padding: 5px;">Kelas</th>
                <th width="165px" style="border: 1px solid black; padding: 5px;">Instruktur</th>
                <th width="165px" style="border: 1px solid black; padding: 5px;">Jumlah Peserta</th>
                <th width="165px" style="border: 1px solid black; padding: 5px;">Jumlah Libur</th>
            </tr>
            @foreach ($report_class as $report)
                <tr>
                    <td style="border: 1px solid black; padding: 5px;">{{ $report->nama_kelas }}</td>
                    <td style="border: 1px solid black; padding: 5px;">{{ $report->nama_instruktur }}</td>
                    <td style="border: 1px solid black; padding: 5px;">{{ $report->jumlah_peserta }}</td>
                    <td style="border: 1px solid black; padding: 5px;">{{ $report->jumlah_libur_kelas }}</td>
                </tr>
            @endforeach
        </table>
	</div>
 
</body>
</html>