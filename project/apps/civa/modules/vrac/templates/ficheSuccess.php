<?php use_helper('Date') ?>

<?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SUPPRESSION) && !$vrac->hasContratApplication()): ?>
	<div class="btn_header">
		<a style="padding-left: 30px; margin-bottom: 10px;" class="btn_majeur btn_noir" href="<?php echo url_for('vrac_supprimer', $vrac) ?>">
			<svg style="position: absolute; left: 10px;" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash" viewBox="0 0 16 16"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6z"/><path fill-rule="evenodd" d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1zM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118zM2.5 3V2h11v1h-11z"/></svg> <?php if($vrac->isValide()): ?>Annuler<?php else: ?>Supprimer<?php endif; ?>
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
	<li class="<?php if($vrac->isApplicationPluriannuel()): ?>ui-tabs<?php else: ?>ui-tabs-selected<?php endif; ?>" style="position: relative;">
		<a style="height: 18px; padding-left: 25px;" href="<?php echo url_for('vrac_fiche', $vrac->getContratDeReference()) ?>">
            <span style="position: absolute; left: 7px; top: 4px; font-size: 17px;" class="icon-<?php echo strtolower($vrac->type_contrat) ?>"></span>
		<?php if ($vrac->isValide()): ?>
			Contrat de <?php echo strtolower($vrac->type_contrat) ?> <?php if($vrac->getContratDeReference()->isPluriannuelCadre()): ?>pluriannuel <?php endif; ?><?php if ($vrac->getContratDeReference()->numero_archive): ?>(visa n° <?php echo $vrac->getContratDeReference()->numero_archive ?>)<?php endif; ?>
		<?php else: ?>
			Validation du contrat de <?php echo strtolower($vrac->type_contrat) ?> <?php if($vrac->isPluriannuelCadre()): ?>pluriannuel<?php endif; ?>
		<?php endif; ?>
		</a>
	</li>

    <?php if (count($contratsApplication)>0): ?>
		<?php foreach($contratsApplication as $numContratApplication => $contratApplication): ?>
			<?php if($contratApplication): ?>
                <li class="<?php if($contratApplication->_id == $vrac->_id): ?>ui-tabs-selected<?php else: ?>ui-tabs<?php endif; ?>">
                <a href="<?php echo url_for('vrac_fiche', $contratApplication) ?>" style="position: relative;"><?php echo $contratApplication->campagne ?></a></li>
            <?php elseif($formApplication && $numContratApplication == $formApplication->getObject()->numero_contrat && ($user && $user->_id == $vrac->createur_identifiant)): ?>
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


		<?php
            include_partial('vrac/soussignes', array('vrac' => $vrac, 'user' => $user, 'fiche' => true));
        ?>

		<?php
			if($validation->hasPoints()) {
				include_partial('global/validation', array('validation' => $validation));
			}
		?>
<hr class="printonly"/>
		<?php include_partial('vrac/produits', array('vrac' => $vrac, 'form' => $form, 'produits_hash_in_error' => $validation->getProduitsHashInError(), 'popup_saisie_prix' => $formSaisiePrix)) ?>

        <?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::FORCE_CLOTURE) && !$vrac->isPluriannuelCadre()): ?>
			<a class="noprint" style="float: right; bottom: 6px; color: #2A2A2A; text-decoration: none;margin-left: 10px;" onclick="return confirm('Êtes-vous sûr de vouloir forcer la clotûre de ce contrat ?');" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('vrac_forcer_cloture', $vrac) ?>">Forcer la clotûre  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#666" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><title>Action disponible uniquement en mode admin</title><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg></a>
		<?php endif; ?>
        <?php if(VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::FORCE_VALIDATION)): ?>
			<a class="noprint" style="float: right; bottom: 6px; color: #2A2A2A; text-decoration: none;" onclick="return confirm('Êtes-vous sûr de vouloir forcer la validation de ce contrat ?');" class="btn_majeur btn_petit btn_jaune" href="<?php echo url_for('vrac_forcer_validation', $vrac) ?>">Forcer la validation du contrat  <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="#666" class="bi bi-info-circle-fill" viewBox="0 0 16 16"><title>Action disponible uniquement en mode admin</title><path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/></svg></a>
        <?php endif; ?>

