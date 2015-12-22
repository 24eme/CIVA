function(doc) {

  if (!(doc.type && doc.type == "DS" && doc.validee && doc.modifiee && !doc.import_db2))  {
    return;
  }

  if(doc.type_ds && doc.type_ds == "negoce") {
    return;
  }

  if(doc.periode.match(/12$/)) {
    return;
  }

  emit([doc.periode.substring(0,4), doc.declaration_insee, doc.identifiant], 1);
}