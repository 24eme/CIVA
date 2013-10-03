<?php
/**
 * BaseCourtier
 * 
 * Base model for Courtier
 *
 * @property string $statut
 * @property acCouchdbJson $compte
 * @property string $civaba
 * @property string $intitule
 * @property string $nom
 * @property string $telephone
 * @property string $fax
 * @property string $email
 * @property string $web
 * @property acCouchdbJson $lieux_stockage
 * @property _TiersExploitant $exploitant
 * @property _TiersSiege $siege
 * @property string $categorie
 * @property acCouchdbJson $db2
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $cvi
 * @property string $no_accises
 * @property string $no_carte_professionnelle
 * @property string $declaration_insee
 * @property string $declaration_commune
 * @property acCouchdbJson $gamma
 * @property string $siret
 * @property string $siren

 * @method string getStatut()
 * @method string setStatut()
 * @method acCouchdbJson getCompte()
 * @method acCouchdbJson setCompte()
 * @method string getCivaba()
 * @method string setCivaba()
 * @method string getIntitule()
 * @method string setIntitule()
 * @method string getNom()
 * @method string setNom()
 * @method string getTelephone()
 * @method string setTelephone()
 * @method string getFax()
 * @method string setFax()
 * @method string getEmail()
 * @method string setEmail()
 * @method string getWeb()
 * @method string setWeb()
 * @method acCouchdbJson getLieuxStockage()
 * @method acCouchdbJson setLieuxStockage()
 * @method _TiersExploitant getExploitant()
 * @method _TiersExploitant setExploitant()
 * @method _TiersSiege getSiege()
 * @method _TiersSiege setSiege()
 * @method string getCategorie()
 * @method string setCategorie()
 * @method acCouchdbJson getDb2()
 * @method acCouchdbJson setDb2()
 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCvi()
 * @method string setCvi()
 * @method string getNoAccises()
 * @method string setNoAccises()
 * @method string getNoCarteProfessionnelle()
 * @method string setNoCarteProfessionnelle()
 * @method string getDeclarationInsee()
 * @method string setDeclarationInsee()
 * @method string getDeclarationCommune()
 * @method string setDeclarationCommune()
 * @method acCouchdbJson getGamma()
 * @method acCouchdbJson setGamma()
 * @method string getSiret()
 * @method string setSiret()
 * @method string getSiren()
 * @method string setSiren()
 
 */
 
abstract class BaseCourtier extends _Tiers {

    public function getDocumentDefinitionModel() {
        return 'Courtier';
    }
    
}