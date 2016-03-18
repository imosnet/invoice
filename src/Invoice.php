<?php

namespace Imos\Invoice;

use DateTime;
use Imos\Invoice\Exception\InvalidPriceTypeException;

class Invoice
{

    const PRICE_NET = 1;
    const PRICE_GROSS = 2;

    /** @var int $precision Precision to use for monetary calculations */
    protected $precision = 2;
    /** @var int $type List price type - self::PRICE_NET or self::PRICE_GROSS*/
    protected $type = self::PRICE_NET;

    /** @var string */
    protected $currency;
    /** @var string[] */
    protected $customerAddress;
    /** @var string */
    protected $customerNumber;
    /** @var string */
    protected $invoiceNumber;
    /** @var DateTime */
    protected $invoiceDate;
    /** @var DateTime */
    protected $dueDate;
    /** @var DateRange */
    protected $billingPeriod;
    /** @var string */
    protected $commission;

    /** @var string[][] */
    protected $taxIds = array();

    /** @var string */
    protected $paymentTerms;
    /** @var string[] */
    protected $extraInfo = array();

    /** @var LineItem[] */
    protected $lineItems = array();


    /****************************************************
     * Basic settings
     ****************************************************/

    /**
     * Sets the price type (net or gross) for line items, which determines how taxes are calculated.
     *
     * When set to net, prices are considered net and taxes are calculated "forwards" from net subtotals.
     * When set to gross, prices are considered gross and taxes are calculated "backwards" from gross subtotals.
     *
     * @param int $type Invoice::PRICE_GROSS or Invoice::PRICE_NET
     * @return $this
     * @throws InvalidPriceTypeException
     */
    public function setPriceType($type)
    {
        if (!in_array($type, [self::PRICE_NET, self::PRICE_GROSS])) {
            throw new InvalidPriceTypeException;
        }
        $this->type = $type;
        return $this;
    }

