function(doc) {
	if (doc.type == "DS" && doc.mouts != null ) {
		if(!doc.identifiant.match(/^(67|68)/)) {
			return;
        	}

		if(doc.ds_principale === 0) {
			return;
		}
  		emit([doc.campagne, doc.validee != null, doc.modifiee != null, doc.validee, doc.num_etape], 1);
	}
}