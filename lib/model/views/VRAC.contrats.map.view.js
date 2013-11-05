function(doc) {
	
	if (doc.type != "Vrac") {
		return;
	}

	var date = (doc.date_modification)? doc.date_modification : doc.valide.validation;
	var statut = doc.valide.statut;
	
	var numero_archive = doc.numero;
	var type_contrat = "P";
	var mercuriales = "M";
	if (doc.vendeur_type == 'caves_cooperatives') {
		mercuriales = "C";
	}
	if (doc.vendeur_type == 'negociants') {
		mercuriales = "X";
	}
	if (doc.acheteur_type == 'recoltants') {
		mercuriales = "V";
	}
	var montant_cotisation = null;
	var montant_cotisation_paye = null;
	var mode_de_paiement = null;
	var cvi_acheteur = doc.acheteur.civaba;
	var type_acheteur = null;
	var tca = null;
	var cvi_vendeur = doc.vendeur.cvi;
	var type_vendeur = null;
	var numero_contrat = doc.numero_archive;
	var daa = null;
	var date_arrivee = doc.date_saisie;
	var date_traitement = doc.date_saisie;
	var date_saisie = doc.valide.validation;
	var date_circulation = doc.valide.validation;
	var identifiant_courtier = doc.mandataire.siret;
	var reccod = null;
	var total_volume_propose = doc.volume_propose_total;
	var total_volume_enleve = doc.volume_enleve_total;
	var quantite_transferee = null;
	var top_suppression = null;
	var top_instance = null;
	var nombre_contrats = null;
	var heure_traitement = null;
	var utilisateur = "TELEDECL";
	var date_modif = date;
	
	emit([statut, date], [numero_archive, type_contrat, mercuriales, montant_cotisation, montant_cotisation_paye, mode_de_paiement, cvi_acheteur, type_acheteur, tca, cvi_vendeur, type_vendeur, numero_contrat, daa, date_arrivee, date_traitement, date_saisie, date_circulation, identifiant_courtier, reccod, total_volume_propose, total_volume_enleve, quantite_transferee, top_suppression, top_instance, nombre_contrats, heure_traitement, utilisateur, date_modif]);
}