<?php

class Export extends BaseExport
{
	public function generateCle() {
		$this->cle = sha1(uniqid($this->get('_id')).rand(1, 9999).time());
	}
}