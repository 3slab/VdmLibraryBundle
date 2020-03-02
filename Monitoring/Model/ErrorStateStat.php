<?php


namespace App\Monitoring\Model;


class ErrorStateStat
{
    /**
     * @var int
     */
    protected $code;

    /**
     * ErrorStateStat constructor.
     * @param int $code
     */
    public function __construct(int $code = 0)
    {
        $this->code = $code;
    }

    /**
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }
}
