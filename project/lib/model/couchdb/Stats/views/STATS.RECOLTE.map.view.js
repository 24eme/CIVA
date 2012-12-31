function(doc) {
    
    return; // Car trop gourmande

    if (!(doc.type && doc.type == "DR" && doc.cvi.match('^(67|68)') && doc.validee && doc.modifiee )) {
        return;
    } 


    var genre = doc.recolte.certification.genre;
    for(appellation_key in genre) {
        if(appellation_key.match('^appellation')) {
            var appellation = genre[appellation_key];
            var appellation_dplc = 0;
            var appellation_volume_revendique = 0;
            var appellation_usages_industriels = 0;
            var appellation_usages_industriels_saisi = 0;
            for(lieu_key in appellation.mention) {
                if(lieu_key.match('^lieu')) {
                    var lieu = appellation.mention[lieu_key];
                    appellation_dplc += lieu.dplc;
                    appellation_volume_revendique += lieu.volume_revendique;
                    appellation_usages_industriels += lieu.usages_industriels_calcule;
                    appellation_usages_industriels_saisi += lieu.usages_industriels_saisi;
                    for(couleur_key in lieu) {
                        if(couleur_key.match('^couleur')) {
                            var couleur = lieu[couleur_key];
                            for(cepage_key in couleur) {
                                if(cepage_key.match('^cepage')) {
                                    var cepage = couleur[cepage_key];
                                    emit([doc.campagne, appellation_key, cepage_key], 
                                    {
                                     "nb_declarations_cepage": 0,
                                     "total_volume": cepage.total_volume,
                                     "total_superficie": cepage.total_superficie,  
                                   });
                                }
                            }
                        }
                    }
                }
            }
            emit([doc.campagne, appellation_key, null], 
            {
             "nb_declarations_appellation": 0,
             "dplc": appellation_dplc,
             "usages_industriels": appellation_volume_revendique,
             "usages_industriels_saisi": appellation_usages_industriels_saisi,
             "volume_revendique": appellation_volume_revendique 
            });
        }
    }


   emit([doc.campagne, null, null], 
        {
         "nb_declarations": 1  
        });

}