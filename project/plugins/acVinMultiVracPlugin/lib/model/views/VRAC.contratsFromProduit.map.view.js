function(doc) {
   	if (doc.type != "Vrac"){
  		return;
  	}
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
    	    	    	    	    	    						var produitHash = "/declaration/"+certification+"/"+genre+"/"+appellation+"/"+mention+"/"+lieu+"/"+couleur+"/"+cepage;
                                            	var volume_propose = (produit.volume_propose)? produit.volume_propose : 0;
    	    	    	    	    	    						var volume_enleve = (produit.volume_enleve === null)? produit.volume_propose : produit.volume_enleve;
    	    	    	    	    	    						volume_enleve = (volume_enleve)? volume_enleve : 0;
    	    	    	    	    	    						emit([doc.valide.statut, doc.type_contrat, doc.vendeur_identifiant, produitHash],[doc.numero_archive, doc.acheteur_identifiant, volume_propose, volume_enleve]);
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
