function(doc) { 
    if (doc.type != 'Acheteur' && doc.type != 'Recoltant') {

    	return;
    }

    if (!doc.acheteur_dr) {

        return;
    }
    
    if(doc.commune) {
	commune = doc.commune;
    } else {
	commune = doc.siege.commune;
    }

    emit([doc.qualite, doc.nom], {cvi: doc.cvi, nom: doc.nom, commune: commune});
}