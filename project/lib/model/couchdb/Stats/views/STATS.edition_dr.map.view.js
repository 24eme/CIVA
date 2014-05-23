function(doc) {
    if (doc.type == "DR" && doc.utilisateurs&& doc.utilisateurs.edition) {
      	if(!doc.cvi.match(/^(67|68)/)) {
			return;
      	} 

      	for (u  in doc.utilisateurs.edition) {
        	emit([doc.campagne, u], 1);
			if(u == "csv") {
				continue;
			}
        	break;
      	}
    }   
}