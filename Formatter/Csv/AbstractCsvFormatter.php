<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Formatter\Csv;

use Psr\Log\LoggerInterface;

/**
 * class AbstractCsvFormatter
 */
abstract class AbstractCsvFormatter
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    protected const FILE_NAME = null;

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var array
     */
    protected $rows    = [];

    public function supports(string $fileName): bool
    {
        return static::FILE_NAME === $fileName;
    }

    /**
     * Reads the CSV file and stores it internally.
     *
     * @param  string $path
     *
     * @return void
     */
    public function readFile(string $path): iterable
    {   
        $pass   = 0;
        $handle = fopen($path, 'r');

        while (($data = $this->getCsv($handle)) !== false) {
            if (!$pass++) {
                $this->headers = $data;

                continue;
            }

            // In case the row is shorter than the header
            if (count($this->headers) !== count($data)) {
                $this->logger->warning('Found different lengths in header ({countHeader}) and row ({countRow}). HEADER: {header}. ROW: {row}', [
                    'countHeader' => count($this->headers),
                    'countRow'    => count($data),
                    'header'      => json_encode($this->headers),
                    'row'         => json_encode($data),
                ]);

                $pad  = array_fill(count($data), count($this->headers) - count($data), null);
                $data = array_merge($data, $pad);
            }

            $row = array_combine($this->headers, $data);

            yield $this->getRow($row);
        }
    }

    /**
     * Returns unaltered values. Implementations may have to parse them. That's where it should be done.
     *
     * @param  array  $data
     *
     * @return array
     */
    protected function getRow(array $data): array
    {
        return $data;
    }

    protected function getCsv($handle)
    {
        return fgetcsv($handle, 1000, ";");
    }
}
