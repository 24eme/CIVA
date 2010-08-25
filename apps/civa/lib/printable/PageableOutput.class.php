<?php

class PageableOutput {

  protected $title;
  protected $link;
  protected $subtitle;
  protected $filename;
  

  public function __construct($title, $subtitle, $filename = '', $link = ' de ') {
    $this->title = $title;
    $this->link = $link;
    $this->subtitle = $subtitle;
    $this->filename = $filename;
    $this->init();
  }

  public function addPage($html) {
  }

  public function output() {
  }
}

