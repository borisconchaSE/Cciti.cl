<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Widget\Definitions\Table\TableDefinition;

#[Widget(Template: 'TableRow')]
class TableRow extends GenericWidget {

    public function __construct(
        public array  $Columns, // array of TableColumn
        public array  $Classes = [],
        public array  $Styles = [],
        public array  $Attributes = [],
        public array  $Properties = [],
        public array  $FormatFunctions = [],   
    )
    {
        $this->AddClasses($Classes);
        $this->AddStyles($Styles);
        $this->AddAttributes($Attributes);
        $this->AddProperties($Properties);

        $columns = '';
        foreach($this->Columns as $column) {

            if ($column instanceof TableColumn) {
                if ($columns != "") {
                    $columns .= "\n";
                }

                if ($column->PropertyName != '' && isset($this->FormatFunctions[$column->PropertyName])) {
                    $column->FormatFunction = $this->FormatFunctions[$column->PropertyName];
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

    public static function FromArray(array $rows, array $formatFunctions = []) {

        $rows = [];

        foreach($rows as $cols) {
            $columns = [];
            foreach($cols as $name => $value) {

                if (isset($formatFunctions[$name])) {
                    $formatFunction = $formatFunctions[$name];
                }
                else {
                    $formatFunction = null;
                }


                $newCol = new TableColumn(
                    Content: $value,
                    PropertyName: $name,
                    FormatFunction: $formatFunction,
                    Object: $cols
                );                

                array_push($columns, $newCol);
            }

            array_push(
                $rows, 
                new TableRow(Columns: $columns)
            );
        }

        return $rows;
    }

    public static function FromCollection(GenericCollection | null $Values, array $ColumnDefinitions = []) {

        $colDefs = [];

        foreach($ColumnDefinitions as $def) {
            $colDefs[$def->PropertyName] = $def;
        }

        $rows = [];

        if (isset($Values) && $Values->Count() > 0) {

            foreach($Values as $object) {
                $columns = [];
                foreach($colDefs as $prop => $colDef) {

                    if (isset($colDef->FormatFunction)) {
                        $formatFunction = $colDef->FormatFunction;
                    }
                    else {
                        $formatFunction = null;
                    }

                    $newCol = new TableColumn(
                        Content: isset($object->$prop) ? $object->$prop : '',
                        PropertyName: $prop,
                        Classes: $colDef->Classes,
                        Styles: $colDef->Styles,
                        Attributes: $colDef->Attributes,
                        Properties: $colDef->Properties,
                        FormatFunction: $formatFunction,
                        Object: $object
                    );

                    array_push($columns, $newCol);
                }

                array_push(
                    $rows, 
                    new TableRow(Columns: $columns)
                );
            }
        }

        return $rows;

    }
}