<form action="<?php echo url_for('import/csv') ?>" method="POST" enctype="multipart/form-data">
  <table>
    <?php echo $csvform ?>
    <tr>
      <td colspan="2">
        <input type="submit" />
      </td>
    </tr>
  </table>
</form>