<?php if(!$vrac->isPapier()): ?>
<hr class="printonly"/>
<?php include_partial('vrac/ficheConditions', array('vrac' => $vrac, 'fiche' => true)); ?>
<?php endif; ?>

<?php include_partial('vrac/chronologie_contrat', array('vrac' => $vrac)); ?>

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
					<?php elseif($formApplication && $numContratApplication == $formApplication->getObject()->numero_contrat && ($user && $user->_id == $vrac->createur_identifiant)): ?>
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
                    <?php if ($vrac->isProjetAcheteur()): ?>
                    <a href="<?php echo url_for('vrac_refuser_projet', array('sf_subject' => $vrac)) ?>" style="margin-right: 50px;" onclick="return confirm('Etes-vous sûr de vouloir refuser ce projet de contrat ?')">
                        [x] Refuser le projet de contrat
                    </a>
                    <a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
                        <button class="btn_majeur btn_vert btn_grand btn_upper_case">Signer</button>
                    </a>
                    <?php else: ?>
					<?php if ($vrac->isApplicationPluriannuel()): ?>
						<a href="" style="margin-right: 50px;" onclick="return confirm('Etes-vous sûr de vouloir refuser ce projet de contrat ?')">
	                        [x] Refuser
	                    </a>
					<?php endif; ?>
					<a href="<?php echo url_for('vrac_validation', array('sf_subject' => $vrac)) ?>" id="signatureVrac">
                        <button class="btn_majeur btn_vert btn_grand btn_upper_case"><?php if($vrac->isApplicationPluriannuel()): ?>Valider<?php else: ?>Signer<?php endif; ?></button>
					</a>
                    <?php endif; ?>
				<?php endif; ?>
                <?php if(!VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE) && $vrac->isProjetVendeur() && $user->_id == $vrac->vendeur_identifiant): ?>
                    <p>En attente de validation du projet par l'acheteur</p>
                <?php elseif(!VracSecurity::getInstance($compte, $vrac)->isAuthorized(VracSecurity::SIGNATURE) && $vrac->isProjetAcheteur()): ?>
                    <p>En attente de signature par le vendeur</p>
                <?php endif; ?>
				<?php if(!$vrac->isValide() && $user->_id && $vrac->hasValide($user->_id) && !$vrac->isApplicationPluriannuel()): ?>
					<p>Vous avez signé le contrat le <strong><?php echo format_date($vrac->getUserDateValidation($user->_id), 'p', 'fr') ?></strong></p>
				<?php endif; ?>
                <?php if(!$vrac->isValide() && $user->_id && $vrac->hasValide($user->_id) && $vrac->isApplicationPluriannuel()): ?>
					<p>Vous avez validé le contrat le <strong><?php echo format_date($vrac->getUserDateValidation($user->_id), 'p', 'fr') ?></strong></p>
				<?php endif; ?>
				<?php if ($form): ?>
                    <button type="submit" class="btn_majeur btn_vert btn_grand btn_upper_case">Valider vos enlèvements</button>
				<?php endif; ?>
				<?php if(!$form && $vrac->isCloture() && ! $vrac->isPapier()): ?>
					<p>Contrat vrac <?php if($vrac->isPapier()): ?>papier<?php else: ?>télédéclaré<?php endif; ?> numéro de visa <?php echo $vrac->numero_archive ?>, cloturé le <strong><?php echo format_date($vrac->valide->date_cloture, 'p', 'fr') ?></strong></p>
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
    <?php if ($formApplication) include_partial('popupGenerationContratApplication', array('form' => $formApplication, 'validation' => $validationApplication, 'vrac' => $vrac, 'id' => "popup_generation_contratApplication")); ?>
    <?php if ($formSaisiePrix) include_partial('popupGenerationContratApplication', array('form' => $formSaisiePrix, 'validation' => null, 'vrac' => $vrac, 'id' => "popup_saisieprix_contratApplication")); ?>
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
