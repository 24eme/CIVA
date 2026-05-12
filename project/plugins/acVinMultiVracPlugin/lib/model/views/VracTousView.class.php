<?php
class VracTousView extends acCouchdbView
{

    public static function getInstance() {

        return acCouchdbManager::getView('VRAC', 'tous', 'Vrac');
    }

    public function findAll()
    {
    	return $this->client->getView($this->design, $this->view)->rows;
    }

    public function findBy($identifiant, $campagne = null, $statut = null, $type = null, $role = null)
    {
    	if ($type) {
    		$types = array($type);
    	} else {
    		$types = array_keys(VracClient::getContratTypes());
    	}

    	$result = array();
    	foreach ($types as $t) {
			$params = array($identifiant);
            $params[] = $t;
			if ($campagne) {
				$params[] = $campagne;
				if ($statut) {
					$params[] = $statut;
				}
			}
			$startkey = $params;
			$params[] = array();
			$endkey = $params;

	        $result = array_merge($result, $this->client->startkey($startkey)
                                ->endkey($endkey)
                                ->getView($this->design, $this->view)->rows);
    	}

    	return $result;
    }

    public function findSortedBy($identifiant, $campagne = null, $statut = null, $type = null, $role = null, $commercial = null, $temporalite = null) {
    	$items = $this->findBy($identifiant, $campagne, $statut, $type, $role);
    	$result = array();
    	foreach ($items as $item) {
            if($role && $item->value->role != $role) {
                continue;
            }
            if($commercial && $item->value->commercial != $commercial) {
                continue;
            }
            if($temporalite == VracClient::TEMPORALITE_PLURIANNUEL_CADRE && (!$item->value->pluriannuel||$item->value->reference_pluriannuel)) {
                continue;
            }
            if($temporalite == VracClient::TEMPORALITE_PLURIANNUEL_APPLICATION && !$item->value->reference_pluriannuel) {
                continue;
            }
            if($temporalite == VracClient::TEMPORALITE_ANNUEL && ($item->value->reference_pluriannuel||$item->value->pluriannuel)) {
                continue;
            }
    		$result[$item->id] = $item;
    	}
    	krsort($result);
    	return $result;
    }

    public function findSortedByDeclarants(array $tiers, $campagne = null, $statut = null, $type = null, $role = null, $commercial = null, $temporalite = null) {
        $result = array();
        foreach($tiers as $t) {
            foreach($this->findSortedBy($t->_id, $campagne, $statut, $type, $role, $commercial, $temporalite) as $key => $item) {
                if(isset($result[$key])) {
                    continue;
                }
                $result[$key] = $item;
            }
        }
        krsort($result);

        foreach ($result as $key => $vrac) {
            $item = $vrac->value;

            $item->statutAction = $this->getStatutAction($item);

            if (in_array($item->statut, array(Vrac::STATUT_CREE)) && !$item->is_proprietaire) {
                unset($result[$key]);
                continue;
            }
            if($item->type_creation == VracClient::TYPE_CREATION_PAPIER && in_array($item->statut, array(Vrac::STATUT_CREE)) && !$sf_user->hasCredential(CompteSecurityUser::CREDENTIAL_ADMIN)) {
                unset($result[$key]);
                continue;
            }
        }

        return $result;
    }

    public function getStatutAction($vrac) {
        if($vrac->role != "acheteur" && $vrac->statut == Vrac::STATUT_PROJET_VENDEUR) {

            return "EN_ATTENTE";
        }
        if($vrac->role != "vendeur" && $vrac->statut == Vrac::STATUT_PROJET_ACHETEUR) {

            return "EN_ATTENTE";
        }
        if($vrac->role != "vendeur" && $vrac->statut == Vrac::STATUT_PROPOSITION) {

            return "EN_ATTENTE";
        }
        if($vrac->statut == Vrac::STATUT_VALIDE_PARTIELLEMENT && $vrac->soussignes->{$vrac->role}->date_validation) {

            return "EN_ATTENTE";
        }
        foreach($this->getStatutsFromStatutsAction() as $statutAction => $statuts) {
            if(in_array($vrac->statut, $statuts)) {
                return $statutAction;
            }
        }

        return null;
    }

    public function getStatutsFromStatutsAction() {

        return [
            "BROUILLON" => [Vrac::STATUT_CREE, Vrac::STATUT_CREE_APPLICATION],
            "A_SIGNER" => [Vrac::STATUT_VALIDE_PARTIELLEMENT, Vrac::STATUT_PROJET_VENDEUR, Vrac::STATUT_PROJET_ACHETEUR, Vrac::STATUT_PROPOSITION],
            "EN_ATTENTE" => [Vrac::STATUT_VALIDE_PARTIELLEMENT, Vrac::STATUT_PROJET_VENDEUR, Vrac::STATUT_PROJET_ATTENTE_TRANSMISSION, Vrac::STATUT_PROJET_VENDEUR_TRANSMIS, Vrac::STATUT_PROJET_ACHETEUR, Vrac::STATUT_PROPOSITION, Vrac::STATUT_REFUS_PROJET, Vrac::STATUT_SIGNE],
            "EN_COURS" => [Vrac::STATUT_VALIDE, Vrac::STATUT_VALIDE_CADRE, Vrac::STATUT_ENLEVEMENT, Vrac::STATUT_GENERE_AUTOMATIQUEMENT_APPLICATION],
            "CLOTURE" => [Vrac::STATUT_CLOTURE],
            "ANNULE" => [Vrac::STATUT_ANNULE],
        ];
    }

    public function findSortedByDeclarantsAndCleanPluriannuel(array $tiers, $campagne = null, $statut = null, $type = null, $role = null, $commercial = null, $temporalite = null) {
        $result = $this->findSortedByDeclarants($tiers, $campagne, $statut, $type, $role, $commercial, $temporalite);
        $pluriannuelsCadresASupprimer = [];
        foreach($result as $key => $item) {
            if ($item->value->reference_pluriannuel && !in_array($item->value->statut, [Vrac::STATUT_CLOTURE, Vrac::STATUT_ANNULE])) {
                $pluriannuelsCadresASupprimer[$item->value->reference_pluriannuel] = $item->value->reference_pluriannuel;
            }
        }
        foreach($pluriannuelsCadresASupprimer as $pluriannuelCadreASupprimer) {
            if (isset($result[$pluriannuelCadreASupprimer])) {
                unset($result[$pluriannuelCadreASupprimer]);
            }
        }
        return $result;
    }
}
