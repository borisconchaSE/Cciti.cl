<?php
namespace Intouch\Framework\OfficeHelper;

use Intouch\Framework\Collection\GenericCollection;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel{

    public $Sheet= null;
    private $CellNames = array();
    private $FieldNames = array();

    public function __construct(GenericCollection $data)
    {
        $this->CellNames = [
            'A','B','C','D','E','F','G','H','I','J','K','L','M','N',
            'O','P','Q','R','S','T','U','V','W','X','Y','Z',
            'AA','AB','AC','AD','AE','AF','AG','AH','AI','AJ','AK','AL','AM','AN',
            'AO','AP','AQ','AR','AS','AT','AU','AV','AW','AX','AY','AZ'
        ];
        $this->Sheet = $this->GenerateSheet($data);
    }

    private function GenerateSheet(GenericCollection $data){

        // Obtener el DTO que alimenta la hoja
        $dtoName = $data->GetDtoName();

        $sheet = new Spreadsheet();

        // Recorrer las propiedades del DTO para generar el encabezado de la tabla
        //
        $idx = 0;        
        foreach(get_class_vars($dtoName) as $key => $value){

            // Agregar el nombre del campo en un arreglo temporal
            // para tener el orden en el siguiente bucle de llenado de filas
            array_push($this->FieldNames, $key);

            // Modificar el nombre de la propiedad, para cambiar los "_" por " "
            $propiedad = str_replace('_', ' ', $key);

            // Asignar la propiedad a la celda correspondiente de la fila "1" de la tabla
            // que almacena los titulos de las columnas
            $sheet->setActiveSheetIndex(0)
                ->setCellValue($this->CellNames[$idx].'1', $propiedad);

            $idx++;
        }

        // Recorrer el arreglo de resultados para generar las filas de la tabla
        //
        $idxFila = 2;
        foreach($data as $fila){

            // Por cada fila, recorrer las columnas y agregar el valor de la columna
            // en la celda correspondiente
            foreach($this->FieldNames as $key => $field){
                // Obtener el valor
                $valor = $fila->$field;
                // Asignar valor a celda
                $sheet->setActiveSheetIndex(0)
                    ->setCellValue($this->CellNames[$key].$idxFila, $valor);
            }

            $idxFila++;
        }

        return $sheet;
    }

    public function Write($fileName){
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$fileName.'.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $writer = IOFactory::createWriter($this->Sheet, 'Xlsx');
        $writer->save('php://output');
    }
}