<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Public Holidays</title>
    <style>
        body {
            background-color: #0090e3;
            font-family: 'Circular',Helvetica,Arial,Lucida,sans-serif;
            color: #ffffff;
        }
    </style>
</head>
<body>
    @if (empty($holidays))
        <p>No holidays found.</p>
    @else
        <table border="1" style="margin: 0 auto;">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($holidays as $holiday)
                    <tr>
                        <td>{{ \Carbon\Carbon::parse($holiday['date'])->format('d-m-Y') }}</td>
                        <td>{{ $holiday['localName'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
</body>
</html>
