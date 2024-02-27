function [[__BTNKEY__]]_CellButtonFn(element,roles){

    
    //--------------------------------------------------------------------------------
    // Template Config
    //--------------------------------------------------------------------------------
    var btnTEMPLATE         = `[[TEMPLATE]]`;
    let displayFunction     = [[DISPLAYFUNCTION]];
    let enabledFunction     = [[ENABLEDFUNCTION]];
    let badgedFunction      = [[BADGEFUNCTION]];
    let templatefunction    = [[TEMPLTEFUNCTION]];

    var IsVisible           = true;
    var hasBadge            = false;
    var IsEnabled           = false;

    var replaceAttribute = (template,atributename,atributevalue) => {
        return template.replace(`[[${atributename}]]`,atributevalue);
    }

    var isHTML = (str) => {
        var a = document.createElement('div');
        a.innerHTML = str;
      
        for (var c = a.childNodes, i = c.length; i--; ) {
          if (c[i].nodeType == 1) return true; 
        }
      
        return false;
      }
    
    //--------------------------------------------------------------------------------
    // VISUALIZACION CONDICIONADA DEL BTN
    //--------------------------------------------------------------------------------

    // --- se valida si existe la funcion de visualización
    if ( displayFunction ){
        IsVisible = displayFunction(element,roles); 
    }
    if(IsVisible == true || displayFunction == null){
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__VISIBLE__','');
    }else if(displayFunction == null){
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__VISIBLE__','');
    }else{
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__VISIBLE__','display:none;');
    }

    // --- VALIDAMOS SI ESTA ACTIVADO O NO EL BTN
    if(enabledFunction) {
        IsEnabled = enabledFunction(element,roles);
       
    }

    // verificamos si esta activado o no el btn
    if(IsEnabled == true){
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__ENABLED__','');
    }else if(enabledFunction == null){
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__ENABLED__','');
    }else{
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'__ENABLED__','disabled="true"');
    }
    // --- VERIFICAMOS SI VIENE O NO VIENE EL BADGE
    if(badgedFunction){
        hasBadge = badgedFunction(element,roles); 
    }
    
    if(hasBadge * 1 > 0){

        badgevalue = (isNaN(hasBadge * 1)) ? 0 : hasBadge;

        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'BADGE_CONTENT',badgevalue);
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'BADGE_VIEW'   ,'');
    }else{
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'BADGE_CONTENT','');
        btnTEMPLATE = replaceAttribute(btnTEMPLATE,'BADGE_VIEW'   ,'hide');
    } 

    if(templatefunction){
        var templateresult = templatefunction(element,roles,btnTEMPLATE);
      
        if(isHTML(templateresult)){
            btnTEMPLATE = templateresult;
        }
    }


    return btnTEMPLATE;
    
}