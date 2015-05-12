<?php
/**
 * BaseVracMandataire
 * 
 * Base model for VracMandataire

 * @property string $nom
 * @property string $raison_sociale
 * @property string $siret
 * @property string $carte_pro
 * @property string $adresse
 * @property string $code_postal
 * @property string $commune
 * @property string $telephone
 * @property string $email
 * @property string $famille

 * @method string getNom()
 * @method string setNom()
 * @method string getRaisonSociale()
 * @method string setRaisonSociale()
 * @method string getSiret()
 * @method string setSiret()
 * @method string getCartePro()
 * @method string setCartePro()
 * @method string getAdresse()
 * @method string setAdresse()
 * @method string getCodePostal()
 * @method string setCodePostal()
 * @method string getCommune()
 * @method string setCommune()
 * @method string getTelephone()
 * @method string setTelephone()
 * @method string getEmail()
 * @method string setEmail()
 * @method string getFamille()
 * @method string setFamille()
 
 */

abstract class BaseVracMandataire extends _VracTiers {
                
    public function configureTree() {
       $this->_root_class_name = 'Vrac';
       $this->_tree_class_name = 'VracMandataire';
    }
                
}