<?php 

function checkedFamille($soussigneNode,$type){
    if($soussigneNode->famille == $type) return "checked=\"checked\"";
    return '';
}

function boldFamille($soussigneNode,$type,$string) {
    if($soussigneNode->famille == $type) return "<bold>".$string."</bold>";
    return $string;
}

function printCepageKey($detailLine) {
    echo preg_replace('/^cepage_/', '', $detailLine->getCepage()->getKey());
}

function getColorRowDetail($detailLine) {
    return (is_null($detailLine->volume_propose))? "background-color: #bbb;" : "";
}

function getExplicationEtoile(){
return "Le prix s'entend net, c'est-à-dire hors-taxes et tous escomptes déduits, la cotisation interprofessionnelle ainsi que les commissions de courtage étant à régler séparément.";
}



function getLastSentence(){
return "Le vendeur déclare être habilité à produire du vin AOC et qu'il n'est pas sous le coup d'une sanction suite à un manquement.";
}


function truncate_text($text, $length = 30, $truncate_string = '<small>…</small>', $truncate_lastspace = false)
{
  if ($text == '')
  {
    return '';
  }

  $mbstring = extension_loaded('mbstring');
  if($mbstring)
  {
   $old_encoding = mb_internal_encoding();
   @mb_internal_encoding(mb_detect_encoding($text));
  }
  $strlen = ($mbstring) ? 'mb_strlen' : 'strlen';
  $substr = ($mbstring) ? 'mb_substr' : 'substr';

  if ($strlen($text) > $length)
  {
    $truncate_text = $substr($text, 0, $length - 6);
    if ($truncate_lastspace)
    {
      $truncate_text = preg_replace('/\s+?(\S+)?$/', '', $truncate_text);
    }
    $text = $truncate_text.$truncate_string;
  }

  if($mbstring)
  {
   @mb_internal_encoding($old_encoding);
  }

  return $text;
}

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

function echoPrix($prix, $bold = false) {
    if(!is_null($prix)) {
        if($bold) {
            echo "<b>";
        }

        echo sprintFloatFr($prix);

        if($bold) {
            echo "</b>";
        }

        echo "&nbsp;<small>&euro;</small>&nbsp;&nbsp;&nbsp;";
    } else {
        echo "&nbsp;";
    }
}