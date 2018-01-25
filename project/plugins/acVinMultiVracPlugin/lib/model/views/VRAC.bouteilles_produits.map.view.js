function(doc) {
    
    if (doc.type != "Vrac") {
        return;
    }

    if(doc.type_contrat != 'BOUTEILLE') {
        return;
    }

    var numero_archive = doc.numero_archive;
    for(certification in doc.declaration) {
        if (certification.match(/^certification/g)) {
            for(genre in doc.declaration[certification]) {
                if (genre.match(/^genre/g)) {
                    for(appellation in doc.declaration[certification][genre]) {
                        if (appellation.match(/^appellation/g)) {
                            var code_appellation = appellation.replace("appellation_","");
                            for(mention in doc.declaration[certification][genre][appellation]) {
                                if (mention.match(/^mention/g)) {
                                    for(lieu in doc.declaration[certification][genre][appellation][mention]) {
                                        if (lieu.match(/^lieu/g)) {
                                            for(couleur in doc.declaration[certification][genre][appellation][mention][lieu]) {
                                                if (couleur.match(/^couleur/g)) {
                                                    for(cepage in doc.declaration[certification][genre][appellation][mention][lieu][couleur]) {
                                                        if (cepage.match(/^cepage/g)) {
                                                            var code_cepage = cepage.replace("cepage_","");
                                                            for(detail in doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail) {
                                                                var produit = doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail[detail];
                                                                if (produit.actif) {
                                                                    var position = produit.position;
                                                                    var l = produit.lieu_dit;
                                                                    var denomination = null;
                                                                    if (l && produit.denomination) {
                                                                        denomination = l+' '+produit.denomination;
                                                                    } else if (l) {
                                                                        denomination = l;
                                                                    } else if (produit.denomination) {
                                                                        denomination = produit.denomination;
                                                                    }
                                                                    var num_agrement = null;
                                                                    var centilisation = (produit.centilisation)? produit.centilisation : 0;
                                                                    var nb_bouteille = (produit.nb_bouteille)? produit.nb_bouteille : 0;
                                                                    var volume_enleve = (produit.volume_enleve === null)? produit.volume_propose : produit.volume_enleve;
                                                                    volume_enleve = (volume_enleve)? volume_enleve : 0;
                                                                    var prix_unitaire = (produit.prix_unitaire)? produit.prix_unitaire : 0;
                                                                    var millesime = (produit.millesime && (produit.millesime).length > 1)? produit.millesime : null;
                                                                    var vtsgn = null;
                                                                    if (produit.vtsgn == "VT" || produit.vtsgn == "vt") {
                                                                        vtsgn = 1;
                                                                    }
                                                                    if (produit.vtsgn == "SGN" || produit.vtsgn == "sgn") {
                                                                        vtsgn = 2;
                                                                    }
                                                                    emit([numero_archive], [numero_archive, position, millesime, code_appellation, code_cepage, denomination, num_agrement, centilisation, nb_bouteille, volume_enleve, prix_unitaire, vtsgn, produit.denomination]);
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }
}
