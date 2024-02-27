<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Widget\Definitions\Table\ColumnsDefinition;
use Intouch\Framework\Widget\Definitions\Table\HeaderColumn;

#[Widget(Template: 'TableBody')]
class TableBody extends GenericWidget {

    public function __construct(
        public string   $Key = '',
        public array    $Rows = [],
    )
    {
        // Crear el contenido de la tabla
        //
        $rows = '';

        // Generar las filas
        foreach($this->Rows as $row) {
            if ($row instanceof TableRow) {
                
                if ($rows != '') {
                    $rows .= "\n";
                }
                $rows .= $row->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'ROWS'  => $rows,
        ]);
    }
}