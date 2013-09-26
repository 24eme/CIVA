function(doc) {
    if (doc.type == "DS" && doc.mouts != null && doc.utilisateurs && doc.utilisateurs.edition) {
      
	if(!doc.identifiant.match(/^(67|68)/)) {
		return;
	}

	if(doc.ds_principale === 0) {
		return;
	}

	for (u  in doc.utilisateurs.edition) {
         emit([doc.campagne, u], 1);
       	 break;
        }
    }   
}