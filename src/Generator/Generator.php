<?php


namespace Imos\Invoice\Generator;


use Imos\Invoice\Invoice;

interface Generator {
    public function generate(Invoice $invoice);
}
