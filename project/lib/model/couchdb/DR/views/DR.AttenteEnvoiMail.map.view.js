function(doc) {

    if (!(doc.type == "DR" && doc.validee && doc.modifiee && doc.en_attente_envoi)) {
        return;
    }
    emit([doc.cvi, doc.declarant.email], 1);
}