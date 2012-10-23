function(doc) {
    if (doc.type == "Recoltant" || doc.type == "MetteurEnMarche") {
      emit(null, [doc.db2.num, doc.compte[0]]);

    }
}