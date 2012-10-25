function(doc) {

    if (!(doc.type == "DR" && doc.campagne == "2011" && doc.utilisateurs && doc.utilisateurs.validation)) {
        return;
    }

    if (doc.validee >= "2011-12-16") {
    for(u in doc.utilisateurs.validation) {
        return;
    }
    emit([doc.cvi], 1);
    }

}
