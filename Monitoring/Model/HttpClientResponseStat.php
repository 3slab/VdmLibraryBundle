<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Monitoring\Model;

class HttpClientResponseStat
{
    /**
     * @var float
     */
    protected $time;
    
    /**
     * @var int
     */
    protected $bodySize;
    
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * MemoryStat constructor.
     *
     * @param float|null $time
     * @param int|null $bodySize
     * @param int $statusCode
     */
    public function __construct(?float $time = null, ?int $bodySize = null, int $statusCode = 0)
    {
        $this->time = $time;
        $this->bodySize = $bodySize;
        $this->statusCode = $statusCode;
    }

    /**
     * @return float|null
     */
    public function getTime(): ?float
    {
        return $this->time;
    }

    /**
     * @return int|null
     */
    public function getBodySize(): ?int
    {
        return $this->bodySize;
    }

    /**
     * @return int
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
