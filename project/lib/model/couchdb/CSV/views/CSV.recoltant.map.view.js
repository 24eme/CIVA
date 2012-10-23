function(doc) {  
    if (!doc.type || doc.type != 'CSV') {
        return ;  
    }

    for(id in doc.recoltants)  {
        emit([doc.campagne+'', doc.recoltants[id]+'', doc._id], 1);
    }
}