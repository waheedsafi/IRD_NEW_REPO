<?php

namespace App\Http\Controllers\api\app\ngo;

use App\Enums\pdfFooter\PdfFooterEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Mpdf\Config\ConfigVariables;
use Mpdf\Config\FontVariables;
use Mpdf\Mpdf;
use App\Traits\Report\PdfGeneratorTrait;

class RegistertionFormController extends Controller
{
    //
    use PdfGeneratorTrait;




    public function generateForm(Request $request/*,$id*/){



        $mpdf =  $this->generatePdf();

        $this->setWatermark($mpdf);


        $this->setFooter($mpdf,PdfFooterEnum::REGISTER_FOOTER->value);


        
        $data =[
            'register_number' =>'hello',
        ];
        $this->pdfFilePart($mpdf,'ngo.registeration.en.registeration',$data);
        // Write additional HTML content

        $mpdf->AddPage();


        // Output the generated PDF to the browser
        return $mpdf->Output('document.pdf', 'I'); // Stream PDF to browser

    }




}
