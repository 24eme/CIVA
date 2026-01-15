<div id="contrats_vrac">
	<h2 class="titre_principal">Historique de vos contrats de vente</h2>
    <div class="clearfix">
    </div>

    <div class="row">
        <div class="col-xs-9">
            <?php include_partial('vrac/liste', array('vracs' => $vracs, 'tiers' => $sf_user->getDeclarantsVrac(), 'limite' => false, 'archive' => true)) ?>
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
                <button class="btn btn-default btn-block" href="#">
                    <span class="glyphicon glyphicon-plus"></span>
                    Créer un contrat
                </button>
            </div>

            <h3 style="margin-top:0">Filtrage</h3>

            <?php if (empty($current_filters) === false): ?>
                <a href="?"><span class="glyphicon glyphicon-trash"></span> Supprimer les filtres</a>
            <?php endif ?>

            <h4>Soussignés</h4>
            <div class="input-group">
                <span class="input-group-addon" id="soussignes_search"><span class="glyphicon glyphicon-filter"></span></span>
                <input type="text" class="form-control" placeholder="Soussigné" aria-describedby="soussignes_search">
            </div>

            <h4>Campagne</h4>
            <div class="list-group">
                <?php foreach ($campagnes as $k => $c): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $c === $campagne ? 'active' : null ?> <?php echo ($k > 4 && $c !== $campagne) ? "hidden" : "" ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => $c])) ?>">
                        <?php echo $c ?>
                        <span class="badge pull-right">
                        <?php echo array_count_values(array_column(array_column($vracs->getRawValue(), 'key'), 2))[$c] ?? "?" ?>
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
                <a class="list-group-item list-group-item-xs <?php echo $type === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => null])) ?>">Tous</a>
                <?php foreach ($types as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $type ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => $k])) ?>">
                        <?php echo $s ?>
                        <span class="badge pull-right">
                        <?php echo array_count_values(array_column(array_column($vracs->getRawValue(), 'key'), 1))[$k] ?? 0 ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>

            <h4>Temporalité</h4>
            <div class="list-group">
                <a class="list-group-item list-group-item-xs <?php echo $temporalite === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => null])) ?>">Tous</a>
                <?php foreach ($temporalites as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $temporalite ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => $k])) ?>">
                        <?php echo $s ?>
                    </a>
                <?php endforeach; ?>
            </div>

            <h4>Statuts</h4>
            <div class="list-group">
                <a class="list-group-item <?php echo $statut === null ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => null])) ?>">Tous</a>
                <?php foreach ($statuts as $k => $s): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $k === $statut ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => $k])) ?>">
                        <?php echo $s ?>
                        <span class="badge pull-right">
                        <?php echo array_count_values(array_column(array_column($vracs->getRawValue(), 'key'), 3))[$k] ?? 0 ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
        const col_filters = document.getElementById('col-filters')
        col_filters.addEventListener('click', function (e) {
            if (e.target.dataset.sens) {
                const sens = e.target.dataset.sens
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
    </script>
</div>

<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></li>
</ul>
