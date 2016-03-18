<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Formatter;
use Imos\Invoice\Invoice;

interface GeneratorInterface
{
    /**
     * @param Invoice $invoice
     * @return string
     */
    public function generate(Invoice $invoice);

    /**
     * @param Formatter $formatter
     * @return mixed
     */
    public function setFormatter(Formatter $formatter);
}
