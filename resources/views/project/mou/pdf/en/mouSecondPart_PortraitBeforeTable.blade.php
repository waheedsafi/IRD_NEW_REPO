<!DOCTYPE html>
<html lang="en">

    <head>
        <meta charset="UTF-8">
        <title>Memorandum of Understanding</title>
        <style>
            /* Your existing styles here (min-logo, header, etc.) */
            .min-logo {
                height: 70px;
                width: 70px;
                float: right;
                margin-top: -40px;
            }

            .moph-logo {
                height: 70px;
                width: 70px;
                float: left;
                margin-top: -40px;
            }

            .header-logo {
                height: 50px;
                width: 100%;
                margin-top: 120px;
            }

            .min-logo-div {
                width: 100%;
                text-align: center;
            }

            * {
                margin: 0;
                padding: 0;
            }

            .header-text-cont {
                margin-top: 80px;
            }

            .header-text {
                margin: 0;
            }

            .first-page-text {
                text-align: center;
            }

            .page {
                page-break-after: always;
            }

            .min-contents p {
                font-size: 15px;
            }

            .sing-page {
                width: 100%;
                margin-top: 50px;
                padding-top: 50px;
            }

            .irddirector,
            .ngodirector {
                width: 50%;
                margin-top: 10px;
                height: 50px;
            }

            .irddirector {
                float: left;
            }

            .ngodirector {
                float: right;
            }

            .article-title {
                width: 100%;
                text-align: center;
            }

            .article-title h3 {
                color: #0000FF
            }

            .content-title {
                margin: 0;
            }
        </style>
    </head>

    <body>

        <div class="min-contents">

            {{-- artical one --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article One</h3>

            </div>
            {!! '<tocentry content=" Preamble" level="3" />' !!}
            <h4 class="content-title">
                Preamble:</h4>
            {{ $preamble }}
            </p>
            {{-- artical two --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Two</h3>

            </div>
            {!! '<tocentry content=" Parties" level="3" />' !!}
            <h4>
                Parties: </h4>
            => Ministry of Public Health (MoPH)
            <br>
            => {{ $ngo_name }}
            <br>
            </p>

            {{-- artical Three --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Three</h3>

            </div>
            {!! '<tocentry content="  Introduction of the Organization" level="3" />' !!}
            <h4>
                Introduction of the Organization:</h4>

            {{ $introduction_ngo }}
            <br>
            </p>

            {{-- Article Four --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Four</h3>

            </div>
            {!! '<tocentry content=" Abbreviations/definition of key terms " level="3" />' !!}
            <h4>
                Abbreviations/definition of key terms: </h4>

            {{ $abbr }}
            <br>
            </p>
            {{-- Article five --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Five</h3>

            </div>
            {!! '<tocentry content="  Organization`s Vision " level="3" />' !!}
            <h4>
                Organization`s Vision: </h4>

            {{ $org_vision }}
            <br>
            </p>
            {{-- Article six --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article SiX</h3>

            </div>
            {!! '<tocentry content=" Organization`s Mission" level="3" />' !!}
            <h4>
                Organization`s Mission: </h4>

            {{ $org_mission }}
            <br>
            </p>

            {{-- Article seven --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Seven</h3>

            </div>
            {!! '<tocentry content="Organization`s Senior Management Team and Working Areas" level="3" />' !!}
            <h4>
                Organization`s Senior Management Team and Working Areas: </h4>

            {{ $org_management_working_area }}

            <br>
            {!! '<tocentry content="Structure of the project" level="3" />' !!}
            <h5>
                Structure of the project:</h5>
            {{ $project_structure }}
            </p>

            {{-- Article Eight --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Eight</h3>

            </div>
            {!! '<tocentry content="Background/Experience of Working in Health Sector" level="3" />' !!}
            <h4>
                Background/Experience of Working in Health Sector: </h4>

            {{ $backgroud_experince }}

            <br>
            {!! '<tocentry content="Provision of Health Services" level="3" />' !!}
            <h5>
                Provision of Health Services:</h5><br>
            {{ $provision_health_service }}
            </p>

            {{--   Article Nine --}}
            <p class="content-text" id="mou-section-3">

            <div class="article-title">

                <h3>
                    Article Nine</h3>

            </div>
            {!! '<tocentry content="Introduction of the Current Project" level="3" />' !!}
            <h4>
                Introduction of the Current Project: </h4>

            {{ $introduction_current_project }}
            <br>
            <div class="page"></div>
