function(doc) {

  if (doc.type && doc.type == "DS" && doc.validee && doc.modifiee && !doc.import_db2)  {
	emit([doc.periode.substring(0,4), doc.declaration_insee, doc.identifiant], 1);
  }
}