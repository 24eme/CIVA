function(doc) {

    if (!(doc.type == "DR" && doc.utilisateurs && doc.utilisateurs.validation)) {
        return;
    }

    for (var u in doc.utilisateurs.validation) {
        if(u.match("^COMPTE-[0-9]{10}")){
            emit([doc.campagne, doc.cvi, doc.declarant.email], 1);
            break;
        }
    }

}