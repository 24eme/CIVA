<!DOCTYPE html>
<!--[if lte IE 6 ]><html class="ie6 ielt7 ielt8 ielt9" lang="fr"><![endif]-->
<!--[if IE 7 ]><html class="ie7 ielt8 ielt9" lang="fr"><![endif]-->
<!--[if IE 8 ]><html class="ie8 ielt9" lang="fr"><![endif]-->
<!--[if IE 9 ]><html class="ie9" lang="fr"><![endif]-->
<!--[if gt IE 9]><!--><html lang="fr"><!--<![endif]-->
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
      <title>
        <?php $title = $sf_context->getInstance()->getResponse()->getTitle();
        printf(html_entity_decode($title) , $sf_request->getParameter('annee', date("Y"))); ?>
      </title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
    <script type="text/javascript" src="/js/lib/jquery-1.4.2.min.js"></script>
  </head>
  <body id="declaration_recolte" class="<?php if(acCouchdbManager::getClient("Current")->hasCurrentFromTheFuture()): ?>bttf<?php endif; ?>">
    <!-- #global -->
	<div id="global">
	  <?php include_partial('global/header'); ?>
          <?php include_partial('global/errorFlash') ?>
           <div id="contenu">
                <?php echo $sf_content ?>
            </div>
            <div id="ajax-modal" class="modal"></div>
            <?php include_partial('global/footer') ?>
        </div>
    <!-- fin #global -->
    <?php include_partial('global/init') ?>
    <script type="text/javascript" src="/js/lib/jquery-ui-1.8.1.min.js"></script>
    <?php include_javascripts() ?>

    <?php include_partial('global/ieCssJavascript') ?>
    <?php include_partial('global/ajaxNotification') ?>
  </body>
</html>
