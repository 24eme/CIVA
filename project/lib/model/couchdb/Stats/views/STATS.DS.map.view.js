function(doc) {
    if (doc.type == "DS" && doc.mouts != null ) {
        if(!doc.identifiant.match(/^(67|68)/)) {
            return;
            }

        if(doc.ds_principale === 0) {
            return;
        }
        emit([doc.periode, doc.validee != null, doc.modifiee != null, doc.validee, doc.num_etape, doc.date_depot_mairie], 1);
    }
}