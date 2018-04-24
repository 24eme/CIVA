function(doc) {

	if (doc.type != "Vrac") {
		return;
	}

	if(doc.type_contrat != 'BOUTEILLE') {
        return;
    }

	var regexpDate = /-/g;
	var date = (doc.valide.date_validation)? doc.valide.date_validation : doc.valide.date_saisie;
	var statut = doc.valide.statut;
	var type = doc.type_contrat;
	var numero_archive = doc.numero_archive;
	var cvi_acheteur = doc.acheteur.civaba;
	var type_acheteur = null;
	var cvi_vendeur = doc.vendeur.cvi;
	var type_vendeur = null;
	var identifiant_courtier = (doc.mandataire.num_db2)? doc.mandataire.num_db2 : 0;
	var daa = null;
	var total_volume_enleve = (doc.volume_enleve_total)? doc.volume_enleve_total : 0;
	var numero_contrat = doc.numero_db2;
	var date_arrivee = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_contrat = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_traitement = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var date_modification = (doc.valide.date_validation)? (doc.valide.date_validation).replace(regexpDate,"") : 0;
	var top_suppression = null;
	var top_instance = null;
	var utilisateur = "TELEDECL";
	if(doc.papier) {
		utilisateur = "PAPIER";
	}
	var date_export_creation = doc.date_export_creation;

	emit([type, statut, date], [numero_archive, cvi_vendeur, type_acheteur, cvi_acheteur, type_vendeur, identifiant_courtier, daa, total_volume_enleve, numero_contrat, date_arrivee, date_contrat, date_traitement, date_modification, top_instance, top_suppression, utilisateur, date_export_creation]);
}
