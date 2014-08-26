function(doc) {
    if (doc.type == "DS" && doc.mouts != null && doc.utilisateurs && doc.utilisateurs.edition) {
      
    if(!doc.identifiant.match(/^(67|68)/)) {
        return;
    }

    if(doc.ds_principale === 0) {
        return;
    }

    var type_ds = doc.type_ds;
    if(!doc.type_ds) {
        type_ds = "propriete";
    }

    for (u  in doc.utilisateurs.edition) {
            emit([type_ds, doc.periode, u], 1);
            break;
        }
    }   
}