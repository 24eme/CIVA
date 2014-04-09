<?php

class PublicTasks
{
    protected $path;
    protected $tasks;

    public function __construct($path) {
        $this->path = $path;
        $this->read();
    }

    public function read() {
        $tasks = array();
        $files = sfFinder::type('file')->name('*.sh')->relative()->in($this->path);

        foreach($files as $file) {
            $namespaces = explode('/', $file);
            array_pop($namespaces);
            $namespace = implode('/', $namespaces);
            $task = new PublicTask($this->path.'/'.$file, $namespace);

            $tasks[$namespace][$task->getSlug()] = $task;
        }

        $this->tasks = $tasks;
    }

    public function find($namespace, $slug) {
        if(!array_key_exists($namespace, $this->tasks)) {

            return null;
        }

        if(!array_key_exists($slug, $this->tasks[$namespace])) {

            return null;
        }

        return $this->tasks[$namespace][$slug];
    }

    public function getTasks() {

        return $this->tasks;
    }
}