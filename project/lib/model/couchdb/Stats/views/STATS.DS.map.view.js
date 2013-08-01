function(doc) {
	if (doc.type == "DS" && doc.mouts != null) {
	  emit([doc.campagne, doc.validee != null, doc.modifiee != null, doc.validee, doc.num_etape], 1);
	}
}