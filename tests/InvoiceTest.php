<?php


namespace Imos\Invoice\Tests;


use DateTime;
use Imos\Invoice\DateRange;
use Imos\Invoice\Invoice;
use Imos\Invoice\LineItem;

class InvoiceTest extends \PHPUnit_Framework_TestCase
{
    public function testGettersSetters()
    {
        $invoice = new Invoice;

        $this->assertTrue($invoice === $invoice->setPriceType(Invoice::PRICE_GROSS));
        $this->assertEquals(Invoice::PRICE_GROSS, $invoice->getPriceType());

        $this->assertTrue($invoice === $invoice->setCurrency('EUR'));
        $this->assertEquals('EUR', $invoice->getCurrency());
        $this->assertEquals(2, $invoice->getPrecision());

        $this->assertTrue($invoice === $invoice->setCurrency('JPY', 0));
        $this->assertEquals('JPY', $invoice->getCurrency());
        $this->assertEquals(0, $invoice->getPrecision());

        $address = array(
            'imos GmbH',
            'Alfons-Feifel-Str. 9',
            '73037 Göppingen',
        );
        $this->assertTrue($invoice === $invoice->setCustomerAddress($address));
        $this->assertEquals($address, $invoice->getCustomerAddress());

        $this->assertTrue($invoice === $invoice->setCustomerNumber('99999'));
        $this->assertEquals('99999', $invoice->getCustomerNumber());

        $this->assertTrue($invoice === $invoice->setInvoiceNumber('RE-12345'));
        $this->assertEquals('RE-12345', $invoice->getInvoiceNumber());

        $this->assertTrue($invoice === $invoice->setInvoiceDate(new DateTime('2016-01-01')));
        $this->assertEquals(new DateTime('2016-01-01'), $invoice->getInvoiceDate());

        $this->assertTrue($invoice === $invoice->setDueDate(new DateTime('2016-02-01')));
        $this->assertEquals(new DateTime('2016-02-01'), $invoice->getDueDate());

        $this->assertTrue($invoice === $invoice->setBillingPeriod(DateRange::create('2016-01-01', '2016-02-01')));
        $this->assertEquals(DateRange::create('2016-01-01', '2016-02-01'), $invoice->getBillingPeriod());

        $this->assertTrue($invoice === $invoice->setCommission('Partner 1'));
        $this->assertEquals('Partner 1', $invoice->getCommission());

        $this->assertTrue($invoice === $invoice->clearTaxIds());
        $this->assertTrue($invoice === $invoice->addTaxId('Ust-ID', 'DE999999999'));
        $this->assertTrue($invoice === $invoice->addTaxId('Steuernummer', '12345 / 67890'));
        $this->assertEquals(array(
            array(
                'label' => 'Ust-ID',
                'value' => 'DE999999999',
            ),
            array(
                'label' => 'Steuernummer',
                'value' => '12345 / 67890',
            ),
        ), $invoice->getTaxIds());

        $this->assertTrue($invoice === $invoice->setPaymentTerms('30 days strictly net'));
        $this->assertEquals('30 days strictly net', $invoice->getPaymentTerms());

        $this->assertTrue($invoice === $invoice->clearExtraInfo());
        $this->assertTrue($invoice === $invoice->addExtraInfo('Payable by bank transfer.'));
        $this->assertTrue($invoice === $invoice->addExtraInfo('IBAN: DE99 1234 5678 9012 3456 78'));
        $this->assertEquals(array(
            'Payable by bank transfer.',
            'IBAN: DE99 1234 5678 9012 3456 78',
        ), $invoice->getExtraInfo());



        $item1 = new LineItem;
        $item2 = new LineItem;

        $this->assertTrue($invoice === $invoice->clearLineItems());
        $this->assertTrue($invoice === $invoice->addLineItem($item1));
        $this->assertTrue($invoice === $invoice->addLineItem($item2));

        $result = $invoice->getLineItems();
        $this->assertCount(2, $result);
        $this->assertTrue($item1 === $result[0]);
        $this->assertTrue($item2 === $result[1]);

    }

