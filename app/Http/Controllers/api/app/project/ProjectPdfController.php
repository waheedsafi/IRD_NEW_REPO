<?php

namespace App\Http\Controllers\api\app\project;

use App\Enums\pdfFooter\PdfFooterEnum;
use App\Http\Controllers\Controller;
use App\Traits\Address\AddressTrait;
use App\Traits\Report\PdfGeneratorTrait;
use Illuminate\Http\Request;

class ProjectPdfController extends Controller
{
    //
    use PdfGeneratorTrait, AddressTrait;


    public function generateForm(Request $request, $id)
    {
        $mpdf =  $this->generatePdf();
        $this->setWatermark($mpdf);
        $lang = $request->input('language_name');

        // $this->setFooter($mpdf, PdfFooterEnum::REGISTER_FOOTER->value);
        $this->setFooter($mpdf, PdfFooterEnum::MOU_FOOTER_en->value);
        $lang = 'en';
        $id = 1;
        $data = $this->loadProjectData($lang, $id);


        $this->pdfFilePart($mpdf, "project.mou.pdf.{$lang}.mou", $data);
        // Write additional HTML content

        // $mpdf->AddPage();


        // Output the generated PDF to the browser
        return $mpdf->Output('document.pdf', 'I'); // Stream PDF to browser

    }


    protected function loadProjectData($lang, $id)
    {


        $data = [
            'register_number' => '$ngo->registration_no',
            'date_of_sign' => '................',
            'ngo_name' => '................',
            'abbr' => '................',
            'contact' => '................',
            'address' => '................',
            'director' => '................',
            'director_address' => '................',
            'email' => '................',
            'establishment_date' => '................',
            'place_of_establishment' => '................',
            'ministry_economy_no' => '................',
            'general_objective' => '................',
            'afganistan_objective' => '................',
            'mission' => '................',
            'vission' => '................',
            'ird_director' => '................',



        ];
        return $data;
    }
}
