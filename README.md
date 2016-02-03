Invoices
========


Example
--------

```php

$lineItem = (new LineItem)
    ->setDescription('Internet Widget')
    ->setReference('IW-42')
    ->setQuantity(2)
    ->setUnit('pc.')
    ->setUnitPrice(25)
    ->setTaxRate(19) // 19%
    ->setTaxName('VAT (DE)')
    ->setDiscount(20); // 20%

$lineItemTotal = $lineItem->getTotal(); // 40

$invoice = (new Invoice)
    ->setPriceType(Invoice::PRICE_NET)
    ->setCurrency('EUR')
//  ->setCurrency('JPY', 0)

    ->setCustomerAddress(array(
        'imos GmbH',
        'Alfons-Feifel-Str. 9',
        '73037 Göppingen',
    ))
    ->setCustomerNumber('99999')
    ->setInvoiceNumber('RE-1234')
    ->setInvoiceDate(new DateTime)
    ->setDueDate(new DateTime('+30 days'))
    ->setBillingPeriod(DateRange::create('2016-01-01', '2016-02-01'))
    ->setCommission('ACME Partner')

    ->addTaxId('VATIN', 'DE999999999')
    ->addTaxId('Steuernummer', '12345 / 67890')

    ->setPaymentTerms('30 days strictly net')

    ->addExtraInfo('Payable by bank transfer.')
    ->addExtraInfo('IBAN: DE99 1234 5678 9012 3456 78')

    ->addLineItem($lineItem)
    ->addLineItem($lineItem);

$netTotal = $invoice->getTotal(Invoice::PRICE_NET); // 40
$grossTotal = $invoice->getTotal(Invoice::PRICE_GROSS); // 47.6
$taxes = $invoice->getTaxes();
/*
    array(
        array(
            'name' => 'VAT (DE)',
            'rate' => 19,
            'total' => 7.6,
        )
    )
*/

```
