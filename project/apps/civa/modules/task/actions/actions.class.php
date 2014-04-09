<?php
class taskActions extends sfActions 
{    
    public function executeList(sfWebRequest $request) 
    {
        $this->tasks_container = new PublicTasks(sfConfig::get('sf_root_dir').'/bin/task');
    }

    public function executeInfo(sfWebRequest $request) {
        $tasks_container = new PublicTasks(sfConfig::get('sf_root_dir').'/bin/task');
        $this->task = $tasks_container->find($request->getParameter('namespace'), $request->getParameter('slug'));
        $this->forward404Unless($this->task);

        $this->info = $this->execCmd($this->task->getCmdInfo());
    }

    public function executeRun(sfWebRequest $request) {
        $tasks_container = new PublicTasks(sfConfig::get('sf_root_dir').'/bin/task');
        $this->task = $tasks_container->find($request->getParameter('namespace'), $request->getParameter('slug'));
        $this->forward404Unless($this->task);
        
        $this->result = $this->execCmd($this->task->getCmdRun());
    }

    private function execCmd($cmd) {

        return shell_exec(sprintf("cd %s; %s", sfConfig::get('sf_root_dir'), $cmd));
    }
}
