<h2>INFORMATIONS</h2>

<ul>
	<li>Nom : <strong><?php echo $tiers->nom ?></strong></li>
	<li>Siret : <strong><?php echo $tiers->siret ?></strong></li>
	<li>Téléphone : <strong><?php echo $tiers->telephone ?></strong></li>
	<li>Fax : <strong><?php echo $tiers->fax ?></strong></li>
	<li>Adresse : <strong><?php echo $tiers->siege->adresse ?></strong></li>
	<li>Code postal : <strong><?php echo $tiers->siege->code_postal ?></strong></li>
	<li>Commune : <strong><?php echo $tiers->siege->commune ?></strong></li>
</ul>