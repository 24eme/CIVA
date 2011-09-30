<?php

class sfCouchdbValueCollection extends sfCouchdbCollection {

    protected function load($data) {
        if (!is_null($data)) {
            try {
                if ($this->_hydrate == sfCouchdbClient::HYDRATE_ARRAY) {
                    foreach ($data["rows"] as $item) {
                        $this->_datas[$item['id']] = $item["doc"];
                    }
                } else {
                    foreach ($data->rows as $item) {
                        if ($this->_hydrate == sfCouchdbClient::HYDRATE_JSON) {
                            $this->_datas[$item->id] = $item->value;
                        }
                    }
                }
            } catch (Exception $exc) {
                throw new sfCouchdbException('Load error : data invalid');
            }
        }
    }

    public function getValues() {
        return $this->getDatas();
    }

}