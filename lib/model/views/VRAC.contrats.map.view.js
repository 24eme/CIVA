function(doc) {
	
	if (doc.type != "Vrac") {
		return;
	}

	var date = doc.date_modification;
	var statut = doc.valide.statut;
	
	var numero_archive = doc.numero_archive;
	var type_contrat = "P";
	var mercuriales = null;
	var montant_cotisation = null;
	var montant_cotisation_paye = null;
	var mode_de_paiement = null;
	var cvi_acheteur = doc.acheteur.cvi;
	var type_acheteur = null;//doc.acheteur_type;
	var tca = null;
	var cvi_vendeur = doc.vendeur.cvi;
	var type_vendeur = null;//doc.vendeur_type;
	var numero_contrat = doc.numero_contrat;
	var daa = null;
	var date_arrivee = null;
	var date_traitement = null;
	var date_saisie = doc.valide.date_saisie;
	var date_circulation = null;
	var identifiant_courtier = doc.mandataire.siret;
	var reccod = null;
	var total_volume_propose = doc.volume_propose_total;
	var total_volume_enleve = doc.volume_enleve_total;
	var quantite_transferee = null;
	var top_suppression = null;
	var top_instance = null;
	var nombre_contrats = null;
	var heure_traitement = null;
	var utilisateur = null;
	var date_modif = null;
	
	emit([statut, date], [numero_archive, type_contrat, mercuriales, montant_cotisation, montant_cotisation_paye, mode_de_paiement, cvi_acheteur, type_acheteur, tca, cvi_vendeur, type_vendeur, numero_contrat, daa, date_arrivee, date_traitement, date_saisie, date_circulation, identifiant_courtier, reccod, total_volume_propose, total_volume_enleve, quantite_transferee, top_suppression, top_instance, nombre_contrats, heure_traitement, utilisateur, date_modif]);
}