<h3 class="titre_section">Export CSV des Tiers</h3>
<div class="contenu_section">
    <ul>
        <li><a href="<?php echo url_for('@csv_tiers') ?>">Tous</a></li>
        <li><a href="<?php echo url_for('@csv_tiers_dr_en_cours') ?>">Déclaration en cours</a></li>
        <li><a href="<?php echo url_for('@csv_tiers_non_validee_civa') ?>">Non validée par le CIVA</a></li>
        <!--<li><a href="<?php echo url_for('@csv_tiers_modifications') ?>">Modifications</a></li>-->
        <li><a href="<?php echo url_for('@csv_tiers_modifications_email') ?>">Email Modifications</a></li>
    </ul>
</div>