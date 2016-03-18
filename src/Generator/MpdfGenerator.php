<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Invoice;

class MpdfGenerator extends HtmlGenerator implements GeneratorInterface
{
    /** @var \mPDF Eine mPDF-Instanz */
    protected $mpdf;

    protected $htmlTemplate = 'mpdf.twig';
    protected $stylesheets = ['default.css', 'mpdf.css'];

    protected $pdfTemplate;

    /**
     * @param \mPDF $mpdf
     */
    public function __construct(\mPDF $mpdf)
    {
        $this->mpdf = $mpdf;
    }

    /**
     * Sets a PDF to use as a template
     *
     * @see \mPDF::SetDocTemplate()
     *
     * @param string $filename
     * @return $this
     */
    public function setPdfTemplate($filename)
    {
        $this->mpdf->SetImportUse();
        $this->mpdf->SetDocTemplate($filename, true);
        return $this;
    }

    /**
     * @param Invoice $invoice
     * @return \mPDF
     */
    public function generateMpdf(Invoice $invoice)
    {
        $this->mpdf->WriteHTML(parent::generate($invoice));

        return $this->mpdf;
    }

    /**
     * @param Invoice $invoice
     * @param string|null $filename Filename of the generated PDF
     * @return string
     */
    public function generate(Invoice $invoice, $filename = null)
    {
        return $this->generateMpdf($invoice)->Output($filename, 'S');
    }
}
