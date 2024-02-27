<?php

namespace Intouch\Framework\View\DisplayEvents;

class SexyPageButtonOnClickEvent extends Event {

    public function __construct(
        array $Actions = []
    ) {

        parent::__construct(
            Actions: $Actions,
            jsEvent: JsEventEnum::CLICK
        );

    }

    function GetStandardCall(?object $element = null) {

        return "
        // Obtener el SexyTabPage de este boton
        var pageElement = $(this).closest('div.ui-tabs-panel');
        // Obtener el contenedor para el contenido del page
        var pageContentElement = $(pageElement).find('div.sexytab-page-content');

        // Obtener los DATA asociados al page
        var pageData = $(pageElement).data();

        if ( typeof " . $element->Key . "_OnClick === 'function') {
            " . $element->Key . "_OnClick({
                PageElement: pageElement,
                PageData: pageData, 
                PageContentElement: pageContentElement
            });
        }
        ";
        
    }

}