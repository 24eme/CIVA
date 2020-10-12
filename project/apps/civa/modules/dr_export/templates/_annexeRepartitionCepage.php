<?php use_helper('Float'); ?>
<?php use_helper('drExport'); ?>
<style>
table {
  padding-left: 0px;
}
</style>
<br /><br />
<span style="text-align: center; font-size: 12pt; font-weight:bold;">ANNEXE</span>
<br /><br />
<?php
$dontvci = 0;
foreach($acheteursCepage as $acheteurCepage) {
    foreach ($acheteurCepage["acheteurs"] as $type_key => $acheteurs_type) {
        foreach($acheteurs_type as $cvi => $a) {
            $dontvci += $a->dontvci;
        }
    }
}
?>
<span style="background-color: black; color: white; font-weight: bold;">AOC Alsace Blanc - Répartition des achats par cépage</span><br/>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
    <tr style="font-weight: bold;"><th style="border: 1px solid black;width: 110px; text-align: left;">&nbsp;Produit</th><th style="border: 1px solid black;width: 95px;">N° CVI</th><th style="border: 1px solid black;width: 295px;">Raison sociale</th><th style="width: 100px;border: 1px solid black;">Superficie</th><th style="border: 1px solid black;width: 100px;">Volume</th><th style="border: 1px solid black;width: 120px;">Dont dépassement</th><?php if($dr->recolte->canHaveVci() && $dontvci > 0): ?><th style="border: 1px solid black;width: 100px;">Dont VCI</th><?php endif; ?></tr>
    <?php foreach($acheteursCepage as $acheteurCepage): ?>
        <?php foreach ($acheteurCepage["acheteurs"] as $type_key => $acheteurs_type) : ?>
            <?php foreach($acheteurs_type as $cvi => $a) : ?>
            <tr>
                <td style="border: 1px solid black;width: 110px; text-align: left;">&nbsp;<?php echo $acheteurCepage['libelle']; ?></td>
                <td style="border: 1px solid black;width: 95px;"><?php echo $cvi; ?></td>
                <td style="border: 1px solid black;width: 295px;">
                    <?php echo $a->nom.' - '.$a->commune; ?>
                    <?php if ($type_key == 'mouts'): ?>
                        <br />
                        <small><i>(Acheteur de mouts)</i></small>
                    <?php endif; ?>
                </td>
                <td style="border: 1px solid black;width: 100px; text-align: right;"><?php echoSuperficie($a->superficie); ?></td>
                <td  style="border: 1px solid black;width: 100px; text-align: right;"><?php echoVolume($a->volume); ?></td>
                <td style="border: 1px solid black;width: 120px; text-align: right;"><?php echoVolume($a->dontdplc); ?></td>
                <?php if($dr->recolte->canHaveVci() && $dontvci > 0): ?>
                    <td style="border: 1px solid black;width: 100px; text-align: right;"><?php echoVolume($a->dontvci); ?></td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
    <?php endforeach; ?>
</table>
