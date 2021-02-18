<?php


namespace App\Imports;


use App\Models\Utility;
use Box\Spout\Common\Type;
use Box\Spout\Reader\ReaderFactory;
use Box\Spout\Reader\ReaderInterface;
use Box\Spout\Reader\XLSX\Sheet;
use Illuminate\Support\Facades\Log;
use Throwable;
use Exception;

class BaseImport
{
    /**
     * @var Log
     */
    public $logger;
    /**
     * @var array
     */
    public $options;
    /**
     * @var array
     */
    public $results;
    /**
     * @var ReaderInterface
     */
    public $reader;
    /**
     * @var Utility
     */
    public $utility;

    /**
     * @param Utility $utility
     * @throws Throwable
     */
    public function init(Utility $utility)
    {
        $this->reader = ReaderFactory::create(Type::XLSX);
        $this->reader->open($utility->getTempStoreUrl());
        $this->utility = $utility;
        $this->logger = Log::channel('import');
    }

    /**
     * @throws Throwable
     */
    public function finally()
    {
        $this->reader->close();
    }

    /**
     * @return ReaderInterface
     */
    public function getReader()
    {
        return $this->reader;
    }

    /**
     * @return Log
     */
    public function getLogger()
    {
        return $this->logger;
    }

    /**
     * @return Utility
     */
    public function getUtility()
    {
        return $this->utility;
    }

    /**
     * @return array
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @param $sheet_code
     * @return Sheet|null
     */
    public function getSheet($sheet_code)
    {
        try {
            return $this->options['required_sheets'][$sheet_code]['sheet'];
        } catch (Exception $exception) {
            return null;
        }
    }

    /**
     * @param $key
     * @return Sheet
     */
    public function getRequiredSheetByKey($key)
    {
        return $this->options['required_sheets'][$key]['sheet'];
    }

    /**
     * @throws Throwable
     */
    public function validateRequiredSheets()
    {
        $required_sheets = $this->options['required_sheets'];

        foreach ($this->getReader()->getSheetIterator() as $sheet) {
            foreach ($required_sheets as &$sheetData) {
                if ($sheet->getName() == $sheetData['name']) {
                    $sheetData['sheet'] = $sheet;
                    break;
                }
            }
        }

        // Get missing sheets
        $missing_sheets = array_filter($required_sheets, function ($item) {
            return empty($item['sheet']);
        });

        if (count($missing_sheets)) {
            $sheetList = implode(', ', array_column($missing_sheets, 'name'));
            throw new Exception(trans('messages.error_message.missing_sheet_in_file', ['name' => $sheetList]));
        }

        // If passed. Re-assign to options variable
        $this->options['required_sheets'] = $required_sheets;
    }

    /**
     * Check whether if header is enough columns
     *
     * @throws Exception
     */
    public function validateHeaderSheets()
    {
        $required_sheets = $this->options['required_sheets'];

        foreach ($required_sheets as $required_sheet)
        {
            if (!empty($required_sheet['header'])
            && $required_sheet['sheet']
            && $required_sheet['sheet'] instanceof Sheet
            ) {
                $header = $required_sheet['header'];
                $header_excel = $this->getFirstRow($required_sheet['sheet']);

                $missing_fields = array_diff($header, $header_excel);
                if (!empty($missing_fields)) {
                    $missing_fields = implode(', ', $missing_fields);
                } else {
                    $missing_fields = null;
                }

                if (!is_null($missing_fields)) {
                    throw new Exception(
                        "Sheet: {$required_sheet['name']}." .
                        trans('messages.error_message.missing_fields_in_file',
                        ['name' => $missing_fields]));
                }
            }
        }
    }

    /**
     * Get first row of the Sheet
     *
     * @param Sheet $sheet
     * @return array|string[]
     */
    public function getFirstRow(Sheet $sheet)
    {
        $first_row = [];
        foreach ($sheet->getRowIterator() as $row) {
            $first_row = str_replace(' (*)', '', $row);
            break;
        }

        return $first_row;
    }

    public function toStrings($value)
    {
        if ($value instanceof \Datetime) {
            $value = $value->getTimestamp();
        } elseif (!is_numeric($value)) {
            $value = (string) $value;
        }

        return $value;
    }
}
