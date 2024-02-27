<?php

namespace Intouch\Framework\Widget;

interface IDrawable {

    function Draw($echoResult = false);
    function OnBeforeDraw();
    
}