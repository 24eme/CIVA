function(doc) {
    if (doc.type == "DS" && doc.mouts != null ) {
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

        emit([type_ds, doc.periode, doc.validee != null, doc.modifiee != null, doc.validee, doc.num_etape, doc.date_depot_mairie], 1);
    }
}