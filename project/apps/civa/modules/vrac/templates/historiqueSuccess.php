<?php include_component('tiers', 'ongletsBootstrap', array('active' => 'vrac', 'compte' => $compte)); ?>

<div id="contrats_vrac">
    <div class="vrac-stats-compteurs">
        <div class="stats-compteur">
            <div class="icone glyphicon glyphicon-edit"></div>
            <div class="texte">
                <span class="chiffre"><?php echo $statuts_globaux['A_TERMINER'] ?? 0; ?></span>
                <a href="?statut=BROUILLON" class="link">Brouillon(s)</a>
            </div>
        </div>
        <div class="stats-compteur">
            <div class="icone glyphicon"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-vector-pen" viewBox="0 0 16 16">
  <path fill-rule="evenodd" d="M10.646.646a.5.5 0 0 1 .708 0l4 4a.5.5 0 0 1 0 .708l-1.902 1.902-.829 3.313a1.5 1.5 0 0 1-1.024 1.073L1.254 14.746 4.358 4.4A1.5 1.5 0 0 1 5.43 3.377l3.313-.828zm-1.8 2.908-3.173.793a.5.5 0 0 0-.358.342l-2.57 8.565 8.567-2.57a.5.5 0 0 0 .34-.357l.794-3.174-3.6-3.6z"/>
  <path fill-rule="evenodd" d="M2.832 13.228 8 9a1 1 0 1 0-1-1l-4.228 5.168-.026.086z"/>
