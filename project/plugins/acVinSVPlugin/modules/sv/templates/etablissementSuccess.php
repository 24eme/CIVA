<ol class="breadcrumb">
    <li><a href="<?php echo url_for('sv') ?>">SV11 / SV12</a></li>
    <li class="active"><a href="<?php echo url_for('sv_etablissement', array('identifiant' => $etablissement->identifiant)) ?>"><?php echo $etablissement->nom ?> (<?php echo $etablissement->identifiant ?>)</a></li>
</ol>

