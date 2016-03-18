<?php

namespace Imos\Invoice\Generator;

use Imos\Invoice\Formatter;

abstract class AbstractGenerator
{
    /**
     * @var Formatter
     */
    private $formatter;

    /**
     * @return Formatter
     */
    protected function getFormatter()
    {
        if (is_null($this->formatter)) {
            $this->formatter = new Formatter;
        }
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
