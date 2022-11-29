<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SUPPRESSION)): ?>
	<div class="btn_header">
		<a id="btn_precedent" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
			<img alt="Retourner à l'étape précédente" src="/images/boutons/btn_supprimer_contrat.png">
		</a>
	</div>
<?php endif; ?>
<style media="screen">.printonly {display: none;}</style>
<style  media="print">
.noprint, strong.responsable, #actions_fiche, #titre_rubrique .utilisateur, #logo, #acces_directs, .btn_header, .modal, #footer, #ajax_notifications,  .popup_ajout, .popup_loader, .produits thead {display: none; }
li {list-style-type: none;}
.produits td {display: block; width: 100%}
td.echeance {display: inline;}
.produit, .validation thead th {font-weight: bold;text-align: left;}
#onglets_majeurs {padding: 0}
#onglets_majeurs a {text-decoration: none; color: black;}
</style>
<div id="contrat_onglet">
<ul id="onglets_majeurs" class="clearfix">
	<li class="<?php if($vrac->isApplicationPluriannuel()): ?>ui-tabs<?php else: ?>ui-tabs-selected<?php endif; ?>">
		<a style="height: 18px;" href="<?php echo url_for('vrac_fiche', $vrac->getContratDeReference()) ?>">
		<?php if ($vrac->isValide()): ?>
			Contrat <?php if($vrac->getContratDeReference()->isPluriannuelCadre()): ?>pluriannuel<?php endif; ?> <?php if($vrac->isPapier()): ?>papier<?php else: ?>télédéclaré<?php endif; ?> <?php if ($vrac->getContratDeReference()->numero_archive): ?>(visa n° <?php echo $vrac->getContratDeReference()->numero_archive ?>)<?php endif; ?>
		<?php else: ?>
			Validation de votre contrat <?php if($vrac->isPluriannuelCadre()): ?>pluriannuel<?php endif; ?>
		<?php endif; ?>
		</a>
	</li>
    <?php if (count($contratsApplication)>0): ?>
		<?php foreach($contratsApplication as $numContratApplication => $contratApplication): ?>
			<?php if($contratApplication): ?>
                <li class="<?php if($contratApplication->_id == $vrac->_id): ?>ui-tabs-selected<?php else: ?>ui-tabs<?php endif; ?>"><a href="<?php echo url_for('vrac_fiche', $contratApplication) ?>"><?php echo $contratApplication->campagne ?></a></li>
            <?php elseif($formApplication && $numContratApplication == $formApplication->getObject()->numero_contrat): ?>
                <li class="ui-tabs" style="opacity: 0.5;"><a href="" class="generationContratApplication"><?php echo substr($numContratApplication, -4).'-'.(substr($numContratApplication, -4)+1) ?></a></li>
            <?php else: ?>
                <li class="ui-tabs" style="opacity: 0.5;"><a href="javascript:void(0)"><?php echo substr($numContratApplication, -4).'-'.(substr($numContratApplication, -4)+1) ?></a></li>
			<?php endif; ?>
		<?php endforeach; ?>
    <?php endif; ?>
	<li style="float: right; opacity: 0.2;">
			<span><a href="<?php echo url_for('vrac_mercuriale', $vrac); ?>">Merc. <?php echo $vrac->getMercurialeValue(); ?></a>
			</span>
		</span>
	</li>
    <li style="float: right">
		<span class="statut"><?php if($vrac->isPapier()): ?>Saisie papier<?php else: ?><?php echo VracClient::getInstance()->getStatutLibelle($vrac->valide->statut) ?><?php endif; ?>
    </li>
</ul>
</div>
<div id="contrats_vrac" class="fiche_contrat">
	<?php if ($form): ?>
	<form id="principal" class="ui-tabs" method="post" action="<?php echo url_for('vrac_fiche', array('sf_subject' => $vrac)) ?>">
		<?php echo $form->renderHiddenFields() ?>
		<?php echo $form->renderGlobalErrors() ?>
	<?php endif; ?>
	<div class="fond">

		<?php if($sf_user->hasFlash('notice')) : ?>
			<p class="flash_message" style="margin-bottom: 20px;"><?php echo $sf_user->getFlash('notice'); ?></p>
		<?php endif; ?>

		<?php use_helper('Date') ?>

		<?php if (!$vrac->isValide() && $user->_id && !$vrac->hasValide($user->_id) && !$vrac->isPluriannuelCadre()): ?>
		<fieldset class="message">
		    <legend class="message_title">Points de vigilance <a href="#" class="msg_aide_ds" rel="help_popup_validation_log_vigilance_ds" title="Message aide"></a></legend>
		     <ul class="messages_log">
		        <li>
	                En cas d'erreur sur le contrat, veuillez contacter votre interlocuteur, le responsable du contrat.
		        </li>
			</ul>
		</fieldset>
		<?php endif; ?>

		<?php
            include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => true));
        ?>

		<?php
			if($validation->hasPoints()) {
				include_partial('global/validation', array('validation' => $validation));
			}
		?>
