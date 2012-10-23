function(doc) {
if (doc.type == "DR" && doc.campagne == "2011") {
  emit([doc.validee != null, doc.modifiee != null, doc.validee, doc.etape], 1);
}
}