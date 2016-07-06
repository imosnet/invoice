<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Invoice;

class HtmlGenerator extends AbstractGenerator implements GeneratorInterface
{
    /** @var string[] Columns available on the invoice */
    protected $availableColumns = array(
        'description',
        'reference',
        'quantity',
        'unit',
        'unitPrice',
        'discount',
        'total',
    );

    /** @var string Filename of a Twig template */
    protected $htmlTemplate = 'default.twig';

    /** @var string[] Filenames of stylesheets to use */
    protected $stylesheets = ['default.css'];

    /** @var string[] CSS snippets to inject */
    protected $css = array();

    /** @var string[] File paths to include Twig templates from */
    protected $htmlTemplatePaths = array();

    protected function getHtmlTemplateDefaultPath()
    {
        return __DIR__ . '/../../resources/html_templates/';
    }

    /**
     * Adds an include path to find Twig templates in
     *
     * @param string $path
     * @return $this
     */
    public function addHtmlTemplatePath($path)
    {
        $this->htmlTemplatePaths[] = $path;
        return $this;
    }

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
    public function addStylesheet($stylesheet)
    {
        $this->stylesheets[] = $stylesheet;
        return $this;
    }

    /** @deprecated Alias for addStylesheet() */
    public function addSytlesheet($stylesheet)
    {
        return $this->addStylesheet($stylesheet);
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
     * @param string $css CSS snippet to inject
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
            'css' => $this->css,
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
        $paths = array_merge(
            $this->htmlTemplatePaths,
            array($this->getHtmlTemplateDefaultPath())
        );
        $loader = new \Twig_Loader_Filesystem($paths);
        $twig = new \Twig_Environment($loader);

        $twig->addFilter(new \Twig_SimpleFilter('currency', function ($value) use ($invoice) {
            return $this->getFormatter()->formatCurrency($value, $invoice);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('numeric', function ($value, $precision = null) {
            return $this->getFormatter()->formatNumber($value, $precision);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('percentage', function ($value) {
            return $this->getFormatter()->formatPercentage($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('date', function ($value) {
            return $this->getFormatter()->formatDate($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('dateRange', function ($value) {
            return $this->getFormatter()->formatDateRange($value);
        }));
        $twig->addFilter(new \Twig_SimpleFilter('text', function ($value) use ($invoice) {
            return $this->getFormatter()->formatText($value, $invoice);
        }));
        $twig->addFunction(new \Twig_SimpleFunction('string', function ($handle) {
            return $this->getFormatter()->getString($handle);
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
