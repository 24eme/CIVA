<?php
/**
 * BaseEtablissement
 *
 * Base model for Etablissement
 *
 * @property string $_id
 * @property string $_rev
 * @property string $type
 * @property string $cooperative
 * @property string $interpro
 * @property string $identifiant
 * @property string $id_societe
 * @property string $statut
 * @property string $raisins_mouts
 * @property string $exclusion_drm
 * @property string $relance_ds
 * @property string $teledeclaration_email
 * @property string $nature_inao
 * @property string $siret
 * @property string $acheteur_raisin
 * @property acCouchdbJson $recette_locale
 * @property string $region
 * @property string $type_dr
 * @property acCouchdbJson $liaisons_operateurs
 * @property string $site_fiche
 * @property string $compte
 * @property string $compte_exploitant
 * @property string $num_interne
 * @property string $num_reprise
 * @property string $raison_sociale
 * @property string $nom
 * @property string $intitule
 * @property string $cvi
 * @property string $no_accises
 * @property string $carte_pro
 * @property string $famille
 * @property string $sous_famille
 * @property string $email
 * @property string $telephone
 * @property string $fax
 * @property string $commentaire
 * @property string $crd_regime
 * @property string $adresse_compta
 * @property string $caution
 * @property string $raison_sociale_cautionneur
 * @property string $declaration_insee
 * @property string $declaration_commune
 * @property EtablissementExploitant $exploitant
 * @property acCouchdbJson $siege
 * @property acCouchdbJson $comptabilite
 * @property acCouchdbJson $lieux_stockage

 * @method string get_id()
 * @method string set_id()
 * @method string get_rev()
 * @method string set_rev()
 * @method string getType()
 * @method string setType()
 * @method string getCooperative()
 * @method string setCooperative()
 * @method string getInterpro()
 * @method string setInterpro()
 * @method string getIdentifiant()
 * @method string setIdentifiant()
 * @method string getIdSociete()
 * @method string setIdSociete()
 * @method string getStatut()
 * @method string setStatut()
 * @method string getRaisinsMouts()
 * @method string setRaisinsMouts()
 * @method string getExclusionDrm()
 * @method string setExclusionDrm()
 * @method string getRelanceDs()
 * @method string setRelanceDs()
 * @method string getTeledeclarationEmail()
 * @method string setTeledeclarationEmail()
 * @method string getNatureInao()
 * @method string setNatureInao()
 * @method string getSiret()
 * @method string setSiret()
 * @method string getAcheteurRaisin()
 * @method string setAcheteurRaisin()
 * @method acCouchdbJson getRecetteLocale()
 * @method acCouchdbJson setRecetteLocale()
 * @method string getRegion()
 * @method string setRegion()
 * @method string getTypeDr()
 * @method string setTypeDr()
 * @method acCouchdbJson getLiaisonsOperateurs()
 * @method acCouchdbJson setLiaisonsOperateurs()
 * @method string getSiteFiche()
 * @method string setSiteFiche()
 * @method string getCompte()
 * @method string setCompte()
 * @method string getCompteExploitant()
 * @method string setCompteExploitant()
 * @method string getNumInterne()
 * @method string setNumInterne()
 * @method string getNumReprise()
 * @method string setNumReprise()
 * @method string getRaisonSociale()
 * @method string setRaisonSociale()
 * @method string getNom()
 * @method string setNom()
 * @method string getIntitule()
 * @method string setIntitule()
 * @method string getCvi()
 * @method string setCvi()
 * @method string getNoAccises()
 * @method string setNoAccises()
 * @method string getCartePro()
 * @method string setCartePro()
 * @method string getFamille()
 * @method string setFamille()
 * @method string getSousFamille()
 * @method string setSousFamille()
 * @method string getEmail()
 * @method string setEmail()
 * @method string getTelephone()
 * @method string setTelephone()
 * @method string getFax()
 * @method string setFax()
 * @method string getCommentaire()
 * @method string setCommentaire()
 * @method string getCrdRegime()
 * @method string setCrdRegime()
 * @method string getAdresseCompta()
 * @method string setAdresseCompta()
 * @method string getCaution()
 * @method string setCaution()
 * @method string getRaisonSocialeCautionneur()
 * @method string setRaisonSocialeCautionneur()
 * @method string getDeclarationInsee()
 * @method string setDeclarationInsee()
 * @method string getDeclarationCommune()
 * @method string setDeclarationCommune()
 * @method EtablissementExploitant getExploitant()
 * @method EtablissementExploitant setExploitant()
 * @method acCouchdbJson getSiege()
 * @method acCouchdbJson setSiege()
 * @method acCouchdbJson getComptabilite()
 * @method acCouchdbJson setComptabilite()
 * @method acCouchdbJson getLieuxStockage()
 * @method acCouchdbJson setLieuxStockage()

 */

abstract class BaseEtablissement extends CompteGenerique {

    public function getDocumentDefinitionModel() {
        return 'Etablissement';
    }

}
