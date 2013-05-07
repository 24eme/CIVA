<?php
echo preg_replace('/\'/', '&#39;', acCouchdbManager::getClient('Messages')->getMessage($id));

