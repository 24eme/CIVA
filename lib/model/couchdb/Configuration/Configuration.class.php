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

    private static function normalizeLibelle($libelle) {
      $libelle = preg_replace('/&nbsp;/', '', strtolower($libelle));
      $libelle = str_replace(array('é', 'è'), 'e', $libelle);
      $libelle = preg_replace('/[^a-z ]/', '', preg_replace('/  */', ' ', preg_replace('/&([a-z])[^;]+;/i', '\1', $libelle)));
      return $libelle;
    }

    public function identifyProduct($appellation, $lieu, $cepage) {
      $appid = null;
      $lieuid = 'lieu';
      $cepageid = null;
      $libelle = self::normalizeLibelle($appellation);
      foreach ( $this->getRecolte()->filter('^appellation') as $appellation_key => $appellation) {
	if ($libelle == self::normalizeLibelle($appellation->getLibelle())) {
	  $appid=$appellation_key;
	  break;
	}
      }
      if (!$appid)
	return null;

      if ($lieu) {
	$libelle = self::normalizeLibelle($lieu);
	foreach($appellation->filter('^lieu') as $lieu_key => $lieu) {
	  if ($libelle == self::normalizeLibelle($lieu->getLibelle())) {
	    $lieuid=$lieu_key;
	    break;
	  }
	}
      }else {
	if (!$appellation->exist('lieu'))
	  return null;
      }

      $libelle = self::normalizeLibelle($cepage);
      $eval = null;
      foreach($appellation->get($lieuid)->filter('^cepage') as $cepage_key => $cepage) {
	$cepage_libelle = self::normalizeLibelle($cepage->getLibelle());
	if ($libelle == $cepage_libelle) {
	  $cepageid = $cepage_key;
	  break;
	}
	if (preg_match('/^'.$cepage_libelle.'/', $libelle)) {
	  if ($eval === null)
	    $eval = $cepage_key;
	  else
	    $eval = 0;
	}
      }
      if (!$cepageid) {
	if ($eval)
	  $cepageid = $eval;
	else
	  return null;
      }
      return $appid.'/'.$lieuid.'/'.$cepageid;
    }

}