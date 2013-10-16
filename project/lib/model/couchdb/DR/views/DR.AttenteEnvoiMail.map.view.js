function(doc) {

    if (!(doc.type == "DR" && doc.validee && doc.en_attente_envoi)) {
        return;
    }
    emit([doc.campagne, doc.cvi, doc.declarant.email], 1);
}