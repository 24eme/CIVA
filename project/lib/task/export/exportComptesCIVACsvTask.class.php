<?php

/* This file is part of the acVinComptePlugin package.
 * Copyright (c) 2011 Actualys
 * Authors :
 * Tangui Morlier <tangui@tangui.eu.org>
 * Charlotte De Vichet <c.devichet@gmail.com>
 * Vincent Laurent <vince.laurent@gmail.com>
 * Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * acVinComptePlugin task.
 *
 * @package    acVinComptePlugin
 * @subpackage lib
 * @author     Tangui Morlier <tangui@tangui.eu.org>
 * @author     Charlotte De Vichet <c.devichet@gmail.com>
 * @author     Vincent Laurent <vince.laurent@gmail.com>
 * @author     Jean-Baptiste Le Metayer <lemetayer.jb@gmail.com>
 * @version    0.1
 */
class exportComptesCIVACsvTask extends sfBaseTask
{

    protected function configure()
    {
        $this->addArguments(array(
            new sfCommandArgument('tiers_types', sfCommandArgument::IS_ARRAY, 'Type du tiers : Recoltant|MetteurEnMarche|Acheteur', array("Recoltant", "MetteurEnMarche", "Acheteur")),
        ));

        $this->addOptions(array(
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ));

        $this->namespace = 'exportciva';
        $this->name = 'comptes-csv';
        $this->briefDescription = '';
        $this->detailedDescription = <<<EOF
The [setTiersPassword|INFO] task does things.
Call it with:

  [php symfony maintenanceExportTiersGammaTask|INFO]
EOF;
    }

    protected function execute($arguments = array(), $options = array())
    {
        ini_set('memory_limit', '512M');
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $rows = EtablissementAllView::getInstance()->getAll();

        $csv = new ExportCsv(array(
                    "type" => "Type",
                    "login" => "Login",
                    "statut" => "Statut",
                    "date_creation" => "Date de création",
                    "mot_de_passe" => "Code de création",
                    "email" => "Email",
                    "cvi" => "Numéro CVI",
                    "civaba" => "Numéro CIVABA",
                    "siret" => "Numéro Siret",
                    "qualite" => "Qualité",
                    "civilite" => "Civilité",
                    "nom" => "Nom Prénom",
                    "adresse" => "Adresse",
                    "code postal" => "Code postal",
                    "commune" => "Commune",
                    "exploitant_sexe" => "Sexe de l'exploitant",
                    "exploitant_nom" => "Nom de l'exploitant"
                ));

        $validation = array(
            "type" => array("required" => true, "type" => "string"),
            "login" => array("required" => true, "type" => "string"),
            "statut" => array("required" => true, "type" => "string"),
            "date_creation" => array("required" => true, "type" => "string"),
            "mot_de_passe" => array("required" => true, "type" => "string"),
            "email" => array("required" => false, "type" => "string"),
            "cvi" => array("required" => false, "type" => "string"),
            "interne" => array("required" => false, "type" => "string"),
            "siret" => array("required" => false, "type" => "string"),
            "qualite" => array("required" => false, "type" => "string"),
            "civilite" => array("required" => false, "type" => "string"),
            "nom" => array("required" => true, "type" => "string"),
            "adresse" => array("required" => false, "type" => "string"),
            "code postal" => array("required" => false, "type" => "string"),
            "commune" => array("required" => false, "type" => "string"),
            "exploitant_civilite" => array("required" => false, "type" => "string"),
            "exploitant_nom" => array("required" => false, "type" => "string")
        );

        foreach ($rows as $row) {
            SocieteClient::getInstance()->clearSingleton();
            $etablissement = EtablissementClient::getInstance()->find($row->id);
            $compte = $etablissement->getMasterCompte();
            $compteSociete = $etablissement->getSociete()->getMasterCompte();
            $mot_de_passe = "Compte déjà créé";
            if (substr($compteSociete->mot_de_passe, 0, 6) == "{TEXT}") {
                $mot_de_passe = preg_replace('/^\{TEXT\}/', "", $compteSociete->mot_de_passe);
            }
            if(!$compteSociete->mot_de_passe) {
                $mot_de_passe = "Aucun";
            }
            if(!$compteSociete->mot_de_passe && preg_match("/01$/", $compteSociete->identifiant)) {
                continue;
            }

            $csv->add(array(
            "type" => $compte->societe_informations->type,
            "login" => $compteSociete->login,
            "statut" => $compteSociete->getStatutTeledeclarant(),
            "date_creation" => ($compteSociete->exist('extras/date_creation') && $compteSociete->get('extras/date_creation')) ? $compteSociete->get('extras/date_creation') : null,
            "mot_de_passe" => $mot_de_passe,
            "email" => $compte->email,
            "cvi" => $etablissement->getCvi(),
            "interne" => $etablissement->num_interne,
            "siret" => $etablissement->siret,
            "qualite" => $etablissement->famille,
            "civilite" => $etablissement->intitule,
            "nom" => $etablissement->nom,
            "adresse" => $etablissement->adresse,
            "code postal" => $etablissement->code_postal,
            "commune" => $etablissement->commune,
            "civilite de l'exploitant" => ($etablissement->exist('exploitant') && $etablissement->getExploitant()) ? $etablissement->getExploitant()->civilite : $compte->civilite,
            "nom de l'exploitant" => ($etablissement->exist('exploitant') && $etablissement->getExploitant()) ? $etablissement->getExploitant()->nom : $compte->nom,
                ), $validation);
        }
        echo $csv->output(false);
    }

    protected function getTiersField($tiers, $field, $default = null) {
        $value = $default;
        if (isset($tiers->{$field})) {
            $value = $tiers->{$field};
        }
        return $value;
    }
}