    public function testTotalsNet()
    {
        bcscale(10);

        $invoice = (new Invoice)
            ->setPriceType(Invoice::PRICE_NET)
            ->addLineItem(
                (new LineItem)
                    ->setUnitPrice(25)
                    ->setTaxRate(19)
            )
            ->addLineItem(
                (new LineItem)
                    ->setQuantity(12.4)
                    ->setUnitPrice(120)
                    ->setTaxRate(19)
            );

        $this->assertEquals('1513', $invoice->getTotal(Invoice::PRICE_NET));

        $taxes = $invoice->getTaxes();
        $this->assertCount(1, $taxes);
        $this->assertNull($taxes[0]['name']);
        $this->assertEquals('19', $taxes[0]['rate']);
        $this->assertEquals('287.47', $taxes[0]['total']);

        $this->assertEquals('287.47', $invoice->getTaxTotal());
        $this->assertEquals('1800.47', $invoice->getTotal(Invoice::PRICE_GROSS));


        // Split taxes by adding a label
        $invoice->getLineItems()[0]->setTaxName('VAT');

        $this->assertEquals('1513', $invoice->getTotal(Invoice::PRICE_NET));

        $taxes = $invoice->getTaxes();
        $this->assertCount(2, $taxes);

        $this->assertEquals('VAT', $taxes[0]['name']);
        $this->assertEquals('19', $taxes[0]['rate']);
        $this->assertEquals('4.75', $taxes[0]['total']);

        $this->assertNull($taxes[1]['name']);
        $this->assertEquals('19', $taxes[1]['rate']);
        $this->assertEquals('282.72', $taxes[1]['total']);

        $this->assertEquals('287.47', $invoice->getTaxTotal());
        $this->assertEquals('1800.47', $invoice->getTotal(Invoice::PRICE_GROSS));


    }

    public function testTotalsGross()
    {
        bcscale(10);

        $invoice = (new Invoice)
            ->setPriceType(Invoice::PRICE_GROSS)
            ->addLineItem(
                (new LineItem)
                    ->setUnitPrice(29.75)
                    ->setTaxRate(19)
            )
            ->addLineItem(
                (new LineItem)
                    ->setQuantity(12.4)
                    ->setUnitPrice(142.8)
                    ->setTaxRate(19)
            );

        $this->assertEquals('1513', $invoice->getTotal(Invoice::PRICE_NET));

        $taxes = $invoice->getTaxes();
        $this->assertCount(1, $taxes);
        $this->assertNull($taxes[0]['name']);
        $this->assertEquals('19', $taxes[0]['rate']);
        $this->assertEquals('287.47', $taxes[0]['total']);

        $this->assertEquals('287.47', $invoice->getTaxTotal());
        $this->assertEquals('1800.47', $invoice->getTotal(Invoice::PRICE_GROSS));


        // Split taxes by adding a label
        $invoice->getLineItems()[0]->setTaxName('VAT');

        $this->assertEquals('1513', $invoice->getTotal(Invoice::PRICE_NET));

        $taxes = $invoice->getTaxes();
        $this->assertCount(2, $taxes);

        $this->assertEquals('VAT', $taxes[0]['name']);
        $this->assertEquals('19', $taxes[0]['rate']);
        $this->assertEquals('4.75', $taxes[0]['total']);

        $this->assertNull($taxes[1]['name']);
        $this->assertEquals('19', $taxes[1]['rate']);
        $this->assertEquals('282.72', $taxes[1]['total']);

        $this->assertEquals('287.47', $invoice->getTaxTotal());
        $this->assertEquals('1800.47', $invoice->getTotal(Invoice::PRICE_GROSS));


    }

    public function testRound()
    {
        bcscale(10);

        $invoice = new Invoice;

        $invoice->setCurrency('EUR', 2);

        $this->assertEquals('1.25', $invoice->round('1.254'));
        $this->assertEquals('1.26', $invoice->round('1.255'));
        $this->assertEquals('1.26', $invoice->round('1.256'));

        $invoice->setCurrency('JPY', 0);

        $this->assertEquals('125', $invoice->round('125.4'));
        $this->assertEquals('126', $invoice->round('125.5'));
        $this->assertEquals('126', $invoice->round('125.6'));

    }
}
