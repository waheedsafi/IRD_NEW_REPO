<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;

class PdfService
{
    /**
     * Generate PDF from a view
     * 
     * @param string $view
     * @param array $data
     * @param string $fileName
     * @return \Barryvdh\DomPDF\PDF
     */
    public function generateFromView(string $view, array $data, string $fileName = 'document.pdf')
    {
        // Render the PDF content from the view
        // $pdf = Pdf::loadView($view, $data);

        // Save the PDF file to the specified location
        // $pdf->save(storage_path('app/pdf/' . $fileName));

        // return $pdf;
    }

    /**
     * Download PDF
     *
     * @param string $view
     * @param array $data
     * @param string $fileName
     * @return \Barryvdh\DomPDF\PDF
     */
    public function downloadFromView(string $view, array $data, string $fileName = 'document.pdf')
    {
        // Generate the PDF and force a download
        // return Pdf::loadView($view, $data)->download($fileName);
    }
}
