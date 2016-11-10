<div id="error_page">
    <img src="/images/pictos/hopla_big.png" alt="Hop'La" />
    <h1>La page demandée est introuvable.</h1>
    <br />
    <p>Ce lien n’existe plus ou vous avez saisi une adresse incorrecte.</p>
    <br/>
    <ul>
        <li><a href="<?php try { echo url_for('mon_espace_civa', $sf_user->getCompte()); } catch (Exception $e) {} ?>">Retour à Mon espace Civa</a></li>
        <li><a onclick="history.back();">Retour à la page précédente</a></li>
    </ul>
</div>
