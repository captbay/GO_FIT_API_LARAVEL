<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css"
        integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Kelas Presensi for {{ $member->name }} GoFit</title>
</head>

<body>
    <h1>Gofit</h1>
    <p>Jl. Centralpark No. 10 Yogyakarta</p>
    <br/><br/>
    <h1>STRUK PRESENSI KELAS</h1>
    <p>No Struk     : {{ $class_history->no_class_history }}</p>
    <p>Tanggal      : {{ $class_history->date_time }}</p>
    <br/><br/>
    <p>Member       : {{ $member->no_member }} / {{ $member->name}}</p>
    <p>Kelas        : {{ $class_detail->name }}</p>
    <p>Instruktur   : {{ $instruktur->name }}</p>
    <p>Tariff       : {{ $class_detail->price }}</p>
    <p>Sisa Deposit : {{ $class_history->sisa_deposit }}</p>

    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
        integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous">
    </script>
</body>

</html>
