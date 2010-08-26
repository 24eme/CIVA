<p>Traitement en cours ...</p>

<script type="text/javascript">
<?php if ($url instanceof sfOutputEscaperArrayDecorator): ?>
    <?php $url = $url->getRawValue() ?>
<?php endif; ?>
document.location.href='<?php echo url_for($url) ?>'
</script>