</svg></div>
            <div class="texte">
                <span class="chiffre"><?php echo $statuts_globaux['A_SIGNER'] ?? 0; ?></span>
                <a href="?statut=A_SIGNER" class="link">À signer</a>
            </div>
        </div>
        <div class="stats-compteur">
            <div class="icone glyphicon glyphicon-hourglass"></div>
            <div class="texte">
                <span class="chiffre"><?php echo $statuts_globaux['EN_ATTENTE'] ?? 0; ?></span>
                <a href="?statut=EN_ATTENTE" class="link">En attente</a>
            </div>
        </div>
        <div class="stats-compteur">
            <div class="icone glyphicon">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-truck" viewBox="0 0 16 16">
                    <path d="M0 3.5A1.5 1.5 0 0 1 1.5 2h9A1.5 1.5 0 0 1 12 3.5V5h1.02a1.5 1.5 0 0 1 1.17.563l1.481 1.85a1.5 1.5 0 0 1 .329.938V10.5a1.5 1.5 0 0 1-1.5 1.5H14a2 2 0 1 1-4 0H5a2 2 0 1 1-3.998-.085A1.5 1.5 0 0 1 0 10.5zm1.294 7.456A2 2 0 0 1 4.732 11h5.536a2 2 0 0 1 .732-.732V3.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5v7a.5.5 0 0 0 .294.456M12 10a2 2 0 0 1 1.732 1h.768a.5.5 0 0 0 .5-.5V8.35a.5.5 0 0 0-.11-.312l-1.48-1.85A.5.5 0 0 0 13.02 6H12zm-9 1a1 1 0 1 0 0 2 1 1 0 0 0 0-2m9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2"/>
                </svg>
            </div>
            <div class="texte">
                <span class="chiffre"><?php echo $statuts_globaux['A_ENLEVER'] ?? 0; ?></span>
                <a href="?statut=EN_COURS&type=<?php echo VracClient::TYPE_VRAC ?>" class="link">À enlever</a>
            </div>
        </div>
        <div class="stats-compteur">
            <div class="icone glyphicon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16">
                    <path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2"/>
                    <path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0"/>
                </svg>
            </div>
            <div class="texte">
                <span class="chiffre"><?php echo $statuts_globaux['PLURIANNUEL_EN_COURS'] ?? 0; ?></span>
                <a href="?temporalite=<?php echo VracClient::TEMPORALITE_PLURIANNUEL_CADRE ?>&statut=EN_COURS" class="link">Pluriannuel en cours</a>
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
                <div class="btn-group btn-block" style="display: flex; align-items: stretch; align-content: stretch;">
                    <?php if ($hasDoubt): ?>
                        <a type="button" class="btn btn-success" style="flex-grow: 1" data-toggle="modal" data-target="#popup_choix_typeVrac"><span class="glyphicon glyphicon-plus"></span> Créer un contrat</a>
                    <?php else: ?>
                        <a type="button" class="btn btn-success" style="flex-grow: 1" href="<?php echo url_for('vrac_nouveau') ?>"><span class="glyphicon glyphicon-plus"></span> Créer un contrat</a>
                    <?php endif ?>
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="caret"></span>
                      <span class="sr-only">Toggle Dropdown</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="<?php echo url_for('vrac_csv_accueil', ['identifiant' => $compte->identifiant]); ?>">Importer un fichier</a></li>
                    </ul>
                </div>
                <div class="btn-group btn-block">
                    <button class="btn btn-default btn-block dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      <span class="glyphicon glyphicon-export"></span> Export <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="<?php echo url_for('vracs_export_csv', array('identifiant' => $compte->getIdentifiant(), 'campagne' => $campagne)) ?>">CSV</a></li>
                        <li><a href="<?php echo url_for('vracs_export_pdf', array('identifiant' => $compte->getIdentifiant(), 'campagne' => $campagne)) ?>">PDF</a></li>
                    </ul>
                </div>
                <a class="btn btn-default btn-block" href="<?php echo url_for('annuaire') ?>">
                  <span class="glyphicon glyphicon-book"></span> Gérer son annuaire
                </a>

                <a class="btn btn-default btn-block" href="<?php echo url_for('telecharger_la_notice_vrac') ?>">
                    <span class="glyphicon glyphicon-question-sign"></span> Document d'aide
                </a>
            </div>

            <div class="input-group">
                <span class="input-group-addon" style="background: white;"><span class="glyphicon glyphicon-search"></span></span>
                <input type="text" id="soussignes_search" class="form-control" placeholder="Soussignés, n° de contrat, ..." aria-describedby="soussignes_search">
            </div>

            <hr/>

            <p style="margin-bottom: 5px;"><strong><?php echo count($vracs) ?></strong> contrat(s) trouvé(s)</p>
            <div class="active-filters-list">
                <?php foreach ($current_filters as $filter => $filter_value): ?>
                    <div class="active-filter">
                        <?php echo ucfirst($filter) ?> :
                        <?php switch ($filter) {
                            case "type": echo $types[$filter_value]; break;
                            case "statut": echo $statuts[$filter_value]; break;
                            case "temporalite": echo $temporalites[$filter_value]; break;
                            default: echo $filter_value; break;
                        } ?>
                        <a href="<?php echo '?'.http_build_query(array_merge($current_filters, [$filter => null])) ?>" class="btn btn-clear"></a>
                    </div>
                <?php endforeach ?>
            </div>

            <h4>Statuts</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $statut === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => null])) ?>">Tous <span class="badge pull-right"><?php echo !$statut ? array_sum($facettes['statut']->getRawValue()) : "?" ?></span></a>
                <?php foreach ($statuts as $k => $s): ?>
                    <a title="<?php echo $s ?>" class="list-group-item list-group-item-xs <?php echo $k === $statut ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => $k])) ?>">
                        <span style="max-width: 165px; display: inline-block; text-wrap: nowrap; text-overflow: ellipsis; overflow: hidden;"><?php echo $s ?></span>
                            <span class="badge pull-right" data-key="<?php echo $k; ?>">
                                <?php echo (isset($facettes['statut']->getRawValue()[$k])) ? $facettes['statut'][$k] : ((!$statut || $statut == $k) ? 0 : "?") ?>
                            </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <h4>Types de contrat</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $type === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => null])) ?>">
                    <span style="width:25px; height:18px; text-align: center;display: inline-block"> &nbsp; </span>
                    Tous <span class="badge pull-right"><?php echo !$type ? array_sum($facettes['type']->getRawValue()) : "?" ?></span>
                </a>
                <?php foreach ($types as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $type ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => $k])) ?>">
                        <span style="width: 25px; height: 18px; text-align: center;display: inline-block"><img src="/images/pictos/pi_<?php echo strtolower($k) ?>.png"/></span>
                        <?php echo $s ?>
                            <span class="badge pull-right">
                                <?php echo (isset($facettes['type']->getRawValue()[$k])) ? $facettes['type'][$k] : ((!$type || $type == $k) ? 0 : "?") ?>
                            </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <h4 style="margin-top: 15px;">Campagnes</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $campagne === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => null])) ?>">
                    Toutes les campagnes <span class="badge pull-right"><?php echo $campagne === null ? array_sum($facettes['campagne']->getRawValue()) : "?" ?></span>
                </a>
                <?php foreach ($campagnes as $k => $c): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $c === $campagne ? 'active' : null ?> <?php echo ($k > 4 && $c !== $campagne) ? "hidden" : "" ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => $c])) ?>">
                        <?php echo $c ?>
                        <span class="badge pull-right">
                            <?php echo (isset($facettes['campagne']->getRawValue()[$c])) ? $facettes['campagne'][$c] : ((!$campagne || $campagne == $k) ? 0 : "?") ?>
                        </span>
                    </a>
                <?php endforeach; ?>
                <?php if (count($campagnes) > 4): ?>
                    <div class="list-group-item list-group-item-xs text-center" data-sens="more">
                        <span class="glyphicon glyphicon-chevron-down"></span>
                    </div>
                <?php endif ?>
            </div>

            <h4>Temporalités</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $temporalite === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => null])) ?>">
                    <span style="width:25px; height:18px; text-align: center;display: inline-block"> &nbsp; </span>
                        Tous <span class="badge pull-right"><?php echo !$temporalite ? array_sum($facettes['temporalite']->getRawValue()) : "?" ?></span>
                </a>
                <?php foreach ($temporalites as $k => $s): ?>
                <a class="list-group-item list-group-item-xs <?php echo $k === $temporalite ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => $k])) ?>">
                <span style="width:25px; height:18px; text-align: center;display: inline-block">
                <?php switch ($k) {
                    case 'PLURIANNUEL_APPLICATION':
                        echo '<svg style="color: #7e8601; margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-file" viewBox="0 0 16 16" ><path d="M4 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H4zm0 1h8a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1z"/></svg>';
                        break;
                    case 'PLURIANNUEL_CADRE':
                        echo '<svg style="color: #7e8601; margin-left: 5px;" xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor" class="bi bi-journals" viewBox="0 0 16 16"><path d="M5 0h8a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2 2 2 0 0 1-2 2H3a2 2 0 0 1-2-2h1a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1H3a1 1 0 0 0-1 1H1a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v9a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H5a1 1 0 0 0-1 1H3a2 2 0 0 1 2-2z"/><path d="M1 6v-.5a.5.5 0 0 1 1 0V6h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 3v-.5a.5.5 0 0 1 1 0V9h.5a.5.5 0 0 1 0 1h-2a.5.5 0 0 1 0-1H1zm0 2.5v.5H.5a.5.5 0 0 0 0 1h2a.5.5 0 0 0 0-1H2v-.5a.5.5 0 0 0-1 0z"/></svg>';
                        break;
                    default:
                        echo " &nbsp; ";
                        break;
                } ?></span>
                    <span style="max-width: 135px; display: inline-block; text-wrap: nowrap; text-overflow: ellipsis; overflow: hidden;">
                        <?php echo $s ?>
                    </span>
                    <span class="badge pull-right">
                        <?php echo (isset($facettes['temporalite']->getRawValue()[$k])) ? $facettes['temporalite'][$k] : ((!$temporalite || $temporalite == $k) ? 0 : "?") ?>
                    </span>
                </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const col_filters = document.getElementById('col-filters')
        const table_soussignes = document.getElementById('soussignes_listing')
        const stats = document.querySelector('.vrac-stats-compteurs')

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

        stats.addEventListener('click', function (e) {
            if (e.target.closest('.stats-compteur')) {
                const card = e.target.closest('.stats-compteur')
                const link = card.querySelector('.link')
                const isTextSelected = window.getSelection().toString();

                if (! isTextSelected) {
                    link.click();
                }
            }
        })
    </script>
</div>

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
