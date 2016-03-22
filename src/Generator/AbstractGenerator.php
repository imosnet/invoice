<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Formatter;

abstract class AbstractGenerator
{
    /** @var Formatter */
    private $formatter;

    /**
     * @param Formatter $formatter
     */
    public function __construct(Formatter $formatter)
    {
        $this->setFormatter($formatter);
    }

    /**
     * @return Formatter
     */
    public function getFormatter()
    {
        return $this->formatter;
    }

    /**
     * @param Formatter $formatter
     * @return $this
     */
    public function setFormatter(Formatter $formatter)
    {
        $this->formatter = $formatter;
        return $this;
    }
}
