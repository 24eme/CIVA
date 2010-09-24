<?php

class Configuration extends BaseConfiguration {
    public function getArrayAppellationsMout() {
        $appellations = $this->getRecolte();
        $appellations_array_mouts = array();
        foreach ($appellations->filter('^appellation') as $appellation_key => $appellation) {
            if ($appellation->getMout() == 1) {
                $appellations_array_mouts[$appellation_key] = $appellation;
            }
        }
        return $appellations_array_mouts;
    }

    public function getArrayAppellations() {
        $appellations = $this->getRecolte();
        $appellations_array = array();
        foreach ($appellations->filter('^appellation') as $appellation_key => $appellation) {
            $appellations_array[$appellation_key] = $appellation;
        }

        return $appellations_array;
    }

    public function isCompteAdminExist($login, $mot_de_passe) {
        foreach ($this->compte_admin as $item) {
            if (strlen($item->mot_de_passe) > 6) {
                if ($login == $item->login) {
                    $is_mot_de_passe_valid = false;
                    $cryptage = str_replace(array('{', '}'), array('', ''), substr($item->mot_de_passe, 0, 6));
                    $mot_de_passe_compte = substr($item->mot_de_passe, 6, strlen($item->mot_de_passe) - 6);
                    if ($cryptage == 'SSHA') {
                        $is_mot_de_passe_valid = ($mot_de_passe_compte == sha1($mot_de_passe));
                    } elseif ($cryptage == 'TEXT') {
                        $is_mot_de_passe_valid = ($mot_de_passe_compte == $mot_de_passe);
                    }
                    if ($is_mot_de_passe_valid) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

}