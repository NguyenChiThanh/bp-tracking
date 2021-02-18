<?php

use Box\Spout\Reader\SheetInterface;

if (!function_exists('importSheet_fastexcel')) {
    function importSheet_fastexcel_withheader(SheetInterface $sheet, callable $callback)
    {
        $headers = [];
        $count_header = 0;

        // always with header
        // in addition, process header to remove (*)
        foreach ($sheet->getRowIterator() as $k => $row) {
            if ($k == 1) {
                $headers = str_replace(' (*)', '', $row);
                $count_header = count($headers);
                continue;
            }
            if ($count_header > $count_row = count($row)) {
                $row = array_merge($row, array_fill(0, $count_header - $count_row, null));
            } elseif ($count_header < $count_row = count($row)) {
                $row = array_slice($row, 0, $count_header);
            }

            // always call $callback
            $callback(array_combine($headers, $row), $k);
        }
        // no return
    }
}
