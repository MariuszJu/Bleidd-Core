<?php

namespace Bleidd\Form;

use Bleidd\Form\Traits\FormElementsTrait;

class Fieldset
{

    use FormElementsTrait;

    /** @var string */
    protected $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

}
