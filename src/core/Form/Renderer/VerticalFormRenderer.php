<?php

namespace Bleidd\Form\Renderer;

use Bleidd\Application\Runtime;
use Bleidd\Form\Element\Button;
use Bleidd\Form\FormBuilder;

class VerticalFormRenderer implements FormRenderer
{

    /**
     * @param FormBuilder $form
     * @return string
     */
    public function render(FormBuilder $form): string
    {
        $elementsHtml = '';
        $buttonsHtml = '';

        foreach ($form->getElements() as $element) {
            if ($element instanceof Button) {
                $buttonsHtml .= (string) $element;
                continue;
            }

            $elementsHtml .= Runtime::view('form.element.element', [
                'element' => $element
            ])->render(true);
        }

        $form->addAttribute('class', 'form-layout--vertical');

        return Runtime::view('form.vertical', [
            'elements' => $elementsHtml,
            'buttons'  => $buttonsHtml,
            'form'     => $form,
        ])->render(true);
    }

}
