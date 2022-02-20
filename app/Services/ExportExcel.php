<?php

namespace App\Services;

use \PhpOffice\PhpSpreadsheet\Spreadsheet;
use \PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportExcel
{
    /**
     * Create a new job instance.
     *
     * @return void
     */
    /** sample data structure
     * {
     *      filename: <filename>,
     *      headers: [Header1, Header2],
     *      fills: [fill1, fill2],
     *      data:[
     *              {fill1data1, fill2data1},
     *              {fill1data2, fill2data2}
     *      ]
     * }
     * 
     *  */

    
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function export()
    {

        /** To Automate the Column of the Excel */
        $initializeLetters = [0 => '', 1 => 'A', 2 => 'B', 3 => 'C',
            4 => 'D', 5 => 'E', 6 => 'F', 7 => 'G', 8 => 'H',
            9 => 'I', 10 => 'J', 11 => 'K', 12 => 'L', 13 => 'M',
            14 => 'N', 15 => 'O', 16 => 'P', 17 => 'Q', 18 => 'R',
            19 => 'S', 20 => 'T', 21 => 'U', 22 => 'V', 23 => 'W',
            24 => 'X', 25 => 'Y', 26 => 'Z',
        ];
        $cells = [];
        for ($xx = 0; $xx <= 26; $xx++) {
            for ($yy = 1; $yy <= 26; $yy++) {
                $cells[] = $initializeLetters[$xx] . $initializeLetters[$yy];
            }
        }
    
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
    
        /** We Are Setting The Headers First */
        $colCount = 1;
        foreach ($this->data->headers as $header) {
            $sheet->setCellValue($initializeLetters[$colCount].'1', $header);
            $sheet->getStyle($initializeLetters[$colCount].'1')->getFont()->setBold(true);
            $sheet->getStyle($initializeLetters[$colCount].'1')->getAlignment()->setVertical('center')->setHorizontal('center');
            $colCount++;
        }

        $rowCount = 2;
        foreach ($this->data->data as $datum) {
            $currentCell = 0;
            foreach ($this->data->fills as $fill) {
                $sheet->setCellValue($cells[$currentCell] . $rowCount, $datum->{$fill});
                $sheet->getStyle($cells[$currentCell] . $rowCount)
                    ->getAlignment()
                    ->setVertical('center')
                    ->setHorizontal('center');
                $currentCell++;
            }
            $rowCount++;
        }

        $writer = new Xlsx($spreadsheet);
        $fileName = $this->data->filename;
        $path = 'uploads/' . $fileName . '.xlsx';
        @unlink('uploads/' . $fileName . '.xlsx');
        $writer->save('uploads/' . $fileName . '.xlsx');
        return $path;
    }
}
