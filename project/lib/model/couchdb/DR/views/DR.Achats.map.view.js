function(doc) {

    if (!doc.type || doc.type != "DR") {

        return;
    }

    if(!doc.cvi.match('^(67|68)')) {

        return;
    }

    /*if(!doc.validee | !doc.modifiee) {

        return;
    }*/

    if(!doc.recolte.certification) {

        return;
    }

    if(!doc.recolte.certification.genre) {

        return;
    }

    for(appellation_key in doc.recolte.certification.genre) {
        if(!appellation_key.match('^appellation')) {

            continue;
        }
        var superficies = new Array();
        superficies['negoces'] = new Array();
        superficies['cooperatives'] = new Array();
        superficies['mouts'] = new Array();
        var dontdplcs = new Array();
        dontdplcs['negoces'] = new Array();
        dontdplcs['cooperatives'] = new Array();
        dontdplcs['mouts'] = new Array();
        var volumes = new Array();
        volumes['negoces'] = new Array();
        volumes['cooperatives'] = new Array();
        volumes['mouts'] = new Array();
        for(mention_key in doc.recolte.certification.genre[appellation_key]) {
            if(!mention_key.match('^mention')) {

                continue;
            }

            for(lieu_key in doc.recolte.certification.genre[appellation_key][mention_key]) {
            if(!lieu_key.match('^lieu')) {

                continue;
            }
            var lieu = doc.recolte.certification.genre[appellation_key][mention_key][lieu_key];
            for(couleur_key in lieu) {
                if(!couleur_key.match('^couleur')) {

                    continue;
                }

                for(cepage_key in lieu[couleur_key]) {
                    if(!(cepage_key.match('^cepage') && cepage_key != 'cepage_RB')) {

                        continue;
                    }
                    for(detail_key in lieu[couleur_key][cepage_key].detail) {
                        for(acheteur_key in lieu[couleur_key][cepage_key].detail[detail_key].negoces) {
                            var acheteur = lieu[couleur_key][cepage_key].detail[detail_key].negoces[acheteur_key];
                            if (!volumes['negoces'][acheteur.cvi]) {
                                volumes['negoces'][acheteur.cvi] = 0;
                            }
                            volumes['negoces'][acheteur.cvi] = volumes['negoces'][acheteur.cvi] + acheteur.quantite_vendue;
                        }
                        for(acheteur_key in lieu[couleur_key][cepage_key].detail[detail_key].cooperatives) {
                            var acheteur = lieu[couleur_key][cepage_key].detail[detail_key].cooperatives[acheteur_key];
                            if (!volumes['cooperatives'][acheteur.cvi]) {
                                volumes['cooperatives'][acheteur.cvi] = 0;
                            }
                            volumes['cooperatives'][acheteur.cvi] = volumes['cooperatives'][acheteur.cvi] + acheteur.quantite_vendue;
                        }
                        if(lieu[couleur_key][cepage_key].detail[detail_key].mouts) {
                            for(acheteur_key in lieu[couleur_key][cepage_key].detail[detail_key].mouts) {
                                var acheteur = lieu[couleur_key][cepage_key].detail[detail_key].mouts[acheteur_key];
                                if (!volumes['mouts'][acheteur.cvi]) {
                                    volumes['mouts'][acheteur.cvi] = 0;
                                }
                                volumes['mouts'][acheteur.cvi] = volumes['mouts'][acheteur.cvi] + acheteur.quantite_vendue;
                            }
                        }
                    }
                }
            }

            for(acheteur_key in lieu.acheteurs.negoces) {
                var detail = lieu.acheteurs.negoces[acheteur_key];
                if (!superficies['negoces'][acheteur_key]) {
                    superficies['negoces'][acheteur_key] = 0;
                }
                superficies['negoces'][acheteur_key] = superficies['negoces'][acheteur_key] + detail.superficie;
                if (!dontdplcs['negoces'][acheteur_key]) {
                    dontdplcs['negoces'][acheteur_key] = 0;
                }
                dontdplcs['negoces'][acheteur_key] = dontdplcs['negoces'][acheteur_key] + detail.dontdplc;
            }

            for(acheteur_key in lieu.acheteurs.cooperatives) {
                var detail = lieu.acheteurs.cooperatives[acheteur_key];
                if (!superficies['cooperatives'][acheteur_key]) {
                    superficies['cooperatives'][acheteur_key] = 0;
                }
                superficies['cooperatives'][acheteur_key] = superficies['cooperatives'][acheteur_key] + detail.superficie;
                if (!dontdplcs['cooperatives'][acheteur_key]) {
                    dontdplcs['cooperatives'][acheteur_key] = 0;
                }
                dontdplcs['cooperatives'][acheteur_key] = dontdplcs['cooperatives'][acheteur_key] + detail.dontdplc;
            }

            for(acheteur_key in lieu.acheteurs.mouts) {
                var detail = lieu.acheteurs.mouts[acheteur_key];
                if (!superficies['mouts'][acheteur_key]) {
                    superficies['mouts'][acheteur_key] = 0;
                }
                superficies['mouts'][acheteur_key] = superficies['mouts'][acheteur_key] + detail.superficie;
                if (!dontdplcs['mouts'][acheteur_key]) {
                    dontdplcs['mouts'][acheteur_key] = 0;
                }
                dontdplcs['mouts'][acheteur_key] = dontdplcs['mouts'][acheteur_key] + detail.dontdplc;
            }
        }
        }
        for(acheteur_type_key in volumes) {
            for(acheteur_key in volumes[acheteur_type_key]) {
                emit([doc.campagne, doc.cvi, appellation_key, acheteur_type_key, acheteur_key],
                     [(Math.round(volumes[acheteur_type_key][acheteur_key]*100)/100).toFixed(2),
                      (Math.round(superficies[acheteur_type_key][acheteur_key]*100)/100).toFixed(2),
                      (Math.round(dontdplcs[acheteur_type_key][acheteur_key]*100)/100).toFixed(2)]);
            }
        }
    }
}
