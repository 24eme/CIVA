<decRec numCvi="<?php echo $dr->cvi; ?>" campagne="<?php echo $dr->campagne; ?>" typeDec="DR">
<?
   foreach ($dr->recolte->filter('^appellation_') as $appellation)
   foreach ($appellation->filter('^lieu') as $lieu) :
   foreach ($lieu->filter('^cepage_') as $cepage)
   foreach ($cepage->detail as $detail) 
   ?><colonne>
<L1><?php $detail->getCodeDouane(); ?></L1>
<L3>B</L3>
<mentionVal><?php echo $detail->denomination; ?></mentionVal>
<L4><?php echo $detail->volume; ?></L4>
</colonne>
<?php endforeach; ?>
</decRec>