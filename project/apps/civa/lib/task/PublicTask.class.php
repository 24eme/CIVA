<?php

class PublicTask
{
    protected $path;
    protected $name;
    protected $slug;
    protected $namespace;

    public function __construct($path, $namespace) {
        $this->path = $path;
        $this->name = $this->buildName($path);
        $this->slug = $this->buildSlug($path);
        $this->namespace = $namespace;
    }

    public function getSlug() {
       
       return $this->slug; 
    }

    public function getNamespace() {
       
       return $this->namespace; 
    }

    public function getName() {

        return $this->name;
    }

    public function getCmdInfo() {

        return $this->getCmd();
    }

    public function getCmdRun() {

        return sprintf("%s run", $this->getCmd());
    }

    public function getCmd() {

        return sprintf("bash %s", $this->path); 
    }

    protected function buildName($path) {
        $parts = explode('/', $path);
        $filename = array_pop($parts);
        $name = str_replace(".sh", "", $filename);
        $name = str_replace("_", " ", $name);
        $name = str_replace("-", " ", $name);
        $name = strtolower($name);
        $name = ucwords($name);

        return $name;
    }

    protected function buildSlug($path) {
        $parts = explode('/', $path);
        $filename = array_pop($parts);
        $slug = str_replace(".sh", "", $filename);

        return $slug;
    }
}