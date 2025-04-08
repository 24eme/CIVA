<div>
    <ul>
    <?php foreach ($csvVrac->getErreurs() as $erreur): ?>
        <li>
            <?php echo $erreur->num_ligne . ': '.$erreur->diagnostic; ?>
        </li>
    <?php endforeach ?>
    </ul>
</div>
