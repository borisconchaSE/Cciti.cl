<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use PhpOffice\PhpSpreadsheet\Chart\Title;

#[Widget(Template: 'TableHeaderRow')]
class TableHeaderRow extends GenericWidget {

    public function __construct(
        public array  $Columns, // array of TableHeaderColumn
        public string $Key = '',
        public int    $Colspan = 1,
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],        
    )
    {

        if ($this->Key != '') {
            $this->AddAttribute('id', $this->Key);
        }

        if ($this->Colspan != 1) {
            $this->AddAttribute('colspan', $this->Colspan);
        }

        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddProperties($Properties);

        $columns = '';
        foreach($this->Columns as $column) {

            if ($column instanceof TableHeaderColumn) {
                if ($columns != "") {
                    $columns .= "\n";
                }
                $columns .= $column->Draw(false);
            }
        }


        parent::__construct(Replace: [
            'COLUMNS'       => $columns,
            'CLASSES'       => $this->DrawClasses(),
            'STYLES'        => $this->DrawStyles(),
            'ATTRIBUTES'    => $this->DrawAttributes(),
            'PROPERTIES'    => $this->DrawProperties()
        ]);
    }    

    public static function FromDefinitionArray(array $HeaderColumnDefinitions = []) {

        $columns = [];

        foreach($HeaderColumnDefinitions as $colDef) {

            $newCol = new TableHeaderColumn(
                Title: $colDef->Title,
                Rowspan: $colDef->Rowspan,
                Colspan: $colDef->Colspan,
                Classes: $colDef->Classes,
                Styles: $colDef->Styles,
                Attributes: $colDef->Attributes,
                Properties: $colDef->Properties
            );

            array_push($columns, $newCol);
        }

        return new TableHeaderRow(Columns: $columns);
    }
}