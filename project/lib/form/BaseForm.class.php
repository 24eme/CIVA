<?php

/**
 * Base project form.
 *
 * @package    civa
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: BaseForm.class.php 20147 2009-07-13 11:46:57Z FabianLange $
 */
class BaseForm extends sfFormSymfony
{
    public function __construct($defaults = array(), $options = array(), $CSRFSecret = null)
    {
        parent::__construct($defaults, $options, $CSRFSecret);

        if(isset($options['bootstrap'])) {
            $this->getWidgetSchema()->addFormFormatter('bootstrap', new bsWidgetFormSchemaFormatterBootstrap($this->getWidgetSchema()));
            sfWidgetFormSchema::setDefaultFormFormatterName('bootstrap');
        }
    }
}
