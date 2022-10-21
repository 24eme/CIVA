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
    return (is_null($detailLine->volume_propose))? "background-color: lightgray;" : "";
}

function getExplicationEtoile(){
return "Le prix s'entend net, c'est-à-dire hors-taxes et tous escomptes déduits, la cotisation interprofessionnelle ainsi que les commissions de courtage étant à régler séparément.";
}



function getLastSentence(){
return "Le vendeur déclare être habilité à produire du vin AOC.";
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

function echoQuantite($volume, $bold = false, $type) {
    if(!is_null($volume)) {
        if($bold) {
            echo "<b>";
        }

        echo sprintFloatFr($volume);

        if($bold) {
            echo "</b>";
        }

        echo "&nbsp;<small>$type</small>&nbsp;&nbsp;";
    } else {
        echo "&nbsp;";
    }
}

function echoVolume($volume, $bold = false) {
    echoQuantite($volume, $bold, 'hl');
}

function echoSurface($surface, $bold = false) {
    echoQuantite($surface, $bold, 'ha');
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

        echo "&nbsp;<small>&euro;</small>&nbsp;&nbsp;";
    } else {
        echo "&nbsp;";
    }
}

function getDateFr($dateIso) {
    $dateExplosed = explode('-', $dateIso);

    if(count($dateExplosed) == 2) {

        return $dateExplosed[1].'/'.$dateExplosed[0];
    }

    return $dateExplosed[2].'/'.$dateExplosed[1].'/'.substr($dateExplosed[0],2);
}

function echoDateFr($dateIso) {    
    echo getDateFr($dateIso);
}

function echoCentilisation($centilisation) {
	if (preg_match('/([0-9,]+)\ ([a-zA-Z]+)/', $centilisation, $matches)) {
		echo $matches[1].'&nbsp;<small>'.$matches[2].'</small>&nbsp;&nbsp;';
	} else {
		echo '&nbsp;'.$centilisation.'&nbsp;';
	}
}