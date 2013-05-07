function(doc) {
    if ((doc.type == "CompteTiers" || doc.type == "CompteProxy")) {
        emit([doc.statut, doc.tiers], 1);
    }
}