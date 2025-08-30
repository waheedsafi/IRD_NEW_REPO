<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    .page {
        /* page-break-after: always; */
        /* Add a page break after each pair of student cards */
    }

    .card {
        width: 100%;
        /* border: 1px solid black; */
        /* border-radius: 15px; */
        overflow: hidden;
        padding-bottom: 20px;
        margin-bottom: 100px;
        top: 0;
        padding-top: -5%;
        /* page-break-inside: avoid; */
        /* page-break-inside: avoid; */
    }

    /* @media print {
      .card {
        page-break-inside: avoid;
      }
    } */
    .info {
        width: 100%;
        align-items: center;
        /* border:1px solid red; */
        grid: auto;
    }

    img.islamic_logo {
        width: 80px;
        height: 80px;
        float: right;
        margin-top: 0px;
        margin-right: -20px
    }

    img.moph_logo {
        width: 85px;
        height: 80px;
        float: left;
        margin-top: 0px;
        margin-left: -20px;
    }

    div.mintext {
        /* position: ; */
        text-align: center;
        float: left;
        width: auto;
        margin: 0;
        padding-top: 0%;
        margin-left: auto;
        /* margin-top:10px; */
        font-size: 1.2rem;
    }

    div.mintextKey {
        /* position: ; */
        text-align: center;
        float: left;
        width: auto;
        margin: 0;
        margin-top: 0px;
        margin-left: auto;
        /* margin-top:10px; */
        font-size: 0.9rem;
    }

    .detials {
        width: 100%;
        text-align: center;
    }

    .title {
        float: left;
        margin-left: 40px;
        text-align: left;
    }

    .detail_value {
        float: right;
        text-align: center;
    }

    .contents {
        float: right;
        right: 0;
        padding-right: -40px;
        margin-left: 200px;
    }

    table.table {
        width: 95%;
        text-align: left;
        /* border: 2px solid black; */
        padding: 0;
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
        height: 30%;
        border: 1px solid black;
        width: 150%;
        padding: 0px;
        margin: 0px;
    }

    .setFooter {
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: rgb(100, 165, 235);
        height: 22%;
        color: white;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px 80px;
        box-sizing: border-box;
        font-family: sans-serif;
        font-size: 10pt;
    }
</style>

<body>

    <div class="page">

        <div class="card">

            <div class="info">

                <img src="images/islamic.png" alt="img" class="islamic_logo" style="">
                <img src="images/moph.png" alt="" class="moph_logo" style="">
                <div class="mintext">
                    Islamic Emirate of Afghanistan
                    <br>
                    Ministry of Public Health
                    {{-- --}}
                    <br>
                    General Directorate of Disease Prevention and Control
                    <br>
                    <strong> VACCINATION CERTIFICATE</strong>
                    <br>
                    <br>
                    <span style="font-size:1rem;">Certificate ID: <strong style="color:blue; "
                            bold>{{ $data[0]['certificate_id'] }}</strong></span>
                    <br>

                    <br>

                    <div class="detials">

                        <div class="title">
                            Full Name: <span
                                style="color:white">_____________________</span>{{ $data[0]['full_name'] }}
                            <br>
                            Date of Birth:<span style="color:white">___________________</span>
                            {{ $data[0]['date_of_birth'] }}
                            <br>
                            Gender: <span style="color:white">____________________
                                ___</span>{{ $data[0]['gender'] }}
                            <br>
                            Passport No: <span style="color:white"> ___________________
                            </span>{{ $data[0]['passport_number'] }}
                            <br>
                            Issue Date: <span style="color:white"> ___________________ _
                            </span>{{ $data[0]['issue_date'] }}

                            <br>
                            <br>
                            <strong style="color: rgb(53, 164, 224)">Vaccination Detials</strong>
                        </div>

                    </div>

                </div>

            </div>

            <table class="table">

                <tr style="background-color: silver">
                    <th>
                        Vaccine
                    </th>
                    <th>
                        Vaccine Center
                    </th>
                    <th>
                        Dose 1
                    </th>
                    <th>
                        Batch No
                    </th>
                    <th>
                        Dose 2
                    </th>
                    <th>
                        Batch No
                    </th>
                </tr>
                <tr>

                    @foreach ($data[0]['vaccines'] as $vaccine)
                <tr>
                    <td>{{ $vaccine['vaccine_type_name'] }}</td>
                    <td>{{ $vaccine['vaccine_center'] ?? 'BHC' }}</td>
                    <td>{{ $vaccine['doses'][0]['vaccine_date'] ?? 'N/A' }}</td>
                    <td>{{ $vaccine['doses'][0]['batch_number'] ?? 'N/A' }}</td>
                    <td>{{ $vaccine['doses'][1]['vaccine_date'] ?? 'N/A' }}</td>
                    <td>{{ $vaccine['doses'][1]['batch_number'] ?? 'N/A' }}</td>
                </tr>
                @endforeach

                </tr>

            </table>

            <div class="maintext">

            </div>

        </div>

    </div>

    <div class="setFooter">
        <table width="100%" style="height: 100%;">
            <tr>
                <!-- Top left: Afghanistan text -->
                <td colspan="2" valign="top"
                    style="font-size: 13pt; font-weight: bold; padding-bottom: 10px; color:white">
                    This certificate is issued by the Ministry of Public Health <br>
                    of the Islamic Emirate of Afghanistan.
                </td>
            </tr>
            <tr>

                <!-- Bottom left: Contact Info -->
                <td valign="bottom" style="width: 70%; margin-top: 20px; padding-left: 20px; padding-bottom:-80px;">
                    Contact: 0767028775<br>
                    Email: wahidsafi@gmail.com<br>
                    Website: www.vaccine.moph.gov.af
                </td>

                <!-- Bottom right: QR Code -->
                <td align="right" valign="bottom" style="width: 30%;">
                    {{-- <img src="images/islamic.png" width="80" height="80" alt="QR Code" /> --}}
                    <img width="100" height="100" alt="QR Code"
                        src="data:image/svg;base64,{{ base64_encode(QrCode::format('svg')->size(150)->merge(public_path('images/moph.png'), 0.3, true)->generate("https://vaccine.moph.gov.af/person/vaccine/detail/{$data[0]['visit_id']}")) }}"
                        style="width: 120px; height: 120px;">

                </td>
            </tr>
        </table>
    </div>
</body>

</html>