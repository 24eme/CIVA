<?php

require_once(sfConfig::get('sf_lib_dir').'/vendor/tcpdf/tcpdf.php');

class PageableHTML extends PageableOutput {

  protected $html;

  protected function init() {
  }

  public function addPage($html) {
    $this->html .= $html;
    $this->html .= '<hr/>';
  }

  public function output() {
    return $this->html;
  }
}

