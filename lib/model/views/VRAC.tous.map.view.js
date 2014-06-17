function(doc) {
	if (doc.type != "Vrac") {
		return;
	}

	var date = doc.valide.date_saisie;
	var statut = doc.valide.statut;
	var vendeur = {identifiant: doc.vendeur_identifiant, intitule: doc.vendeur.intitule, raison_sociale: doc.vendeur.raison_sociale, date_validation: doc.valide.date_validation_vendeur};
	var acheteur = {identifiant: doc.acheteur_identifiant, intitule: doc.acheteur.intitule, raison_sociale: doc.acheteur.raison_sociale, date_validation: doc.valide.date_validation_acheteur};
	var mandataire = {identifiant: doc.mandataire_identifiant, intitule: doc.mandataire.intitule, raison_sociale: doc.mandataire.raison_sociale, date_validation: doc.valide.date_validation_mandataire};
	var numero = doc.numero_contrat;
	var numero_visa = doc.numero_visa;
	var etape = doc.etape;
	var type_contrat = doc.type_contrat;
	
	if (doc.vendeur_identifiant) {
		var is_proprietaire = (doc.vendeur_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.vendeur_identifiant, type_contrat, doc.campagne, doc.valide.statut], {role: "vendeur", soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, numero_visa: numero_visa, etape: etape, type_contrat: type_contrat});
	}
	if (doc.acheteur_identifiant) {
		var is_proprietaire = (doc.acheteur_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.acheteur_identifiant, type_contrat, doc.campagne, doc.valide.statut], {role: "acheteur", soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, numero_visa: numero_visa, etape: etape, type_contrat: type_contrat});
	}
	if (doc.mandataire_identifiant) {
		var is_proprietaire = (doc.mandataire_identifiant == doc.createur_identifiant)? 1 : 0;
		emit([doc.mandataire_identifiant, type_contrat, doc.campagne, doc.valide.statut], {role: "mandataire", soussignes: {vendeur: vendeur, acheteur: acheteur, mandataire: mandataire}, is_proprietaire: is_proprietaire, date: date, statut: statut, numero: numero, numero_visa: numero_visa, etape: etape, type_contrat: type_contrat});
	}
}