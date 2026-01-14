<div id="contrats_vrac">
	<h2 class="titre_principal">Historique de vos contrats de vente</h2>
    <div class="clearfix">
    </div>

    <div class="row">
        <div class="col-xs-9">
            <?php include_partial('vrac/liste', array('vracs' => $vracs, 'tiers' => $sf_user->getDeclarantsVrac(), 'limite' => false, 'archive' => true)) ?>
        </div>
        <div class="col-xs-3" style="border-left: 1px dashed black">
            <?php $current_filters = []; ?>
            <?php parse_str($_SERVER['QUERY_STRING'] ?? '', $current_filters); ?>

            <div style="margin-bottom: 15px">
                <a class="btn btn-default btn-block" href="#">
                    <span class="glyphicon glyphicon-export"></span>
                    Export
                </a>
                <a class="btn btn-default btn-block" href="#">
                    <span class="glyphicon glyphicon-plus"></span>
                    Créer un contrat
                </a>
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
                <?php foreach ($campagnes as $c): ?>
                    <a class="list-group-item list-group-item-xs <?php echo $c === $campagne ? 'active' : null ?>" href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => $c])) ?>">
                        <?php echo $c ?>
                        <span class="badge pull-right">
                        <?php echo array_count_values(array_column(array_column($vracs->getRawValue(), 'key'), 2))[$c] ?? 0 ?>
                        </span>
                    </a>
                <?php endforeach; ?>
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
        const count_selected = document.getElementById('selected_contrats')
        const listing_contrat = document.getElementById('soussignes_listing')

        listing_contrat.addEventListener('click', function (e) {
            if (e.target.closest('input[type=checkbox]') == null) {
                return false
            }

            count_selected.innerHTML = +listing_contrat.querySelectorAll('tbody input[type=checkbox]:checked').length
        })
    </script>
</div>

<ul id="btn_etape" class="btn_prev_suiv">
	<li><a href="<?php echo url_for('mon_espace_civa_vrac', array('identifiant' => $compte->getIdentifiant())) ?>"><img alt="Retourner à l'espace contrats" src="/images/boutons/btn_retour_espace_contrats.png"></a></li>
</ul>