    /**
     * @return int Invoice::PRICE_GROSS or Invoice::PRICE_NET
     */
    public function getPriceType()
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getPrecision()
    {
        return $this->precision;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $currency A currency code, symbol or text
     * @param int $precision The number of digits to round results to
     *
     * @example setCurrency('EUR')
     * @example setCurrency('€', 2)
     * @example setCurrency('US$')
     * @example setCurrency('Yen', 0)
     *
     * @return $this
     */
    public function setCurrency($currency, $precision = 2)
    {
        $this->currency = $currency;
        $this->precision = $precision;
        return $this;
    }


    /****************************************************
     * Getters and setters
     ****************************************************/

    /**
     * @return string[]
     */
    public function getCustomerAddress()
    {
        return $this->customerAddress;
    }

    /**
     * @param string[] $customerAddress Lines of text
     * @return Invoice
     */
    public function setCustomerAddress($customerAddress)
    {
        $this->customerAddress = $customerAddress;
        return $this;
    }

    /**
     * @return string
     */
    public function getCustomerNumber()
    {
        return $this->customerNumber;
    }

    /**
     * @param string $customerNumber
     * @return Invoice
     */
    public function setCustomerNumber($customerNumber)
    {
        $this->customerNumber = $customerNumber;
        return $this;
    }

    /**
     * @return string
     */
    public function getInvoiceNumber()
    {
        return $this->invoiceNumber;
    }

    /**
     * @param string $invoiceNumber
     * @return Invoice
     */
    public function setInvoiceNumber($invoiceNumber)
    {
        $this->invoiceNumber = $invoiceNumber;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getInvoiceDate()
    {
        return $this->invoiceDate;
    }

    /**
     * @param DateTime $invoiceDate
     * @return Invoice
     */
    public function setInvoiceDate(DateTime $invoiceDate)
    {
        $this->invoiceDate = $invoiceDate;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param DateTime $dueDate
     * @return Invoice
     */
    public function setDueDate(DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
        return $this;
    }

    /**
     * @return DateRange
     */
    public function getBillingPeriod()
    {
        return $this->billingPeriod;
    }

    /**
     * @param DateRange $billingPeriod
     * @return Invoice
     */
    public function setBillingPeriod(DateRange $billingPeriod)
    {
        $this->billingPeriod = $billingPeriod;
        return $this;
    }

    /**
     * @return string
     */
    public function getCommission()
    {
        return $this->commission;
    }

    /**
     * @param string $commission
     * @return Invoice
     */
    public function setCommission($commission)
    {
        $this->commission = $commission;
        return $this;
    }

    /**
     * Returns array of arrays with keys 'label' and 'value':
     *
     *     array(
     *         array(
     *             'label' => 'VAT ID',
     *             'value' => 'DE999999999',
     *         ),
     *     )
     *
     * @return array[]
     */
    public function getTaxIds()
    {
        return $this->taxIds;
    }

    /**
     * @param string $label
     * @param string $value
     * @return $this
     */
    public function addTaxId($label, $value)
    {
        $this->taxIds[] = compact('label', 'value');
        return $this;
    }

    /**
     * @return $this
     */
    public function clearTaxIds()
    {
        $this->taxIds = array();
        return $this;
    }

    /**
     * @return string
     */
    public function getPaymentTerms()
    {
        return $this->paymentTerms;
    }

    /**
     * @param string $paymentTerms
     * @return Invoice
     */
    public function setPaymentTerms($paymentTerms)
    {
        $this->paymentTerms = $paymentTerms;
        return $this;
    }

    /**
     * @return string[]
     */
    public function getExtraInfo()
    {
        return $this->extraInfo;
    }

    /**
     * @param string $extraInfo
     * @return $this
     */
    public function addExtraInfo($extraInfo)
    {
        $this->extraInfo[] = $extraInfo;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearExtraInfo()
    {
        $this->extraInfo = array();
        return $this;
    }

    /**
     * @return LineItem[]
     */
    public function getLineItems()
    {
        return $this->lineItems;
    }

    /**
     * @param LineItem $item
     * @return $this
     */
    public function addLineItem(LineItem $item)
    {
        $this->lineItems[] = $item;
        return $this;
    }

    /**
     * @return $this
     */
    public function clearLineItems()
    {
        $this->lineItems = array();
        return $this;
    }


    /****************************************************
     * Calculations
     ****************************************************/

    /**
     * Get all applicable taxes
     *
     *     array(
     *         array(
     *             'name' => 'VAT (DE)',
     *             'rate' => 19,
     *             'total' => 7.6,
     *         )
     *     )
     *
     * @return array[]
     */
    public function getTaxes()
    {
        $taxes = array();
        foreach ($this->lineItems as $lineItem) {
            // Create tax record
            $newTax = array(
                'name' => $lineItem->getTaxName(),
                'rate' => $lineItem->getTaxRate(),
                'total' => $this->round($lineItem->getTotal()),
            );

            if (is_null($newTax['rate']) && is_null($newTax['name'])) {
                continue;
            }

            // Merge array
            $found = false;
            foreach ($taxes as &$tax) {
                if ($tax['name'] == $newTax['name'] && $tax['rate'] == $newTax['rate']) {
                    $found = true;
                    $tax['total'] = bcadd($tax['total'], $newTax['total']);
                }
            }
            unset($tax);

            if (!$found) {
                $taxes[] = $newTax;
            }
        }

        // Calculate taxes
        foreach ($taxes as &$tax) {
            $tax['total'] = $this->calculateTax($tax['total'], $tax['rate']);
        }
        unset($tax);

        return $taxes;
    }

    /**
     * Calculate tax
     *
     * @param int|float|string $total
     * @param int|float|string $rate Percentage (e.g. 19 for 19%)
     * @return int|string
     */
    protected function calculateTax($total, $rate)
    {
        if ($this->type === self::PRICE_NET) {
            $multiplier = bcdiv($rate, 100);
        } else {
            $multiplier = bcsub(1, bcdiv(1, bcadd(1, bcdiv($rate, 100))));
        }
        return $this->round(bcmul($total, $multiplier));
    }

    /**
     * Total amount of tax
     *
     * @return int|string
     */
    public function getTaxTotal()
    {
        return array_reduce(array_column($this->getTaxes(), 'total'), 'bcadd');
    }

    /**
     * Simple sum of line item totals
     *
     * @return int|string
     */
    protected function getLineItemSum()
    {
        $sum = 0;
        foreach ($this->lineItems as $lineItem) {
            $sum = bcadd($sum, $lineItem->getTotal());
        }
        return $this->round($sum);
    }

    /**
     * Calculates invoice total
     *
     * @param int $type Invoice::PRICE_NET or Invoice::PRICE_GROSS
     * @return int|string
     * @throws InvalidPriceTypeException
     */
    public function getTotal($type)
    {
        if ($type === $this->type) {
            // No calculations necessary
            return $this->getLineItemSum();
        } elseif ($type === self::PRICE_GROSS) {
            // Calculate gross from net
            return bcadd($this->getLineItemSum(), $this->getTaxTotal());
        } elseif ($type === self::PRICE_NET) {
            // Calculate net from gross
            return bcsub($this->getLineItemSum(), $this->getTaxTotal());
        }
        throw new InvalidPriceTypeException;
    }


    /****************************************************
     * Utilities
     ****************************************************/

    /**
     * Round value according to currency settings
     *
     * @param int|float|string $number
     * @return int|string
     */
    public function round($number)
    {
        if (strpos($number, '.') !== false) {
            if ($number[0] != '-') {
                return bcadd($number, '0.' . str_repeat('0', $this->precision) . '5', $this->precision);
            }
            return bcsub($number, '0.' . str_repeat('0', $this->precision) . '5', $this->precision);
        }
        return $number;
    }
}
