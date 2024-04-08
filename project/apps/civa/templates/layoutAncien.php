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
	    <?php include_partial('global/header', array('compte' => ($sf_user->isAuthenticated() && $sf_user->getCompte()) ? $sf_user->getCompte() : null, 'compteOrigine' => ($sf_user->isInDelegateMode()) ? $sf_user->getCompte(myUser::NAMESPACE_COMPTE_AUTHENTICATED) : null,'isAdmin' => $sf_user->hasCredential(myUser::CREDENTIAL_OPERATEUR), 'isAuthenticated' => $sf_user->isAuthenticated())); ?>
        <?php include_partial('global/errorFlash') ?>
        <div id="contenu">
            <?php if(sfConfig::get('app_instance') == 'preprod' ): ?>
              <div style="margin-top: -15px; margin-bottom: 5px;"><p style="color:red; text-align:center; font-weight: bold;">Preproduction (la base est succeptible d'être supprimée à tout moment)</p></div>
            <?php endif; ?>
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
