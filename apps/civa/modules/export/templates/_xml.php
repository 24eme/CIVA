
<?php
if (!function_exists('printXml')) {
    function printXml($xml) {
      foreach ($xml as $k => $v) {
        if (!is_numeric($k))
        echo "<$k>";
        if (is_object($v) && get_class($v))
          printXml($v);
        else if (is_array($v)) {
          foreach ($v as $a) {
            printXml(array($k => $a));
          }
        }else if (is_numeric($v) && preg_match('/^(L|volume)/', $k) && $v) {
          if ($k == 'L4' && $v) {
            $v = $v / 100;
            printf('%04.04f', $v);
          }else {
            printf('%04.02f', $v);
          }
        } else
          echo $v;
        if (!is_numeric($k))
        echo "</$k>";
    }
  }
}
?>
<decRec numCvi="<?php echo $dr->cvi; ?>" campagne="<?php echo $dr->campagne; ?>-<?php echo ($dr->campagne+1); ?>" typeDec="DR">
<rensComp><typeViti>C</typeViti><modeFV>P</modeFV><persCtc><?php echo $dr->declarant->nom; ?></persCtc><numTel><?php echo $dr->declarant->telephone; ?></numTel><mel><?php echo $dr->declarant->email; ?></mel></rensComp>
<?php foreach($xml as $colonne) : ?><colonne><?php printXml($colonne); ?></colonne>
<?php endforeach; ?><qteLies><?php printf('%04.02f', $dr->lies); ?></qteLies>
</decRec>