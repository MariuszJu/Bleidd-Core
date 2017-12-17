<?php

namespace Bleidd\Event;

use Bleidd\Application\Application;

final class Dispatcher
{
    
    /** @var array */
    protected $listeners;
    
    private function __clone() {}
    private function __wakeup() {}

    /**
     * Dispatcher constructor
     *
     * @param Application $application
     */
    public function __construct(Application $application)
    {

    }
    
    /**
     * @param string|object $event
     * @param array|null    $params
     * @return array
     */
    public function fire($event, array $params = null)
    {
        $responses = [];
        $eventName = (string) $event;
        
        foreach ($this->getListeners($eventName) as $listener) {
            $responses[] = $listener($event, $params);
        }
        
        return $responses;
    }

    /**
     * @param string $event
     * @param mixed  $listener
     * @param int    $priority
     */
    public function listen(string $event, $listener, int $priority = 0)
    {
        $this->listeners[$event][$priority][] = $this->createListener($listener);
    }
    
    /**
     * @param mixed $listener
     * @return mixed
     */
    private function prepareListenerCallback($listener)
    {
        if ($listener instanceof \Closure) {
            $callback = $listener;
        } else if (class_exists($listener)) {
            $callback = [new $listener, 'handle'];
        } else if (strpos($listener, '::') !== false) {
            $callback = $listener;
        } else if (strpos($listener, '@') !== false) {
            $parts = explode('@', $listener);
            $callback = [new $parts[0], $parts[1]];
        }
        
        return $callback;
    }
    
    /**
     * @param mixed $listener
     * @return \Closure
     */
    private function createListener($listener): \Closure
    {
        $callback = $this->prepareListenerCallback($listener);
        
        return function ($event, $params) use ($callback)
        {
            if (is_object($event)) {
                $payload = [$event];
            } else {
                $payload = is_array($params) ? $params : [$params];
            }
            
            call_user_func_array($callback, $payload);
        };
    }
    
    /**
     * @param string $eventName
     * @return array
     */
    private function getListeners(string $eventName): array
    {
        $sortedListeners = [];
        $listeners = $this->listeners[$eventName] ?? [];
        sort($listeners);
        
        if (empty($listeners)) {
            return [];
        }
    
        foreach ($listeners as $priority => $listenerWithPriority) {
            foreach ($listenerWithPriority as $listener) {
                $sortedListeners[] = $listener;
            }
        }
        
        return $sortedListeners;
    }
    
}
