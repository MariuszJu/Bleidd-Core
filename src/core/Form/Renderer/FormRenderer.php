<?php

namespace Bleidd\Form\Renderer;

use Bleidd\Form\FormBuilder;

interface FormRenderer
{

    /**
     * @param FormBuilder $form
     * @return string
     */
    public function render(FormBuilder $form): string;

}
