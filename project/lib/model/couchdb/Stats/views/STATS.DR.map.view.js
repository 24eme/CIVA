function(doc) {
	if (doc.type == "DR") {
		if(!doc.cvi.match(/^(67|68)/)) {
			return;
      		} 
  		emit([doc.campagne, doc.validee != null, doc.modifiee != null, doc.validee, doc.etape], 1);
	}
}