<?php

declare(strict_types=1);

namespace CommissionGenerator\Service\IOHelpers;

/**
 * Class CSVFileReaderByLine.
 */
class CSVFileReaderByLine implements InputByLine
{
    protected $file;

    /**
     * CSVFileReaderByLine constructor.
     *
     * @param $file
     */
    public function __construct($file)
    {
        $this->file = $file;
    }

    public function getLine(): iterable
    {
        if (($handle = fopen($this->file, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                yield $data;
            }
            fclose($handle);
        }
    }
}
