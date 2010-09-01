<?php function printXml($xml) {
  foreach ($xml as $k => $v) {
    if (!is_numeric($k))
    echo "<$k>";
    if (get_class($v))
      printXml($v);
    else
      print_r($v);
    if (!is_numeric($k))
    echo "</$k>";

  }
  }
?>
<decRec numCvi="<?php echo $dr->cvi; ?>" campagne="<?php echo $dr->campagne; ?>" typeDec="DR">
<rensComp>
<typeViti>C</typeViti>
<modeFV>P</modeFV>
<persCtc><?php echo $dr->declarant->nom; ?></persCtc>
<mel><?php echo $dr->declarant->email; ?></mel>
<numTel><?php echo $dr->declarant->telephone; ?></numTel>
</rensComp>
<?php foreach($xml as $colonne) : ?>
<colonne>
    <?php printXml($colonne); ?>
</colonne>
<?php endforeach; ?>
<qteLies><?php echo $dr->lies; ?></qteLies>
<volTot><?php echo $dr->getTotalVolume(); ?></volTot>
<ratio><?php echo $dr->getRatioLies(); ?></ratio>
</decRec>