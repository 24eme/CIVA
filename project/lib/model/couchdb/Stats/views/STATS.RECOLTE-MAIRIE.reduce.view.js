function (keys, values, rereduce) {

  var total_volume = 0;
 var total_superficie = 0;
 var dplc = 0;
 var volume_revendique = 0;
  var nb_declarations = 0;
  var nb_declarations_appellation = 0;
  var nb_declarations_cepage = 0;

 //var key_ask = keys[0][0];

 for(item in values) {
   if (values[item]['total_volume']) {
     total_volume += values[item]['total_volume'];
   }
   if (values[item]['total_superficie']) {
     total_superficie += values[item]['total_superficie'];
   }
   if (values[item]['dplc']) {
     dplc += values[item]['dplc'];
   }
   if (values[item]['volume_revendique']) {
      volume_revendique += values[item]['volume_revendique'];
   }

   if (values[item]['nb_declarations']) {
      nb_declarations += values[item]['nb_declarations'];
   }

   /*if (values[item]['nb_declarations']) {
      nb_declarations += values[item]['nb_declarations'];
   }*/

   /*if (values[item]['nb_declarations_appellation']) {
      nb_declarations_appellation += values[item]['nb_declarations_appellation'];
   }

   if (values[item]['nb_declarations_cepage']) {
     nb_declarations_cepage += values[item]['nb_declarations_cepage'];
   }*/ 
  }

 total_volume = Math.round(total_volume*100)/100;
  total_superficie = Math.round(total_superficie*100)/100;
  dplc = Math.round(dplc*100)/100;
  volume_revendique = Math.round(volume_revendique*100)/100;

  /*if (key_ask[0] == null || key_ask[1] == null) {
   return {"nb_declarations": nb_declarations, "total_volume": total_volume, "total_superficie": total_superficie, "dplc": dplc, "volume_revendique": volume_revendique};
  } else if(key_ask[0] != null && key_ask[1] != null && key_ask[2] == null) {
   return {"nb_declarations": nb_declarations_appellation, "total_volume": total_volume, "total_superficie": total_superficie, "dplc": dplc, "volume_revendique": volume_revendique};
  } else if(key_ask[0] != null && key_ask[1] != null && key_ask[2] != null) {
   return {"nb_declarations": nb_declarations_cepage, "total_volume": total_volume, "total_superficie": total_superficie};
 } else {
    return "nop";
 }*/

 return {"nb_declarations": nb_declarations, "total_volume": total_volume, "total_superficie": total_superficie, "dplc": dplc, "volume_revendique": volume_revendique};
}