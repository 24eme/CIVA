<?php

class ValidatorImportSVFile extends ValidatorImportCsv
{
  protected function configure($options = array(), $messages = array())
  {
    $this->addRequiredOption('cvi');
    $options['mime_types'] = array('text/plain', 'text/xml');

    return parent::configure($options, $messages);
  }

  protected function doClean($values)
  {
      if($values['type'] == 'text/xml') {
          $xml = simplexml_load_string(file_get_contents($values['tmp_name']));
          $csv = "";
          $cvi = $this->getOption('cvi');
          foreach($xml->ligne_vin as $item) {
              $csv .= $cvi.";;".strval($item->app_fr->num_cvi).";;".strval($item->code_prd).";;;;;".((float)($item->sup_rec)*100).";;".strval($item->vol_app).";".strval($item->vol_dplc).";".strval($item->vol_vci).";".((float)($item->vol_cree)-(float)($item->vol_dplc))."\n";
              if(isset($item->rebeche)) {
                  $csv .= $cvi.";;".strval($item->app_fr->num_cvi).";;".strval($item->rebeche->code_prd).";;;;;;;".strval($item->rebeche->vol_cree).";;;".strval($item->rebeche->vol_cree)."\n";
              }
          }
          file_put_contents($values['tmp_name'], $csv);
      }

      return parent::doClean($values);
  }

}