<hr class="printonly"/>
		<?php include_partial('vrac/produits', array('vrac' => $vrac, 'form' => $form, 'produits_hash_in_error' => $validation->getProduitsHashInError())) ?>

		<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::FORCE_CLOTURE) && !$vrac->isPluriannuelCadre()): ?>
			<a class="noprint" style="float: right; bottom: 6px; color: #2A2A2A; text-decoration: none;" onclick="return confirm('Êtes vous sur de vouloir forcer la cloture de ce contrat ?');" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('vrac_forcer_cloture', $vrac) ?>">Forcer la cloture</a>
		<?php endif; ?>

<?php if(!$vrac->isPapier()): ?>
<hr class="printonly"/>
<?php include_partial('vrac/ficheConditions', array('vrac' => $vrac, 'fiche' => true)); ?>
<?php endif; ?>

<?php if ($vrac->isPluriannuelCadre() && count($contratsApplication)>0): ?>
    <table class="validation table_donnees" style="width: 400px;">
    	<thead>
    		<tr>
    			<th style="width: 212px;">Campagnes pluriannel</th>
    		</tr>
    	</thead>
    	<tbody>
			<?php $i=0;foreach($contratsApplication as $numContratApplication => $contratApplication): ?>
            <tr<?php if($i%2): ?> class="alt"<?php endif; ?>>
    			<td>
    				Campagne <?php echo substr($numContratApplication, -4).'-'.(substr($numContratApplication, -4)+1) ?>
    			</td>
    			<td>
					<?php if($contratApplication): ?>
						<a href="<?php echo url_for('vrac_fiche', $contratApplication) ?>">Voir le contrat</a>
					<?php elseif($formApplication && $numContratApplication == $formApplication->getObject()->numero_contrat && ($user && $user->_id == $vrac->acheteur_identifiant)): ?>
						<a href="" class="generationContratApplication">Générer le contrat</a>
					<?php else: ?>
						<i class="text-muted">Non disponible</i>
					<?php endif; ?>
    			</td>
    		</tr>
			<?php $i++; endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

	</div>
<?php if (!$vrac->isPapier()) : ?>
<div class="printonly">
<br/><br/><br/>
<hr/>
<p>Document non contractuel. Le document original télésigné est disponible sur votre espace VinsAlsace.pro</p>
</div>
<?php endif; ?>
	<table id="actions_fiche">
		<tr>
			<td style="width: 40%"><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></td>
			<td align="center"><?php if ($vrac->isValide() && !$vrac->isPapier()): ?><input type="image" src="/images/boutons/btn_pdf_visualiser.png" alt="Visualiser" name="boutons[previsualiser]" id="previsualiserContrat"><?php endif; ?></td>
			<td style="width: 40%; text-align: right;">
				<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE)): ?>
                    <?php if ($vrac->isProposition()): ?>
                    <a href="<?php echo url_for('vrac_refuser_proposition', array('sf_subject' => $vrac)) ?>" style="margin-right: 50px;" onclick="return confirm('Etes vous surs de vouloir refuser cette proposition ?')">
                        [x] Refuser la proposition
                    </a>
                    <a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
                        <button class="btn_majeur btn_vert btn_grand btn_upper_case">Valider</button>
                    </a>
                    <?php else: ?>
					<a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
						<img alt="Valider le contrat" src="/images/boutons/btn_signer.png">
					</a>
                    <?php endif; ?>
				<?php endif; ?>
                <?php if(!VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE) && $vrac->isBrouillon() && $user->_id == $vrac->vendeur_identifiant): ?>
                    <p>En attente de validation du projet par l'acheteur</p>
                <?php endif; ?>
                <?php if(!VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE) && $vrac->isProposition() && !$vrac->hasValide($user->_id)): ?>
                    <p>En attente de signature par le vendeur</p>
                <?php endif; ?>
				<?php if(!$vrac->isValide() && $user->_id && $vrac->hasValide($user->_id)): ?>
					<p>Vous avez signé le contrat le <strong><?php echo format_date($vrac->getUserDateValidation($user->_id), 'p', 'fr') ?></strong></p>
				<?php endif; ?>
				<?php if ($form): ?>
					<input type="image" src="/images/boutons/btn_valider_final.png" alt="Valider vos enlèvements" />
				<?php endif; ?>
				<?php if(!$form && $vrac->isCloture() && ! $vrac->isPapier()): ?>
					<p>Contrat vrac numéro de visa <?php echo $vrac->numero_archive ?>, cloturé le <strong><?php echo format_date($vrac->valide->date_cloture, 'p', 'fr') ?></strong></p>
				<?php endif; ?>
			</td>
		</tr>
	</table>
	<?php include_partial('popupConfirmeSignature'); ?>
	<?php include_partial('popupClotureContrat', array('vrac' => $vrac, 'validation' => $validation)); ?>
	<?php if ($form): ?>
	</form>
	<?php endif; ?>
	<?php include_partial('vrac/generationPdf', array('vrac' => $vrac)); ?>
    <?php if ($formApplication) include_partial('popupGenerationContratApplication', array('form' => $formApplication)); ?>
</div>
<script type="text/javascript">
$(document).ready(function()
{
	<?php if (($vrac->valide->statut == Vrac::STATUT_ENLEVEMENT) && $vrac->allProduitsClotures() && !$validation->hasErreurs()): ?>
	openPopup($("#popup_cloture_contrat"));
	<?php endif; ?>
	$('#onglets_majeurs .ui-tabs-selected a').focus();
});
</script>
