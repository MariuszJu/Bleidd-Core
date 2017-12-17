<?php

namespace Bleidd\Form\Element;

use Bleidd\Application\Runtime;

class Button extends AbstractElement
{

    /** @var string */
    protected $type = 'button';

    /**
     * @return string
     */
    protected function render(): string
    {
        return Runtime::view('form.element.button', [
            'element' => $this
        ])->render(true);
    }

}
