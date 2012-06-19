<?php

require_once(sfConfig::get('sf_lib_dir').'/vendor/tcpdf/tcpdf.php');

class PageableHTML extends PageableOutput {

  protected $html;

  protected function init() {
    $this->html = '<html xmlns="http://www.w3.org/1999/xhtml" lang="fr" xml:lang="fr">
<head>
  <title>'.$this->title.$this->link.$this->subtitle.'</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  </head></body><h1>'.$this->title.$this->link.$this->subtitle.'</h1>';
  }

  public function addPage($html) {
    $this->html .= $html;
    $this->html .= '<hr/>';
  }

  public function output() {
    echo $this->html.'</body></html>';
    return;
  }
}

