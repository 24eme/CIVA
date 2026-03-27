function(doc)
{
  if (doc.type != "DR" || doc.type != "Vrac") {
    return;
  }

  if (doc.type == "DR") {
    if (! doc.validee || ! doc.modifiee) {
      return;
    }
  }

  if (doc.type == "Vrac") {
    if (! doc.valide.date_validation) {
      return;
    }
  }

  if (! doc.en_attente_envoi) {
    return;
  }

  emit([doc.type, doc.cvi, doc.declarant.email], 1);
}
