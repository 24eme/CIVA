function(doc) {
	if (doc.type != "Vrac") {
		return;
	}

	var date = doc.valide.date_saisie;
	var id = doc._id;
	var valide = (doc.valide.date_validation)? 1 : 0;
	var statut = doc.valide.statut;
	var email_validation = (doc.valide.email_validation)? 1 : 0;
	var email_relance = (doc.valide.email_relance)? 1 : 0;

	emit([valide, email_validation, email_relance], [date, statut, id]);
}