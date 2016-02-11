<?php


namespace Imos\Invoice\Generator;


use Imos\Invoice\Invoice;

class mPdfGenerator extends HtmlGenerator implements Generator
{

    /** @var \mPDF Eine mPDF-Instanz */
    protected $mpdf;

    protected $htmlTemplate = 'mpdf.twig';
    protected $stylesheets = ['default.css', 'mpdf.css'];

    protected $pdfTemplate;

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
    public function generate(Invoice $invoice)
    {
        $this->mpdf->WriteHTML(parent::generate($invoice));

        return $this->mpdf;
    }

}
