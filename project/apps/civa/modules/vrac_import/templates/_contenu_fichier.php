<div class="table-responsive" style="border: 1px solid #999; border-top: 0; max-height: 500px;">
    <table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0; position: relative;">
        <thead style="position: sticky; top: 0; box-shadow: 7px 16px 15px -3px rgba(0,0,0,0.1); border-bottom: 1px solid #999; ">
            <tr>
                <th>Ligne</th>
                <?php foreach ($vracimport->getHeaders() as $header): ?>
                    <th><?php echo $header ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($vracimport->getCsv() as $num => $line): ?>
            <tr style="font-family: monospace;" id="line<?php echo $num + 1 ?>" class="<?php echo count($csvVrac->getErreurs($num + 1)) ? 'danger text-danger' : '' ?>">
                <td><?php echo $num + 1 ?></td>
                <?php foreach ($line as $td): ?>
                    <td><?php echo $td ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
