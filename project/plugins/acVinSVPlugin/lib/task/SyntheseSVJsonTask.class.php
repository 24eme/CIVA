<?php

class SyntheseSVJsonTask extends sfBaseTask
{
    public function configure()
    {
        $this->addArguments([
            new sfCommandArgument('declaration', sfCommandArgument::REQUIRED, "Type de Document de production"),
            new sfCommandArgument('fichier', sfCommandArgument::REQUIRED, "JSON généré")
        ]);

        $this->addOptions([
            new sfCommandOption('application', null, sfCommandOption::PARAMETER_REQUIRED, 'The application name', 'civa'),
            new sfCommandOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'prod'),
            new sfCommandOption('connection', null, sfCommandOption::PARAMETER_REQUIRED, 'The connection name', 'default'),
        ]);

        $this->namespace        = 'sv';
        $this->name             = 'synthese-json';
        $this->briefDescription = 'Synthese superficie rendement des SV11/12 envoyés aux douanes';
        $this->detailedDescription = <<<EOF
[SyntheseJson|INFO] Prends un fichier JSON de SV11/12 et en ressort une synthese des surfaces / hl et le rendement.
  [php symfony SyntheseJson [doc]|INFO]
    }
EOF;
    }

    public function execute($arguments = [], $options = [])
    {
        // initialize the database connection
        $databaseManager = new sfDatabaseManager($this->configuration);
        $connection = $databaseManager->getDatabase($options['connection'])->getConnection();

        $declaration = $arguments['declaration'];
        $fichier = $arguments['fichier'];

        $decoded = json_decode(file_get_contents($fichier));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new sfException(json_last_error_msg());
        }

        if ($declaration === "SV11") {
            $root_node = "declarationsProductionCaves";
            $apport_node = "declarationApports";
            $site_node = "declarationVolumesObtenusParSite";
            $produits_apporteur = "apports";
            $numero_apporteur = "numeroCVIApporteur";
            $apport_raisin = "volumeApportRaisins";
        } else {
            $root_node = "declarationsProductionsNegociants";
            $apport_node = "declarationAchats";
            $site_node = "declarationVolumesObtenusParSite";
            $produits_apporteur = "fournisseurs";
            $numero_apporteur = "numeroEvvFournisseur";
            $apport_raisin = "quantiteAchatRaisins";
        }

        $total = [];

        foreach ($decoded->$root_node as $declaration) {
            foreach ($declaration->$apport_node->produits as $produit) {
                $total[$produit->codeProduit]['libelle'] = '';

                $libelle = ConfigurationClient::getConfiguration()->identifyProductByCodeDouane($produit->codeProduit);
                if (count($libelle)) {
                    $total[$produit->codeProduit]['libelle'] = $libelle[0]->getAppellation()->getLibelle()." ".
                        $libelle[0]->getLieu()->getLibelle()." ".
                        $libelle[0]->getLibelle();
                }

                foreach ($produit->$produits_apporteur as $apporteur) {
                    $total[$produit->codeProduit]["superficie"] = round($total[$produit->codeProduit]["superficie"] + $apporteur->superficieRecolte, 4);

                    if (property_exists($apporteur, $apport_raisin)) {
                        $total[$produit->codeProduit]["apporte"] = round($total[$produit->codeProduit]["apporte"] + $apporteur->$apport_raisin, 2);
                        $total[$produit->codeProduit]["revendique"] = round($total[$produit->codeProduit]["revendique"] + $apporteur->volumeIssuRaisins, 2);
                    }

                    if (property_exists($apporteur, 'volumesAEliminer')) {
                        $total[$produit->codeProduit]["elimine"] = round($total[$produit->codeProduit]["elimine"] + $apporteur->volumesAEliminer->volumeAEliminer, 2);

                        if (property_exists($apporteur->volumesAEliminer, 'volumeComplementaireIndividuel')) {
                            $total[$produit->codeProduit]["vci"] = round($total[$produit->codeProduit]["vci"] + $apporteur->volumesAEliminer->volumeComplementaireIndividuel, 2);
                        }
                    }

                    if (property_exists($apporteur, 'volumeIssuMouts')) {
                        $total[$produit->codeProduit]["mouts"] = round($total[$produit->codeProduit]["mouts"] + $apporteur->volumeAchatMouts, 2);
                        $total[$produit->codeProduit]["mouts_revendique"] = round($total[$produit->codeProduit]["mouts_revendique"] + $apporteur->volumeIssuMouts, 2);
                    }
                }
            }
        }

        $output = fopen('php://output', 'w+');
        fputcsv($output, ['code douane', 'libelle', 'superficie', 'volume apporte', 'volume revendique', 'mout apporte', 'mout revendique', 'volume elimine', 'vci'], ';');
        foreach ($total as $code => $produit) {
            $p = [
                $code,
                $produit['libelle'],
                $produit['superficie'],
                (array_key_exists('apporte', $produit)) ? $produit['apporte'] : 0,
                (array_key_exists('revendique', $produit)) ? $produit['revendique'] : 0,
                (array_key_exists('mouts', $produit)) ? $produit['mouts'] : 0,
                (array_key_exists('mouts_revendique', $produit)) ? $produit['mouts_revendique'] : 0,
                (array_key_exists('elimine', $produit)) ? $produit['elimine'] : 0,
                (array_key_exists('vci', $produit)) ? $produit['vci'] : 0,
            ];

            fputcsv($output, $p, ';');
        }
        fclose($output);
    }
}
