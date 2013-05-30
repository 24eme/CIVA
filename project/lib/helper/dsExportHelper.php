<?php 

function echoVolume($volume, $bold = false) {
    if(!is_null($volume)) {
        echo sprintFloatFr($volume)."&nbsp;<small>hl</small>";
    } else {
        echo "&nbsp;";
    }
}