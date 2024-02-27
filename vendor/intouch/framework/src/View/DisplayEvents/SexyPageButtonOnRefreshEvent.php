<?php

namespace Intouch\Framework\View\DisplayEvents;

class SexyPageButtonOnRefreshEvent extends Event {

    public function __construct(
        public string $ContentSourceUriFunction,
        public string $TabGroupKey
    ) {

        parent::__construct(
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

        // Obtener el URI de servicio para refrescar el contenido
        if ( typeof " . $this->ContentSourceUriFunction . " === 'function') {
            var uri = " . $this->ContentSourceUriFunction . "({
                PageElement: pageElement,
                PageData: pageData, 
                PageContentElement: pageContentElement
            });

            // Refrescar el contenido
            //
            if (typeof RefreshContent === 'function') {
                RefreshContent({
                    ContentElement: pageContentElement, 
                    ContentSourceUri: uri, 
                    OnSuccessCallback: function() {

                        if ( typeof " . $this->TabGroupKey . "_OnRefresh === 'function') {
                            " . $this->TabGroupKey . "_OnRefresh({
                                PageElement: pageElement, 
                                PageData: pageData, 
                                PageContentElement: pageContentElement,
                                Result: true,
                                ErrorCode: 0,
                                ErrorMessage: ''
                            });
                        }
                    }, 
                    OnErrorCallback: function(errorCode, errorMessage) {

                        if ( typeof " . $this->TabGroupKey . "_OnRefresh === 'function') {
                            " . $this->TabGroupKey . "_OnRefresh({
                                PageElement: pageElement, 
                                PageData: pageData, 
                                PageContentElement: pageContentElement,
                                Result: false,
                                ErrorCode: errorCode,
                                ErrorMessage: errorMessage
                            });
                        }
                    }
                });
            }
        }
        ";
        
    }

}