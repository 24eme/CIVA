function(doc) {
    if (doc.type && (doc.type == "CompteVirtuel" || doc.type == "CompteTiers" || doc.type == "CompteProxy")) {
        emit([doc.type, doc._id], doc);
    }
}