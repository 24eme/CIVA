<?php

class sfWidgetFormInputFloat extends sfWidgetFormInputText
{
  public function render($name, $value = null, $attributes = array(), $errors = array())
  {
    $value = sprintFloat($value);
    return parent::render($name, $value, $attributes, $errors);
  }
}