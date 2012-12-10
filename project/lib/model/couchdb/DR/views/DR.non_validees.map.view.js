function(doc) {
	if (doc.type == "DR" && !doc.validee) {	
		emit([doc.campagne, doc.cvi], 1);
	}
}
