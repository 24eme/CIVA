<?php 

function echoVolume($volume, $bold = false) {
    if(!is_null($volume)) {
        if($bold) {
            echo "<b>";
        }

        echo sprintFloatFr($volume);

        if($bold) {
            echo "</b>";
        }

        echo "&nbsp;<small>hl</small>&nbsp;&nbsp;&nbsp;";
    } else {
        echo "&nbsp;";
    }
}