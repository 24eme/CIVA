<div class="table-responsive">
    <table class="table table-bordered table-striped table-condensed">
        <thead>
            <tr>
                <th>Ligne</th>
                <?php foreach ($vracimport->getHeaders() as $header): ?>
                    <th><?php echo $header ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vracimport->getCsv() as $num => $line): ?>
            <tr id="line<?php echo $num + 1 ?>" class="<?php echo count($csvVrac->getErreurs($num + 1)) ? 'danger' : '' ?>">
                <td><?php echo $num + 1 ?></td>
                <?php foreach ($line as $td): ?>
                    <td><?php echo $td ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
