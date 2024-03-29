 <h2 class="titre_principal">Mercuriale</h2>
 
 <form action="<?php echo url_for('@mercuriales') ?>" method="post" id="principal">
	<?php echo $form->renderHiddenFields() ?>
	<?php echo $form->renderGlobalErrors() ?>

    <div class="clearfix" id="application_dr">
        <div id="nouvelle_declaration" style="width: 504px;">
        	<h3 class="titre_section">Transactions en vrac</h3>
            <div class="contenu_section">
            	<p class="intro">Mercuriales générées à partir des données jusqu'au <?php echo date('d/m/Y') ?></p>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['start_date']->renderError() ?>
                    <?php echo $form['start_date']->renderLabel() ?>
                    <?php echo $form['start_date']->render(array('class' => 'datepicker', 'style' => 'width: 172px;')) ?>
                </div>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['end_date']->renderError() ?>
                    <?php echo $form['end_date']->renderLabel() ?>
                    <?php echo $form['end_date']->render(array('class' => 'datepicker', 'style' => 'width: 172px;')) ?>
                </div>
                <div class="ligne_form ligne_form_label">
                    <?php echo $form['filtres']->renderError() ?>
                    <?php echo $form['filtres']->renderLabel() ?>
                    <ul class="checkbox_list" style="width: 190px; display: inline-block;">
                    <?php echo $form['filtres']->render() ?>
                    </ul>
                </div>
                <style>
                #statistiquesMercuriales_mercuriale {
                    display: block !important;
                }
                </style>
                <div class="ligne_form ligne_btn">
                    <input type="image" alt="Valider" src="/images/boutons/btn_valider.png" name="boutons[valider]" class="btn">
                </div>
			</div>
		</div>
		<?php if (count($pdfs) > 0): ?>
		<div id="precedentes_declarations" style="width: 504px; margin: 5px;">
            <h3 class="titre_section">Mercuriales Générées</h3>
            <div class="contenu_section">
                <ul>
                	<?php foreach ($pdfs as $pdf): ?>
                    <li><span style="margin-right: 5px;">[<a href="<?php echo url_for('mercuriales_delete', array('mercuriale' => str_replace('_mercuriales.pdf', '', $pdf)))?>" style="background: none;padding:0;">x</a>]</span><a id="<?php echo str_replace('_mercuriales.pdf', '', $pdf) ?>" href="<?php echo url_for('mercuriales_pdf', array('mercuriale' => str_replace('_mercuriales.pdf', '', $pdf)))?>" download="<?php echo $pdf ?>"><?php echo $pdf ?></a><?php if(file_exists(sfConfig::get('sf_data_dir').'/mercuriales/pdf/'.str_replace('.pdf', '.csv', $pdf))): ?> - <a style="background: none;padding:0;font-size:80%;color:#666;" href="<?php echo url_for('mercuriales_csv', array('mercuriale' => str_replace('_mercuriales.pdf', '', $pdf)))?>">données CSV</a><?php endif; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
		<?php endif; ?>
    </div>
</form>
<style>
#nouvelle_declaration .ligne_form_label .checkbox_list label {
width: 165px;
}
#nouvelle_declaration .ligne_form_label .checkbox_list input {
float: left;
margin-right: 5px;
}
#nouvelle_declaration .ligne_form_label .checkbox_list li {
margin-bottom: 2px;
display: inline-block;
}
</style>
<script type="text/javascript">
var params = new URLSearchParams(location.search);
if (params.has('dl')) {
	var lien = document.getElementById(''+params.get('dl'));
	if (lien) {
		lien.click();
	}
}
</script>
