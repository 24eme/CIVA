<?php

class PageableOutput {

  protected $title;
  protected $link;
  protected $subtitle;
  protected $filename;
  protected $file_dir;
  protected $orientation;
  protected $font_size;

  public function __construct($title, $subtitle, $filename = '', $file_dir = null, $link = ' de ', $orientation = 'L', $font_size = 10) {
    $this->title = $title;
    $this->link = $link;
    $this->subtitle = $subtitle;
    $this->filename = $filename;
    $this->file_dir = $file_dir;
    $this->orientation = $orientation;
    $this->font_size = $font_size;
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

