<?php

namespace Bleidd\Form\Events;

use Bleidd\Form\FormBuilder;
use Bleidd\Event\AbstractEvent;

class BeforeFormRender extends AbstractEvent
{
    
    /** @var string */
    public static $name = 'Before Form Render';
    
    /** @var FormBuilder */
    public $form;
    
    /**
     * BeforeFormRender constructor
     *
     * @param FormBuilder $form
     */
    public function __construct(FormBuilder $form)
    {
        $this->form = $form;
    }
    
}
