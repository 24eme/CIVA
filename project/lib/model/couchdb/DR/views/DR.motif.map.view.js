function(doc) {
  
    if (!(doc.type && doc.type == "DR" && doc.cvi.match('^(67|68)'))) {
      
      return;
    }

    for(certification_key in doc.recolte) {
        if(!certification_key.match('^certification')) { continue; }
        certification = doc.recolte[certification_key];
        for(genre_key in certification) {
            genre = certification[genre_key];
            if(!genre_key.match('^genre')) { continue; }
            for(appellation_key in genre) {
                if(!appellation_key.match('^appellation')) { continue; }
                appellation = genre[appellation_key];
                for(mention_key in appellation) {
                    if(!mention_key.match('^mention')) { continue; }
                    mention = appellation[mention_key];
                    for(lieu_key in mention) {
                        if(!lieu_key.match('^lieu')) { continue; }
                        lieu = mention[lieu_key];
                        for(couleur_key in lieu) {
                            if(!couleur_key.match('^couleur')) { continue; }
                            couleur = lieu[couleur_key];
                            for(cepage_key in couleur) {
                                if(!cepage_key.match('^cepage')) { continue; }
                                cepage = couleur[cepage_key];
                                for(detail_key in cepage.detail) {
                                    detail = cepage.detail[detail_key];
                                    if (!detail.motif_non_recolte) { continue; }
                                    emit([doc.campagne, doc.cvi, appellation_key + "/" + lieu_key + "/" + cepage_key, detail.motif_non_recolte], 1)
                                }
                            } 
                        }
                    }
                }
            }
        }
    }
}