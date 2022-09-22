<?php
/**
 * BaseSV
 * 
 * Base model for SV
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $identifiant
 * @property string $campagne
 * @property string $periode
 * @property string $numero_archive
 * @property acCouchdbJson $declarant
 * @property SVApporteurs $apporteurs
 * @property acCouchdbJson $valide

 * @method string getId()
 * @method string setId()
 * @method string getRev()
 * @method string setRev()
 * @method string getType()
 * @method string setType()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getPeriode()
 * @method string setPeriode()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method acCouchdbJson getDeclarant()
 * @method acCouchdbJson setDeclarant()
 * @method SVApporteurs getApporteurs()
 * @method SVApporteurs setApporteurs()
 * @method acCouchdbJson getValide()
 * @method acCouchdbJson setValide()
 
 */
 
abstract class BaseSV extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'SV';
    }
    
}