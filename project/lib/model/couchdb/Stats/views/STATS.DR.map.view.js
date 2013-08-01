function(doc) {
	if (doc.type == "DR") {
  		emit([doc.campagne, doc.validee != null, doc.modifiee != null, doc.validee, doc.etape], 1);
	}
}