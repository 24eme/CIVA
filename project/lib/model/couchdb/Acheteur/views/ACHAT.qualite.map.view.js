function(doc) {
    if (doc.type != 'Etablissement') {

    	return;
    }

    if (!doc.acheteur_raisin) {

        return;
    }

    if(doc.commune) {
	commune = doc.commune;
    } else {
	commune = doc.siege.commune;
    }

    emit([doc.acheteur_raisin, doc.nom], {cvi: doc.cvi, nom: doc.nom, commune: commune});
}
