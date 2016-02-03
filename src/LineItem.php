<?php

namespace Imos\Invoice;

/**
 * Class LineItem
 * @package Imos\Invoice
 *
 */
class LineItem {

    /** @var string Description of line item */
    protected $description;
    /** @var string Reference */
    protected $reference;
    /** @var int Quantity */
    protected $quantity = 1;
    /** @var string Unit name */
    protected $unit;
    /** @var int|float|string Unit price */
    protected $unitPrice;
    /** @var int|float|string Tax rate in percent */
    protected $taxRate;
    /** @var string Name of tax */
    protected $taxName;
    /** @var int|float|string Discount in percent */
    protected $discount;

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return LineItem
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference()
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return LineItem
     */
    public function setReference($reference)
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * @param int|float|string $quantity
     * @return LineItem
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return $this->unit;
    }

    /**
     * @param string $unit
     * @return LineItem
     */
    public function setUnit($unit)
    {
        $this->unit = $unit;
        return $this;
    }

    /**
     * @return int|float|string
     */
    public function getUnitPrice() {
        return $this->unitPrice;
    }

    /**
     * @param int|float|string $unitPrice
     * @return $this
     */
    public function setUnitPrice($unitPrice) {
        $this->unitPrice = $unitPrice;
        return $this;
    }

    /**
     * @return float|int|string
     */
    public function getTaxRate()
    {
        return $this->taxRate;
    }

    /**
     * @param float|int|string $taxRate
     * @return LineItem
     */
    public function setTaxRate($taxRate)
    {
        $this->taxRate = $taxRate;
        return $this;
    }

    /**
     * @return string
     */
    public function getTaxName()
    {
        return $this->taxName;
    }

    /**
     * @param string $taxName
     * @return LineItem
     */
    public function setTaxName($taxName)
    {
        $this->taxName = $taxName;
        return $this;
    }

    /**
     * @return float|int|string
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * @param float|int|string $discount
     * @return LineItem
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
        return $this;
    }

    protected function getDiscountMultiplier()
    {
        return bcadd(bcdiv($this->discount, -100), 1);
    }

    /**
     * Line total
     *
     * Unit price, quantity and discount are considered in the calculation
     *
     * @param int $type Invoice::PRICE_NET or Invoice::PRICE_GROSS
     * @return int|float|string
     */
    public function getTotal()
    {
        return bcmul(bcmul($this->getUnitPrice(), $this->quantity), $this->getDiscountMultiplier());
    }

}
