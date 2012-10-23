function(doc) {
  return; /* à adapter */


  if (!(doc.type && doc.type == "DR" && doc.cvi.match('^(67|68)') && doc.validee && doc.modifiee )) {
  return;
  }

  for(appellation_key in doc.recolte) {
 if(appellation_key.match('^appellation')) {
   var appellation_dplc = 0;
   var appellation_volume_revendique = 0;
    for(lieu_key in doc.recolte[appellation_key]) {
     if(lieu_key.match('^lieu')) {
       appellation_dplc += doc.recolte[appellation_key][lieu_key].dplc;
        appellation_volume_revendique += doc.recolte[appellation_key][lieu_key].volume_revendique;
        for(couleur_key in doc.recolte[appellation_key][lieu_key]) {
          if(couleur_key.match('^couleur')) {
           for(cepage_key in doc.recolte[appellation_key][lieu_key][couleur_key]) {
              if(cepage_key.match('^cepage')) {
               emit([doc.campagne, appellation_key, cepage_key], 
                     {
                       "nb_declarations_cepage": 0,
                      "total_volume": doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].total_volume,
                       "total_superficie": doc.recolte[appellation_key][lieu_key][couleur_key][cepage_key].total_superficie,  
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
         "volume_revendique": appellation_volume_revendique 
        });
 }
  }


   emit([doc.campagne, null, null], 
        {
         "nb_declarations": 1  
        });

}