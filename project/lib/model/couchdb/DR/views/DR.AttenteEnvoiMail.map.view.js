function(doc)
{
  if (doc.type != "DR") {
    return;
  }

  if (! doc.validee || ! doc.modifiee) {
    return;
  }

  if (! doc.en_attente_envoi) {
    return;
  }

  emit([doc.type, doc.cvi, doc.declarant.email], 1);
}
