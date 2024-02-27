<?php

namespace Intouch\Framework\View;

use Application\BLL\BusinessEnumerations\TipoLayoutFilaEnum;
use Application\BLL\Filters\CustomPdfDatatableFilterDto;
use Application\Resources\AssetManagerFactory;
use Intouch\Framework\Annotation\AnnotationHelper;
use Intouch\Framework\Annotation\Attributes\ViewDisplay;
use Intouch\Framework\Annotation\Attributes\ViewDisplayKey;
use Intouch\Framework\BLL\Filters\CustomExcelSettingsFilterDto;
use Intouch\Framework\BLL\Filters\DataTableSettingsFilterDto;
use Intouch\Framework\Collection\GenericCollection;
use Intouch\Framework\Controllers\BaseController;
use Intouch\Framework\Environment\Ini;
use Intouch\Framework\Environment\IniEnum;
use Intouch\Framework\Environment\Session;
use Intouch\Framework\Environment\RedisDataTable;
use Intouch\Framework\View\DisplayDefinitions\FormRowField;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldCheck;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldContent;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldDate;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldEmpty;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelectDefinition;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSelect;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldText;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldTypeEnum;
use Intouch\Framework\View\DisplayDefinitions\FormRowGroup;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldLabel;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldSubTitle;
use Intouch\Framework\View\DisplayDefinitions\FormRowSection;
use Intouch\Framework\Mensajes\Mensaje;
use Intouch\Framework\View\DisplayDefinitions\Button;
use Intouch\Framework\View\DisplayDefinitions\FormButton;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldFile;
use Intouch\Framework\View\DisplayDefinitions\FormRowFieldHidden;
use Intouch\Framework\View\DisplayDefinitions\JSTable\JSTableCell;
use Intouch\Framework\View\DisplayDefinitions\JSTableButton;
use Intouch\Framework\View\DisplayDefinitions\Tab;
use Intouch\Framework\View\DisplayDefinitions\TableButton;
use Intouch\Framework\View\DisplayDefinitions\TableDropDownButton;
use Intouch\Framework\View\DisplayEvents\Event;
use Intouch\Framework\View\DisplayEvents\FormEvent;
use Intouch\Framework\View\DisplayEvents\SexyPageButtonOnRefreshEvent;
use Intouch\Framework\View\DisplayEvents\TableEvent;
use Intouch\Framework\Widget\ActionButton;
use Intouch\Framework\Widget\ActionButtonDropDown;
use Intouch\Framework\Widget\ActionButtonDropDownChild;
use Intouch\Framework\Widget\Container;
use Intouch\Framework\Widget\Definitions\ActionButton\ButtonStyleEnum;
use Intouch\Framework\Widget\ExcelInput;
use Intouch\Framework\Widget\FaIcon;
use Intouch\Framework\Widget\FaIconText;
use Intouch\Framework\Widget\FileInput;
use Intouch\Framework\Widget\FormGroup;
use Intouch\Framework\Widget\FormGroupColumn;
use Intouch\Framework\Widget\FormGroupColumnContent;
use Intouch\Framework\Widget\FormGroupColumnEmpty;
use Intouch\Framework\Widget\FormGroupColumnLabel;
use Intouch\Framework\Widget\FormGroupRow;
use Intouch\Framework\Widget\FormGroupSeparator;
use Intouch\Framework\Widget\FormGroupStepByStep;
use Intouch\Framework\Widget\GenericWidget;
use Intouch\Framework\Widget\Html;
use Intouch\Framework\Widget\InputCheck;
use Intouch\Framework\Widget\InputColor;
use Intouch\Framework\Widget\InputDate;
use Intouch\Framework\Widget\InputSelect;
use Intouch\Framework\Widget\InputText;
use Intouch\Framework\Widget\InputTextArea;
use Intouch\Framework\Widget\JSTableContent;
use Intouch\Framework\Widget\JSTableScriptFilter;
use Intouch\Framework\Widget\Script;
use Intouch\Framework\Widget\ScriptDirect;
use Intouch\Framework\Widget\SexyTab;
use Intouch\Framework\Widget\SexyTabGroup;
use Intouch\Framework\Widget\SexyTabPage;
use Intouch\Framework\Widget\Table;
use Intouch\Framework\Widget\TableBody;
use Intouch\Framework\Widget\TableColumn;
use Intouch\Framework\Widget\TableHeader;
use Intouch\Framework\Widget\TableHeaderColumn;
use Intouch\Framework\Widget\TableHeaderRow;
use Intouch\Framework\Widget\TableRow;
use Intouch\Framework\Widget\Text;
use PhpOffice\PhpSpreadsheet\Style\Style;
use PhpOffice\PhpSpreadsheet\Writer\Ods\Content;
use ReflectionClass;

class Display {

    private static $Renderer = null;
    private static $Path = "";

    private $_Widgets = [];
    private $_Scripts = [];

    private $_RangeIntervals = [
        1   => "'Hoy': [moment(), moment()]",
        2   => "'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')]",
        3   => "'Ultimos 7 Días': [moment().subtract(6, 'days'), moment()]",
        4   => "'Ultimos 30 Días': [moment().subtract(29, 'days'), moment()]",
        5   => "'Este Mes': [moment().startOf('month'), moment().endOf('month')]",
        6   => "'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]",
        7   => "'Ultimos 6 Meses': [moment().subtract(6, 'months').startOf('month'), moment().subtract(1, 'month').endOf('month')]",
        8   => "'Ultimos 12 Meses': [moment().subtract(12, 'months').startOf('month'), moment().subtract(1, 'month').endOf('month')]",
        9   => "'Este Año': [moment().startOf('year'), moment().endOf('year')]",
        10  => "'Año Anterior' : [ moment().subtract(1, 'year').startOf('year'),   moment().subtract(1, 'year').endOf('year')]",
    ];

