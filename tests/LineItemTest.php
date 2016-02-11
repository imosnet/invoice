<?php


namespace Imos\Invoice\Tests;


use Imos\Invoice\LineItem;

class LineItemTest extends \PHPUnit_Framework_TestCase
{

    function testGettersSetters()
    {
        $item = new LineItem;

        $this->assertTrue($item === $item->setDescription('Internet Widget'));
        $this->assertEquals('Internet Widget', $item->getDescription());

        $this->assertTrue($item === $item->setReference('IW-42'));
        $this->assertEquals('IW-42', $item->getReference());

        $this->assertTrue($item === $item->setQuantity(1));
        $this->assertEquals(1, $item->getQuantity());

        $this->assertTrue($item === $item->setUnit('pc.'));
        $this->assertEquals('pc.', $item->getUnit());

        $this->assertTrue($item === $item->setTaxRate(19));
        $this->assertEquals(19, $item->getTaxRate());

        $this->assertTrue($item === $item->setTaxName('VAT (DE)'));
        $this->assertEquals('VAT (DE)', $item->getTaxName());

        $this->assertTrue($item === $item->setDiscount(25));
        $this->assertEquals(25, $item->getDiscount());

        $this->assertTrue($item === $item->setUnitPrice(127.29));
        $this->assertEquals(127.29, $item->getUnitPrice());

    }

    function testLineTotals()
    {
        bcscale(10);

        $item = (new LineItem)->setUnitPrice('25');

        $this->assertEquals('25', $item->getTotal());

        $item->setTaxRate(19);
        $this->assertEquals('25', $item->getTotal());

        $item->setQuantity(2);
        $this->assertEquals('50', $item->getTotal());

        $item->setDiscount(25);
        $this->assertEquals('37.5', $item->getTotal());

    }
}
