<div id="contrats_vrac">
    <div class="row">
        <div class="col-xs-4">
            <div style="background: #eeeedc; border: 1px solid #e0e1bd; color: #7e8601; border-radius: 3px; text-align: center; padding: 10px; cursor: pointer; position: relative;">
                <a href="?statut=VALIDE_PARTIELLEMENT">
                <span class="glyphicon glyphicon-edit" style="font-size: 24px; position:absolute; left: 30px; top: 20px;"></span>
                <h3 style="margin-top: 0; margin-bottom: 0; font-size: 24px;"><?php echo $statuts_globaux[Vrac::STATUT_VALIDE_PARTIELLEMENT] ?? 0; ?></h3>
                contrat(s) à signer
                </a>
            </div>
        </div>
        <div class="col-xs-4">
            <div style="background: #eeeedc; border: 1px solid #e0e1bd; color: #7e8601; border-radius: 3px; text-align: center; padding: 10px; cursor: pointer; position: relative;">
                <a href="?statut=PROPOSITION">
                <span class="glyphicon glyphicon-hourglass" style="font-size: 24px; position:absolute; left: 30px; top: 20px;"></span>
                <h3 style="margin-top: 0; margin-bottom: 0; font-size: 24px;"><?php echo $statuts_globaux[Vrac::STATUT_PROPOSITION] ?? 0; ?></h3>
                contrat(s) à en attente
                </a>
            </div>
        </div>
        <div class="col-xs-4">
            <div style="background: #eeeedc; border: 1px solid #e0e1bd; color: #7e8601; border-radius: 3px; text-align: center; padding: 10px; cursor: pointer;">
                <a href="?statut=PROJETS_EN_COURS">
                <svg style=" position:absolute; left: 30px; top: 20px;" xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16">
                    <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2"/>
                    <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0"/>
                </svg>
                <h3 style="margin-top: 0; margin-bottom: 0; font-size: 24px;"><?php echo $statuts_globaux['PROJETS_EN_COURS'] ?? 0; ?></h3>
                contrat(s) pluriannuel en cours
                </a>
            </div>
        </div>
    </div>

    <hr />

    <div class="row">
    <div class="col-xs-9">
	    <h2 class="titre_principal">Historique de vos contrats de vente</h2>
        <div class="clearfix"></div>

        <div class="px-1">
            <?php include_partial('vrac/liste', array('vracs' => $vracs, 'tiers' => $sf_user->getDeclarantsVrac(), 'limite' => false, 'archive' => true)) ?>
        </div>
    </div>

    <div id="col-filters" class="col-xs-3" style="border-left: 1px dashed #aeaeae;">
            <?php $current_filters = []; ?>
            <?php parse_str($_SERVER['QUERY_STRING'] ?? '', $current_filters); ?>

            <div style="margin-bottom: 15px">
                <div class="btn-group btn-block">
                    <button class="btn btn-default btn-block dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="glyphicon glyphicon-export"></span> Export <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="<?php echo url_for('vrac_export_csv', array('identifiant' => $compte->getIdentifiant(), 'campagne' => $campagne)) ?>">CSV</a></li>
                        <li><a href="#">PDF</a></li>
                    </ul>
                </div>

                <div class="btn-group btn-block" style="display: flex; align-items: stretch; align-content: stretch;">
                    <a type="button" class="btn btn-default" style="flex-grow: 1" data-toggle="modal" data-target="#popup_choix_typeVrac"><span class="glyphicon glyphicon-plus"></span> Créer un contrat</a>
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="<?php echo url_for('vrac_csv_accueil', ['identifiant' => $compte->identifiant]); ?>">Importer un fichier</a></li>
                    </ul>
                </div>

                <a class="btn btn-default btn-block" href="<?php echo url_for('annuaire') ?>">
                  <span class="glyphicon glyphicon-book"></span> Gérer son annuaire
                </a>

                <a class="btn btn-default btn-block" href="<?php echo url_for('telecharger_la_notice_vrac') ?>">
                    <span class="glyphicon glyphicon-question-sign"></span> Document d'aide
                </a>
            </div>

            <hr/>

            <h3 style="margin-top:0">Filtrage</h3>

            <?php if (empty($current_filters) === false): ?>
                <a href="?"><span class="glyphicon glyphicon-trash"></span> Supprimer les filtres</a>
            <?php endif ?>

            <h4>Recherche</h4>
            <div class="input-group">
                <span class="input-group-addon"><span class="glyphicon glyphicon-filter"></span></span>
                <input type="text" id="soussignes_search" class="form-control" placeholder="Soussignés, n° de contrat, ..." aria-describedby="soussignes_search">
            </div>

            <h4>Campagne</h4>
            <div class="list-group">
                <?php foreach ($campagnes as $k => $c): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $c === $campagne ? 'active' : null ?> <?php echo ($k > 4 && $c !== $campagne) ? "hidden" : "" ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => $c])) ?>">
                        <?php echo $c ?>
                        <span class="badge pull-right">
                        <?php echo $facettes['campagne'][$c] ?? "?" ?>
                        </span>
                    </a>
                <?php endforeach; ?>
                <?php if (count($campagnes) > 4): ?>
                    <div class="list-group-item list-group-item-xs text-center" data-sens="more">
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </div>
                <?php endif ?>
            </div>

            <h4>Type de contrat</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $type === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => null])) ?>">
                    <span style="width:25px; height:18px; text-align: center;display: inline-block"> &nbsp; </span>
                    Tous
                </a>
                <?php foreach ($types as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $type ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => $k])) ?>">
                        <span style="width: 25px; height: 18px; text-align: center;display: inline-block"><img src="/images/pictos/pi_<?php echo strtolower($k) ?>.png"/></span>
                        <?php echo $s ?>
                        <span class="badge pull-right">
                        <?php echo $facettes['type'][$k] ?? 0 ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <h4>Temporalité</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $temporalite === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => null])) ?>">
                    <span style="width:25px; height:18px; text-align: center;display: inline-block"> &nbsp; </span>
                    Tous
                </a>
                <?php foreach ($temporalites as $k => $s): ?>
                <a class="list-group-item list-group-item-xs <?php echo $k === $temporalite ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => $k])) ?>">
                <span style="width:25px; height:18px; text-align: center;display: inline-block">
                <?php switch ($k) {
                    case 'PLURIANNUEL_CADRE':
                        echo '<svg style="color: #7e8601; margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-file" viewBox="0 0 16 16" ><path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/></svg>';
                        break;
                    case 'PLURIANNUEL_APPLICATION':
                        echo '<svg style="color: #7e8601; margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16"><path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/><path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/></svg>';
                        break;
                    default:
                        echo " &nbsp; ";
                        break;
                } ?></span>
                    <?php echo $s ?>
                </a>
                <?php endforeach; ?>
            </div>

            <h4>Statuts</h4>
            <div class="list-group">
                <a class="list-group-item <?php echo $statut === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => null])) ?>">Tous</a>
                <?php foreach ($statuts as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $statut ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => $k])) ?>">
                        <span style="max-width: 150px"><?php echo $s ?></span>
                        <span class="badge pull-right" data-key="<?php echo $k; ?>">
                        <?php echo $facettes['statut'][$k] ?? 0 ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const col_filters = document.getElementById('col-filters')
        const table_soussignes = document.getElementById('soussignes_listing')

        col_filters.addEventListener('click', function (e) {
            if (e.target.closest('[data-sens]')) {
                const sens = e.target.closest('[data-sens]').dataset.sens
                const listgroup = e.target.closest('.list-group')
                listgroup.querySelectorAll('.list-group-item').forEach(function (el, i) {
                    if (i > 4 && el.dataset.sens == undefined) {
                        el.classList.toggle('hidden')
                        if (sens === "more") {
                            listgroup.querySelector('[data-sens] span').classList.remove('glyphicon-chevron-down')
                            listgroup.querySelector('[data-sens] span').classList.add('glyphicon-chevron-up')
                            listgroup.querySelector('[data-sens]').dataset.sens = "less"
                        } else {
                            listgroup.querySelector('[data-sens] span').classList.remove('glyphicon-chevron-up')
                            listgroup.querySelector('[data-sens] span').classList.add('glyphicon-chevron-down')
                            listgroup.querySelector('[data-sens]').dataset.sens = "more"
                        }
                    }
                })
            }
        })

        col_filters.addEventListener('input', function (e) {
            if (e.target.id === "soussignes_search") {
                const terms = document.getElementById(e.target.id).value
                table_soussignes.querySelectorAll('tbody tr').forEach(function (tr) {
                    if (tr.textContent.toLowerCase().includes(terms.toLowerCase())) {
                        tr.classList.remove('hidden')
                    } else {
                        tr.classList.add('hidden')
                    }
                })
            }
        })
    </script>
