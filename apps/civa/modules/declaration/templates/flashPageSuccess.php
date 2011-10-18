<!-- #principal -->
<ul id="onglets_majeurs" class="clearfix">
    <li class="ui-tabs-selected"><a href="#warning">Import</a></li>
</ul>

<!-- #application_dr -->
<div id="application_dr" class="clearfix">

    <!-- #exploitation_administratif -->
    <div id="exploitation_administratif">

   <div class="intro_declaration"><?php echo $sf_user->getFlash('flash_message', ESC_RAW); ?></div>

    </div>
    <!-- fin #exploitation_administratif -->
</div>
<!-- fin #application_dr -->

<form id="principal" action="" method="post">
    <?php include_partial('global/boutons', array('display' => array('precedent', 'suivant'))) ?>
</form>
<!-- fin #principal -->
