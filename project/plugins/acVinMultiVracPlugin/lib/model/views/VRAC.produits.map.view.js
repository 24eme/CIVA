function(doc) {
	
	var fctConvertDateObject = function (dateString) {
    	if (!dateString) {
    		return null;
    	}
    	var m = dateString.match(/^(\d{4})\-(\d{1,2})\-(\d{1,2})/);
    	if (m) {
    		var date = new Date();
    		date.setDate(m[3]);
    		date.setMonth(m[2] - 1);
    		date.setYear(m[1]);
    		return date;
    	}
    	return null;
    }
	
	var fctGetDateCirculation = function (node) {
    	var dateTmp = null;
    	var stringDateTmp = null;
    	if (node.length > 0) {
    		for (var i in node) {
    			var dateNode = fctConvertDateObject(node[i].date);
				if (!dateTmp || (dateNode && dateNode < dateTmp)) {
					dateTmp = dateNode;
					stringDateTmp = node[i].date;
				}
    		}
    	}
    	return stringDateTmp;
    }
	
	if (doc.type != "Vrac") {
		return;
	}

    if(doc.type_contrat != 'VRAC') {
        return;
    }

	var regexpDate = /-/g;
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
    	    	    	    	    	    				var numero_cepage = null;
    	    	    	    	    	    				for(detail in doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail) {
    	    	    	    	    	    					var produit = doc.declaration[certification][genre][appellation][mention][lieu][couleur][cepage].detail[detail];
    	    	    	    	    	    					if (produit.actif) {
    	    	    	    	    	    						var position = produit.position;
    	    	    	    	    	    						var volume_propose = (produit.volume_propose)? produit.volume_propose : 0;
    	    	    	    	    	    						var volume_enleve = (produit.volume_enleve === null)? produit.volume_propose : produit.volume_enleve;
    	    	    	    	    	    						volume_enleve = (volume_enleve)? volume_enleve : 0;
    	    	    	    	    	    						var prix_unitaire = (produit.prix_unitaire)? produit.prix_unitaire : 0;
    	    	    	    	    	    						var degre = 0;
																	if (typeof produit.label != "undefined") {
																		if (produit.label == "BIO") {
																			degre = 99;
																		}
																	}
    	    	    	    	    	    						var top_mercuriale = null;
    	    	    	    	    	    						var millesime = (produit.millesime && (produit.millesime).length > 1)? (produit.millesime).substr((produit.millesime).length - 2, 2) : null;
    	    	    	    	    	    						var vtsgn = null;
    	    	    	    	    	    						if (produit.vtsgn == "VT" || produit.vtsgn == "vt") {
    	    	    	    	    	    							vtsgn = 1;
    	    	    	    	    	    						} 
    	    	    	    	    	    						if (produit.vtsgn == "SGN" || produit.vtsgn == "sgn") {
    	    	    	    	    	    							vtsgn = 2;
    	    	    	    	    	    						} 
    	    	    	    	    	    						var date_circulation = fctGetDateCirculation(produit.retiraisons);
    	    	    	    	    	    						if (!date_circulation) {
    	    	    	    	    	    							date_circulation = doc.valide.date_validation;
    	    	    	    	    	    						}
    	    	    	    	    	    						date_circulation = (date_circulation)? (date_circulation).replace(regexpDate,"") : 0;
    	    	    	    	    	    	    				emit([numero_archive], [numero_archive, numero_cepage, code_cepage, code_appellation, position, volume_propose, volume_enleve, prix_unitaire, degre, top_mercuriale, millesime, vtsgn, date_circulation, produit.denomination]);
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