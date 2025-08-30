<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vaccination Certificate</title>
</head>
<style>
    .page {
        page-break-after: always;
        position: relative;
        height: 100%;
    }

    .card {
        width: 100%;
        overflow: hidden;
        padding-bottom: 20px;
        margin-bottom: 100px;
        position: relative;
    }

    .info {
        width: 100%;
        align-items: center;
    }

    img.islamic_logo {
        width: 80px;
        height: 80px;
        float: right;
        margin-top: 0px;
        margin-right: -20px;
    }

    img.moph_logo {
        width: 80px;
        height: 80px;
        float: left;
        margin-top: 0px;
        margin-left: -20px;
    }

    div.mintext {
        text-align: center;
        float: left;
        width: auto;
        margin: 0;
        font-size: 1.2rem;
    }

    table.table {
        width: 95%;
        text-align: left;
        margin-left: auto;
        margin-right: auto;
        margin-top: 10px;
    }

    table.table tr td {
        border: 1px solid black;
        margin: 0;
        font-size: 1rem;
    }

    table.table tr th {
        border: 1px solid black;
        margin: 0;
        font-size: 1rem;
        text-align: left;
    }

    .bottomdiv {
        position: absolute;
        bottom: 0;
        width: 100%;
        height: 30%;
        background-color: blue;
        color: white;
        text-align: center;
        padding: 10px;
    }

    .bottomtext {
        font-size: 1.2rem;
        font-weight: bold;
    }
</style>

<body>
    <div class="page">
        <div class="card">
            <div class="info">
                <img src="images/islamic.png" alt="img" class="islamic_logo">
                <img src="images/moph.png" alt="" class="moph_logo">
                <div class="mintext">
                    Islamic Emirate of Afghanistan
                    <br>
                    Ministry of Public Health
                    <br>
                    <br>
                    <strong>VACCINATION CERTIFICATE</strong>
                    <br>
                    <br>
                    <span style="font-size:0.8rem;">Certificate ID: <strong style="color:blue;">{{ $data[0]['certificate_id'] }}</strong></span>
                    <br>
                    <br>
                    <br>
                    <br>
                    <div class="detials">
                        <div class="title">
                            Full Name: <span style="color:white">_____________________</span>{{ $data[0]['full_name'] }}
                            <br>
                            Date of Birth:<span style="color:white">___________________</span> {{ $data[0]['date_of_birth'] }}
                            <br>
                            Gender: <span style="color:white">____________________ ___</span>{{ $data[0]['gender'] }}
                            <br>
                            Passport No: <span style="color:white"> ___________________ </span>{{ $data[0]['passport_number'] }}
                            <br>
                            Issue Date: <span style="color:white"> ___________________ _ </span>{{ $data[0]['issue_date'] }}
                            <br>
                            <strong style="color: rgb(53, 164, 224)">Vaccination Details</strong>
                        </div>
                    </div>
                </div>
            </div>

            <table class="table">
                <tr>
                    <th>Vaccine</th>
                    <th>Vaccine Center</th>
                    <th>Dose</th>
                    <th>Batch No</th>
                </tr>
                @foreach ($data[0]['vaccines'] as $vaccine)
                <tr>
                    <td rowspan="4">{{ $vaccine['vaccine_type_name'] }}</td>
                    <td rowspan="4">{{ $vaccine['vaccine_center'] ?? 'BHC' }}</td>
                    <td>Dose 1</td>
                    <td>{{ $vaccine['doses'][0]['batch_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Dose 2</td>
                    <td>{{ $vaccine['doses'][1]['batch_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Dose 3</td>
                    <td>{{ $vaccine['doses'][2]['batch_number'] ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <td>Dose 4</td>
                    <td>{{ $vaccine['doses'][3]['batch_number'] ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </table>

            <div class="bottomdiv">
                <div class="bottomtext">
                    This is the footer content. It will always stay at the bottom of the page.
                </div>
            </div>
        </div>
    </div>
</body>

</html>