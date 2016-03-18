Invoice
=======

imos Invoice is a library for creating and generating invoices.

Setup
-----

This library relies on BCMath to do reliable calculations. Make sure [bcmath.scale] is set
sufficiently high (it should at least be higher than the number of decimal places you will
round to).

```php
ini_set('bcmath.scale', 10);
// OR
bcscale(10);
```

Install [Twig] and/or [mPDF] to use `HtmlGenerator` or `MpdfGenerator`.

[bcmath.scale]: https://php.net/bcmath.scale
[Twig]: https://packagist.org/packages/twig/twig
[mPDF]: https://packagist.org/packages/mpdf/mpdf

Building an Invoice
-------------------

`LineItem` represents a line item on your invoice. It holds basic information for each position.

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

$lineItemTotal = $lineItem->getTotal(); // Get the line total
```

`Invoice` represents an invoice. It can be set to use either net list prices or gross
list prices. Depending on type, it will calculate taxes either "backwards" or "forwards".

```php
$invoice = (new Invoice)
    ->setPriceType(Invoice::PRICE_NET)
    ->setCurrency('EUR')
    // The number of decimal places used can be set with the second parameter
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

// Add line items
    ->addLineItem($lineItem)
    ->addLineItem($lineItem);

$netTotal = $invoice->getTotal(Invoice::PRICE_NET); // 40
$grossTotal = $invoice->getTotal(Invoice::PRICE_GROSS); // 47.6
```

Line items are grouped by tax name and rate.
```php
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


### Placeholders

Text fields support the following placeholders, which are replaced with the
corresponding values from the invoice:

- `{{totalNet}}`
- `{{totalGross}}`
- `{{taxTotal}}`
- `{{customerNumber}}`
- `{{invoiceNumber}}`
- `{{invoiceDate}}`
- `{{dueDate}}`
- `{{billingPeriod}}`
- `{{commission}}`

```php
$invoice->addExtraInfo('This invoice covers services rendered {{billingPeriod}}.');
$invoice->addExtraInfo('Please transfer {{totalGross}} to our account by {{dueDate}}, ' .
                       'using "{{customerNumber}} / {{invoiceNumber}}" as the reference.');
```


Generating an Invoice
---------------------

Generating an invoice is simple:
```php
$generator = new HtmlGenerator;
echo $generator->generate($invoice);
```

The generated invoice can be customized for your locale and/or business:
```php
$formatter = (new Formatter)
    ->setDecimalSeperator('.')
    ->setThousandsSeperator(',')
    ->setDateFormat('d.m.Y')
    ->setCurrencyFormat('%02$s %01$s')
    ->setStrings(array(
        'customer_number' => 'Kundennummer',
        'invoice_number' => 'Rechnungsnummer',
        'invoice_date' => 'Rechnungsdatum',
        'billing_period' => 'Abrechnungszeitraum',
        'commission' => 'Kommission',

        'item_description' => 'Bezeichnung',
        'item_reference' => 'Referenz',
        'item_quantity' => 'Menge',
        'item_unit' => 'Einheit',
        'item_unit_price' => 'Preis',
        'item_discount' => 'Rabatt',
        'item_total' => 'Summe',

        'payment_terms' => 'Zahlungsbedingungen',

        'price_net' => 'Netto',
        'price_gross' => 'Brutto',
    ));
$generator->setFormatter($formatter);
```

### The `HtmlGenerator`

In addition to the translation features, you can add CSS directly to the generated document:
```php
$generator->addCSS('thead th {background: darkcyan;}');
$generator->addStylesheet(__DIR__ . '/custom.css');
```

You can also write your own Twig template to use (see the current ones under
`resources/html_templates/` for a starting point):

```php
$generator->addHtmlTemplatePath(__DIR__ . '/templates');
$generator->setHtmlTemplate('custom.twig');
```

### The `MpdfGenerator`

The `MpdfGenerator` uses mPDF to generate a PDF invoice. In addition to the `generate()` method,
MpdfGenerator can also return you the mPDF object directly.

```php
$generator = new MpdfGenerator(new mPDF);
$generator->generateMpdf($invoice)->Output();
```

A PDF file can be set as a template for the invoice. A template usually consists of two pages,
one for the first page of the invoice (with a letterhead, for example) and one for subsequent
pages. The last page of the template is repeated if the invoice becomes longer that the template.
Page margins can be set using CSS.

```php
$generator
    ->setPdfTemplate('letterhead.pdf')
    ->addCss('@page { margin: 2.5cm 2cm; }
              @page :first { margin-top: 5.5cm; }');
```
