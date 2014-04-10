<?php

function format_bash_result($result)
{
    $result = simple_format_text($result);
    $result = auto_link_text($result, 'all', array('target' => '_blank', 'class' => 'btn_majeur btn_petit btn_jaune', 'style' => 'display: inline-block; margin: 2px 5px;'));
    $result = preg_replace("|>http[s]*://[a-zA-Z0-9_/\.-]+/([a-zA-Z0-9_\.-]+)[/]*<|", '>\1<', $result);

    return $result;
}