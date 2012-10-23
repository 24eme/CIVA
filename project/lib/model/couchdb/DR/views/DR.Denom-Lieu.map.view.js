function(doc) {

  return; /* à adapter */
  
  if (!(doc.type && doc.type == "DR" && doc.cvi.match('^(67|68)') && doc.validee && doc.modifiee )) {
    
    return;
  }

  for(appellation_key in doc.recolte) {
    if(appellation_key.match('^appellation')) {
      for(lieu_key in doc.recolte[appellation_key]) {
        if(lieu_key.match('^lieu')) {
          for(couleur_key in doc.recolte[appellation_key][lieu_key]) {
            if(couleur_key.match('^couleur')) {
              for(cepage_key in doc.recolte[appellation_key][lieu_key][couleur_key]) {
                if(cepage_key.match('^cepage')) {
                  for(detail_key in doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].detail) {
                if (doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].detail[detail_key].denomination) {
                            emit([doc.campagne, doc.cvi, doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].detail[detail_key].denomination], 1)
                }
                if (doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].detail[detail_key].lieu) {
                            emit([doc.campagne, doc.cvi, doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].detail[detail_key].lieu], 1)
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