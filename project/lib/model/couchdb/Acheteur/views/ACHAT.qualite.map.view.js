function(doc) { 
    if (doc['type'] == 'Acheteur') { 
        if (!doc.acheteur_dr) {

            return;
        }
        emit([doc.qualite, doc.nom], {cvi: doc.cvi, nom: doc.nom, commune: doc.commune});
    }
}