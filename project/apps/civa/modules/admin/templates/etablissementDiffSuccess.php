    <div style="padding: 0 20px">
        <h2 style="margin-top: 0px;">Modifications d'établissements</h2>
        <p><?php echo count($diff); ?> établissements modifiés</p>
    </div>

<table class="table table-bordered table-condensed table-striped table-hover">
    <thead>
    <tr>
        <th>Famille</th>
        <th>N°Tiers</th>
        <th>Intitulé</th>
        <th>Raison sociale</th>
        <th>CVI</th>
        <th>CIVABA</th>
        <th>SIRET</th>
        <th>N° ACCISES</th>
        <th>Adresse</th>
        <th>Code postal</th>
        <th>Commune</th>
        <th>Tél</th>
        <th>Fax</th>
        <th>Email</th>
        <th>Exploitant Civilité</th>
        <th>Exploitant Nom</th>
        <th>Exploitant Adresse</th>
        <th>Exploitant Code Postal</th>
        <th>Exploitant Commune</th>
        <th>Exploitant Téléphone</th>
    </tr>
    </thead>
    <tbody>
<?php $keyIgnored = array(0,1,2,4,12,15,16,17,18,20,29); ?>
<?php $keyIgnored = array(); ?>
<?php foreach($diff as $id => $value): ?>
    <?php $etablissementDb2 = (isset($etablissementsDb2[$id])) ? $etablissementsDb2[$id] : null ; ?>
    <?php $etablissementCouchdb = (isset($etablissementsCouchdb[$id])) ? $etablissementsCouchdb[$id] : null ; ?>
    <?php $etablissementReference = ($etablissementCouchdb) ? $etablissementCouchdb : $etablissementDb2; ?>
    <tr>
        <?php foreach($etablissementReference as $key => $null): ?>
            <?php if(in_array($key, $keyIgnored)): continue; endif; ?>
            <?php $isDiff = (trim($etablissementDb2[$key]) != trim($etablissementCouchdb[$key])); ?>
            <td class="<?php if($isDiff): ?>danger<?php endif; ?>">
                <small>
                <?php if(!$isDiff): ?>
                    <?php echo $etablissementReference[$key]; ?>
                <?php else: ?>
                <?php if($etablissementDb2[$key]): ?>
                    <span style="text-decoration:line-through;"><?php echo $etablissementDb2[$key]; ?></span><br />
                <?php endif; ?>
                <strong><?php echo $etablissementCouchdb[$key]; ?></strong>
                <?php endif; ?>
                </small>
            </td>
        <?php endforeach; ?>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
