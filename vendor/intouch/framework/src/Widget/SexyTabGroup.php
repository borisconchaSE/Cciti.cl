<?php

namespace Intouch\Framework\Widget;

use Intouch\Framework\Annotation\Attributes\Widget;

#[Widget(Template: 'SexyTabGroup')]
class SexyTabGroup extends GenericWidget {

    public function __construct(
        public string       $Key = '',
        public array        $Tabs = [],
        public array        $Pages = [],
        public array        $Classes = [],
        public array        $Styles = [],
        public array        $Attributes = [],
        public array        $Properties = [],
        public array        $ListClasses = [],
        public array        $ListStyles = [],
        public array        $ListAttributes = [],
        public array        $ListProperties = [],
        public array        $ContentClasses = [],
        public array        $ContentStyles = [],
        public array        $ContentAttributes = [],
        public array        $ContentProperties = [],
    )
    {
        // CLASSES
        $this->AddClasses($Classes);
        $this->AddClasses(['sexytabs ui-tabs ui-widget ui-widget-content ui-corner-all']);

        $this->AddClasses($ListClasses, 'list');
        $this->AddClasses(['activas-ul ui-tabs-nav ui-helper-reset ui-helper-clearfix ui-widget-header ui-corner-all'], 'list');

        $this->AddClasses($ContentClasses, 'content');
        $this->AddClass('contents', 'content');

        // STYLES
        $this->AddStyles($Styles);
        $this->AddStyles($ListStyles, 'list');
        $this->AddStyles($ContentStyles, 'content');

        // ATTRIBUTES
        $this->AddAttributes($Attributes);
        $this->AddAttribute('id', $Key);

        $this->AddAttributes($ListAttributes, 'list');
        $this->AddAttributes($ContentAttributes, 'content');

        $this->AddAttributes([
            'role'      => 'tablist'
        ], 'list');

        // PROPERTIES
        $this->AddProperties($Properties);
        $this->AddProperties($ListProperties, 'list');
        $this->AddProperties($ContentProperties, 'content');

        $items = '';
        foreach($Tabs as $tab) {
            if ($tab instanceof SexyTab) {
                if ($items != '') {
                    $items .= "\n";
                }

                $items .= $tab->Draw(false);
            }
        }

        $contents = '';
        foreach($Pages as $page) {
            if ($page instanceof SexyTabPage) {
                if ($contents != '') {
                    $contents .= "\n";
                }

                $contents .= $page->Draw(false);
            }
        }

        parent::__construct(Replace: [
            'TABS'                  => $items,
            'PAGES'                 => $contents,
            'CLASSES'               => $this->DrawClasses(),
            'STYLES'                => $this->DrawStyles(),
            'ATTRIBUTES'            => $this->DrawAttributes(),
            'PROPERTIES'            => $this->DrawProperties(),
            'LISTCLASSES'           => $this->DrawClasses('list'),
            'LISTSTYLES'            => $this->DrawStyles('list'),
            'LISTATTRIBUTES'        => $this->DrawAttributes('list'),
            'LISTPROPERTIES'        => $this->DrawProperties('list'),
            'CONTENTCLASSES'        => $this->DrawClasses('content'),
            'CONTENTSTYLES'         => $this->DrawStyles('content'),
            'CONTENTATTRIBUTES'     => $this->DrawAttributes('content'),
            'CONTENTPROPERTIES'     => $this->DrawProperties('content'),
        ]);
    }
}