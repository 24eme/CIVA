<?php if (!$auto): ?>
<div style="text-align: center">
<a style="color: #2A2A2A; text-decoration: none;" class="btn_majeur btn_petit btn_jaune" id="lien_autorisation" href="<?php echo url_for('declaration_transmission', array('url' => $url, 'id' => $id)) ?>">J'autorise la transmission de ma declaration de récolte</a>
</div>
<?php endif; ?>
<p id="loader_autorisation" style="<?php if (!$auto): ?>display: none;<?php endif; ?>">Transmission en cours...</p>
<script type="text/javascript">
$(document).ready(function() {
    <?php if ($auto): ?>
        document.location.href = "<?php echo url_for('declaration_transmission', array('url' => $url, 'id' => $id)) ?>";
    <?php else: ?>
        $('#lien_autorisation').click(function() {
            $('#lien_autorisation').hide();
            $('#loader_autorisation').show();
        });
    <?php endif; ?>
});
</script>