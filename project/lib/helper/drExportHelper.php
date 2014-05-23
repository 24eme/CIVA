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

function echoSuperficie($superficie, $bold = false) {
    if(!is_null($superficie)) {
        if($bold) {
            echo "<b>";
        }

        echo sprintFloatFr($superficie);

        if($bold) {
            echo "</b>";
        }

        echo "&nbsp;<small>ares</small><small>&nbsp;</small>";
    } else {
        echo "&nbsp;";
    }
}