<?php


namespace Imos\Invoice;


use DateInterval;
use DateTime;
use Exception;

class DateRange
{
    protected $start;
    protected $end;

    /**
     * @param DateTime $start
     * @param DateTime $end
     * @throws Exception
     */
    public function __construct($start, $end)
    {
        if ($start->diff($end)->format('%R') === '-') {
            throw new Exception('End date must be before start date.');
        }

        $this->start = $start;
        $this->end = $end;
    }

    /**
     * @param string $start
     * @param string $end
     * @return static
     */
    public static function create($start, $end)
    {
        return new static(new DateTime($start), new DateTime($end));
    }

    /**
     * @return DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * @return DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @return DateInterval
     */
    public function getInterval()
    {
        return $this->start->diff($this->end);
    }
}
