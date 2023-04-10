<!DOCTYPE html>
<html>
<head>
    <title>Member Card GoFit</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>
<body>
    <h1>{{ $title }}</h1>
    <p>{{ $address }}</p>
    
    <h1>{{ $title2 }}</h1>
    <table class="table table-bordered">
        <tr>
            <th>Member ID   : {{ $member->no_member}}</th>
        </tr>
        <tr>
            <th>Nama        : {{ $member->name}}</th>
        </tr>
        <tr>
            <th>Alamat      : {{ $member->address}}</th>
        </tr>
        <tr>
            <th>Telepon     : {{ $member->number_phone}}</th>
        </tr>
    </table>
  
</body>
</html>