<form action="" method="post">
<div style="text-align: center">
    <button type="submit" style="color: #2A2A2A; text-decoration: none;" class="btn_majeur btn_petit btn_jaune" id="lien_autorisation" href="<?php echo url_for('declaration_transmission', array('url' => $url, 'id' => $id)) ?>">J'autorise la transmission de ma declaration de récolte</a>
</div>
</form>
<p id="loader_autorisation" style="display: none;">Transmission en cours...</p>
<script type="text/javascript">
$(document).ready(function() {
        $('#lien_autorisation').click(function() {
            $('#lien_autorisation').hide();
            $('#loader_autorisation').show();
        });
});
</script>