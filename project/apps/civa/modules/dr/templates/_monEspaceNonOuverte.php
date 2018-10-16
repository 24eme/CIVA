<?php use_helper('Date'); ?>

<h3 class="titre_section">Déclaration de l'année <a href="" class="msg_aide" rel="help_popup_mon_espace_civa_ma_dr" title="Message aide"></a></h3>
<div class="contenu_section">
    <p class="intro">Le Téléservice pour la déclaration de récolte <?php echo $campagne; ?> sera ouvert à partir du <?php echo format_date(DRClient::getInstance()->getDateOuverture()->format('Y-m-d'), "dd MMMM", "fr_FR"); ?>.</p>
</div>
