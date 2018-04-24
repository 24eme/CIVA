function(doc) {

	if (doc.type != "Vrac") {
		return;
	}

	if(doc.type_contrat != 'VRAC') {
        return;
    }

	var regexpDate = /-/g;
	var date = (doc.date_modification)? doc.date_modification : doc.valide.date_validation;
	date = (date)? date : doc.valide.date_saisie;
	var statut = doc.valide.statut;
	var type = doc.type_contrat;

	var numero_archive = doc.numero_archive;
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
	var montant_cotisation = 0;
	var montant_cotisation_paye = 0;
	var mode_de_paiement = null;
	var cvi_acheteur = doc.acheteur.civaba;
	var type_acheteur = null;
	var tca = null;
	var cvi_vendeur = doc.vendeur.cvi;
	var type_vendeur = null;
	var numero_contrat = doc.numero_db2;
	var daa = null;
	var date_arrivee = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_traitement = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_saisie = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_circulation = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var identifiant_courtier = (doc.mandataire.num_db2)? doc.mandataire.num_db2 : 0;
	var reccod = null;
	var total_volume_propose = (doc.volume_propose_total)? doc.volume_propose_total : 0;
	var total_volume_enleve = (doc.volume_enleve_total)? doc.volume_enleve_total : 0;
	var quantite_transferee = 0;
	var top_suppression = null;
	var top_instance = null;
	var nombre_contrats = 0;
	var heure_traitement = 0;
	var utilisateur = "TELEDECL";
	if(doc.papier) {
		utilisateur = "PAPIER";
	}
	var date_modif = (date)? (date).replace(regexpDate,"") : 0;
	var creation = (doc.date_export_creation)? 0 : 1;

	emit([type, statut, date], [numero_archive, type_contrat, mercuriales, montant_cotisation, montant_cotisation_paye, mode_de_paiement, cvi_acheteur, type_acheteur, tca, cvi_vendeur, type_vendeur, numero_contrat, daa, date_arrivee, date_traitement, date_saisie, date_circulation, identifiant_courtier, reccod, total_volume_propose, total_volume_enleve, quantite_transferee, top_suppression, top_instance, nombre_contrats, heure_traitement, utilisateur, date_modif, creation]);
}
