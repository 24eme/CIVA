function(doc) {
if (doc.type != "Vrac") {
	return;
}
  emit([doc.createur_identifiant, doc.campagne], doc);
}