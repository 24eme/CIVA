function(doc) {

    if (!(doc.type == "DR" && doc.utilisateurs && doc.utilisateurs.validation)) {
        return;
    }

    for(u in doc.utilisateurs.validation) {
        return;
    }
    emit([doc.campagne, doc.cvi], 1);
}
