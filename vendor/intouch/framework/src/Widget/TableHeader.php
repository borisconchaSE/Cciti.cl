<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'TableHeader')]
class TableHeader extends GenericWidget {

    public function __construct(
        public string   $Key = '',
        public array    $Rows = [], // array of TableHeaderRow (en general será solo 1)
    )
    {
        // Crear el encabezado de la tabla
        //
        $rows = "";

        // Generar las filas
        foreach($this->Rows as $row) {
            if ($row instanceof TableHeaderRow) {
                if ($rows != '') {
                    $rows .= "\n";
                }
                $rows .= $row->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'ROWS'      => $rows,
        ]);
    }
}