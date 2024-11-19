<form action="<?php echo url_for('dr_autorisation', array('url' => $url, 'id' => $id)) ?>" method="post">
<div style="text-align: center">
    <div id="block_autorisation">
    <h1>Transmission numérique de Déclaration de récolte</h1>
    <br/><br/>
    <p>Si vous souhaitez transmettre numériquement votre déclaration de récolte à l'AVA, merci de cliquer sur le bouton suivant :<p>
    <br/><br/>
    <button type="submit" style="color: #2A2A2A; text-decoration: none;" class="btn_majeur btn_petit btn_jaune" id="lien_autorisation" href="<?php echo url_for('dr_autorisation', array('url' => $url, 'id' => $id)) ?>">J'autorise la transmission de ma declaration de récolte</a>
     </div>
</div>
</form>
<div id="loader_autorisation" style="display: none;">
<div style="width: 758px; margin: 200px auto;">
     <h2 style="text-align: center; font-size: 24px; color: #848C03; font-weight: normal;">Le transfert de votre Déclaration de Récolte est en cours</h2>
     <img title="Transmission vers l'ava en cours..." src="/images/loader/civa2ava.gif" style="height: 162px;">
     <p style="text-align: center; color: #848C03;">Veuillez patientez...</p>
</div>
</div>
<script type="text/javascript">
$(document).ready(function() {
        $('#lien_autorisation').click(function() {
            $('#block_autorisation').hide();
            $('#loader_autorisation').show();
        });
});
</script>
