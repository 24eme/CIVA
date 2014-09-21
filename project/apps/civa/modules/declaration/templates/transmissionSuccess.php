<p>Transmission en cours...</p>
<form id="form_transmission" method="post" action="<?php echo $url ?>">
    <input type="hidden" name="csv" value="<?php echo $csv ?>" />
    <input type="hidden" name="pdf" value="<?php echo $pdf ?>" />
</form>
<script type="text/javascript">
$(document).ready(function() {
    $('#form_transmission').submit();
});
</script>