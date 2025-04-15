<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Verification Status</title>
    <style>
        body{
            margin: 0;
            padding: 0;
        }

        .card{
            text-align: center;
            margin: auto;
            width: 40%;
            background-color: rgb(241, 241, 241);
            box-shadow: 0px 10px 10px rgb(202, 202, 202);
            border-radius: 5px;
        }
        h2{
            padding-top: 30px;
        }
        hr{
            width: 300px;
            height: 10px;
            background: #555555;
            box-shadow: 0px 10px 10px rgb(202, 202, 202);
            clip-path: ellipse(50% 10% at 50% 50%);
        }
        .status{
            padding: 30px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>Status Verifikasi</h2>
            <hr>
            <div class="status">
                <strong>{{ $status }}</strong>
            </div>
        </div>
    </div>
</body>
</html>