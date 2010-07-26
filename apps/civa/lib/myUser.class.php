<?php

class myUser extends sfBasicSecurityUser
{
    const SESSION_CVI = 'recoltant_cvi';

    public function getRecoltantCvi() {
        //return $this->getAttribute(self::SESSION_CVI, null);
        return '6701800110';
    }

    public function setRecoltantCvi($cvi) {
        return $this->setAttribute(self::SESSION_CVI, $cvi);
    }
}
