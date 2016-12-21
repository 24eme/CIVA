<?php
if (!function_exists('printXml')) {
    function printXml($xml) {
      foreach ($xml as $k => $v) {
        if (!is_numeric($k) && in_array(substr($k, 0, 3), array("L6_", "L7_", "L8_"))) {
                $k = substr($k, 0, 2);
        }
        if (!is_numeric($k)) {
            echo "<$k>";
        }
        if (is_object($v) && get_class($v))
          printXml($v);
        else if (is_array($v)) {
          foreach ($v as $a) {
            printXml(array($k => $a));
          }
        }else if (is_numeric($v) && preg_match('/^(L|volume)/', $k) && $v) {
          if ($k == 'L4' && $v) {
            $v = $v / 100;
            printf('%04.04f', round($v, 4));
          }else {
            printf('%04.02f', round($v, 2));
          }
        } else
          echo str_replace('&', 'et', $xml->getRaw($k));
        if (!is_numeric($k)) {
            echo "</$k>";
        }
    }
  }
}
?>
<decRec numCvi="<?php echo $dr->cvi; ?>" campagne="<?php echo $dr->campagne; ?>-<?php echo ($dr->campagne+1); ?>" typeDec="DR" dateDepot="<?php echo ($dr->exist('date_depot_mairie') && $dr->date_depot_mairie) ? $dr->date_depot_mairie : $dr->validee ?>">
<rensComp><typeViti>C</typeViti><modeFV>P</modeFV><persCtc><?php echo str_replace('&', 'et', $dr->declarant->getNom(ESC_RAW)) ?></persCtc><numTel><?php echo $dr->declarant->telephone; ?></numTel><mel><?php echo $dr->declarant->email; ?></mel></rensComp>
<?php if (isset($achats)): foreach($achats as $achat) : ?><?php printXml($achat); ?><?php endforeach; echo "\n"; endif;?>
<?php foreach($colonnes as $colonne) : ?><colonne><?php printXml($colonne); ?></colonne><?php echo "\n"; endforeach; ?>
<?php if($destinataire == ExportDRXml::DEST_CIVA): ?><qteLies></qteLies><?php echo "\n" ?><?php endif; ?>
</decRec>
