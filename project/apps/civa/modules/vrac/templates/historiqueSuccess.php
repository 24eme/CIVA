<div id="contrats_vrac">
	<h2 class="titre_principal">Historique de vos contrats de vente</h2>
    <div class="clearfix">
        <div style="margin: 15px 0">
            Agir sur les <span id="selected_contrats">0</span> contrat(s) sélectionné(s) :
            <div class="btn-group">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    Action <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li><a href="#">Signer</a></li>
                    <li><a href="#">Dupliquer</a></li>
                    <li><a href="#">Générer</a></li>
                </ul>
            </div>
            <div class="pull-right">
                <div class="btn-group">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                      Export <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li><a href="#">PDF</a></li>
                        <li><a href="#">CSV</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-9">
            <?php include_partial('vrac/liste', array('vracs' => $vracs, 'tiers' => $sf_user->getDeclarantsVrac(), 'limite' => false, 'archive' => true)) ?>
        </div>
        <div class="col-xs-3" style="border-left: 1px dashed black">
            <?php $current_filters = []; ?>
            <?php parse_str($_SERVER['QUERY_STRING'] ?? '', $current_filters); ?>

            <h3 style="margin-top:0">Filtrage</h3>

            <?php if (empty($current_filters) === false): ?>
                <a href="?"><span class="glyphicon glyphicon-trash"></span> Supprimer les filtres</a>
            <?php endif ?>

            <h5>Soussignés</h5>
            <div class="input-group">
                <span class="input-group-addon" id="soussignes_search"><span class="glyphicon glyphicon-filter"></span></span>
                <input type="text" class="form-control" placeholder="Soussigné" aria-describedby="soussignes_search">
            </div>

            <h5>Campagne</h5>
            <ul class="list-group">
                <?php foreach ($campagnes as $c): ?>
                    <li class="list-group-item <?php echo $c === $campagne ? 'active' : null ?>">
                        <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['campagne' => $c])) ?>"><?php echo $c ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5>Type de contrat</h5>
            <ul class="list-group">
                <li class="list-group-item <?php echo $type === null ? 'active' : null ?>">
                    <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => null])) ?>">Tous</a>
                </li>
                <?php foreach ($types as $k => $s): ?>
                    <li class="list-group-item <?php echo $k === $type ? 'active' : null ?>">
                        <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['type' => $k])) ?>"><?php echo $s ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5>Temporalité</h5>
            <ul class="list-group">
                <li class="list-group-item <?php echo $temporalite === null ? 'active' : null ?>">
                    <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => null])) ?>">Tous</a>
                </li>
                <?php foreach ($temporalites as $k => $s): ?>
                    <li class="list-group-item <?php echo $k === $temporalite ? 'active' : null ?>">
                        <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['temporalite' => $k])) ?>"><?php echo $s ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <h5>Statuts</h5>
            <ul class="list-group">
                <li class="list-group-item <?php echo $statut === null ? 'active' : null ?>">
                    <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => null])) ?>">Tous</a>
                </li>
                <?php foreach ($statuts as $k => $s): ?>
                    <li class="list-group-item <?php echo $k === $statut ? 'active' : null ?>">
                        <a href="<?php echo '?'.http_build_query(array_merge($current_filters, ['statut' => $k])) ?>"><?php echo $s ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
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
