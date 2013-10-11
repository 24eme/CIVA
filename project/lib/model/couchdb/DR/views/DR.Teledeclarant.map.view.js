function(doc) {

    if (!(doc.type == "DR" && doc.utilisateurs && doc.utilisateurs.validation)) {
        return;
    }
    var founded = false;
    for (var u in doc.utilisateurs.validation) {
        if(u.match("^COMPTE-[0-9]{10}")){
            emit([doc.campagne, doc.cvi, doc.declarant.email], 1);
	    founded = true;
            break;
        }
    }
    if(!founded){
    for (var u in doc.utilisateurs.edition) {
        if(u.match("^COMPTE-[0-9]{10}")){
            emit([doc.campagne, doc.cvi, doc.declarant.email], 1);
            break;
        }
    }
    }
}