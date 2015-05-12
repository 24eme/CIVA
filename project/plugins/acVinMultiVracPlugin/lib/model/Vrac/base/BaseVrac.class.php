<?php
/**
 * BaseVrac
 * 
 * Base model for Vrac
 *
 * @property string $_id
 * @property string $_rev
 * @property string $acheteur_identifiant
 * @property VracAcheteur $acheteur
 * @property string $vendeur_identifiant
 * @property VracVendeur $vendeur
 * @property string $mandataire_identifiant
 * @property VracMandataire $mandataire
 * @property VracDeclaration $declaration
 * @property VracValide $valide
 * @property string $campagne
 * @property string $numero_archive
 * @property float $prix_total
 * @property float $volume_total
 * @property string $etape
 * @property string $type

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getAcheteurIdentifiant()
 * @method string setAcheteurIdentifiant()
 * @method VracAcheteur getAcheteur()
 * @method VracAcheteur setAcheteur()
 * @method string getVendeurIdentifiant()
 * @method string setVendeurIdentifiant()
 * @method VracVendeur getVendeur()
 * @method VracVendeur setVendeur()
 * @method string getMandataireIdentifiant()
 * @method string setMandataireIdentifiant()
 * @method VracMandataire getMandataire()
 * @method VracMandataire setMandataire()
 * @method VracDeclaration getDeclaration()
 * @method VracDeclaration setDeclaration()
 * @method VracValide getValide()
 * @method VracValide setValide()
 * @method string getCampagne()
 * @method string setCampagne()
 * @method string getNumeroArchive()
 * @method string setNumeroArchive()
 * @method float getPrixTotal()
 * @method float setPrixTotal()
 * @method float getVolumeTotal()
 * @method float setVolumeTotal()
 * @method string getEtape()
 * @method string setEtape()
 * @method string getType()
 * @method string setType()
 
 */
 
abstract class BaseVrac extends acCouchdbDocument {

    public function getDocumentDefinitionModel() {
        return 'Vrac';
    }
    
}