</div>

<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></li>
</ul>

<div id="popup_choix_typeVrac" class="popup_ajout modal" title="Création du contrat" tabindex="-1" role="dialog" aria-labelledby="">
    <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Création du contrat</h4>
      </div>
      <div class="modal-body">
        <form method="post" action="" id="form_creation_contrats_vrac">
            <div class="form-group">
                <?php $etablissements = VracClient::getInstance()->getEtablissements($sf_user->getCompte()->getSociete()); ?>
                <select class="form-control">
                <?php foreach($etablissements as $etablissement): ?>
                    <?php if(!VracSecurity::getInstance($sf_user->getCompte(), null)->isAuthorizedTiers($etablissement, VracSecurity::CREATION)): continue; endif; ?>
                    <option value="<?php echo $etablissement->_id ?>"><?php echo $etablissement->nom ?> <?php echo $etablissement->cvi ?> <?php echo EtablissementFamilles::$familles[$etablissement->famille]; ?></option>
                <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label style="display:inline-block">Vous êtes :</label>
                <label class="radio-inline">
                  <input required type="radio" name="choix_type" id="choix_type_vendeur" value="<?php echo url_for('vrac_selection_type', ['type' => 'vendeur', 'papier' => 0]) ?>"> Vendeur
                </label>
                <label class="radio-inline">
                  <input required type="radio" name="choix_type" id="choix_type_vendeur" value="<?php echo url_for('vrac_selection_type', ['type' => 'acheteur', 'papier' => 0]) ?>"> Acheteur
                </label>
            </div>
        </form>

        <script type="text/javascript">
            const form = document.getElementById("form_creation_contrats_vrac")
            form.addEventListener('submit', function (e) {
                e.preventDefault()
                const url = form.querySelector('input[type=radio]:checked').value
                const creator = form.querySelector('select > option:checked').value

                document.location.href = url + "&createur=" + creator
                return false;
            })
        </script>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Annuler</button>
        <button form="form_creation_contrats_vrac" class="btn btn-primary">Valider</button>
      </div>
    </div>
  </div>
</div>
