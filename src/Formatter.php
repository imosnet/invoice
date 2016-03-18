<?php

namespace Imos\Invoice;

class Formatter
{
    protected $maxPrecision = 50;
    protected $decimalSeperator = '.';
    protected $thousandsSeperator = ',';

    protected $dateFormat = 'Y-m-d';
    protected $dateRangeFormat = '%s – %s'; // &thinsp;&ndash;&thinsp;
    protected $percentageFormat = '%s%%';
    protected $currencyFormat = '%s %s'; // &#8239; narrow no-break space

    protected $strings = array(
        'customer_number' => 'Customer no.',
        'invoice_number' => 'Invoice no.',
        'invoice_date' => 'Invoice date',
        'billing_period' => 'Billing period',
        'commission' => 'Commission',

        'item_description' => 'Description',
        'item_reference' => 'Reference',
        'item_quantity' => 'Qty.',
        'item_unit' => 'Unit',
        'item_unit_price' => 'Price',
        'item_discount' => 'Discount',
        'item_total' => 'Subtotal',

        'payment_terms' => 'Payment terms',

        'price_net' => 'Net total',
        'price_gross' => 'Gross total',
    );

    /**
     * Set maximum precision for formatted numbers with automatic precision
     *
     * @param int $maxPrecision
     * @return $this
     */
    public function setMaxPrecision($maxPrecision)
    {
        $this->maxPrecision = $maxPrecision;
        return $this;
    }

    /**
     * Set decimal seperator
     *
     * @param string $decimalSeperator
     * @return $this
     */
    public function setDecimalSeperator($decimalSeperator)
    {
        $this->decimalSeperator = $decimalSeperator;
        return $this;
    }

    /**
     * Set thousands seperator
     *
     * @param string $thousandsSeperator
     * @return $this
     */
    public function setThousandsSeperator($thousandsSeperator)
    {
        $this->thousandsSeperator = $thousandsSeperator;
        return $this;
    }

    /**
     * Set date format
     * @see date()
     *
     * @param string $dateFormat
     * @return $this
     */
    public function setDateFormat($dateFormat)
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    /**
     * A sprintf() format which receives formatted start and end dates
     * @see sprintf()
     *
     * @param string $dateRangeFormat
     * @return $this
     */
    public function setDateRangeFormat($dateRangeFormat)
    {
        $this->dateRangeFormat = $dateRangeFormat;
        return $this;
    }

    /**
     * A sprintf() format which receives a formatted number
     * @see sprintf()
     *
     * @param string $percentageFormat
     * @return $this
     */
    public function setPercentageFormat($percentageFormat)
    {
        $this->percentageFormat = $percentageFormat;
        return $this;
    }

    /**
     * A sprintf() format which receives a formatted number
     * @see sprintf()
     *
     * @param string $currencyFormat
     * @return $this
     */
    public function setCurrencyFormat($currencyFormat)
    {
        $this->currencyFormat = $currencyFormat;
        return $this;
    }

    /**
     * @param string $handle
     * @param string $text
     * @return $this
     */
    public function setString($handle, $text)
    {
        $this->strings[$handle] = $text;
        return $this;
    }

    /**
     * @param array $strings Associative array $handle => $text
     * @return $this
     */
    public function setStrings($strings)
    {
        foreach ($strings as $handle => $text) {
            $this->setString($handle, $text);
        }
        return $this;
    }

    /**
     * @param string $handle
     * @return string
     */
    public function getString($handle)
    {
        return $this->strings[$handle];
    }

    /**
     * @param float|int $value Number to be formatted
     * @param int|null $precision Number of decimal places to print. If null, determines precision automatically
     * @return mixed
     */
    public function formatNumber($value, $precision = null)
    {
        if (is_null($value)) {
            return null;
        }

        // Automatic precision
        if ($precision === null) {
            $parts = explode(localeconv()['decimal_point'], (string)$value);
            $precision = isset($parts[1]) ? strlen($parts[1]) : 0;
        }

        return number_format($value, $precision, $this->decimalSeperator, $this->thousandsSeperator);
    }

    /**
     * @param \DateTime $value
     * @return string
     */
    public function formatDate(\DateTime $value = null)
    {
        return is_null($value) ? null : $value->format($this->dateFormat);
    }

    /**
     * @param DateRange $value
     * @return string
     */
    public function formatDateRange(DateRange $value = null)
    {
        return is_null($value) ? null : sprintf($this->dateRangeFormat, $this->formatDate($value->getStart()), $this->formatDate($value->getEnd()));
    }

    /**
     * @param float|int $value
     * @return string
     */
    public function formatPercentage($value)
    {
        return is_null($value) ? null : sprintf($this->percentageFormat, $this->formatNumber($value));
    }

    /**
     * @param float|int $value
     * @param Invoice $invoice
     * @return string
     */
    public function formatCurrency($value, Invoice $invoice)
    {
        return is_null($value) ? null : sprintf($this->currencyFormat, $invoice->getCurrency(), $this->formatNumber($value, $invoice->getPrecision()));
    }

    /**
     * Replaces placeholders in text with values from an invoice
     *
     * @param string $value
     * @param Invoice $invoice
     * @return string
     */
    public function formatText($value, Invoice $invoice)
    {
        return preg_replace_callback('/\{\{([a-z]+)\}\}/i', function ($matches) use ($invoice) {
            switch ($matches[1]) {
                case 'totalNet':
                    return $this->formatCurrency($invoice->getTotal(Invoice::PRICE_NET), $invoice);
                case 'totalGross':
                    return $this->formatCurrency($invoice->getTotal(Invoice::PRICE_GROSS), $invoice);
                case 'taxTotal':
                    return $this->formatCurrency($invoice->getTaxTotal(), $invoice);
                case 'customerNumber':
                    return $invoice->getCustomerNumber();
                case 'invoiceNumber':
                    return $invoice->getInvoiceNumber();
                case 'invoiceDate':
                    return $this->formatDate($invoice->getInvoiceDate());
                case 'dueDate':
                    return $this->formatDate($invoice->getDueDate());
                case 'billingPeriod':
                    return $this->formatDateRange($invoice->getBillingPeriod());
                case 'commission':
                    return $invoice->getCommission();
                default:
                    return $matches[0];
            }
        }, $value);
    }
}
