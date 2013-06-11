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
 * @property float $rebeches
 * @property float $dplc
 * @property float $lies
 * @property float $mouts
 * @property string $num_etape
 * @property string $ds_neant
 * @property acCouchdbJson $declarant
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
 * @method float getRebeches()
 * @method float setRebeches()
 * @method float getDplc()
 * @method float setDplc()
 * @method float getLies()
 * @method float setLies()
 * @method float getMouts()
 * @method float setMouts()
 * @method string getNumEtape()
 * @method string setNumEtape()
 * @method string getDsNeant()
 * @method string setDsNeant()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method DSDeclaration getDeclaration()
 * @method DSDeclaration setDeclaration()
 
 */
 
abstract class BaseDS extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'DS';
    }
    
}