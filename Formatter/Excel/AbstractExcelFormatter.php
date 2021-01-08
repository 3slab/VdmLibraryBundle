<?php

/**
 * @package    3slab/VdmLibraryBundle
 * @copyright  2020 Suez Smart Solutions 3S.lab
 * @license    https://github.com/3slab/VdmLibraryBundle/blob/master/LICENSE
 */

namespace Vdm\Bundle\LibraryBundle\Formatter\Excel;

use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Worksheet\Row;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Shared\Date as ExcelDate;
use Psr\Log\LoggerInterface;
use PhpOffice\PhpSpreadsheet\Calculation\Exception as CalculationException;

class AbstractExcelFormatter
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var IOFactory
     */
    protected $reader;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->reader = IOFactory::createReader(static::FILE_EXTENSION);
    }

    protected const FILE_NAME = null;

    protected const HEADER_ROW_INDEX = 1;

    protected const FILE_EXTENSION = 'Xlsx';

    protected const SHEETS = [];

    protected const OVERWRITE_VALUES = [];

    protected const IGNORE_CELLS = [];

    protected const DATE_FIELDS = [];

    protected const DATE_FORMAT = 'Y-m-d';

    protected const DATA_ONLY = true;

    protected $worksheets = [];

    public function supports(string $fileName): bool
    {
        return static::FILE_NAME === $fileName;
    }

    /**
     * Reads the Excel file and stores it internally.
     *
     * @param  string $path
     *
     * @return iterable
     */
    public function readFile(string $path): iterable
    {
        $this->reader->setReadDataOnly(self::DATA_ONLY);
        $spreadsheet = $this->reader->load($path);

        $worksheets = $spreadsheet->getAllSheets();
        foreach ($worksheets as $worksheet) {
            $worksheetTitle = $worksheet->getTitle();
            if (!empty(static::SHEETS) && !in_array($worksheetTitle, static::SHEETS, true)) {
                continue;
            }
            yield from $this->readWorksheet($worksheet);
        }
    }

    /**
     * @param Worksheet $worksheet
     * @return iterable
     */
    protected function readWorksheet(Worksheet $worksheet): iterable
    {
        $worksheetTitle = $worksheet->getTitle();
        $this->worksheets[$worksheetTitle]['headers'] = [];
        $this->worksheets[$worksheetTitle]['countColumn'] = 0;
        $this->worksheets[$worksheetTitle]['ignoreCells'] = static::IGNORE_CELLS[$worksheetTitle] ?? [];
        foreach ($worksheet->getRowIterator() as $index => $row) {
            yield from $this->readRow($row, $index, $worksheetTitle);
        }
    }

    /**
     * @param Row $row
     * @param int $index
     * @param string $worksheetTitle
     * @return iterable
     */
    protected function readRow(Row $row, int $index, string $worksheetTitle): iterable
    {
        $worksheetInfo = $this->worksheets[$worksheetTitle];
        $head = static::HEADER_ROW_INDEX === $row->getRowIndex();
        $cellIterator = $row->getCellIterator();
        $cellIterator->setIterateOnlyExistingCells(false);
        $cells = [];
        foreach ($cellIterator as $cell) {
            $coordinate = $cell->getCoordinate();
            if (in_array($coordinate, $worksheetInfo['ignoreCells'], true)) {
                continue;
            }
            $value = $cell->getValue();
            $overwriteValue = static::OVERWRITE_VALUES[$worksheetTitle][$coordinate] ?? null;
            if ($overwriteValue) {
                $value = $overwriteValue;
            }
            if (is_string($value) && ''!== $value && '=' === $value[0]) {
                try {
                    $value = $cell->getCalculatedValue();
                } catch (CalculationException $e) {
                    $this->logger->warning('[Excel] Error calculating value.', [
                        'message' => $e->getMessage(),
                        'coordinate' => $coordinate,
                        'formula' => $value
                    ]);
                    $value = null;
                }
            }
            // Clean null of header values but not data values
            if (!$head || ($head && !is_null($value))) {
                $cells[] = $value;
            }
        }

        if (!empty($cells)) {
            if ($head) {
                $headers = $cells;
                $this->worksheets[$worksheetTitle]['headers'] = $headers;
                $this->worksheets[$worksheetTitle]['countColumn'] = count($headers);
            } elseif (!empty($worksheetInfo['headers'])) {
                $cellsData = array_combine($worksheetInfo['headers'], array_slice($cells, 0, $worksheetInfo['countColumn']));
                $data['data'] = $cellsData;
                $data['rowIndex'] = $index;
                $data['worksheet'] = $worksheetTitle;
                yield $this->getRow($data);
            }
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

    /**
     * The value is amount of days passed since 1900. Change to DateTime
     *
     * @param $data
     * @return array
     */
    public function formatDateFromExcel($data): array
    {
        foreach (static::DATE_FIELDS as $field) {
            if (!empty($data[$field])) {
                try {
                    $data[$field] = ExcelDate::excelToDateTimeObject($data[$field])->format(static::DATE_FORMAT);
                } catch (\Exception $e) {
                    $this->logger->warning('[Excel] Extraction of date failed. Value: "'.$data[$field].'"');
                }
            }
        }

        return $data;
    }
}