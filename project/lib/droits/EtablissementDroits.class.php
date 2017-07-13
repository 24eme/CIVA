<?php

class EtablissementDroits
{
    public static function getDroits($etablissement) {
        $droits = array();

        if(in_array($etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))) {

            $droits[Roles::TELEDECLARATION_DR] = Roles::TELEDECLARATION_DR;
        }

        if($etablissement->exist('acheteur_raisin') && $etablissement->acheteur_raisin) {

            $droits[Roles::TELEDECLARATION_DR_ACHETEUR] = Roles::TELEDECLARATION_DR_ACHETEUR;
        }

        if(in_array($etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR, EtablissementFamilles::FAMILLE_COOPERATIVE))) {

            $droits[Roles::TELEDECLARATION_DS_PROPRIETE] = Roles::TELEDECLARATION_DS_PROPRIETE;
            $droits[Roles::TELEDECLARATION_DRM] = Roles::TELEDECLARATION_DRM;
        }

        if(in_array($etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_NEGOCIANT, EtablissementFamilles::FAMILLE_COOPERATIVE))) {

            $droits[Roles::TELEDECLARATION_DS_NEGOCE] = Roles::TELEDECLARATION_DS_NEGOCE;
            $droits[Roles::TELEDECLARATION_DRM] = Roles::TELEDECLARATION_DRM;
        }

        if($etablissement->exist('no_accises') && $etablissement->no_accises) {
            $droits[Roles::TELEDECLARATION_GAMMA] = Roles::TELEDECLARATION_GAMMA;
        }

        $droits[Roles::TELEDECLARATION_VRAC] = Roles::TELEDECLARATION_VRAC;

        if(!in_array($etablissement->getFamille(), array(EtablissementFamilles::FAMILLE_PRODUCTEUR, EtablissementFamilles::FAMILLE_PRODUCTEUR_VINIFICATEUR))) {
            $droits[Roles::TELEDECLARATION_VRAC_CREATION] = Roles::TELEDECLARATION_VRAC_CREATION;
        }

        if($etablissement->exist('ds_decembre')) {

            $droits[Roles::TELEDECLARATION_DS_DECEMBRE] = Roles::TELEDECLARATION_DS_DECEMBRE;
        }

        return $droits;
    }
}
