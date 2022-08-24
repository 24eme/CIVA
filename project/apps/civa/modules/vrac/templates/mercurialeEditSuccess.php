<div id="contrat_onglet">
<ul id="onglets_majeurs" class="clearfix">
	<li class="ui-tabs-selected">
		<a href="#" style="height: 18px;">
			Edition valeur mercuriale
        </a>
    </li>
</ul>
</div>
<div id="contrat_onglet">
<div id="contrats_vrac" class="fiche_contrat">
<div class="fond">
    <h2>Mercuriales</h2>
    <p>Valeur Mercuriale : <?php echo $vrac->getMercurialeValue(); ?></p>
    <br>
    <p>
    <ul>
    <li<?php if ($vrac->getMercurialeValue() == 'I') {echo ' style="font-weight: bold;"';} ?>>I : contrat interne (vrac->interne = true)</li>
    <li<?php if ($vrac->getMercurialeValue() == 'C') {echo ' style="font-weight: bold;"';} ?>>C : vendeur est une cave cooperative (vendeur_type == caves_cooperatives)</li>
    <li<?php if ($vrac->getMercurialeValue() == 'X') {echo ' style="font-weight: bold;"';} ?>>X : vendeur est négociant (vendeur_type == negociants)</li>
    <li<?php if ($vrac->getMercurialeValue() == 'V') {echo ' style="font-weight: bold;"';} ?>>V : l'acheteur est un récoltant (acheteur_type == recoltants)</li>
    <li<?php if ($vrac->getMercurialeValue() == 'M') {echo ' style="font-weight: bold;"';} ?>>M : contrat par défaut (acheteur pas récoltant et vendeur ni coopérative ni négociant)</li>
    </ul>
    </p>
    <br>
    <hr/>
    <br>
<?php include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => null, 'fiche' => true)) ?>
<br>
<hr/>
<br>
<h1>Edition des champs mercuriales</h1>
<br>
<ul>
<form method="POST">
<?php echo $form->renderGlobalErrors(); ?>
<?php echo $form->renderHiddenFields(); ?>
<table style="margin: 20px;">
<tr>
    <td width=150px><label><?php echo $form['interne']->renderLabel() ?></label></td>
    <td>
        <?php echo $form['interne']->render() ?>
        <span><?php echo $form['interne']->renderError() ?></span>
    </td>
</tr>
<tr>
    <td width=150px><label><?php echo $form['acheteur_type']->renderLabel() ?></label></td>
    <td>
        <?php echo $form['acheteur_type']->render() ?>
        <span><?php echo $form['acheteur_type']->renderError() ?></span>
    </td>
</tr>
<tr>
    <td width=150px><label><?php echo $form['vendeur_type']->renderLabel() ?></label></td>
    <td>
        <?php echo $form['vendeur_type']->render() ?>
        <span><?php echo $form['vendeur_type']->renderError() ?></span>
    </td>
</tr>
<tr><td>&nbsp;</td><td>
<br/><input type="submit" value="Modifier"/>
</td></tr>
</table>
</form>
</ul>
</div>
<a tabindex="-1" id="btn_precedent" href="<?php echo url_for('vrac_fiche', $vrac) ?>">
    <img alt="Retourner à l'étape précédente" src="/images/boutons/btn_retourner_etape_prec.png">
</a>
</div>
</div>