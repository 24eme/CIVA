<style>
    .table-responsive tr th {
        position: sticky;
        top: 0;
        position: -webkit-sticky;
        box-shadow: 0px 12px 17px -10px rgba(0,0,0,0.1);
    }
</style>
<div class="table-responsive" style="max-height: 500px; border: 1px solid #c6c8c7; border-left: 0;">
    <table class="table table-bordered table-striped table-condensed" style="margin-bottom: 0; position: relative;  border: 0;">
        <thead>
            <tr>
                <th>N°&nbsp;Ligne</th>
                <?php foreach ($vracimport->getHeaders() as $header): ?>
                    <th><?php echo $header ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody style="margin-top: 100px;">
            <?php foreach ($vracimport->getCsv() as $num => $line): ?>
            <tr style="font-family: monospace;" id="line<?php echo $num + 1 ?>" class="<?php echo count($csvVrac->getErreurs($num + 1)) ? 'danger text-danger' : '' ?>">
                <td class="text-right"><?php echo $num + 1 ?></td>
                <?php foreach ($line as $td): ?>
                    <td><?php echo $td ?></td>
                <?php endforeach; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
