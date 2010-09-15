<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
  <head>
    <?php include_http_metas() ?>
    <?php include_metas() ?>
      <title>
        <?php $title = $sf_context->getInstance()->getResponse()->getTitle();
        printf(html_entity_decode($title) , date("Y")); ?>
      </title>
    <link rel="shortcut icon" href="/favicon.ico" />
    <?php include_stylesheets() ?>
  </head>
  <body id="declaration_recolte">
    <!-- #global -->
	<div id="global">
            <?php include_partial('global/header') ?>
            <div id="contenu">
                <?php echo $sf_content ?>
            </div>
            <?php include_partial('global/footer') ?>
        </div>
    <!-- fin #global -->
    <?php include_partial('global/init') ?>
    <?php include_javascripts() ?>
    <?php include_partial('global/ieCssJavascript') ?>
  </body>
</html>