    private $_RangeIntervalStartEnd = [
        1   => ["moment()", "moment()"],
        2   => ["moment().subtract(1, 'days')", "moment().subtract(1, 'days')"],
        3   => ["moment().subtract(6, 'days')", "moment()"],
        4   => ["moment().subtract(29, 'days')", "moment()"],
        5   => ["moment().startOf('month')", "moment().endOf('month')"],
        6   => ["moment().subtract(1, 'month').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
        7   => ["moment().subtract(6, 'months').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
        8   => ["moment().subtract(12, 'months').startOf('month')", "moment().subtract(1, 'month').endOf('month')"],
        9   => ["moment().startOf('year')", "moment().endOf('year')"],
        10  => ["moment().subtract(1, 'year').startOf('year')", "moment().subtract(1, 'year').endOf('year')"],
    ];
    
    public static function _($message) {
        return self::GetRenderer()->RenderMensaje($message);
    }

    public function Widgets() {
        return $this->_Widgets;
    }

    public function Scripts() {
        return $this->_Scripts;
    }

    // Imprime una fecha con formato dinámico para cambio de idioma en línea
    public static function DrawDateTime(\DateTime $fecha, $format, $classes = '') {

        $fmt = Mensaje::_($format);
        $fechaDisplay = $fecha->format($fmt);
        $Y = $fecha->format('Y');
        $m = $fecha->format('m');
        $d = $fecha->format('d');
        $H = $fecha->format('H');
        $i = $fecha->format('i');
        $s = $fecha->format('s');

        $html = "<span class='dynamic-date $classes' data-format='$format' data-y='$Y' data-m='$m' data-d='$d' data-h='$H' data-i='$i', data-s='$s'>$fechaDisplay</span>";

        echo $html;
    }

    public static function GetRenderer($path = "", $forceNew = false): BaseController | null {

        if ($forceNew || self::$Renderer == null || $path != self::$Path) {
            self::$Renderer = new BaseController(assetManagerFactory: new AssetManagerFactory(), forcedController: $path);
            self::$Path = $path;
        }

        return self::$Renderer;
    }

    public static function DrawSection($title, $info, $classes = "") {
        echo "
            <div class='info info-section " . $classes . "'>
                <div class='info info-data'><label><strong>" . $info . "</strong></label></div>
                <div class='info info-label'><label>" . $title . "</label></div>
            </div>
        ";
    }

    public static function DrawInputSection($title, $id, $placeholder, $inputType, $inputAtributes = "", $classes = "") {
        echo "
            <div class='info info-section $classes'>
                <div class='info info-label'><label>$title</label></div>
                <div class='info info-data'>
                    <input id='$id' name='$id' type='$inputType' class='form-control' placeholder='$placeholder' $inputAtributes>
                </div>                
            </div>
        ";
    }

    public static function DrawInputTextAreaSection($title, $id, $placeholder, $inputType, $inputAtributes = "", $classes = "") {
        echo "
            <div class='info info-section $classes'>
                <div class='info info-label'><label>$title</label></div>
                <div class='info info-data'>
                    <textarea id='$id' name='$id' type='$inputType' class='form-control' placeholder='$placeholder' $inputAtributes></textarea>
                </div>                
            </div>
        ";
    }

    public static function DrawLabelSection($title, $info, $labelType, $classes = "") {

        echo "
            <div class='info info-section " . $classes . "'>
                <div class='info info-data  label label-" . $labelType . "' >" . $info . "</div>
                <div class='info info-label' ><label>" . $title . "</label></div>
            </div>
        ";
    }

    public static function DrawProgressSection($title, $percentage, $labelType, $classes = "") {

        $progressType = "";

        if ($labelType == "") {
            $labelType = "default";
            $progressType = "info";
        }
        else {
            $progressType = $labelType;
        }

        echo "
        <div class='info info-section " . $classes . "'>
            <div>
                <div class='font-bold m-b-sm label label-" . $labelType . "' style='font-variant-caps: normal;'>
                    $title " . number_format($percentage, 0, '.', ',') . " %
                </div>
            </div>
            <div>
                <div class='progress m-t-xs full progress-small'>
                    <div style='width: " . number_format($percentage, 0, '.', ',') . "%' 
                            aria-valuemax='100' aria-valuemin='0' 
                            aria-valuenow='" . number_format($percentage, 0, '.', ',') . "' 
                            role='progressbar' class=' progress-bar progress-bar-" . $progressType . "'>
                    </div>                                         
                </div>
            </div> 
        </div>
        ";
    }

    public static function DrawProgressBar(
            $progressId,
            $containerStyle = "background-color: #f5f5f5; margin-top: 5px;", 
            $progressStyle = "background-color: transparent;"
        ) {        

        echo "
            <div class='loader-progress-container' style='" . $containerStyle . "'>
                <div class='loader-progress' id='loader-progress-" . $progressId . "' style='" . $progressStyle . "'>
                    <div class='progress full progress-striped progress-small active'>
                        <div style='width: 100%' aria-valuemax='100' aria-valuemin='0' aria-valuenow='100' role='progressbar' class='text-left progress-bar  progress-bar-info'>
                            <div id='loader-progress-bar-" . $progressId . "' class='loader-progress-bar'></div>
                        </div>
                    </div>
                </div>
            </div>
        ";
    }

    public static function DrawPanelLabel($title, $size = 0) {

        if (isset($size) && $size > 0) {
            $font = "font-size: " . $size . "px;";
        }
        else {
            $font = "";
        }

        echo "
            <div class='info info-label'><label style='margin: 2px 0px 0px 0px; font-weight: 600;" . $font . " '>" . $title . "</label></div>
        ";
    }

    public static function DrawInfoLabel($title) {

        echo "
            <div class='info info-label'><label>" . $title . "</label></div>
        ";
    }

    public static function DrawPageTile($title, $subtitle) {
        echo "
            <div>
                <h2 class='font-light m-b-xs'>
                    " . $title ."
                </h2>
            </div>
            <small>" . $subtitle . "</small>
        ";
    }

    public static function DrawInfoBox($content, $closable = false, $show = true) {

        echo "
            <div class='note-box'>
                $content
            </div>
        ";
    }

    public static function DrawInfoIcon($text, $placement = "top", $isHtml = false, $icon="fa-info-circle") {
        echo "<i data-toggle='tooltip' data-placement='$placement' " . (($isHtml) ? "data-html='true'" : "") . "' title='$text' class='fa fa-info-circle info-tooltip' style='color: #ffb606; font-size: 15px;'></i>";
    }

    public static function DrawValueLabel($value, $style = 'default', $disableOnZero = true) {

        if ($disableOnZero && ($value == 0 || $value == '')) {
            echo "<span class='label md label-disabled'>0</span>";
        }
        else {
            echo "<span class='label md label-$style'>$value</span>";
        }
    }

    public static function DrawTabTitle($title = "", $icon = "", $iconsize = 20) {

        if ($title=="") {
            echo "
            <div style='cursor: pointer;'>
                <i class='$icon tab-icon' style='font-size: " . $iconsize . "px;'></i>
            </div>
            ";
        }
        else if ($icon=="") {
            echo "
            <div style='cursor: pointer;'>
                <span>$title</span>
            </div>
            ";
        }
        else {
            echo "
            <div style='cursor: pointer;'>
                <i class='$icon tab-icon' style='font-size: " . $iconsize . "px; position: relative; left: -8px; top: -5px'></i>
                <span style='top: 0px'>$title</span>
            </div>
            ";
        }
    }

    private function AddAnyButton(Button $button) {

        if ($button instanceof FormButton) {
            $type   = 'FormButton';
            $key    = $button->Key;
        }
        else {
            $type   = 'Button';
            $key    = $button->Key;
        }

        $actionButton = new ActionButton(
            Key: $key,
            ButtonStyle: $button->ButtonStyle,
            Child: $button->Child,
            Classes: $button->Classes,
            Styles: $button->Styles,
            Attributes: $button->Attributes,
            Properties: $button->Properties
        );
        
        $this->_Widgets[$button->Key] = $actionButton;

        // Get Scripts
        //       
        // Scripts del evento actual
        //
        $eventScripts = [];
        foreach($button->Events as $event) {
            if ($event instanceof Event) {
                $eventScripts[
                    (new \ReflectionClass($event))->getShortName()
                ] = $event->GetScript(element: $button);
            }
            else if ($event instanceof FormEvent) {
                $eventScripts[
                    (new \ReflectionClass($event))->getShortName()
                ] = $event->GetScript(
                    field           : $button, 
                    object          : null, 
                    formKey         : $button->FormKey, 
                    fields          : [],
                    formGroupRows   : [],
                    rows            : []
                );
            }
        }

        // GLOBAL Scripts
        // Scripts globales del campo, se definen fuera de cualquier evento, dentro del evento principal $(function(){});
        //
        $globalScript = $button->GetGlobalScripts();

        // Asociar los scripts con el boton
        $this->_Scripts[$button->Key] = [
            'Type'    => $type,
            'Scripts' => [
                'Key'    => $button->Key,
                'Events' => $eventScripts,
                'Global' => $globalScript
            ]
        ];
        
    }

    public function AddButton(Button $button) {

        $this->AddAnyButton($button);

    }

    public function AddSexyTabGroup(string $Key, array $Tabs = []) {

        $cantTabs = 0;
        foreach($Tabs as $tab) {
            if ($tab instanceof Tab) {
                $cantTabs++;
            }
        }

        if ($cantTabs <= 5) {
            $porcentaje = 20;
            $primero = 20;            
        }
        else {
            // Calcular el porcentaje
            $porcentaje = round(98/$cantTabs, 0);
            $suma = $porcentaje * $cantTabs;        

            $primero = $porcentaje;
            // if ($suma < 98) {
            //     $primero = $primero + (98-$suma);
            // }
        }

        $sexyTabs  = [];
        $sexyPages = [];
        $buttonScripts = [];
        $tabScripts = [];

        // Crear los WIDGETS
        //
        foreach($Tabs as $idx => $tab) {

            if ($tab instanceof Tab) {
                
                if ($idx == 0) {
                    $width = $primero;
                }
                else {
                    $width = $porcentaje;
                }

                array_push($sexyTabs, new SexyTab(
                    Key: $tab->TabKey,
                    Order: $idx+1,
                    Icon: $tab->Icon,
                    Title: $tab->Title,
                    Styles: [
                        ['width', $width . '% !important']
                    ],
                    Attributes: [
                        ['data-contentsourceurifunction', $tab->ContentSourceUriFunction]                        
                    ]
                ));

                //array_push($tabScripts, $this->GenerateTabScript(Tab: $tab, Order: $idx+1));

                $buttons = [];
                foreach($tab->PageButtons as $idxBtn => $button) {
                    if ($button instanceof Button) {
                        array_push($buttons, $button);
                    }
                }

                $pageButtons = [];
                foreach($buttons as $idxBtn => $button) {

                    // Sólo el primer botón ignora margen izquierdo
                    if ($idxBtn > 0) {
                        array_push($button->Styles, ['margin-left', '8px']);
                    }
                    
                    // Crear el widget de este boton
                    array_push($pageButtons, new ActionButton(
                        Key: $button->Key,
                        Child: $button->Child,
                        ButtonStyle: $button->ButtonStyle,
                        Classes: $button->Classes,
                        Styles: $button->Styles,
                        Attributes: $button->Attributes,
                        Properties: $button->Properties,
                    ));

                    // Obtener el script de cada evento asociado al botón
                    foreach($button->Events as $event) {
                        if ($event instanceof Event) {
                            $eventScript = $event->GetScript($button);
                            array_push($buttonScripts, $eventScript);
                        }
                    }
                    
                }

                // Agregar el botón REFRESCAR
                if ($tab->ContentSourceUriFunction != '') {
                    $button = new Button(
                        Key: 'BtnRefrescar' . $tab->TabKey,
                        ButtonStyle: ButtonStyleEnum::BUTTON_DEFAULT,
                        Child: new FaIconText('fa-sync-alt', 'Refrescar'),
                        Styles: [
                            ['margin-left', '8px']
                        ],
                        Events: [
                            new SexyPageButtonOnRefreshEvent(
                                ContentSourceUriFunction: $tab->ContentSourceUriFunction,
                                TabGroupKey: $Key)
                        ]
                    );

                    array_push($pageButtons, new ActionButton(
                        Key: $button->Key,
                        Child: $button->Child,
                        ButtonStyle: $button->ButtonStyle,
                        Classes: $button->Classes,
                        Styles: $button->Styles,
                        Attributes: $button->Attributes,
                        Properties: $button->Properties,
                    ));

                    foreach($button->Events as $event) {
                        if ($event instanceof Event) {
                            $eventScript = $event->GetScript($button);
                            array_push($buttonScripts, $eventScript);
                        }
                    }
                }

                $dataAttributes = [];
                foreach($tab->TabData as $key => $data) {
                    $dataAttributes['data-' . $key] = $data;
                }

                array_push($sexyPages, new SexyTabPage(
                    Key: $tab->TabKey,
                    Order: $idx+1,
                    Content: '',
                    Attributes: $dataAttributes,
                    ContentClasses: ['sexytab-page-content'],
                    TopButtons: $pageButtons,
                ));
            }
        }

        // Crear el grupo
        $grupo = new SexyTabGroup(
            Key: $Key,
            Tabs: $sexyTabs,
            Pages: $sexyPages,
        );

        // Agregar el grupoTab a la colección de widgets
        $this->_Widgets[$Key] = $grupo;

        // Agregar los scripts de este grupoTab a la colección de scripts
        $this->_Scripts[$Key] = [
            'Type' => 'SexyTabGroup',
            'Scripts' => [
                'TabScripts'    => $tabScripts,
                'ButtonScripts' => $buttonScripts,
                'GroupScripts'  => [$this->GenerateSexyTabsOnLoadScript(Key: $Key)],
            ]
        ];

        return true;
    }

    /**
     * ObjectToForm: genera un formulario para mostrar campos seleccionados desde un objeto
     * 
     * @param Object: el objeto de origen
     * @param Rows: arreglo de campos del objeto origen que serán incluidos en el formulario. Cada elemento del arreglo constituirá una fila del formulario
     *                y consiste a su vez en un arreglo con los campos que irán en dicha fila
     * 
     */
    public function AddFormFromObject(
                                        string  $formKey, 
                                        object  $object,
                                        array   $rowGroups, 
                                        string  $keyFieldName, 
                                        bool    $fillData = false,
                                        bool    $StepByStep = false
    ): bool {

        // Revisar si este formulario ya existe
        if (isset($this->_Widgets[$formKey])) {
            throw new \Exception('El elemento: ' . $formKey . ' ya ha sido definido previamente');
            return false;
        }

        // // Obtener la metadata del objeto desde sus atributos
        // $metadata = $this->GetObjectMetadata($object);

        // // Autocompletar campo llave, si no se especificó, y si lo encontramos en la metadata
        // if ($keyFieldName == '') {
        //     $keyFieldName = $metadata->KeyFieldName;
        // }

        // obtener todas las filas
        $rows = [];
        $groups = [];
        foreach($rowGroups as $rowGroup) {

            if ($rowGroup instanceof FormRowGroup) {

                $groups[$rowGroup->Key] = $rowGroup;

                foreach($rowGroup->Rows as $row) {
                    array_push($rows, $row);
                }
            }
        }
        
        // // Validar el objeto y la propiedades solicitadas en $Rows
        // if (!$this->ValidateObject($object, $rows, $keyFieldName)) {
        //     throw new \Exception('El objeto origen para el elemento: ' . $formKey . ' no está bien configurado');
        //     return false;
        // }

        $keyFieldValue = (property_exists($object,$keyFieldName)) ? $object->$keyFieldName : null;

        $dataRows = [];
        $first = true;
        $firstGlobal = true;

        // Recuperar los links entre listas <SELECT>
        // y obtener demás la metadata del campo
        //
        // foreach ($rows as $row) {
            
        //     foreach($row as $field) {

        //         // Autocompletar el campo
        //         if ( !($field instanceof FormRowFieldEmpty)) {
        //             $field = $this->AutocompleteField(
        //                 field: $field, 
        //                 //metadata: $metadata,
        //                 formKey: $formKey
        //             );
        //         }
        //     }
        // }

        // Recorrer los grupos para generar los widgets
        //
        $fields         = [];
        $formGroups     = []; 
        foreach($rowGroups as $rowGroup) {

            $formGroups[$rowGroup->Key] = new \stdClass();
            $formGroups[$rowGroup->Key]->Group = $rowGroup;
            $formGroups[$rowGroup->Key]->Rows = [];

            // Ver si tiene titulo para agregar una fila
            if (isset($rowGroup->Title) && $rowGroup->Title != '') {

                $classes = [];
                if (!$firstGlobal) {
                    $classes = ['separator'];
                }

                $dataRow = new FormGroupSeparator(
                    Title: $rowGroup->Title,
                    Classes: $classes
                );
                array_push($dataRows, $dataRow);
                array_push($formGroups[$rowGroup->Key]->Rows, $dataRow);

                $firstGlobal = false;
            }

            foreach($rowGroup->Rows as $row) {
                
                $dataColumns = [];
                
                // Convertir los componentes de las columnas en Widgets
                //
                foreach($row as $field) {

                    if (isset($field->PropertyName) && $field->PropertyName != '') {
                        $field->Id = $formKey . '-' . $field->PropertyName;
                    }
                    else if (isset($field->Key) && $field->Key != '') {
                        $field->Id = $formKey . '-' . $field->Key;
                    }

                    if ($field instanceof FormRowFieldLabel) {

                        $column = $this->PropertyToLabel(
                            field: $field,
                            formKey: $formKey,
                            object: $object,
                            layout: $rowGroup->Layout
                        );
                    }
                    else if ($field instanceof FormRowFieldSubTitle) {
                        $column = $this->PropertyToSubTitle(
                            field: $field,
                            formKey: $formKey,
                            layout: $rowGroup->Layout
                        );
                    }
                    else if ($field instanceof FormRowFieldContent) {
                        $column = $this->PropertyToContent(
                            field: $field,
                            layout: $rowGroup->Layout
                        );
                    }
                    else if ($field instanceof FormRowFieldEmpty) {
                        $column = $this->PropertyToEmpty(
                            field: $field,
                            layout: $rowGroup->Layout
                        );
                    }
                    else if ($field instanceof FormRowFieldHidden) {
                        $column = $this->PropertyToHidden(
                            field: $field,
                            formKey: $formKey,
                            object: $object
                        );
                    }
                    else {

                        // Validar el campo
                        if (!$this->ValidateObjectField($object, $field)) {
                            throw new \Exception('El campo ' . $field->PropertyName . ' tiene problemas de validación');
                            return false;
                        }

                        // // Autocompletar el campo con datos del objeto
                        // $field = $this->AutocompleteField(
                        //     field: $field, 
                        //     //metadata: $metadata,
                        //     formKey: $formKey
                        // );

                        // Agregar el campo al listado, para procesar posteriormente los scripts
                        $fields[$field->PropertyName] = $field;

                        // Buscar el valor de la propiedad, en el objeto de origen
                        $propertyName = $field->PropertyName;

                        if (isset($object->$propertyName)) {
                            $propertyValue = $object->$propertyName;
                        }
                        else {
                            $propertyValue = '';
                        }

                        // Asignar el valor al campo
                        $field->Value = $propertyValue;

                        $column = null;

                        if ($field instanceof FormRowFieldCheck) {
                            $column = $this->PropertyToCheck(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }
                        else if ($field instanceof FormRowFieldText && (
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_TEXT ||
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_PASSWORD ||
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_EMAIL
                        )) {
                            $column = $this->PropertyToTextInput(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }
                        else if ($field instanceof FormRowFieldText &&  $field->FieldType == FormRowFieldTypeEnum::INPUT_COLOR  ) 
                        {
                            $column = $this->PropertytoColorInput(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }
                        else if ($field instanceof FormRowFieldText && (
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_TEXTAREA
                        )) {
                            $column = $this->PropertyToTextArea(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }
                        else if ($field instanceof FormRowFieldText && (
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_FILE ||
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_FILE_PROFILE_PICTURE
                        )) {
                            $column = $this->PropertyToFileInput(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }else if ( $field instanceof FormRowFieldText &&  $field->FieldType == FormRowFieldTypeEnum::INPUT_FILE_EXCEL ) {
                            $column = $this->PropertyToFileInputExcel(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );
                        }
                        else if ($field instanceof FormRowFieldDate && (
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_DATE ||
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_DATETIME ||
                            $field->FieldType == FormRowFieldTypeEnum::INPUT_TIME
                        )) {

                            $column = $this->PropertyToDateInput(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                keyFieldValue: $keyFieldValue,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );

                        }
                        else if ($field instanceof FormRowFieldDate && $field->FieldType == FormRowFieldTypeEnum::INPUT_DATERANGE) {

                            $column = $this->PropertyToDateRangeInput(
                                field: $field, 
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                keyFieldValue: $keyFieldValue,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );

                        }
                        else if ($field instanceof FormRowFieldSelect) {

                            $column = $this->PropertyToSelectInput(
                                field: $field,
                                propertyValue: $propertyValue, 
                                keyFieldName: $keyFieldName,
                                keyFieldValue: $keyFieldValue,
                                formKey: $formKey,
                                fillData: $fillData,
                                layout: $rowGroup->Layout
                            );

                        }

                    }

                    if (isset($column)) {
                        array_push($dataColumns, $column);
                    }
                }

                if (count($dataColumns) > 0) {
                    // Agregar la fila al formulario
                    $dataRow = new FormGroupRow(
                        Columns: $dataColumns,
                        First: $first,
                        Layout: $rowGroup->Layout
                    );

                    array_push($dataRows, $dataRow);
                    array_push($formGroups[$rowGroup->Key]->Rows, $dataRow);

                    $first = false;
                    $firstGlobal = false;
                }

            }
        }

        // Recorrer los grupos para generar los script
        //
        $scripts = [];

        foreach($rowGroups as $rowGroup) {
            foreach($rowGroup->Rows as $row) {
                foreach($row as $field) {
                    
                    if ($field instanceof FormRowField) {
                        
                        // Scripts del campo actual
                        //
                        $eventScripts = [];
                        foreach($field->Events as $event) {

                            if ($event instanceof FormEvent) {
                                $eventScripts[
                                    (new \ReflectionClass($event))->getShortName()
                                ] = $event->GetScript($field, $object, $fields, $groups, $rows, $formKey);
                            }
                        }                        

                        // Scripts especiales de fields
                        if ($field->FieldType == FormRowFieldTypeEnum::INPUT_FILE) {
                            $eventScripts['FileInputInit'] = $this->GenerateFileInputScript($field);
                        }
                         // Scripts especiales de fields
                         if ($field->FieldType == FormRowFieldTypeEnum::INPUT_FILE_PROFILE_PICTURE) {
                            $eventScripts['FileInputInit'] = $this->GenerateOneFileInputScript($field);
                        }

                        // SCrips para FileInput para Excels
                        if ($field->FieldType == FormRowFieldTypeEnum::INPUT_FILE_EXCEL) {
                            $eventScripts['FileInputInit'] = $this->GenerateFileInputScriptExcel($field);
                        }

                        //GENERACION DEL DATERANGE
                        if ($field->FieldType == FormRowFieldTypeEnum::INPUT_DATERANGE) {
                            $eventScripts['DateRangeInit'] = $this->GenerateDateRangeOnKeyUpScript($field->PropertyName, $formKey);
                        }

                        // GLOBAL Scripts
                        // Scripts globales del campo, se definen fuera de cualquier evento, dentro del evento principal $(function(){});
                        //
                        $globalScript = $field->GetGlobalScripts();

                        // Asociar los scripts con el campo
                        $scripts[$field->PropertyName] = [
                            'Events' => $eventScripts,
                            'Global' => $globalScript
                        ];
                    }
                }
            }
        }

        // Crear el formulario
        $form = null;

        $htmlStepByStepUL       =  "";
        $NumeroEtapa            = 0;
        if (count($dataRows) > 0) {

            $containerGroups = [];
            
            $maxData    =   count($formGroups) ?: 1; 
            foreach($formGroups as $groupKey => $groupData) {
                $NumeroEtapa++;

                $groupClasses = [];
                if (!$groupData->Group->Visible) {
                    array_push($groupClasses, 'hide');
                }

                $groupRows = $groupData->Rows;

                if ($StepByStep == true){

                    $icon       =   $formGroups[ $groupKey]->Group->Icon        ?:  "fa-users";
                    $tooltip    =   $formGroups[ $groupKey]->Group->Tooltip     ?:  "Etapa N° " . $NumeroEtapa;

                    $Active     =   $NumeroEtapa == 1 ? "active" : "";

                    if ($NumeroEtapa == 1){
                        array_push($groupClasses, $Active);
                    }
                    

                    $htmlStepByStepUL .= '   
                    <li class="nav-item '.$Active.'">
                        <a href="#'. $groupKey .'" class="nav-link" data-toggle="tab">
                            <div class="step-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="" data-bs-original-title="'.$tooltip.'">
                                <i class="fa '.$icon.'"></i>
                            </div>
                        </a>
                    </li>'; 

                    $containerGroups[$groupKey] = new FormGroupStepByStep(
                        Key             :   $groupKey,
                        Rows            :   $groupRows,
                        Classes         :   $groupClasses
               
                    );



                }else{
                    $containerGroups[$groupKey] = new FormGroup(
                        Key: $groupKey,
                        Rows: $groupRows,
                        Classes: $groupClasses
                    );
                }
                

            }

            if ($StepByStep == true){

                /* GENERAMOS EL HTML */
                $html   =   new Html( '<ul class="twitter-bs-wizard-nav nav nav-pills nav-justified">'.$htmlStepByStepUL.'</ul>' ) ;

                $form = new Container(
                    Key         :   'twitter-bs-wizard',
                    Classes     :   ['twitter-bs-wizard'],
                    Children    : [
                        $html,
                        new Container(
                            Classes:['tab-content','twitter-bs-wizard-tab-content'],
                            Children:$containerGroups
                        )
                    ]
                );


            }else{
                $form = new Container( 
                    Children    : $containerGroups
                );
            }


        }

        // Agregar el formulario a la colección de widgets
        $this->_Widgets[$formKey] = $form;

        // Agregar los scripts de este formulario a la colección de scripts
        $this->_Scripts[$formKey] = [
            'Type' => 'Form',
            'Scripts' => [
                'FieldScripts' => $scripts,
                'FormScripts'  => [$this->GenerateFormOnLoadScript($formKey)] 
            ]
        ];

        return true;
    }

    private function GetObjectMetadata(object $object) {

        $annotations = AnnotationHelper::FromObject($object);
        $meta = new \stdClass();

        // Buscar la llave
        $llave = $annotations->FindAttributeProperty(ViewDisplayKey::class);
        $meta->KeyFieldName = (isset($llave) && isset($llave->propertyName)) ? $llave->propertyName : '';
        
        $attributes = $annotations->GetAttributeProperties(ViewDisplay::class);

        $props = [];

        if (isset($attributes)) {
            foreach($attributes as $attr) {
                $prop = new \stdClass();
                $prop->PropertyName = $attr->propertyName;
                $prop->FieldType = isset($attr->attribute->FieldType) ? $attr->attribute->FieldType : '';
                $prop->Label = isset($attr->attribute->Label) ? $attr->attribute->Label : '';
                $prop->Required = isset($attr->attribute->Required) ? $attr->attribute->Required : null;
                $prop->DisplayFunction = isset($attr->attribute->DisplayFunction) ? $attr->attribute->DisplayFunction : '';

                $props[$attr->propertyName] = $prop;
            }
        }

        $meta->Properties = $props;

        return $meta;
    }

    // private function AutocompleteField(object $field, $metadata, string $formKey) {

    //     if ( (isset($field->PropertyName) && $field->PropertyName != '') || ( isset($field->Key) && $field->Key != '') ) {
    //         $id = (($formKey != '') ? $formKey . '-' : '') . ( (isset($field->PropertyName) && $field->PropertyName != '') ? $field->PropertyName : $field->Key );
    //     }
    //     else {
    //         $id = '';
    //     }

    //     $field->Id = $id;

    //     if (!isset($field->FieldType) || $field->FieldType == '') {

    //         if (isset($field->PropertyName) && isset($metadata->Properties[$field->PropertyName])) {
    //             $field->FieldType = $metadata->Properties[$field->PropertyName]->FieldType;
    //         }
    //     }
        
    //     if (!isset($field->Label) || $field->Label == '') {
    //         if (isset($field->PropertyName) && isset($metadata->Properties[$field->PropertyName])) {
    //             $field->Label = $metadata->Properties[$field->PropertyName]->Label;
    //         }
    //     }

    //     if (!isset($field->Required)) {
    //         $field->Required = false;
    //         if (isset($field->PropertyName) && isset($metadata->Properties[$field->PropertyName])) {
    //             if (isset($metadata->Properties[$field->PropertyName]->Required))
    //                 $field->Required = $metadata->Properties[$field->PropertyName]->Required;                            
    //         }
    //     }

    //     return $field;
    // }

    private function GenerateFileInputScript(FormRowField $field) {

        return "
        $('#" . $field->Id . "').fileinput({
            language: 'es',
            showUpload: false,
            showCancel: false,
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            uploadExtraData : {
                'proceso' :'Evidencia',
                'tipo' : 'foto',
                'idcompromiso': $('#SubirEvidenciaIdCompromiso').val()
            },
            uploadUrl : '/api/core/files',
            uploadAsync: false
        }).on('filebatchuploadsuccess', function (event, data) {
            if (data) {
                if (data.response) {
                    if (data.response.ErrorCode == 0) {
                        if (data.response.Result) {

                            var evidenciaSvc = new EvidenciaSvc();
                            evidenciaSvc.GuardarEvidencias(data.response.Result,$('#formSubirArchivos-Descripcion').val());

                        }
                        else {

                            Toast.fire({
                                icon: 'error',
                                html: 'Ocurrió un error al subir los archivos'
                            });
                        }
                    }
                    else {
                        Toast.fire({
                            icon: 'error',
                            html: data.response.ErrorMessage
                        });
                    }
                }
            }
        }).on('', function () {
    
        });";
    }

    private function GenerateOneFileInputScript(FormRowField $field) {

        
        return "
        console.log('generado');
        $('#" . $field->Id . "').fileinput({
            language: 'es',
            showUpload: false,
            showCancel: false,
            maxFileCount: 1,
            allowedFileExtensions: ['jpg', 'jpeg', 'png'],
            uploadExtraData     : {
                'proceso'       :'PictureProfile',
                'tipo'          : 'foto',
                'idUsuario'  : $('#IdUsuario').val()
            },
            uploadUrl : '/api/core/files',
            uploadAsync: false
        }).on('filebatchuploadsuccess', function (event, data) {
            if (data) {
                if (data.response) {
                    if (data.response.ErrorCode == 0) {
                        if (data.response.Result) {

                            GuardarProfilePicture(data.response.Result);

                        }
                        else {

                            Toast.fire({
                                icon: 'error',
                                html: 'Ocurrió un error al subir los archivos'
                            });
                        }
                    }
                    else {
                        Toast.fire({
                            icon: 'error',
                            html: data.response.ErrorMessage
                        });
                    }
                }
            }
        }).on('', function () {
    
        });";
    }
    private function GenerateFileInputScriptExcel(FormRowField $field) {

        return "
        $('#" . $field->Id . "').fileinput({
            language: 'es',
            showUpload: false,
            showCancel: false,
            allowedFileExtensions: ['xlsx'],         
            uploadAsync: false
        });";
    }

    private function GenerateGlobalPageScript() {
        return "    
    // SCRIPT GLOBAL
    //
    if ( typeof Page_OnLoad === 'function') {
        Page_OnLoad();
    }        
        ";
    }

    private function GenerateFormOnLoadScript($formKey) {

        return "
    
    // FORMULARIO: " . $formKey . "
    //
    // EVENTO: ONLOAD
    // Llamar a la funcion de carga inicial del formulario actual
    //
    if ( typeof " . $formKey . "_OnLoad === 'function') {
        var myForm      = null;
        var myElements  = null;

        if (typeof ReadForm === 'function') {
            myForm = ReadForm('" . $formKey . "');
        }

        if (typeof ReadFormElements === 'function') {
            myElements = ReadFormElements('" . $formKey . "');
        }

        eventInfo = {
            FormData     : myForm,
            FormElements : myElements
        }

        if ( typeof " . $formKey . "_OnLoad === 'function' ) {
            " . $formKey . "_OnLoad(eventInfo);
        }
    }
        ";

    }

    /*
    public function GenerateTabScript(Tab $Tab, $Order) {

        if ($Tab->ContentSourceUriFunction != '') {

            $number = '00' . $Order;
            $number = substr($number, -3);
    
            $key = $number . '-' . $Tab->TabKey;

            return "
    // TAB: " . $Tab->TabKey . "
    // OnClick Event
    $('ul.activas-ul > li).on('click', function () {

        if ( typeof " . $Tab->ContentSourceUriFunction . " === 'function') {
            if (typeof RefreshContent === 'function') {

                // Obtener el SexyTabPage de este boton
                var pageElement = $('div.ui-tabs-panel#" . $key . "');
        
                // Obtener el contenedor para el contenido del page
                var pageContentElement = $(pageElement).find('div.sexytab-page-content');
        
                // Obtener los DATA asociados al page
                var pageData = $(pageElement).data();
                
                var uri = " . $Tab->ContentSourceUriFunction . "({
                    PageElement: pageElement,
                    PageData: pageData, 
                    PageContentElement: pageContentElement
                });
    
                // Refrescar el contenido
                RefreshContent({
                    ContentElement: pageContentElement, 
                    ContentSourceUri: uri, 
                    OnSuccessCallback: function() {

                        if ( typeof " . $Tab->TabKey . "_OnRefresh === 'function') {
                            " . $Tab->TabKey . "_OnRefresh({
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

                        if ( typeof " . $Tab->TabKey . "_OnRefresh === 'function') {
                            " . $Tab->TabKey . "_OnRefresh({
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
    });
            ";
        }
        else {
            return '';
        }
    }
    */

    public function GenerateDateRangeOnKeyUpScript($field, string $formKey) {

        $key = $field->PropertyName;

        $ranges = "";
        // Si no hay intervalos, dibujamos los intervalos estandar
        //
        $start = "";
        $end   = "";

        if (!isset($field->RangeIntervals) || !is_array($field->RangeIntervals) || count($field->RangeIntervals) == 0) {
            $ranges .= "
                'Hoy': [moment(), moment()],
                'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Ultimos 7 Días': [moment().subtract(6, 'days'), moment()],
                'Ultimos 30 Días': [moment().subtract(29, 'days'), moment()],
                'Este Mes': [moment().startOf('month'), moment().endOf('month')],
                'Mes Anterior': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                'Este Año': [moment().startOf('year'), moment().endOf('year')]\n";

            $start = $this->_RangeIntervalStartEnd[1][0];
            $end = $this->_RangeIntervalStartEnd[1][1];
        }
        else {
            $intervals = "";

            foreach($field->RangeIntervals as $id) {
                if ($intervals != "") {
                    $intervals .= ",\n";
                }
                else {
                    $start = $this->_RangeIntervalStartEnd[$id][0];
                    $end = $this->_RangeIntervalStartEnd[$id][1];
                }
                $intervals .= "\t\t" . $this->_RangeIntervals[$id];
            }

            $ranges .= $intervals;
        }

        $script = "

        // Establecer los parametros iniciales del DateRange
        //
        var start = $start;
        var end = $end;
    
        $('#$formKey-$key').daterangepicker({
            startDate: start,
            endDate: end,
            alwaysShowCalendars: true,
            showCustomRangeLabel: false,
            autoApply: true,
            autoUpdateInput: false,
            ranges: {\n";

        $script .= $ranges;
        
        $script .= "
            }
        }, function (start, end, label) {
    
            if ( typeof " . $formKey . $key . "_OnChange === 'function' ) {
                " . $formKey . $key . "_OnChange(start, end);
            }
        });
        
        // Inicializar llamada
        //
        if ( typeof " . $formKey . $key . "_OnChange === 'function' ) {
            " . $formKey . $key . "_OnChange(start, end);
        }        
        ";

        return $script;

    }

    public function GenerateSexyTabsOnLoadScript(string $Key) {

        
        return "
    
    // SEXYTABS: " . $Key . "
    //
    // EVENTO: ONLOAD
    // Configurar el grupo de tabs
    //

    // Obtener el SexyTab
    var sexyTabGroup = $('#" . $Key . "');

    // Obtener los DATA asociados al tabgroup
    var sexyTabGroupData = $(sexyTabGroup).data();

    if ( typeof " . $Key . "_OnLoad === 'function') {
        " . $Key . "_OnLoad({
            TabGroupElement: sexyTabGroup,
            TabGroupData: sexyTabGroupData
        });
    }

    // Inicializar el grupo de tabs
    $('#" . $Key . "').tabs({ 
        show: { effect: 'slide', direction: 'left', duration: 200, easing: 'easeOutBack' } ,
        hide: { effect: 'slide', direction: 'right', duration: 200, easing: 'easeInQuad' } 
    });

    $('ul.activas-ul > li').on('click', function () {

        var tabId = $(this).attr('aria-controls');

        // Obtener el SexyTabPage
        var pageElement = $('.ui-tabs-panel[id=\"' + tabId + '\"]');
        
        // Obtener el contenedor para el contenido del page
        var pageContentElement = $(pageElement).find('div.sexytab-page-content');

        // Si el contenido ya existe, no hacer nada        
        if ( $(pageContentElement).html() &&  $(pageContentElement).html().trim() != '')
            return;

        // Obtener los DATA asociados al page
        var pageData = $(pageElement).data();

        // Obtener el URI para refrescar el contenido
        var contentSourceUriFunction = $(this).data('contentsourceurifunction');
        var refreshFunction = $(this).data('refreshfunction');

        if ( typeof window[contentSourceUriFunction] === 'function' ) {

            if (typeof RefreshContent === 'function') {
                
                var uri = window[contentSourceUriFunction]({
                    PageElement: pageElement,
                    PageData: pageData, 
                    PageContentElement: pageContentElement
                });
    
                // Refrescar el contenido
                RefreshContent({
                    ContentElement: pageContentElement, 
                    ContentSourceUri: uri, 
                    OnSuccessCallback: function() {

                        if ( typeof window[refreshFunction] === 'function') {
                            window[refreshFunction]({
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

                        if ( typeof window[refreshFunction] === 'function') {
                            window[refreshFunction]({
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
    });

    // Activar el primer elemento del SexyTab
    //
    $('ul.activas-ul > li').first().click();
            ";
        
    }

    public function DrawScripts(bool $echo = true, bool $addLoadEvent = true) {

        // Agregar el script global
        if ($addLoadEvent) {
            $script = $this->GenerateGlobalPageScript();
        }
        else {
            $script = "";
        }

        foreach($this->_Scripts as $key => $def) {
            
            switch($def['Type']) {

                case 'Form':
                    // FormScripts
                    $script .= $this->DrawFormScripts($def['Scripts']);
                    $script .= $this->DrawFormFieldScripts($def['Scripts']);
                    break;

                case 'Table':
                    $script .= $this->DrawTableButtonsScripts($def['Scripts'], $key);
                    $script .= $this->DrawTableScripts($def['Scripts']);
                    break;

                case 'Button':
                case 'FormButton':
                    $script .= $this->DrawButtonScripts($def['Scripts']);
                    break;

                case 'SexyTabGroup':
                    $script .= $this->DrawSexyTabsScripts($def['Scripts']);
                    $script .= $this->DrawSexyTabGroupScripts($def['Scripts']);
                    $script .= $this->DrawSexyTabsButtonScripts($def['Scripts']);                    
                    break;                
            }
        }

        if ($addLoadEvent) {
            $scriptEvents = new Script(
                [$script]
            );
        }
        else {
            $scriptEvents = new ScriptDirect(
                [$script]
            );
        }

        return $scriptEvents->Draw($echo);
    }

    private function DrawTableScripts($scripts) {

        $script = '';

        foreach($scripts['TableScripts'] as $tableScript) {
            $script .= $tableScript;
        }

        return $script;
    }

    private function DrawTableButtonsScripts($scripts, $tableKey) {


        $script = "
    // ENLAZAR EVENTOS DE BOTON DE LA TABLA: $tableKey
    //
    function " . $tableKey . "_BindButtons() {
            
        ";

        foreach($scripts['ButtonScripts'] as $buttonKey => $buttonScripts) {

            $eventScripts = $buttonScripts['Events'];
            $globalScripts = $buttonScripts['Global'];
            
            if (count($eventScripts) > 0 || count($globalScripts) > 0) {
                // Agregar comentarios del campo
                $script = _att($script, "
        // BOTON DE TABLA: " . $buttonKey . "
        //", "\n");
            }

            if (count($eventScripts) > 0) {
                $script = _att($script, "
        // EVENTOS DEL BOTON
        //", "\n");

                foreach($eventScripts as $eventScript) {
                    $script = _att($script, $eventScript, "\n");
                }
            }

            if (count($globalScripts) > 0) {        
                $script = _att($script, "
        // SCRIPTS GLOBALES DEL BOTON
        //", "\n");

                foreach($globalScripts as $globalScript) {
                    $script = _att($script, $globalScript, "\n");
                } 
            }

            $script = _att($script, "
        // DESASOCIAR EVENTOS EN REFRESCOS DE LA TABLA
        $('." . $buttonScripts['OnClickClass'] . ".new').removeClass('new');
            ", "\n");

        }

        $script .= "\n
    }\n";

        return $script;

    }

    private function DrawButtonScripts($scripts) {

        $script = "";

        $eventScripts   = $scripts['Events'];
        $globalScripts  = $scripts['Global'];
        $buttonKey      = $scripts['Key'];
        
        if (count($eventScripts) > 0 || count($globalScripts) > 0) {
            // Agregar comentarios del campo
            $script = _att($script, "
    // BOTON : " . $buttonKey . "
    //", "\n");
        }

        if (count($eventScripts) > 0) {
            $script = _att($script, "
    // EVENTOS DEL BOTON
    //", "\n");

            foreach($eventScripts as $eventScript) {
                $script = _att($script, $eventScript, "\n");
            }                
        }

        if (count($globalScripts) > 0) {        
            $script = _att($script, "
    // SCRIPTS GLOBALES DEL BOTON
    //", "\n");

            foreach($globalScripts as $globalScript) {
                $script = _att($script, $globalScript, "\n");
            } 
        }

        return $script;

    }

    private function DrawFormFieldScripts($scripts) {

        /*
            $scripts[$field->PropertyName] = [
                'Events' => $eventScripts,
                'Global' => $globalScript
            ];
        */
        $script = '';

        foreach($scripts['FieldScripts'] as $propertyName => $fieldScripts) {

            $eventScripts = $fieldScripts['Events'];
            $globalScripts = $fieldScripts['Global'];
            
            if (count($eventScripts) > 0 || count($globalScripts) > 0) {
                // Agregar comentarios del campo
                $script = _att($script, "
        // CAMPO DE FORMULARIO: " . $propertyName . "
        //
                ", "\n");
            }

            if (count($eventScripts) > 0) {
                $script = _att($script, "
        // EVENTOS DEL CAMPO
        //", "\n");

                foreach($eventScripts as $eventScript) {
                    $script = _att($script, $eventScript, "\n");
                }                
            }

            if (count($globalScripts) > 0) {        
                $script = _att($script, "
        // SCRIPTS GLOBALES DEL CAMPO
        //", "\n");

                foreach($globalScripts as $globalScript) {
                    $script = _att($script, $globalScript, "\n");
                } 
            }
        }

        return $script;
    }

    private function DrawFormScripts($scripts) {
        /*
        'Scripts' => [
            'FieldScripts' => $scripts,
            'FormScripts'  => [$this->GenerateFormOnLoadScript($formKey)] 
        ]
        */

        $script = "";

        foreach($scripts['FormScripts'] as $formScript) {
            if ($script != "") $script .= "\n";

            $script .= $formScript;
        }

        return $script;
    }

    private function DrawSexyTabsScripts($scripts) {

        $script = "";

        foreach($scripts['TabScripts'] as $formScript) {
            if ($script != "") $script .= "\n";

            $script .= $formScript;
        }

        return $script;
    }

    private function DrawSexyTabGroupScripts($scripts) {

        $script = "";

        foreach($scripts['GroupScripts'] as $formScript) {
            if ($script != "") $script .= "\n";

            $script .= $formScript;
        }

        return $script;
    }

    private function DrawSexyTabsButtonScripts($scripts) {
        $script = "";

        foreach($scripts['ButtonScripts'] as $formScript) {
            if ($script != "") $script .= "\n";

            $script .= $formScript;
        }

        return $script;
    }

    /**
     * Genera el script principal de la pagina con todos los scripts generados
     */
    public function GenerateScripts(array $scripts, array $globalScripts) {

        $scripts = array_merge($scripts, $globalScripts);

        $scriptEvents = new Script(
            $scripts
        );

        return $scriptEvents->Draw(false);
    }

    private function PropertyToContent(FormRowField $field, int $layout = TipoLayoutFilaEnum::BOOTSTRAP) {


        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }

        $result = new FormGroupColumnContent(            
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Classes: $groupclasses,
            Child: $field->Content,
            Layout:         $layout,
            Colspan: $field->Colspan
        );

        return $result;

    }

    private function PropertyToEmpty(FormRowField $field,
                                        int $layout = TipoLayoutFilaEnum::BOOTSTRAP) {

        $result = new FormGroupColumnEmpty(
            Layout:         $layout,
            Colspan: $field->Colspan
        );

        return $result;

    }

    private function PropertyToLabel(FormRowFieldLabel $field, $formKey, $object = null, int $layout = TipoLayoutFilaEnum::BOOTSTRAP) {

        $classes = [];
        $styles = [];
        if ($field->LabelStyle != '') {
            $classes = ['label', $field->LabelStyle];
            $styles = [
                ['height', '33px'],
                ['font-size', '110%'],
                ['padding', '9px 0px 8px 0px'],
                ['display', 'block']
            ];
        }

        if (isset($field->DisplayFunction)) {
            $func = $field->DisplayFunction;
            $content = $func($object);

            if ($content instanceof GenericWidget) {
                $content = $content->Draw(false);
            }
        }
        else {
            $content = $field->Content;
        }

        if (isset($field->Id) && $field->Id != '') {            
            $key = $field->Key;
            $hiddenValue = isset($object->$key) ? $object->$key : '';
            $key = $field->Id;
        }
        else if (isset($field->Key) && $field->Key != '') {
            $key = $field->Key;
            $hiddenValue = isset($object->$key) ? $object->$key : '';
        }
        else {
            $hiddenValue = null;
        }

        $groupclasses = [];
        if (isset($field->GroupClass) && $field->GroupClass != "") {
            array_push($groupclasses, $field->GroupClass);
        }

        $result = new FormGroupColumnLabel(
            Key: (isset($field->GroupKey) && $field->GroupKey != '') ? $field->GroupKey : $key,
            HiddenValue:    $hiddenValue,
            Content:        $content,
            Label:          $field->Label,
            Layout:         $layout,
            Colspan:        $field->Colspan,
            Classes:        $groupclasses,
            ContentClasses: $classes,
            ContentStyles:  $styles,
            HiddenAttributes: [ 
                ['data-form-id', $formKey] 
            ],
            HiddenClasses: ['form-input']
        );

        return $result;

    }

    private function PropertyToSubTitle(FormRowFieldSubTitle $field, $formKey, int $layout = TipoLayoutFilaEnum::BOOTSTRAP) {

        $classes = ['modal-group-subtitle-container'];
        $styles = [];
        $titleClasses = ['separator modal-group-subtitle'];
        $titleStyles = [];

        if (isset($field->Classes) && count($field->Classes) > 0) {            
            foreach($field->Classes as $class) {
                $classes[] = $class;
            }
        }
        if (isset($field->Styles) && count($field->Styles) > 0) {
            foreach($field->Styles as $style) {
                $styles[] = $style;
            }
        }

        if (isset($field->TitleClasses) && count($field->TitleClasses) > 0) {            
            foreach($field->TitleClasses as $class) {
                $titleClasses[] = $class;
            }
        }
        if (isset($field->TitleStyles) && count($field->TitleStyles) > 0) {
            foreach($field->TitleStyles as $style) {
                $titleStyles[] = $style;
            }
        }

        $content = new Container(
            Classes: $classes,
            Styles : $styles,
            Children: [
                new Text(
                    Content: $field->Title,
                    Classes: $titleClasses,
                    Styles:  $titleStyles
                )
            ]
        );

        $result = new FormGroupColumnContent(
            Child: $content,
            Layout:         $layout,
            Colspan: $field->Colspan
        );

        return $result;

    }

    private function PropertyToCheck(
        FormRowField $field, $propertyValue,
        string $keyFieldName, string $formKey,
        $keyFieldValue = '', bool $fillData = false,
        int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $checked = false;
        if ($fillData && isset($field->Checked) && $field->Checked) {
            $checked = true;
        }
        else if ($fillData && $propertyValue > 0) {
            $checked = true;
        }

        array_push($field->Attributes, ['data-form-id', $formKey]);

        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }       

        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Classes: $groupclasses,
            Input: new InputCheck(
                Key: $field->Id, 
                Label: $field->Title,
                Checked: $checked,
                Styles: [
                    ['padding-top', '4px;']
                ],
                Attributes: [
                    ['data-' . $keyFieldName, $keyFieldValue]
                ],
                InputClasses: ['form-input'],
                InputAttributes: $field->Attributes
            ),
        );

        return $result;
    }

    // private static function PropertyToSeparator(FormRowField $field) {

    //     $result = new FormGroupSeparator(
    //         Title: $field->Label
    //     );

    //     return $result;

    // }

    private function PropertyToTextInput(
                                FormRowField $field, $propertyValue, 
                                string $keyFieldName, string $formKey,
                                $keyFieldValue = '', bool $fillData = false,
                                int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }

        $InputAttributes     =   [
            ['data-' . $keyFieldName, $keyFieldValue],
            ['data-form-id', $formKey]
        ];

        if (!empty($field->Attributes)){
            $InputAttributes = array_merge($InputAttributes,$field->Attributes );
        }
        
        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Classes: $groupclasses,
            Disabled: $field->Disabled,
            Input: new InputText(
                Key: $field->Id,
                Type: $field->FieldType,
                Value: ($fillData) ? $propertyValue : '',
                Required: $field->Required,
                Disabled: $field->Disabled,
                Placeholder: $field->Placeholder,
                Attributes: $InputAttributes,
                Classes: ['form-input'],
            ),
        );

        return $result;
    }

    private function PropertyToHidden(FormRowFieldHidden $field, $formKey, $object = null) {

        if (isset($field->Value) && $field->Value != '') {
            $hiddenValue = $field->Value;
        }
        else if (isset($field->PropertyName) && $field->PropertyName != '') {
            $key = $field->PropertyName;
            $hiddenValue = $object->$key;
        }
        else {
            $hiddenValue = '';
        }

        $result = new InputText(
            Key: $field->PropertyName,
            Type: FormRowFieldTypeEnum::INPUT_HIDDEN,
            Value: $hiddenValue,
            Required: false,
            Placeholder: '',
            Attributes: [
                ['data-form-id', $formKey]
            ],
            Classes: ['form-input'],
        );

        return $result;

    }

    private function PropertyToFileInput(
        FormRowField $field, $propertyValue, 
        string $keyFieldName, string $formKey,
        $keyFieldValue = '', bool $fillData = false,
        ?int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $result = new FormGroupColumn(
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Input: new FileInput(
                Key: $field->Id,
                Multiple: $field->Multiple,
                Attributes: [
                    ['data-' . $keyFieldName, $keyFieldValue],
                    ['data-form-id', $formKey]
                ],
                Classes: ['form-input'],
            ),
        );

        return $result; 
    }

    private function PropertyToFileInputExcel(
        FormRowField $field, $propertyValue, 
        string $keyFieldName, string $formKey,
        $keyFieldValue = '', bool $fillData = false,
        ?int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $result = new FormGroupColumn(
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Input: new ExcelInput(
                Key: $field->Id,
                Multiple: $field->Multiple,
                Attributes: [
                    ['data-' . $keyFieldName, $keyFieldValue],
                    ['data-form-id', $formKey]
                ],
                Classes: ['form-input'],
            ),
        );

        return $result; 
    }

    private function PropertytoColorInput(
        FormRowField $field, $propertyValue, 
        string $keyFieldName, string $formKey,
        $keyFieldValue = '', bool $fillData = false,
        int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null 
    {

        $groupclasses = [];
        if (isset($field->GroupClass)) {
        array_push($groupclasses, $field->GroupClass);
        }

        $result = new FormGroupColumn(
        Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
        Label: $field->Label,
        Layout:         $layout,
        Colspan: $field->Colspan,
        Required: $field->Required,
        Classes: $groupclasses,
        Disabled: $field->Disabled,
        Input: new InputColor(
            Key: $field->Id,
            Type: $field->FieldType,
            Value: ($fillData) ? $propertyValue : '',
            Required: $field->Required,
            Disabled: $field->Disabled,
            Placeholder: $field->Placeholder,
            Attributes: [
            ['data-' . $keyFieldName, $keyFieldValue],
            ['data-form-id', $formKey]
            ],
            Classes: ['form-input'],
            ),
        );

        return $result; 
    }

    private function PropertyToTextArea(
                                FormRowField $field, $propertyValue, 
                                string $keyFieldName, string $formKey,
                                $keyFieldValue = '', bool $fillData = false,
                                int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }

        $InputAttributes     =   [
            ['data-' . $keyFieldName, $keyFieldValue],
            ['data-form-id', $formKey]
        ];
        if (!empty($field->Attributes)){
            $InputAttributes = array_merge($InputAttributes,$field->Attributes );
        }

        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Disabled: $field->Disabled, 
            Classes: $groupclasses,
            Input: new InputTextArea(
                Key: $field->Id,
                Type: $field->FieldType,
                Value: ($fillData) ? $propertyValue : '',
                Required: $field->Required,
                Disabled: $field->Disabled,
                Placeholder: $field->Placeholder,
                Lines: $field->Lines,
                Resize: $field->Resize,
                Attributes: $InputAttributes,
                Classes: ['form-input'],
            ),
        );

        return $result;
    }

    private function PropertyToDateRangeInput(
        FormRowField $field, $propertyValue, 
        string $keyFieldName, string $formKey,
        $keyFieldValue = '', bool $fillData = false,
        int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        $value = '';

        $groupclasses = [];
        if (isset($field->GroupClass)) {
        array_push($groupclasses, $field->GroupClass);
        }

        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Classes: $groupclasses,
            Input: new InputText(
                Key: $field->Id,
                Type: $field->FieldType,
                Value: ($fillData) ? $value : '',
                Required: $field->Required,
                Placeholder: $field->Placeholder,
            ),
        );

        return $result;
}

    private function PropertyToDateInput(
                                FormRowField $field, $propertyValue, 
                                string $keyFieldName, string $formKey,
                                $keyFieldValue = '', bool $fillData = false,
                                int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        // Select icon and value
        $icon = null;
        $value = '';
        switch($field->FieldType) {
            case "date":
                $icon = new FaIcon(
                    'fa-calendar'
                );
                $value = (new \Datetime($propertyValue))->format('d-m-Y');
                break;
            case "datetime":
                $icon = new FaIcon(
                    'fa-calendar'
                );
                $value = (new \Datetime($propertyValue))->format('d-m-Y H:i');
                break;
            case "time":
                $icon = new FaIcon(
                    'fa-clock'
                );
                $value = (new \Datetime($propertyValue))->format('H:i');
                break;
        }

        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }

        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,
            Classes: $groupclasses,
            Input: new InputDate(
                Key: $field->Id,
                Icon: $icon,
                Type: $field->FieldType,
                Value: ($fillData) ? $value : '',
                Required: $field->Required,
                Disabled: $field->Disabled,
                Placeholder: $field->Placeholder,
                DateAttributes: [
                    ['data-' . $keyFieldName, $keyFieldValue],
                    ['data-form-id', $formKey]
                ],
                DateClasses: ['form-input']
            ),
        );

        return $result;
    }

    private function PropertyToSelectInput(
                                FormRowField $field, $propertyValue, 
                                string $keyFieldName, string $formKey,
                                $keyFieldValue = '',
                                $fillData = false,
                                array $attributes = [],
                                int $layout = TipoLayoutFilaEnum::BOOTSTRAP): FormGroupColumn | null {

        // Search o sin search
        $classes = ['form-input new'];
        array_push($classes,  ($field->SelectDefinition->DisplaySearch && $field->SelectDefinition->MultipleSelection == false) ? 'search-on' : 'search-off');

        if($field->SelectDefinition->MultipleSelection == true){
            $classes = ['form-input new js-example-basic-multiple'];
        }

        if (is_array($field->SelectDefinition->Values)) {
            $values = $field->SelectDefinition->Values;
        }
        else if ($field->SelectDefinition->Values instanceof GenericCollection) {
            $values = $field->SelectDefinition->Values->Values;
        }

        $groupclasses = [];
        if (isset($field->GroupClass)) {
            array_push($groupclasses, $field->GroupClass);
        }

        $valueDecoration =  ($field->SelectDefinition->MultipleSelection == true) ? "[]" : "";

        

        // Description
        $result = new FormGroupColumn(
            Key: (isset($field->GroupKey)) ? $field->GroupKey : '',
            Label: $field->Label,
            Layout:         $layout,
            Colspan: $field->Colspan,
            Required: $field->Required,    
            Classes: $groupclasses,
            Attributes: $attributes,        
            Input: new InputSelect(
                Key: $field->Id,
                ValueField: $field->SelectDefinition->Key . $valueDecoration,
                DescriptionField: $field->SelectDefinition->Description,
                DescriptionFunction: $field->SelectDefinition->DescriptionFunction,
                Values: $values,
                SelectedValue: ($fillData) ? $propertyValue : $field->SelectDefinition->SelectedValue,
                Required: $field->Required,
                Disabled: $field->Disabled,
                Classes: $classes,                
                Attributes: [
                    ['data-' . $keyFieldName, $keyFieldValue],
                    ['data-form-id', $formKey]
                ],
                OptionAttributeNames: $field->SelectDefinition->OptionAttributeNames,
                MultipleSelection: $field->SelectDefinition->MultipleSelection
            ),
        );

        return $result;
    }

    // Validacion del objeto y las propiedades solicitadas
    private function ValidateObject(object $object, array $Rows, $keyFieldName): bool {
               
        // Validar la llave
        if (!isset($object->$keyFieldName)) {
            return false;
        }

        $props = get_object_vars($object);

        // Verificar todas las propiedades solicitadas, que existan en el objeto de origen
        foreach($Rows as $row) {

            if (is_array($row)) {
                foreach($row as $field) {

                    if ($field instanceof FormRowFieldLabel || $field instanceof FormRowFieldEmpty) {
                        return true;
                    }

                    // Verificar que el objeto contenga la propiedad
                    if (!($field instanceof FormRowField || $field instanceof FormRowFieldLabel)) {
                        return false;
                    }
                    else if (!array_key_exists($field->PropertyName, $props) 
                                    && $field->FieldType != FormRowFieldTypeEnum::EMPTY) {
                        return false;
                    }
                }
            }
            else if (!($row instanceof FormRowSection)) {
                return false;
            }
        }

        return true;
    }

    // Validacion del campo según tipo de campo
    private function ValidateObjectField(object $Object, FormRowField $field): bool {

        if (!isset($field->FieldType) || $field->FieldType == '') {            
            throw new \Exception('El campo ' . $field->PropertyName . ' no tiene definido el tipo');
            return false;
        }

        // los SELECT, necesitan el campo 3, de extras (lista con la que se llena el select)
        if (strtolower($field->FieldType) == FormRowFieldTypeEnum::INPUT_SELECT) {
            
            if (!isset($field->SelectDefinition)) {
                throw new \Exception('Falta la especificacion SelectDefinition');
                return false;
            }

            if (!($field->SelectDefinition instanceof FormRowFieldSelectDefinition)) {
                throw new \Exception('Falta la especificacion SelectDefinition');
                return false;
            }

            $wrongType = true;
            if (isset($field->SelectDefinition->Values)) {
                if ($field->SelectDefinition->Values instanceof GenericCollection) {
                    $wrongType = false;
                }
                else if (is_array($field->SelectDefinition->Values)) {
                    $wrongType = false;
                }
            }

            if ($wrongType) {
                throw new \Exception('La colección para el SELECT viene en null o es del tipo incorrecto');
                return false;
            }

            //$values = $field->SelectDefinition->Values;
            // La lista de valores debe ser un arreglo de objetos
            // if (!is_array($values)) {
            //     throw new \Exception('La colección para el SELECT debe ser un arreglo de objetos');
            //     return false;
            // }            
        }

        return true;        
    } 

    public function AddTableFromCollection(
                                string                          $tableKey,
                                string                          $RowIdFieldName,
                                array                           $CellDefinitions,
                                ?GenericCollection              $Data               = null,
                                array                           $RowAttributeNames  = [],
                                array                           $Buttons            = [],
                                bool                            $TablaSimple        = false,
                                bool                            $CustomPdf          = false,
                                bool                            $JSRenderTheTable   = false,
                                ?DataTableSettingsFilterDto     $CustomDataTable    = null,
                                ?CustomExcelSettingsFilterDto   $customExcel        = null
    ): bool | null {

        // Obtener la metadata
        $metadata = [];
        if (isset($Data) && $Data->Count() > 0) {
            $pivot = $Data->First();
            $metadata = $this->GetObjectMetadata($pivot);
        }

        // CREAR LOS WIDGETS
        //

        // Crear el encabezado
        $header = $this->ObjectToTableHeader(
            CellDefinitions         : $CellDefinitions, 
            Metadata                : $metadata,
            IncludeActionColumn     : (count($Buttons) > 0),
            btnNumber               : count($Buttons) ?: 0,
            JSTable                 : $JSRenderTheTable
        );

        // verificamos si es el servidor o el front quien debe renderizar la tabla
        if($JSRenderTheTable == true) {
            
            // si es el front generamos la plantilla para el render
            $body = $this->ObjectsToJSTableBody(
                RowIdFieldName: $RowIdFieldName,
                CellDefinitions: $CellDefinitions,
                Values: isset($Data) ? $Data->Values : [],
                RowAttributeNames: $RowAttributeNames,
                Buttons: $Buttons,
                tableKey: $tableKey,
                CustomDataTable: $CustomDataTable,
                customExcel     :   $customExcel

            );

            $tabla = new Table(
                Header: $header,
                Body: $body->Body,
                Key: $tableKey,
                JSRenderTheTable : $JSRenderTheTable
            );
    
            $this->_Widgets[$tableKey] = $tabla;

            // retornamos para asegurnarnos que no generemos ningun script demas
            return true;

        }else{
            
            // Crear el cuerpo de la tabla
            $body = $this->ObjectsToTableBody(
                RowIdFieldName: $RowIdFieldName,
                CellDefinitions: $CellDefinitions,
                Values: isset($Data) ? $Data->Values : [],
                RowAttributeNames: $RowAttributeNames,
                Buttons: $Buttons
            );
        }
       

        $tabla = new Table(
            Header: $header,
            Body: $body,
            Key: $tableKey
        );

        $this->_Widgets[$tableKey] = $tabla;


        // aca generamos una diferencia entre el flujo de la tabla normal y el nuevo flujos

       

        // CREAR LOS SCRIPTS
        //
        // Recorrer los botones para generar los scripts
        $scripts = [];
        foreach($Buttons as $button) {

            //Instancia de Botones Normales
            if ($button instanceof TableButton) {

                // Scripts del boton actual
                //
                if ($button->OnClickClass != '') {
                    // Scripts del boton actual
                    $eventScripts = [];
                    foreach($button->Events as $event) {
                        if ($event instanceof TableEvent) {
                            $eventScripts[
                                (new \ReflectionClass($event))->getShortName()
                            ] = $event->GetScript($button, null, $tableKey);
                        }
                    }
                }

                // GLOBAL Scripts
                $globalScripts = $button->GetGlobalScripts();

                // Asociar los scripts con el boton
                $scripts[$button->Key] = [
                    'OnClickClass' => $button->OnClickClass,
                    'Events' => $eventScripts,
                    'Global' => $globalScripts
                ];
            }elseif($button instanceof TableDropDownButton){

                foreach($button->Children as $item){
                    // Scripts del boton actual
                    //
                    if ($item->OnClickClass != '') {
                        // Scripts del boton actual
                        $eventScripts = [];
                        foreach($item->Events as $event) {
                            if ($event instanceof TableEvent) {
                                $eventScripts[
                                    (new \ReflectionClass($event))->getShortName()
                                ] = $event->GetScript($item, null, $tableKey);
                            }
                        }
                    }

                    // GLOBAL Scripts
                    $globalScripts = $item->GetGlobalScripts();

                    // Asociar los scripts con el boton
                    $scripts[$item->Key] = [
                        'OnClickClass' => $item->OnClickClass,
                        'Events' => $eventScripts,
                        'Global' => $globalScripts
                    ];
                }


            }

            // Instancia Dropdown
        }
       
        if (!$TablaSimple) {
            if(!empty($CustomDataTable) AND $CustomDataTable instanceof DataTableSettingsFilterDto){
                $z = 'null';
                if(!empty($customExcel) && $customExcel instanceof CustomExcelSettingsFilterDto ) {
                    $z = json_encode($customExcel);                
                } 

                $x = json_encode($CustomDataTable);
                $tablaInit = "var " . $tableKey . " = $('#" . $tableKey . "').TablaEstandar($x,null,$z);";
                $x = null;
                unset($x);
            }else{

                $x = 'null';
                
                if(!empty($customExcel) && $customExcel instanceof CustomExcelSettingsFilterDto ) {
                
                    $x = json_encode($customExcel);
                
                } 
                
                $CustomDataTable        = (!empty($CustomDataTable))    ? $CustomDataTable      : 'null';
                $CustomPdf              = (!empty($CustomPdf))          ? $CustomPdf            : 'null';

                $tablaInit = "var " . $tableKey . " = $('#" . $tableKey . "').TablaEstandar($CustomPdf,null,$x);";
                
                unset($x);
                
                
 
            }

            
        }
        else {
            $tablaInit = "";
        }

            $tableScript = "
    // TABLA: " . $tableKey . "
    // Init
    try {

        " . $tablaInit . "

        // Llamada inicial
        if ( typeof " . $tableKey . "_BindButtons === 'function' ) {
            " . $tableKey . "_BindButtons();
        }

        // Enlazar botones al cambiar de página o realizar búsquedas
        $('#" . $tableKey . "').on('draw.dt', function() {
            " . $tableKey . "_BindButtons();
        });
        
    }
    catch (err) {}
    ";
        

        $this->_Scripts[$tableKey] = [
            'Type'      => 'Table',
            'Scripts'   => [
                'TableScripts'  => [$tableScript],
                'ButtonScripts' => $scripts
            ]
        ];

        return true;
    }


        private function ObjectsToJSTableBody(
                                string $RowIdFieldName, 
                                string $tableKey,
                                array  $CellDefinitions, 
                                array  $Values,
                                array  $RowAttributeNames = [], 
                                array  $Buttons = [],
                                ?DataTableSettingsFilterDto $CustomDataTable = null,
                                ?CustomExcelSettingsFilterDto $customExcel = null
                                )  {
        

        
        // Recorrer los campos del objeto
        $rows               = [];  
        $CellTemplateList   = "";   
        $TemplateRenderizado= false;
        $ListaPropiedades   = [];   
        $ToJson             = [];
        $rc = new ReflectionClass(get_class($this));
        $ruta = dirname($rc->getFileName());
        $switchFunction     = "";
        $CellPropertiesList = [];

        if(is_array($RowAttributeNames) AND !empty($RowAttributeNames)){
            $ListaPropiedades = $RowAttributeNames;
        }
        //Llamamos al usuario que inicio sesión
        $userdata = Session::Instance()->usuario;

        $residsname  = $tableKey."_cell";
        RedisDataTable::Instance()->$residsname = array();
        $Cell_Property_cache = [];
        
        $DtoName = null;

        $rolesList = [];

        if(!empty($userdata->Perfil->Roles)){

            foreach($userdata->Perfil->Roles as $rolename => $roledata){

                $rolesList[] = $rolename;

            }

        }

        foreach($Values as $index => $element) { 
            $columns = [];   

            if($DtoName == null){
                $DtoName = get_class($element);
            }
            // --------------------------------------------------------------------------------------------------
            // generamos el template
            // --------------------------------------------------------------------------------------------------
            if($TemplateRenderizado == false){
                foreach($CellDefinitions as $key => $cell) {

                    if (!isset($cell) || !$cell instanceof JSTableCell ) {
                        continue; 
                    }
                    $Cell_Property_cache[] = (object) [
                        "property"  => $cell->PropertyName,
                        "label"  => $cell->Label
                    ];
                    
    
                    // -----------------------------------------------------------------------------------------------------------------
                    // EN PRIMER LUGAR GENERAMOS LA LISTA DE PROPIEDADES A USAR DEL OBJETO
                    // -----------------------------------------------------------------------------------------------------------------
                  

                    if(isset($element->{$cell->PropertyName}) && !in_array($cell->PropertyName,$CellPropertiesList)){ 
                        $CellPropertiesList[] = $cell->PropertyName;
                    }
                    // Validamos la propiedad principal de la celda
                    if(isset($element->{$cell->PropertyName}) && !in_array($cell->PropertyName,$ListaPropiedades)){                    
                        $ListaPropiedades[] = $cell->PropertyName;
                    }
                    // validamos las propiedades secundarias
                    if(!empty($cell->PropertyList) && is_array($cell->PropertyList) ){
                        foreach($cell->PropertyList as $PropertyItem) {
                            if(     property_exists($element,$PropertyItem) && !in_array($PropertyItem,$ListaPropiedades)){                    
                                $ListaPropiedades[] = $PropertyItem;        
                            }
                        }
                    }
                    
                    // -----------------------------------------------------------------------------------------------------------------
                    // GENERAMOS LA PLANTILLA DE RENDERIZADO DE ESTA CELDA
                    // -----------------------------------------------------------------------------------------------------------------
    
                    // -- Validamos si existe o no existe una definición del WidgetTemplate
                    $HtmlTemplate = "";
                    $RenderScripts = "";
                    if(isset($cell->WidgetFunction)){
    
                        // en caso de que existe el widgetFunction (Que Genera un WidgetTemplate) lo renderizamos
                        $func = $cell->WidgetFunction;
    
                        // Generamos el template y enviamos información del elemento y los roles del usuario que inicio sesión
                        $HtmlTemplate =  $func($element,$userdata->Roles);
    
                        // verificamos que no se encuentre vacio
                        if(empty($HtmlTemplate)){
                            $HtmlTemplate = "[[$cell->PropertyName]]";
                        }
                        // verificamos si vuelve un widget
                        if($HtmlTemplate instanceof GenericWidget){
    
                            // antes de renderizar el widget busco dependencias al renderizado (atributos extras);
                            $JSTableContents = [];
                            if(!$HtmlTemplate instanceof JSTableContent){
                                
                                if(isset( $HtmlTemplate->Children )  && !empty( $HtmlTemplate->Children )){
    
                                    $JSTableContents = $this->JSWidgetChildSearch($HtmlTemplate);
    
                                }
    
                            }else{
                                $JSTableContents[] = $HtmlTemplate;
                            }
                            
                            //---------------------------------------------------------------------------
                            // GENERAMOS LOS SCRIPTS QUE VAN A REMPLAZAR LOS CONTENIDOS EN LA VISTA
                            //---------------------------------------------------------------------------
                            // en caso de ser un widget lo renderizamos
    
                           
    
                            if(!empty($JSTableContents)){
    
                                foreach($JSTableContents as $JSTableItem){
    
                                    if($JSTableItem instanceof JSTableContent){
                                        
                                        // traemos el nombre de la propiedad
                                        $Propiedad = $JSTableItem->PropertyName;
    
                                        // agregamos a la lista de propiedades que necesitamos
                                        if(!in_array($Propiedad,$ListaPropiedades)){
                                            $ListaPropiedades[] = $Propiedad;
                                        }
    
                                        // verificamos si existe una función en JS que deba filtrar esta información
                                        if(!empty( $JSTableItem->JSFilterName)){
    
                                            $JSFunct = trim($JSTableItem->JSFilterName)."_$key";
                                            
    
                                            $RenderScripts .= 
                                            " 
                                                // Obtenemos los valores
                                                var val_$Propiedad = (element?.$Propiedad) ? element.$Propiedad : '';    
                                                // si es una funcion la llamamos                                        
                                                if( filter_$JSFunct ) {
                                                    val_$Propiedad = filter_$JSFunct(element,roles);
                                                }
    
                                                // renderizamos el contenido
                                                htmltemplate = replaceAttribute( htmltemplate, '$Propiedad', val_$Propiedad );
                                            ";
    
                                        }else{
                                            $RenderScripts .= 
                                            " 
                                                // Obtenemos los valores
                                                var val_$Propiedad = (element?.$Propiedad) ? element.$Propiedad : '';    
                                                // si es una funcion la llamamos   
                                                
                                                // renderizamos el contenido
                                                htmltemplate = replaceAttribute( htmltemplate, '$Propiedad', val_$Propiedad );
                                                
                                            ";
                                        }
                                    }
    
                                }
    
    
                            }
                            //---------------------------------------------------------------------------
                            // SE RENDERIZA EL TEMPLATE
                            //---------------------------------------------------------------------------
                            $HtmlTemplate = $HtmlTemplate->Draw(false);
                        }
                        
    
                    }else{ 
                         

                        $Propiedad = $cell->PropertyName;

                        $HtmlTemplate = "[[$Propiedad]]";
    
                        if(!in_array($Propiedad,$ListaPropiedades)){
                            $ListaPropiedades[] = $Propiedad;
                        }
    
                        $RenderScripts .= 
                        " 
                            // Obtenemos los valores
                            var val_$Propiedad = (element?.$Propiedad) ? element.$Propiedad : '';    
                            // si es una funcion la llamamos   
                            
                            // renderizamos el contenido
                            htmltemplate = replaceAttribute( htmltemplate, '$Propiedad', val_$Propiedad );
                        ";
                    }  
                    // -----------------------------------------------------------------------------------------------------------------
                    // GENERAMOS LA LISTA DE SCRIPTS QUE NECESITA ESTA CELDA
                    // -----------------------------------------------------------------------------------------------------------------
                    $scriptList = "";
                    
                    if(!empty( $cell->JSDataFilter )){

                        foreach($cell->JSDataFilter as $scripts){

                            if($scripts instanceof JSTableScriptFilter){

                                $scriptname = trim($scripts->FunctionName)."_$key";

                                $func       = $scripts->Script[0];

                                $scriptList .=                                 
                                "
                                    
                                    // -----------------------------------------
                                    // FILTER NAME : $scriptname;
                                    // -----------------------------------------
                                    var filter_$scriptname = (element,roles) => {
                                        $func
                                    }
                                    // -----------------------------------------

                                ";


                            }

                        }

                    }
    
                    // -----------------------------------------------------------------------------------------------------------------
                    // Una vez generado el template... se agrega a la lista de plantillas
                    // -----------------------------------------------------------------------------------------------------------------
                    $funcCellName = $tableKey."_cell_".$key;
                 
                    
                    $ScriptTemplate = file_get_contents($ruta.'/DisplayDefinitions/JSTable/Template/TableCellTemplate.js');
                    $ScriptTemplate = str_replace('[[__function__name__]]'          ,trim($funcCellName)    ,$ScriptTemplate);
                    $ScriptTemplate = str_replace('[[PRIMARY_PROPERTY]]'            ,$cell->PropertyName    ,$ScriptTemplate);
                    $ScriptTemplate = str_replace('[[__PK__]]'                      ,$element->$RowIdFieldName ?: 0 ,$ScriptTemplate);
                    
                    
                    // ---- REMPLAZAMOS LAS PROPIEDADES FIJAS PARA CONSTRUIR EL JS
                    $ScriptTemplate = trim($ScriptTemplate);
                    $ScriptTemplate = str_replace('[[HTML_TEMPLATE]]'           ,trim($HtmlTemplate)    ,$ScriptTemplate);
                    $ScriptTemplate = str_replace('[[_TEMPLATE_REPLACES_]]'     ,trim($RenderScripts)   ,$ScriptTemplate);
                    $ScriptTemplate = str_replace('[[_SCRIPTS_FILTERS_]]'       ,trim($scriptList)      ,$ScriptTemplate);

                    $claseses = "";
                    if(!empty($cell->BodyClasses)){
                        $claseses = $cell->BodyClasses[0];
                    }
                    $ScriptTemplate = str_replace('"[[__CLASSESS__]]"'       ,trim($claseses)      ,$ScriptTemplate);

                    $__styles = json_encode([]);
                    
                    if($cell->BodyStyles){
                        $__styles = json_encode($cell->BodyStyles);
                    }

                    $ScriptTemplate = str_replace('"[[JSON_STYLE]]"'       ,$__styles ,$ScriptTemplate);
                    
                    
               
    
                    // -----------------------------------------------------------------------------------------------------------------
                    // GENERAMOS EL ARREGLO
                    // -----------------------------------------------------------------------------------------------------------------

                    

                    $switchFunction .= 
                    "case $key : 
                        template = window.".$tableKey."_controller.$funcCellName(element,roles);
                    break;";

                    $CellTemplateList .= $ScriptTemplate;                  
                  
                } 

               
            }

            $TemplateRenderizado = true;
            // --------------------------------------------------------------------------------------------------
            // UNA VEZ FINALIZADO EL CICLO DE GENERADO DEL TEMPLATE, SE VALIDA EL OBJETO JSON A ENVIAR A LA VISTA
            // --------------------------------------------------------------------------------------------------
            foreach($element as $propertie => $__item){
                if(in_array($propertie,$ListaPropiedades)){
                    $ToJson[$index][$propertie] = $__item;
                }
            } 
        }


        RedisDataTable::Instance()->$residsname = $Cell_Property_cache;

        
       

        $body = new TableBody(
            Rows: []
        );

        $BaseScript = file_get_contents($ruta.'/DisplayDefinitions/JSTable/Template/TableBodyTemplate.js');
        

        RedisDataTable::Instance()->$tableKey = new GenericCollection(
            DtoName : $DtoName,
            Values  : $ToJson
        );

        $BaseScript = str_replace('"[[__JSON__DATA__]]"'        ,json_encode($ToJson)           ,$BaseScript);
        $BaseScript = str_replace('"[[tabkey___]]"'             ,$tableKey                      ,$BaseScript);
        $BaseScript = str_replace('[[__switchFunction__]]'      ,$switchFunction                ,$BaseScript);
        $BaseScript = str_replace('[[CELL_CONFIG_TEMPLATE]]'    ,trim($CellTemplateList)        ,$BaseScript);
        $BaseScript = str_replace('[[TABLE_KEY]]'               ,trim($tableKey)        ,$BaseScript);

        $FileTitle      = "Compromisos";
        $FileModule     = "Compromisos";

        if(!empty($customExcel) && $customExcel instanceof CustomExcelSettingsFilterDto ) {
                
            $x = json_encode($customExcel);
            $CustomButtons  = [ 
                "{
                    text: `<i class=' bx bx-download '></i> Exportar`,
                    className: 'btn btn-link waves-effect waves-light no-decoration',
                    action: function ( e, dt, node, config ) {
        
                        __customtableExcel($x);
                    }
                }",
          
            ] ;
        
        }else{
            $CustomButtons  =   [];
            $HideButton     =   "'1'";
        }

        $HideButton     = 'false';

       

        $xhrCustom = ""; 
        $groupButtons = 'true';
        if(!empty($CustomDataTable) and $CustomDataTable instanceof DataTableSettingsFilterDto){

            if($CustomDataTable->HideDefaultButtons == true){
                $CustomButtons = [];
            }   

            if(!empty($CustomDataTable->TituloPdf)){
                $FileTitle  = $CustomDataTable->TituloPdf;
            }
            if(!empty($CustomDataTable->Modulo)){
                $FileModule = $CustomDataTable->Modulo;
            }

            $HideButton = "'".$CustomDataTable->HideAllButtons."'";

            if(is_array($CustomDataTable->JSCustomButton) && !empty($CustomDataTable->JSCustomButton)){
                $CustomButtons = array_merge($CustomButtons,$CustomDataTable->JSCustomButton);
            }   

            if($CustomDataTable->DrawTableCallback != null && !empty($CustomDataTable->DrawTableCallback)) {
                $xhrCustom = implode(' ', $CustomDataTable->DrawTableCallback);
            }

            if($CustomDataTable->GroupedButtons == false){
                $groupButtons = 'false';
            }



           
        }

        $CustomButtons  = implode(',',$CustomButtons); 
        $BaseScript     = str_replace('[[CUSTOMBUTTONS]]'           ,trim($CustomButtons)           ,$BaseScript);
        $BaseScript     = str_replace('[[XHRCALLBACK]]'             ,trim($xhrCustom)               ,$BaseScript);
        $BaseScript     = str_replace('[[__GROUPED__STATUS__]]'      ,trim($groupButtons)            ,$BaseScript);
        

        $RowAttributeList = "";

        foreach($RowAttributeNames as $atr){

            $attributename = strtolower($atr);
            $RowAttributeList .= "   \n 
            if(rowTarget.data('". $attributename ."')){
                // existe el dato no se hace nada
            }else{
                // en caso que no exista se genera la propiedad
                rowTarget.data('". $attributename ."', target.data.".$atr."); \n
                rowTarget.attr('". $attributename ."', target.data.".$atr."); \n
            } ";
            
            

        }

        // --------------------------------------------------------------------------------------------------
        // SE GENERA EL TEMPLATE PARA EL RENDERIZADO DE LOS BOTONES
        // --------------------------------------------------------------------------------------------------
        $BtnTemplateList    = "";
        $BtnScriptList      = "";
        $BtnBindList        = "";
        $EventToTemplate    = "";
        if(!empty($Buttons)){
            $globalScripts_btn = [];
            $btnScriptList = [];
            $BaseScript = str_replace('[[HASBUTTON]]','true',$BaseScript) ;

         
            $BaseBtnTemplate    = file_get_contents($ruta.'/DisplayDefinitions/JSTable/Template/TableButtonCellTemplate.js');
            foreach($Buttons as $btn){ 

                $stop = 1;
                // aca dependiendo de la instancia del btn se genera un nuevos script
                $ActualBtnTemplate = $BaseBtnTemplate;
                //generamos un script dependiendo del btn
             
                if($btn instanceof JSTableButton){
                    $eventScripts   = [];
                 
                    $ScriptList = "";
                    if ($btn->OnClickClass != '') {
                        // Scripts del boton actual
                    
                        foreach($btn->Events as $event) {
                            if ($event instanceof TableEvent) {
                                $events_    =   $event->GetScript($btn, null, $tableKey);
                                $eventScripts[
                                    (new \ReflectionClass($event))->getShortName()
                                ] = $events_;
                            }
                        }
                         // GLOBAL Scripts
                        $globalScripts_btn = $btn->GetGlobalScripts();
                        $EventToTemplate .= " if($('.$btn->OnClickClass.new').length > 0 ) { ";
                            $EventToTemplate .= "\n";
                        $EventToTemplate .= trim(implode("\n", $eventScripts) ?: "");
                        $EventToTemplate .= '$(".'.$btn->OnClickClass.'.new").removeClass("new") ';
                        $EventToTemplate .= "\n";
                        $EventToTemplate .= " } ";
                        $EventToTemplate .= "\n";
                        // Asociar los scripts con el boton
                        $btnScriptList[$btn->Key] = [
                            'OnClickClass' => $btn->OnClickClass,
                            'Events' => $eventScripts,
                            'Global' => $globalScripts_btn
                        ];
                    }
 
                    // -----------------------------------------------------------
                    // GENERAMOS EL TEMPLATE DEL BOTON
                    // -----------------------------------------------------------
                    $Toggle = ($btn->TogglePopUp == true) ? 'data-bs-toggle="tooltip" data-bs-placement="top"  title=""  data-bs-original-title="'. $btn->ToggleText .'" '  : "";
                    $child = (!empty($btn->Child) && $btn->Child instanceof GenericWidget) ? $btn->Child->Draw(false) : "";
                    $buttonClasses_ = ($groupButtons == 'false') ? 'btn' : 'btn-sm btn-block' ;
                    $buttonClasses_ = !empty($btn->Classes[0]) ? $buttonClasses_ ." ". $btn->Classes[0] : $buttonClasses_;

                    $BaseButtonHTML = 
                    trim('
                    <button class="'. $btn->OnClickClass .' btn  '. $buttonClasses_ .' '. $btn->ButtonStyle .' new " 
                            style="position: relative;  cursor: pointer; [[__VISIBLE__]] " 
                            id="'. $btn->Key .'" 
                            name="'. $btn->Key .'" 
                            '.$Toggle.'
                            data-table="'. $tableKey .'"
                            [[__ENABLED__]]
                            data-bs-original-title="" 
                            title=""
                    >
                        '. $child .'
                        <div class="[[BADGE_VIEW]] badge-element" style="position: absolute;  right: 2px;  top: 0px;  cursor: pointer; " id="'. $btn->Key .'Badge">
                            <span class="label label-danger sm badge-text-element" style="border: 1px solid #ffffff;  padding: 0px 4px 1px 4px;  font-size: 10px;  cursor: pointer; " id="'. $btn->Key .'BadgeContent">
                            [[BADGE_CONTENT]]
                            </span>
                        </div>
                    </button>
                    ');
                    // -----------------------------------------------------------
                    // GENERAMOS LOS SCRIPTS QUE CONDICIONAN EL BTN
                    // -----------------------------------------------------------

                    $DisplayFunction    = "null";
                    $EnabledFunction    = "null";
                    $BadgeFunction      = "null";

                    // -- display
                    if($btn->DisplayFunction instanceof JSTableScriptFilter){
                        $DisplayFunction    = 
                        trim('
                            (element,roles) => {
                                '. trim($btn->DisplayFunction->Script[0]) .'
                            }

                        ');
                    }else{
                        $DisplayFunction    = 
                        trim('
                      
                            (element,roles,template) => { 
                                return true;
                            }

                        ');
                    }
                     // -- enabledFunction
                     if($btn->EnabledFunction instanceof JSTableScriptFilter){
                        $EnabledFunction    = 
                        trim('
                        
                            (element,roles) => {
                                '. trim($btn->EnabledFunction->Script[0]) .'
                            }

                        ');
                    }else{
                        $EnabledFunction    = 
                        trim('
                      
                            (element,roles,template) => { 
                                return true;
                            }

                        ');
                    }
                     // -- badgeFunction
                     
                     if($btn->TempalteFunction instanceof JSTableScriptFilter && !empty($btn->TempalteFunction)){
                        $TEMPLTEFUNCTION    = 
                        trim('
                      
                            (element,roles,template) => {
                              
                                '. trim($btn->TempalteFunction->Script[0]) .'
                            }

                        ');
                    }else{
                        $TEMPLTEFUNCTION    = 
                        trim('
                      
                            (element,roles,template) => { 
                            }

                        ');
                    }

                     if($btn->BadgeFunction instanceof JSTableScriptFilter){
                        $BadgeFunction    = 
                        trim('
                      
                            (element,roles) => {
                           
                                '. trim($btn->BadgeFunction->Script[0]) .'
                            }

                        ');
                    }else{
                        $BadgeFunction    = 
                        trim('
                      
                            (element,roles,template) => { 
                                return false;
                            }

                        ');
                    }
                    

                    // -----------------------------------------------------------
                    // REMPLAZAMOS LOS VALORES EN EL TEMPLATE
                    // -----------------------------------------------------------
                    
                    if($groupButtons == 'false'){
                        $BaseButtonHTML = $BaseButtonHTML;
                    }else{
                        $BaseButtonHTML = '<li>'.$BaseButtonHTML.'</li>';
                    }
                    
                   
                    $ActualBtnTemplate  = str_replace('[[__BTNKEY__]]'          , $tableKey."_".$btn->Key            ,$ActualBtnTemplate);
                    $ActualBtnTemplate  = str_replace('[[TEMPLATE]]'            ,$BaseButtonHTML        ,$ActualBtnTemplate);
                    $ActualBtnTemplate  = str_replace('[[DISPLAYFUNCTION]]'     ,$DisplayFunction       ,$ActualBtnTemplate);
                    $ActualBtnTemplate  = str_replace('[[ENABLEDFUNCTION]]'     ,$EnabledFunction       ,$ActualBtnTemplate);
                    $ActualBtnTemplate  = str_replace('[[BADGEFUNCTION]]'       ,$BadgeFunction         ,$ActualBtnTemplate);
                    $ActualBtnTemplate  = str_replace('[[TEMPLTEFUNCTION]]'     ,$TEMPLTEFUNCTION         ,$ActualBtnTemplate);
                    
                    $BtnTemplateList    .= $ActualBtnTemplate . "  ";
                    $BtnScriptList      .= " btntempalte +=  ".$tableKey."_".$btn->Key ."_CellButtonFn(element,". json_encode($rolesList) .") \n";

                }


            }
        }else{
            $BaseScript = str_replace('[[HASBUTTON]]','false',$BaseScript) ;
        }
        $id = (new \DateTime())->format('YmdHis') . random_int(1,5000); 

        $BaseScript     = str_replace('[[BTN_CONFIG_TEMPLATE]]'     ,trim($BtnTemplateList)        ,$BaseScript);
        $BaseScript     = str_replace('[[BTN_LIST_FNC]]'            ,trim($BtnScriptList)        ,$BaseScript);
        $BaseScript     = str_replace('[[_BUTTON_EVENTS_TEMPLATE_]]',trim($EventToTemplate)        ,$BaseScript);
        $BaseScript     = str_replace('[[TR_ATTRIBUTE_ADD]]'        ,trim($RowAttributeList)        ,$BaseScript);         
        $BaseScript     = str_replace('[[FILE_TITLE]]'         ,trim($FileTitle)         ,$BaseScript);
        $BaseScript     = str_replace('[[FILE_MODULO]]'        ,trim($FileModule)        ,$BaseScript);
        $BaseScript     = str_replace('[[HIDEBUTTONS]]'        ,trim($HideButton)        ,$BaseScript);
        $BaseScript = str_replace('[[TABLE_KEY]]'               ,trim($tableKey)        ,$BaseScript);
        
        
        
        
 
        
    
        $BaseScript     = str_replace('[[__SCRIPT__ID__]]'            ,trim($id)        ,$BaseScript);
       

        $this->_Scripts[$tableKey] = [
            'Type'      => 'Table',
            'Scripts'   => [
                'TableScripts'  => [$BaseScript], 
            ]
        ];

        return (object) [
            "Body"          => $body,
            "JSTemplate"    => $CellTemplateList
        ];
    }

    private function ObjectsToTableBody(
                                string $RowIdFieldName, 
                                array  $CellDefinitions, 
                                array  $Values,
                                array  $RowAttributeNames = [], 
                                array  $Buttons = []) : TableBody | null {
        

        
        // Recorrer los campos del objeto
        $rows = [];

        // Aumentar la RAM
        Ini::Instance()->Change(IniEnum::MEMORY_LIMIT, '256M');

        foreach($Values as $element) {

            $columns = [];

            // Revisar estados de los botones para esta fila
            foreach($Buttons as $idxButton => $action) {
                
                if($action instanceof TableDropDownButton){
                    foreach($action->Children as $idxChildButton => $item){
                        $showButton = true;
                        if (isset($item->DisplayFunction) && is_callable($item->DisplayFunction)) {
                            $func = $item->DisplayFunction;
            
                            if (is_callable($func))
                                $showButton = $func($item, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                        }
            
                        $enableButton = true;
                        if ($showButton === true) {                
                            if (isset($item->EnabledFunction) && is_callable($item->EnabledFunction)) {
                                $func = $item->EnabledFunction;
            
                                if (is_callable($func))
                                    $enableButton = $func($item, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                            }
                        }
        
                        $badge = null;
                        if (isset($item->BadgeFunction) && is_callable($item->BadgeFunction)) {
                            $func = $item->BadgeFunction;
            
                            if (is_callable($func))
                                $badge = $func($item, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                        }
            
                        $Buttons[$idxButton]->Children[$idxChildButton]->Show      = $showButton;
                        $Buttons[$idxButton]->Children[$idxChildButton]->Enabled   = $enableButton;
                        $Buttons[$idxButton]->Children[$idxChildButton]->Badge     = $badge;
                    }
                }

                $showButton = true;
                if (isset($action->DisplayFunction) && is_callable($action->DisplayFunction)) {
                    $func = $action->DisplayFunction;
    
                    if (is_callable($func))
                        $showButton = $func($action, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                }
    
                $enableButton = true;
                if ($showButton === true) {                
                    if (isset($action->EnabledFunction) && is_callable($action->EnabledFunction)) {
                        $func = $action->EnabledFunction;
    
                        if (is_callable($func))
                            $enableButton = $func($action, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                    }
                }

                $badge = null;
                if (isset($action->BadgeFunction) && is_callable($action->BadgeFunction)) {
                    $func = $action->BadgeFunction;
    
                    if (is_callable($func))
                        $badge = $func($action, Session::Instance()->usuario, Session::Instance()->usuario->Perfil, Session::Instance()->usuario->Perfil->Roles, $element);
                }
    
                $Buttons[$idxButton]->Show      = $showButton;
                $Buttons[$idxButton]->Enabled   = $enableButton;
                $Buttons[$idxButton]->Badge     = $badge;
            }

            foreach($CellDefinitions as $cell) {

                if (!isset($cell))
                    continue; 

                // Agregar el ID que define a esta fila
                array_push($cell->BodyAttributes, ['data-pk', $element->$RowIdFieldName]);

                // Verificar si se debe obtener el contenido desde la función de formato
                if (isset($cell->FormatFunction)) {
                    $func = $cell->FormatFunction;
                    $result = $func($element,$cell);

                    // Revisar el tipo de valor obtenido
                    if ($result instanceof GenericWidget) {
                        $value = $result->Draw(false);
                    }
                    else {
                        $value = $result;
                    }
                     
                }
                else {
                    // Buscar el valor en el objeto
                    $prop = $cell->PropertyName;
                    $value = $element->$prop;
                }

                if (!isset($value)) {
                    $value = '&nbsp;';
                }

                array_push($cell->BodyAttributes, ['data-property-name', str_replace(' ', '', $cell->PropertyName)]);

                $col = new TableColumn(
                    PropertyName: $cell->PropertyName,
                    Content: $value,
                    Classes: $cell->BodyClasses,
                    Styles: $cell->BodyStyles,
                    Attributes: $cell->BodyAttributes,
                    Properties: $cell->BodyProperties
                );

                array_push($columns, $col);
            }

            // Agregar botones
            $buttons = [];
            $DropDown = false;
            foreach($Buttons as $buttonIndex => $action) {
                // settings de los botones
                if ($action->Show === true) {
                    if ($action->Enabled === false) {
                        $buttonProperties = [
                            ['disabled', 'true']
                        ];
                        array_push($action->Classes,"disabled");
                    }
                    else {
                        $buttonProperties = [];
                    }
                    $buttonClasses = $action->Classes;
                    $buttonAtttributes = [];
                    if ($action instanceof TableButton && $action->OnClickClass != '') {
                        array_push($buttonClasses, $action->OnClickClass);
                        array_push($buttonClasses, 'new');
                    } 
               
                    if ($action instanceof Button && $action->TogglePopUp) {
                        array_push($buttonAtttributes, 
                            ['data-bs-toggle', 'tooltip'],
                            ['data-bs-placement', $action->TogglePlacement],
                            ['data-bs-original-title', $action->ToggleText]
                        );
                    }
                    // Agregar pk que define esta fila 
                    array_push($buttonAtttributes, ['data-pk', $element->$RowIdFieldName]);


                    // EN CASO DE QUE EL BUTTON SEA DE TIPO DROPDOWN
                    if($action instanceof TableDropDownButton){
                        $DropDown = true;
                        //SE VERIFICA QUE TENGA BOTONES HIJOS
                        if(!empty($action->Children) || count($action->Children) > 0){
                            //SI LOS BOTONES HIJOS EXISTEN SE FORMATEAN Y SE GUARDAN

                            //CONTENEDOR TEMPORAL DE LOS HIJOS
                            $DropButtonChild = [];

                            foreach($action->Children as $item){

                                if ($item->Show === true) {
                                    if ($item->Enabled === false) {
                                        $buttonProperties = [
                                            ['disabled', 'true']
                                        ];
                                    }
                                    else {
                                        $buttonProperties = [];
                                    }
                                     
                                    $buttonClasses = $item->Classes;
                                    $buttonAtttributes = [];
                                    
                                    // Agregar pk que define esta fila 
                                    array_push($buttonAtttributes, ['data-pk', $element->$RowIdFieldName]);
                                    if ($item instanceof TableButton && $item->OnClickClass != '') {
                                        array_push($buttonClasses, $item->OnClickClass);
                                        array_push($buttonClasses, 'new');
                                    }
                                    if ($item instanceof Button && $item->TogglePopUp) {
                                        
                                        array_push($buttonAtttributes, 
                                            ['data-bs-toggle', 'tooltip'],
                                            ['data-bs-placement', $action->TogglePlacement],
                                            ['data-bs-original-title', $action->ToggleText]
                                        );
                                    } 
                               

                                    $DropButtonChild[] = new ActionButtonDropDownChild(
                                        Key:            $item->Key,
                                        ButtonStyle:    $item->ButtonStyle,
                                        Badge:          $item->Badge,
                                        BadgeStyle:     $item->BadgeStyle,
                                        Child:          $item->Child,
                                        Properties:     $buttonProperties,
                                        Classes:        $buttonClasses,
                                        Attributes:     $buttonAtttributes
                                    ) ;
                                }
                            }
                        }


                        //SE GUARDA EL BOTÓN DE TIPO DROPDOWN
                        array_push($buttons, new ActionButtonDropDown(
                            Key         : $action->Key,
                            ButtonStyle : $action->ButtonStyle,
                            Badge       : $action->Badge,
                            BadgeStyle  : $action->BadgeStyle,
                            Content     : $action->Content,
                            Children    : $DropButtonChild,
                            Properties  : $action->Properties,
                            Classes     : $action->Classes,
                            Attributes  : $action->Attributes
                        ));
                        

                    }else{
                        array_push($buttons, new ActionButton(
                            Key:            $action->Key,
                            ButtonStyle:    $action->ButtonStyle,
                            Badge:          $action->Badge,
                            BadgeStyle:     $action->BadgeStyle,
                            Child:          $action->Child,
                            Properties:     $buttonProperties,
                            Classes:        $buttonClasses,
                            Attributes:     $buttonAtttributes
                        ));
                    }

                    
                }

               
            }

            if (count($Buttons) > 0) {
                if($DropDown){
                    $botonera = new Container(
                        Classes: [''],
                        Children: $buttons
                    );
                }else{
                    $botonera = new Container(
                        Classes: ['text-right', 'one-line-text'],
                        Children: $buttons
                    );
                }
                
                array_push(
                    $columns,
                    new TableColumn(
                        Content: $botonera->Draw(false)
                    )
                );
            }

            // Preparar Atributos
            //
            $rowAttributes = [];

            // Agregar el ID que define a esta fila
            array_push($rowAttributes, ['data-pk', $element->$RowIdFieldName]);

            // Agregar los atributos especificados por el sistema
            foreach($RowAttributeNames as $rowAttribute) {
                // Obtener el valor
                $valor = $element->$rowAttribute;

                if (!isset($valor)) {
                    $valor = '';
                }

                array_push($rowAttributes, ['data-' . $rowAttribute, $valor]);
            }
            //

            $row = new TableRow(
                Columns: $columns,
                Attributes: $rowAttributes
            );

            array_push($rows, $row);
        }

        $body = new TableBody(
            Rows: $rows
        );

        // Bajar la RAM
        Ini::Instance()->Rollback(IniEnum::MEMORY_LIMIT);

        return $body;
    }

 

    private function ObjectToTableHeader(array $CellDefinitions, $Metadata, bool $IncludeActionColumn = false, int $btnNumber = 0, bool $JSTable = false) : TableHeader | null {

        // Recorrer los campos del objeto
        $columns = [];
        foreach($CellDefinitions as $cell) {

            if (!isset($cell))
                continue;

            // Preferencia, la etiqueta de la definicion, sino, la etiqueta de la metadata
            // y si no existen, usar el nombre de la propiedad
            if (isset($cell->Label) && $cell->Label != '') {
                $label = $cell->Label;
            }
            else if (
                        isset($Metadata->Properties[$cell->PropertyName]) 
                        && isset($Metadata->Properties[$cell->PropertyName]->Label)
                        && $Metadata->Properties[$cell->PropertyName]->Label != ''
            ) {
                $label = $Metadata->Properties[$cell->PropertyName]->Label;
            }
            else {
                $label = $cell->PropertyName;
            }

            $col = new TableHeaderColumn(
                Title:      $label,
                Classes:    $cell->HeaderClasses,
                Styles:     $cell->HeaderStyles,
                Attributes: $cell->HeaderAttributes,
                Properties: $cell->HeaderProperties
            );

            array_push($columns, $col);
        }

        if ($IncludeActionColumn) {

            
            if($JSTable == true){

                $width = $btnNumber * 30;
                array_push(
                    $columns, 
                    new TableHeaderColumn(
                        Title   : '::',
                        Classes : ['text-center','btnAccion'], //Clase btnAccion para ocultar en la exportacion de PDF o Excel
                        Styles  :[
                            ["width",$width."px"]
                        ]
                    )
                );

            }else{
                array_push(
                    $columns, 
                    new TableHeaderColumn(
                        Title: '::',
                        Classes: ['text-center','btnAccion'] //Clase btnAccion para ocultar en la exportacion de PDF o Excel
                    )
                );
            }
            
        }

        $row = new TableHeaderRow(
            Columns: $columns
        );

        return new TableHeader(
            Rows: [$row]
        );
    }

    

    private function GenerateTableScripts(array $scripts, array $globalScripts, $tableKey) {

        // Scripts globales
        $scriptGlobal = "
    // Configuración inicial de la tabla
    $('#" . $tableKey . "').TablaEstandar();

    // Llamar a la funcion de carga inicial de la tabla
    if ( typeof " . $tableKey . "_OnLoad === 'function') {
        " . $tableKey . "_OnLoad(eventInfo);
    }
        ";

        array_push($scripts, $scriptGlobal);
        $scripts = array_merge($scripts, $globalScripts);

        $scriptEvents = new Script(
            $scripts
        );

        return $scriptEvents->Draw(false);

    }

    private function JSWidgetChildSearch($Widget,$founded = []){

    
    
        if($Widget instanceof GenericWidget){

            if($Widget instanceof JSTableContent){
          
                if(is_array($founded)){
                    array_push($founded,$Widget);
                }else{
                    $founded = [ $Widget ];
                }
                return $founded;
            }


            if(isset($Widget->Children)){            
     
                $founded = $this->JSWidgetChildSearch($Widget->Children,$founded);
                return $founded;

            }elseif($Widget->Child){

                $founded = $this->JSWidgetChildSearch($Widget->Child,$founded);
                return $founded;

            }elseif($Widget->Content){

                $founded = $this->JSWidgetChildSearch($Widget->Content,$founded);
                return $founded;

            }else{

                return $founded;
                
            }
            

        }elseif(is_array($Widget)){

            $response = null;

            foreach($Widget as $item){  

                $founded = $this->JSWidgetChildSearch($item,$founded); 

            }
            return $founded;

        }

        return $founded;

    }

}