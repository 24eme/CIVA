<?php

class PageableOutput {

  protected $title;
  protected $link;
  protected $subtitle;
  protected $filename;
  protected $file_dir;
  protected $orientation;
  protected $config;

  public function __construct($title, $subtitle, $filename = '', $file_dir = null, $link = ' de ', $orientation = 'L', $config = array()) {
    $this->title = $title;
    $this->link = $link;
    $this->subtitle = $subtitle;
    $this->filename = $filename;
    $this->file_dir = $file_dir;
    $this->orientation = $orientation;
    $this->config = $config;
    $this->init();
  }

  public function addPage($html) {
  }

  public function output() {
  }

  public function isCached() {
  }

  public function removeCache() {
    return true;
  }

  public function addHeaders($response) {
  }

  public function generatePDF($no_cache = false) {
  }

}

