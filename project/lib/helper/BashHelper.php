<?php

function format_bash_result($result, $start_line = "> ")
{
    $result = preg_replace("|\n$|", "", $result);
    $result = simple_format_text($result, $start_line);
    //$result = auto_link_text($result, 'all', array('target' => '_blank', 'class' => 'btn_majeur btn_petit btn_jaune', 'style' => 'display: inline-block; margin: 2px 5px;'));
    $result = preg_replace("/\[(.+)\]\((.+)\)/", '<a target="_blank" class="simple" href="\2">\1</a>', $result);
    //$result = preg_replace("/\[(.+)\]\((.+)\)/", '<p style="text-align:center"><a style="display: inline-block; margin: 10px 0" class="btn_majeur btn_petit btn_jaune" href="\2">\1</a></p>', $result);
    $result = preg_replace("|\n<br />\n<br />|", "<br />", $result);

    return $result;
}

function extract_link($result)
{
    $output = null;
    while(preg_match("/\[(.+)\]\((.+)\)/", $result, $matches)) {
        $result = str_replace($matches[0], "", $result);
        $output .= sprintf("<a target=\"_blank\" class=\"btn_majeur btn_petit btn_jaune btn_tache\" href=\"%s\">%s</a>", $matches[2], $matches[1]);
    }

    return $output;
}

function simple_format_text($text, $start_line = "> ", $options = array())
{
  $css = (isset($options['class'])) ? ' class="'.$options['class'].'"' : '';

  $text = sfToolkit::pregtr($text, array("/(\r\n|\r)/"        => "\n",               // lets make them newlines crossplatform
                                         "/\n{2,}/"           => "</p><p$css>"));    // turn two and more newlines into paragraph

  // turn single newline into <br/>
  $text = str_replace("\n", "\n<br />".$start_line, $text);
  return '<p'.$css.'>'.$start_line.$text.'</p>'; // wrap the first and last line in paragraphs before we're done
}