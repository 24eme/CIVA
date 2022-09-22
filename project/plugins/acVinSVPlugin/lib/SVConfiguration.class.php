<?php

class SVConfiguration {

    private static $_instance = null;
    protected $configuration;

    const ALL_KEY = "_ALL";

    public static function getInstance() {
        if (is_null(self::$_instance)) {
            self::$_instance = new SV12Configuration();
        }
        return self::$_instance;
    }

    public function __construct() {
        if(!sfConfig::has('sv_configuration_sv')) {
			throw new sfException("La configuration pour les sv n'a pas été défini pour cette application");
		}

        $this->configuration = sfConfig::get('sv_configuration_sv', array());
    }

    public function getAll() {

        return $this->configuration;
    }

