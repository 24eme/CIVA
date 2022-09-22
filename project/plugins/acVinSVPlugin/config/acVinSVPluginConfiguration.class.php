<?php

class acVinSVPluginConfiguration extends sfPluginConfiguration
{

    public function setup() {
        if ($this->configuration instanceof sfApplicationConfiguration) {
            $configCache = $this->configuration->getConfigCache();
            $configCache->registerConfigHandler('config/sv.yml', 'sfDefineEnvironmentConfigHandler', array('prefix' => 'sv_'));
            $configCache->checkConfig('config/sv.yml');
        }
    }

    public function initialize() {
        if ($this->configuration instanceof sfApplicationConfiguration) {
            $configCache = $this->configuration->getConfigCache();
            include($configCache->checkConfig('config/sv.yml'));
        }
    }
}
