function(doc) { 
    if (doc['type'] == 'Acheteur') { 
        if (doc.cvi == '6702108000' || doc.cvi == '6734808000' || doc.cvi == '6831050010' || doc.cvi == '7523700888') {

            return;
        }
        emit([doc.qualite, doc.nom], {cvi: doc.cvi, nom: doc.nom, commune: doc.commune});
    }
}