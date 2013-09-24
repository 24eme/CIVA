function(doc) {
	if (doc.type != "Vrac") {
		return;
	}

	var date = (doc.valide.date_validation)? doc.valide.date_validation : doc.valide.date_saisie;
	var statut = doc.valide.statut;
	var vendeur = {identifiant: doc.vendeur_identifiant, raison_sociale: doc.vendeur.raison_sociale, date_validation: doc.valide.date_validation_vendeur};
	var acheteur = {identifiant: doc.acheteur_identifiant, raison_sociale: doc.acheteur.raison_sociale, date_validation: doc.valide.date_validation_acheteur};
	var mandataire = {identifiant: doc.mandataire_identifiant, raison_sociale: doc.mandataire.raison_sociale, date_validation: doc.valide.date_validation_mandataire};
	var numero = doc.numero_contrat;
	var etape = doc.etape;
	
	if (doc.vendeur_identifiant) {
		var is_proprietaire = (doc.vendeur_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.vendeur_identifiant, doc.campagne, date], {soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, etape: etape});
	}
	if (doc.acheteur_identifiant) {
		var is_proprietaire = (doc.acheteur_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.acheteur_identifiant, doc.campagne, date], {soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, etape: etape});
	}
	if (doc.mandataire_identifiant) {
		var is_proprietaire = (doc.mandataire_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.mandataire_identifiant, doc.campagne, date], {soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, etape: etape});
	}
}