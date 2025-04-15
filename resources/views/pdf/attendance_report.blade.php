<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>{{ $title }}</h2>
        <p>Generated on: {{ $date }}</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Employee ID</th>
                <th>Name</th>
                <th>Department</th>
                <th>Date</th>
                <th>Clock In</th>
                <th>Clock Out</th>
                <th>Status</th>
                <th>Duration</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $attendance)
            <tr>
                <td>{{ $attendance['employee_id'] }}</td>
                <td>{{ $attendance['name'] }}</td>
                <td>{{ $attendance['department'] }}</td>
                <td>{{ $attendance['date'] }}</td>
                <td>{{ $attendance['clock_in'] }}</td>
                <td>{{ $attendance['clock_out'] }}</td>
                <td>{{ $attendance['status'] }}</td>
                <td>{{ $attendance['work_duration'] }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="footer">
        <p>© {{ date('Y') }} AttendX - All Rights Reserved</p>
    </div>
</body>
</html>