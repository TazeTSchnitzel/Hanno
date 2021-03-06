<?php
namespace ajf\Hanno;

require_once '../vendor/autoload.php';

use \SplQueue as Queue;

class Awaitable
{
    private $finished = false;
    private $listeners;
    private $task = NULL;
    
    public function __construct()
    {
        $this->listeners = new Queue;
    }
    
    private function assertNotFinished()
    {
        if ($this->finished) {
            throw new \Exception("Cannot call addListener or finish on a finished Awaitable");
        }
    }
    
    public function addListener(callable $listener)
    {
        $this->assertNotFinished();
        $this->listeners->enqueue($listener);
    }
    
    public function setTask(\Generator $task)
    {
        if ($this->task) {
            throw new \Exception("Cannot set a task if Awaitable already has one");
        }
        $this->task = $task;
    }
    
    public function getTask()
    {
        return $this->task;
    }
    
    public function finish($obj)
    {
        $this->assertNotFinished();
        while (!$this->listeners->isEmpty()) {
            $listener = $this->listeners->dequeue();
            $listener($obj);
        }
        $this->finished = true;
    }
    
    public function isFinished()
    {
        return $this->finished;
    }
}