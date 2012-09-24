<?php $first = true ?>
<ul class="liste_sepages">
<?php foreach($onglets->getCurrentAppellation()->getRawValue()->getMentions() as $key_m => $mention ) : ?>
<li class="<?php if ( $onglets->getCurrentKeyMention() == $key_m): ?>ui-tabs-selected<?php endif; ?> <?php if ($first):?>premier<?php endif; ?>">
    <a href="<?php echo url_for($onglets->getUrl('recolte', $appellation_key, $key_m)->getRawValue()) ?>"><?php echo $mention->getConfig()->libelle ?></a>
</li>
<?php $first = false; ?>
<?php endforeach;?>
</ul>
