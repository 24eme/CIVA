function(doc) {

    if (!(doc.type == "DR" && doc.campagne == "2011" && doc.utilisateurs && doc.utilisateurs.validation)) {
        return;
    }

    if (doc.validee < "2011-12-01") {
    emit([doc.cvi, doc.declarant.email], 1);
    return;
    }

    if (doc.validee == "2011-12-01" && doc.utilisateurs.validation.length == 0) {
    emit([doc.cvi, doc.declarant.email], 1);
    return;
    }

    for (var u  in doc.utilisateurs.validation) {
        if(u.match("^COMPTE-+[0-9]{10}")){
            emit([doc.cvi, doc.declarant.email], 1);
            break;
        }
    }

}
