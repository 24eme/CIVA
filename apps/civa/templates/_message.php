<?php
echo htmlentities(sfCouchdbManager::getClient('Messages')->getMessage($id), ENT_QUOTES);

