<?php

namespace App\Service;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    /**
     * @var Dompdf
     */
    private Dompdf $domPdf;

    public function __construct()
    {
        $this->domPdf = new Dompdf();
        $pdfOptions = new Options();

        $pdfOptions->set('isPhpEnabled', true);
        $pdfOptions->set('isHtml5ParserEnabled', true);
        $pdfOptions->set('isRemoteEnabled', true);
        $pdfOptions->setDefaultFont('Helvetica');
        $this->domPdf->setPaper('A4');
        $this->domPdf->setOptions($pdfOptions);
    }

    /**
     * @param string $filename
     * @param string $html
     * @return void
     */
    public function generatePdfFile(string $filename, string $html): void
    {
        $this->domPdf->loadHtml($html);
        $this->domPdf->render();
        $this->domPdf->stream($filename, [
            'Attachement' => false
        ]);
    }
}