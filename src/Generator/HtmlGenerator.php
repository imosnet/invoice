<?php


namespace Imos\Invoice\Generator;


use Imos\Invoice\Invoice;

class HtmlGenerator extends AbstractGenerator implements Generator
{

    protected $availableColumns = array(
        'description',
        'reference',
        'quantity',
        'unit',
        'unitPrice',
        'discount',
        'total',
    );

    protected $htmlTemplate = 'default.twig';
    protected $stylesheets = ['default.css'];
    protected $css = array();

    /**
     * Sets a twig template for generating the invoice
     *
     * @param string $template
     * @return $this
     */
    public function setHtmlTemplate($template)
    {
        $this->htmlTemplate = $template;
        return $this;
    }

    /**
     * Removes all set stylesheets
     *
     * @return $this
     */
    public function clearStylesheets()
    {
        $this->stylesheets = array();
        return $this;
    }

    /**
     * Adds a CSS stylesheet
     *
     * @param string $stylesheet
     * @return $this
     */
    public function addSytlesheet($stylesheet)
    {
        $this->stylesheets[] = $stylesheet;
        return $this;
    }

    /**
     * Removes set CSS
     * @return $this
     */
    public function clearCss()
    {
        $this->css = array();
        return $this;
    }

    /**
     * Add CSS
     *
     * @param string $css
     * @return $this
     */
    public function addCss($css)
    {
        $this->css[] = $css;
        return $this;
    }

    /**
     * @param Invoice $invoice
     * @return string
     */
    public function generate(Invoice $invoice)
    {
        $twig = $this->createTwigEnvironment($invoice);
        $template = $twig->loadTemplate($this->htmlTemplate);

        return $template->render(array(
            'stylesheets' => $this->stylesheets,
            'invoice' => $invoice,
            'columns' => $this->getColumns($invoice),
            'net' => Invoice::PRICE_NET,
            'gross' => Invoice::PRICE_GROSS,
        ));
    }

    /**
     * @param Invoice $invoice
     * @return \Twig_Environment
     */
    protected function createTwigEnvironment(Invoice $invoice)
    {
        $loader = new \Twig_Loader_Filesystem(__DIR__ . '/../../resources/html_templates/');
        $twig = new \Twig_Environment($loader);

        $twig->addFilter(new \Twig_SimpleFilter('currency', function ($value) use ($invoice) {
            return $this->formatCurrency($value, $invoice);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('numeric', function ($value) {
            return $this->formatNumber($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('percentage', function ($value) {
            return $this->formatPercentage($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('date', function ($value) {
            return $this->formatDate($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('dateRange', function ($value) {
            return $this->formatDateRange($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('text', function ($value) use ($invoice) {
            return $this->formatText($value, $invoice);
        }));
        $twig->addFunction(new \Twig_SimpleFunction('string', function($handle){
            return $this->strings[$handle];
        }));

        return $twig;
    }

    /**
     * @param Invoice $invoice
     * @return array
     */
    protected function getColumns(Invoice $invoice)
    {
        $columns = array();
        foreach ($this->availableColumns as $column) {
            foreach ($invoice->getLineItems() as $lineItem) {
                $getMethod = 'get' . strtoupper($column[0]) . substr($column, 1);
                if (!is_null($lineItem->$getMethod())) {
                    $columns[$column] = $column;
                    continue 2; // To next column
                }
            }
        }

        return $columns;
    }

}
