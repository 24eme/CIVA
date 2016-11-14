<div style="padding: 0 20px">
    <h2 style="margin-top: 0px;">Modifications d'établissements</h2>
    <p><?php echo count($diff); ?> établissements modifiés</p>
</div>

<table class="table table-bordered table-condensed table-hover">
    <thead>
    <tr>
        <th></th>
        <th>Famille</th>
        <th>Intitulé</th>
        <th>Raison sociale</th>
        <th>CVI</th>
        <th>CIVABA</th>
        <th>Adresse</th>
        <th>Code postal</th>
        <th>Commune</th>
        <th>Tél</th>
        <th>Fax</th>
        <th>Email</th>
    </tr>
    </thead>
    <tbody>
<?php $num = 1; ?>
<?php $keyIgnored = array(0,1,2,4,9,10,11,15,16,17,18,20); ?>
<?php foreach($diff as $id => $value): ?>
<?php $etablissementDb2 = (isset($etablissementsDb2[$id])) ? $etablissementsDb2[$id] : null ; ?>
<?php $etablissementCouchdb = (isset($etablissementsCouchdb[$id])) ? $etablissementsCouchdb[$id] : null ; ?>
<?php $etablissementReference = ($etablissementCouchdb) ? $etablissementCouchdb : $etablissementDb2; ?>
<?php $idEtablissementCorrespondant = null; ?>
<?php if($etablissementReference[0] == "ETABLISSEMENT_EXPLOITANT"): ?>
<?php $idEtablissementCorrespondant = str_replace("_EXPLOITANT", "", $id); ?>
<?php $etablissementCorrespondant = $etablissementsCouchdb[$idEtablissementCorrespondant]; ?>
<?php endif; ?>
<?php if($idEtablissementCorrespondant && !isset($diff[$idEtablissementCorrespondant])): ?>
    <tr <?php if($num % 2): ?>style="background: #fff;"<?php endif; ?>>
        <td><span title="Établissement" class="glyphicon glyphicon-home"></span></td>
        <?php foreach($etablissementCorrespondant as $key => $null): ?>
            <?php if(in_array($key, $keyIgnored)): continue; endif; ?>
            <td><small><?php echo str_replace("PRODUCTEUR_VINIFICATEUR", "PRODUCTEUR<br />VINIFICATEUR", $etablissementCorrespondant[$key]); ?></small></td>
        <?php endforeach; ?>
    </tr>
<?php elseif($idEtablissementCorrespondant): ?>
<?php $num = $num - 1; ?>
<?php endif; ?>
<tr <?php if($num % 2): ?>style="background: #fff;"<?php endif; ?>>
    <td><span title="<?php if(!$idEtablissementCorrespondant): ?>Etablissement<?php else: ?>Exploitant<?php endif; ?>" class="glyphicon <?php if(!$idEtablissementCorrespondant): ?>glyphicon-home<?php else: ?>glyphicon-user<?php endif; ?>"</td>
    <?php foreach($etablissementReference as $key => $null): ?>
        <?php if(in_array($key, $keyIgnored)): continue; endif; ?>
        <?php $isDiff = (trim($etablissementDb2[$key]) != trim($etablissementCouchdb[$key])); ?>
        <td class="<?php if($isDiff): ?>danger<?php endif; ?>">
            <small>
            <?php if(!$isDiff): ?>
                <?php echo str_replace("PRODUCTEUR_VINIFICATEUR", "PRODUCTEUR<br />VINIFICATEUR", $etablissementReference[$key]); ?>
            <?php else: ?>
            <?php if($etablissementDb2[$key]): ?>
                <span style="text-decoration:line-through;"><?php echo str_replace("PRODUCTEUR_VINIFICATEUR", "PRODUCTEUR<br />VINIFICATEUR", $etablissementDb2[$key]); ?></span><br />
            <?php endif; ?>
            <strong><?php echo str_replace("PRODUCTEUR_VINIFICATEUR", "PRODUCTEUR<br />VINIFICATEUR", $etablissementCouchdb[$key]); ?></strong>
            <?php endif; ?>
            </small>
        </td>
    <?php endforeach; ?>
</tr>
<?php $num++; ?>
<?php endforeach; ?>
    </tbody>
</table>
