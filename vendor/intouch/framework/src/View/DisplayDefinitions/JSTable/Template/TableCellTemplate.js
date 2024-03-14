[[__function__name__]](element,roles){

    var htmltemplate    = `[[HTML_TEMPLATE]]`;
    
    var cellClass       = '"[[__CLASSESS__]]"';
    var cellStyle       = "[[JSON_STYLE]]";

    var cellProperty    = "[[PRIMARY_PROPERTY]]";
    var tablePrimaryKey = "[[__TABLEPRIMARYKEY__]]"; 
    var CellPK          = element[tablePrimaryKey];
    
 

    [[_SCRIPTS_FILTERS_]]
    // la estrucutura del atributo es
    // atribute[key]= {
    //   propertyname   : property,
    //   propertyvalue  : value 
    // }

    // CREAMOS UNA FUNCIÓN QUE REMPLACE LA INFORMACIÓN DE LOS TEMPLATES

    var replaceAttribute = (template,atributename,atributevalue) => {
        return template.replace(`[[${atributename}]]`,atributevalue);
    }

    //--------------------------------------------------------------------------------
    // SECCION DE DIBUJADO DEL TEMPLATE
    //--------------------------------------------------------------------------------
    [[_TEMPLATE_REPLACES_]]

    return {
        "Template"          : htmltemplate,
        "Class"             : cellClass,
        "Styless"           : cellStyle,
        "data"              : element,
        "roles"             : roles,
        "cellProperty"      : cellProperty,
        "pk"                : CellPK
    };
    
}