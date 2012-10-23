function(doc) {
    if(doc.type && doc.type == 'DR') {
        emit([doc.declaration_insee, doc.declaration_commune], 1);
    }   
}