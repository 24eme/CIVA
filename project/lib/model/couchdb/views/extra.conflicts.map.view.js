function(doc) {
  if (doc._conflicts) 
  emit([doc.type, doc._id, doc._conflicts], 1);
}