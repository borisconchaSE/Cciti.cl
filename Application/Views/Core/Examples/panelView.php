<?php

use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\PageHeader;
use Intouch\Framework\Widget\Panel;

// PageHeader
// (Utilizar el snippet: wgPageHeader)
(new PageHeader(
    Title: "Página de Ejemplo",
    Description: "Dibujar un Panel"
))->Draw();

?>

@@Layout(authenticated)

<?php

(
    // snippet: wgContainer
    new Container(
        Classes: ['view-content'],
        Styles: [],
        Children: [
            // snippet: wgPanel
            new Panel(
                Body: new Container(
                    Classes: [],
                    Styles: [],
                    Children: []
                ),
            )
        ]
    )
)->Draw();
