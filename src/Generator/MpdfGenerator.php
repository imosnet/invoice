<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Formatter;
use Imos\Invoice\Invoice;

class MpdfGenerator extends HtmlGenerator implements GeneratorInterface
{
    /** @var \mPDF Eine mPDF-Instanz */
    protected $mpdf;

    /** @inheritdoc */
    protected $htmlTemplate = 'mpdf.twig';

    /** @inheritdoc */
    protected $stylesheets = ['default.css', 'mpdf.css'];

    /**
     * @param Formatter $formatter
     * @param \mPDF $mpdf
     */
    public function __construct(Formatter $formatter, \mPDF $mpdf)
    {
        parent::__construct($formatter);
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
