<?php

namespace Intouch\Framework\View\DisplayEvents;

use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableCell;

class TableButtonOnClickEvent extends TableEvent {

    public function __construct(
        array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CLICK
        );

    }

    function GetStandardCall(TableCell | TableButton $element, string $tableKey) {

        return "
            if ( typeof " . $element->Key . "_OnClick === 'function') {
                " . $element->Key . "_OnClick({
                    Element: me,
                    RowData: myRowData
                });
            }
        ";
        
    }

}