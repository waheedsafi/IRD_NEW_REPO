<?php

namespace App\Http\Controllers\api\app\projects;

use Mpdf\Mpdf;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\Address\AddressTrait;
use App\Enums\pdfFooter\PdfFooterEnum;
use App\Traits\Report\PdfGeneratorTrait;

class ProjectPdfController extends Controller
{
    //
    use PdfGeneratorTrait, AddressTrait;



    public function generateForm(Request $request, $id)
    {
        $mpdf = $this->generatePdf();
        $this->setWatermark($mpdf);

        $lang = 'en';
        $data = $this->loadProjectData($lang, $id);

        // Set footer
        // $this->setFooter($mpdf, PdfFooterEnum::MOU_FIRST_FOOTER_en->value);

        // STEP 1: Configure TOC layout
        $mpdf->TOC([
            'toc-preHTML' => '<h1 style="text-align:center;">Table of Contents</h1><div class="toc">',
            'toc-postHTML' => '</div>',
            'toc-bookmarkText' => 'Table of Contents',
            'paging' => true,
            'links' => true,
        ]);

        // ✅ STEP 2: Insert TOC page here


        // STEP 3: Render Blade view (with <tocentry> tags inside)
        $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouFirstPart", $data);

        $mpdf->WriteHTML('<tocpagebreak />');
        $mpdf->WriteHTML('
        <style>
            .toc a {
                cursor: pointer;
                color: blue;
                text-decoration: underline;
            }
        </style>
    ');

        $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mouSecondPart", $data);

        // Done
        return $mpdf->Output('document.pdf', 'I');
    }




    public function generateFor1m(Request $request, $id)
    {
        $mpdf = $this->generatePdf();
        $this->setWatermark($mpdf);

        $lang = 'en'; // Assuming English
        $data = $this->loadProjectData($lang, $id);

        // **SECTION 1** - Apply First Footer
        // $this->setFooter($mpdf, PdfFooterEnum::MOU_FIRST_FOOTER_en->value);
        $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mou", $data);

        // Add a new page to start Section 2
        $mpdf->AddPage();

        // **SECTION 2** - Apply Second Footer
        // $this->setFooter($mpdf, PdfFooterEnum::MOU_FOOTER_en->value);
        // $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mou_section2", $data);

        // Output the generated PDF
        return $mpdf->Output('document.pdf', 'I'); // Stream PDF to browser
    }



    protected function loadProjectData($lang, $id)
    {


        $data = [
            'preamble' => 'premable content',

            'ngo_name' => '................',
            'introduction_ngo' => 'organiztion introuducaton data',
            'abbr' => 'abbrevition content',
            'org_vision' => 'org_vision',
            'org_mission' => 'org_mission',
            'org_management_working_area' => 'org_management_working_area',
            'project_structure' => 'project_structure',
            'backgroud_experince' => 'backgroud_experince',
            'provision_health_service' => 'provision_health_service',
            'introduction_current_project' => 'introduction_current_project',
            'health_facilities' => [
                ['province' => 'kabul', 'facilities' => 'arzan_qimat'],
                ['province' => 'logor', 'facilities' => 'pole alam']
            ],
            'goals' => 'goals',
            'objectives' => 'objectives',
            'expected_outcomes' => 'expected_outcomes',
            'expected_impact' => 'expected_impact',
            'subject' => 'subject',
            'activities' => 'activities',
            'implementing_org' => 'implementing_org',
            'funder' => 'funder',
            'budget' => 'budget',
            'start_date' => 'start_date',
            'end_date' => 'end_date',
            'mou_date' => 'mou_date',
            'location' => 'location',
            'provinces' => 'provinces',
            'areas' => 'areas',
            'direct_beneficiaries' => 'direct_beneficiaries',
            'indirect_beneficiaries' => 'indirect_beneficiaries',
            'org_structure' => 'org_structure',
            'health_staff' => 'health_staff',
            'admin_staff' => 'admin_staff',
            'action_plan' => 'action_plan',
            'ngo_director_contact' => '023783209023',
            'project_focal_point_contact' => '983403932',
            'project_provinces' => 'kabul,zabul',



            'director' => '................',


            'ird_director' => '................',



        ];
        return $data;
    }
}
