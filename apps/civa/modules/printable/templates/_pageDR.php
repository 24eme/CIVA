<style>
.tableau td, .tableau th, .tableau table {border: 1px solid black; }
pre {display: inline;}
</style>

<span style="background-color: black; color: white; font-weight: bold;">Exploitation</span><br/>
<table style="border: 1px solid black;"><tr><td>
<table border="0">
  <tr><td>N° CVI : <i><?php echo $recoltant->cvi; ?></i></td><td>Nom : <i><?php echo $recoltant->intitule.' '.$recoltant->nom; ?></i></td></tr>
  <tr><td>SIRET : <i><?php echo $recoltant->siret; ?></i></td><td>Adresse : <i><?php echo $recoltant->siege->adresse; ?></i></td></tr>
  <tr><td>Régime Fiscal : <i><?php echo $recoltant->regime_fiscal; ?></i></td><td>Commune : <i><?php echo $recoltant->siege->code_postal." ".$recoltant->siege->commune; ?></i></td></tr>
  <tr><td>Tel. : <i><?php echo $recoltant->telephone; ?></i></td><td>Fax : <i><?php echo $recoltant->fax; ?></i></td></tr>
</table>
</td></tr></table>

<span style="background-color: black; color: white; font-weight: bold;">Gestionnaire de l exploitation</span><br/>
<table style="border: 1px solid black;"><tr><td>
<table border="0" style="margin: 0px; padding: 0px;">
  <tr><td>Nom et prénom : <i><?php echo $recoltant->exploitant->nom; ?></i></td><td>Né le <i><?php echo $recoltant->exploitant->date_naissance; ?></i></td></tr>
  <tr><td>Adresse complete : <i><?php echo $recoltant->exploitant->adresse.', '.$recoltant->exploitant->code_postal.' '.$recoltant->exploitant->commune; ?></i></td><td>Tel. <i><?php echo $recoltant->exploitant->telephone; ?></i></td></tr>
</table>
</td></tr></table>

  <div><span style="background-color: black; color: white; font-weight: bold;"><?php echo $appellation_lieu->getLibelleWithAppellation(); ?></span></div>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
<tr><th style="text-align: left; font-weight: bold;">Cépage</th><td>Pinot</td><td>Pinot</td></tr>
<tr><th style="text-align: left; font-weight: bold;">Dénom. complém.</th><td>&nbsp;</td><td>Noir</td></tr>
<tr><th style="text-align: left; font-weight: bold;">Superficie</th><td class="number">75,35&nbsp;<small>ares</small></td><td class="number">75,35&nbsp;<small>ares</small></td></tr>
<tr><th style="text-align: left; font-weight: bold;">Récolte totale</th><td class="number">52,09&nbsp;<small>hl</small></td><td class="number">52,09&nbsp;<small>hl</small></td></tr>
<tr><th style="text-align: left; font-weight: bold;">Vente à Dopff du Moulin</th><td class="number">52,09&nbsp;<small>hl</small></td><td class="number">52,09&nbsp;<small>hl</small></td></tr>
</table>

<div style="margin-top: 20px;">
<div><span style="background-color: black; color: white; font-weight: bold;">Identification des acheteurs et caves coopératives</span></div>
<table border=1 cellspacing=0 cellpaggind=0 style="text-align: center; border: 1px solid black;">
  <tr style="font-weight: bold;"><th style="width: 100px;">N° CVI</th><th style="width: 300px;">Raison social (commune)</th><th style="width: 100px;">Surface</th><th style="width: 120px;">Vente raisins</th><th style="width: 100px;">dont DPLC</th></tr>
<tr><td style="width: 100px;">6827780010</td><td style="width: 300px;">Dopff du Moulin (68340 Riquemihr)</td><td style="width: 100px;">&nbsp;</td><td  style="width: 120px;">65,00&nbsp;<small>hl</small></td><td style="width: 100px;">&nbsp;</td></tr>
</table>
</div>
</div>


