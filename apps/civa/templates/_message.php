<?php
echo preg_replace('/\'/', '&#39;', sfCouchdbManager::getClient('Messages')->getMessage($id));

