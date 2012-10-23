function(doc) {
    if ((doc.type == "CompteTiers" || doc.type == "CompteProxy")) {
        emit([doc.statut], 1);
    }
}