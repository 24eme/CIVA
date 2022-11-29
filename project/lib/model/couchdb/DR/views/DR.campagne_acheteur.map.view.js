function(doc) {
    if (doc.type && doc.type != 'DR') return;
    if (!doc.validee || !doc.modifiee) return;
    for(appellation_key in doc.acheteurs.certification.genre) {
    var appellation = doc.acheteurs.certification.genre[appellation_key];
        if (appellation.negoces) {
            for (id in appellation.negoces) {
                emit([doc.campagne, appellation.negoces[id]+'', doc.cvi], {"_rev": doc._rev});
            }
        }
 	      if (appellation.mouts) {
            for (id in appellation.mouts) {
                emit([doc.campagne, appellation.mouts[id]+'', doc.cvi], {"_rev": doc._rev});
            }
        }
        if (appellation.cooperatives) {
            for (id in appellation.cooperatives) {
                emit([doc.campagne, appellation.cooperatives[id]+'', doc.cvi], {"_rev": doc._rev});
            }
        }
    }
}