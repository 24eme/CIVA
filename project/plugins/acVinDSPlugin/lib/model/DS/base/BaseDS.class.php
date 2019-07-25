<?php
/**
 * BaseDS
 * 
 * Base model for DS
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $identifiant
 * @property string $date_emission
 * @property string $date_echeance
 * @property string $date_stock
 * @property string $drm_origine
 * @property string $campagne
 * @property string $periode
 * @property string $numero_archive
 * @property string $statut
 * @property string $commentaire
 * @property string $ds_principale
 * @property float $rebeches
 * @property float $dplc
 * @property float $dplc_rouge
 * @property float $lies
 * @property float $mouts
 * @property string $num_etape
 * @property string $ds_neant
 * @property string $validee
 * @property string $modifiee
 * @property string $courant_stock
 * @property string $declaration_commune
 * @property string $declaration_insee
 * @property string $date_depot_mairie
 * @property acCouchdbJson $declarant
 * @property acCouchdbJson $stockage
 * @property acCouchdbJson $utilisateurs
 * @property DSDeclaration $declaration

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getDateEmission()
 * @method string setDateEmission()
 * @method string getDateEcheance()
 * @method string setDateEcheance()
 * @method string getDateStock()
 * @method string setDateStock()
 * @method string getDrmOrigine()
 * @method string setDrmOrigine()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getPeriode()
 * @method string setPeriode()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method string getStatut()
 * @method string setStatut()
 * @method string getCommentaire()
 * @method string setCommentaire()
 * @method string getDsPrincipale()
 * @method string setDsPrincipale()
 * @method float getRebeches()
 * @method float setRebeches()
 * @method float getDplc()
 * @method float setDplc()
 * @method float getDplcRouge()
 * @method float setDplcRouge()
 * @method float getLies()
 * @method float setLies()
 * @method float getMouts()
 * @method float setMouts()
 * @method string getNumEtape()
 * @method string setNumEtape()
 * @method string getDsNeant()
 * @method string setDsNeant()
 * @method string getValidee()
 * @method string setValidee()
 * @method string getModifiee()
 * @method string setModifiee()
 * @method string getCourantStock()
 * @method string setCourantStock()
 * @method string getDeclarationCommune()
 * @method string setDeclarationCommune()
 * @method string getDeclarationInsee()
 * @method string setDeclarationInsee()
 * @method string getDateDepotMairie()
 * @method string setDateDepotMairie()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method acCouchdbJson getStockage()
 * @method acCouchdbJson setStockage()
 * @method acCouchdbJson getUtilisateurs()
 * @method acCouchdbJson setUtilisateurs()
 * @method DSDeclaration getDeclaration()
 * @method DSDeclaration setDeclaration()
 
 */
 
abstract class BaseDS extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'DS';
    }
    
}