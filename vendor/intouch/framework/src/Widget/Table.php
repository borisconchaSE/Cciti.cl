<?php

namespace Intouch\Framework\Widget;

use Attribute;
use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'Table')]
class Table extends GenericWidget {

    public function __construct(
        public string               $Key,
        public TableHeader          $Header,
        public TableBody            $Body,
        public string               $EmptyMessage = 'No se han encontrado registros',
        public array                $Classes = [],
        public array                $Styles = [],
        public array                $Attributes = [],
        public array                $Properties = [],
        public bool                 $JSRenderTheTable = false
    )
    {
        $this->AddClass('table');
        $this->AddClass('table-bordered');

        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddProperties($Properties);

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        // HEADER
        //
        $header = $this->Header->Draw(false);

        // BODY
        //
        if (count($this->Body->Rows) == 0) {
            // Crear un body vacío
            // cantidad de columnas las dicta la última fila del header (para casos multi-fila)
            if($JSRenderTheTable == true){
                $body = (new TableBody(
                    Rows: [
                        
                    ]
                ))->Draw(false);
            }else{
                $span = count($this->Header->Rows[count($this->Header->Rows)-1]->Columns);
             
                $body = (new TableBody(
                    Rows: [
                        new TableRow(
                            Columns: [
                                new TableColumn(
                                    Content: $this->EmptyMessage,
                                    Attributes: [
                                        ['colspan', $span]
                                    ]
                                )
                            ]
                        )
                    ]
                ))->Draw(false);
            }
        }
        else {
            $body = $this->Body->Draw(false);
        }

        parent::__construct(Replace: [
            'HEADER'        => $header,
            'BODY'          => $body,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }
}