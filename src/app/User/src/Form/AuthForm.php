<?php

namespace Bleidd\App\User\Form;

use Bleidd\Facade\URL;
use Bleidd\Facade\Language;
use Bleidd\Form\FormBuilder;
use Bleidd\Form\Element\Text;
use Bleidd\Form\Element\Submit;
use Bleidd\App\User\Model\User;
use Bleidd\App\User\Repository\UserRepository;

class AuthForm
{

    /**
     * @return FormBuilder
     */
    public function build(): FormBuilder
    {
        $formBuilder = (new FormBuilder('auth_form'))
            ->setAction(URL::fromRoute('admin.login'))
            ->setMethod('POST')
            ->addAttribute('class', 'form-buttons--bottom-right')
            ->setHydrator(FormBuilder::CLASS_METHODS_CAMELCASE_HYDRATOR);

        $formBuilder->add((new Text('email'))
            ->setLabel(Language::translate('user.email'))
        );
        $formBuilder->add((new Text('password'))
            ->setLabel(Language::translate('user.password'))
        );
        $formBuilder->add((new Submit('submit'))
            ->setLabel("<i class='fa fa-check'></i>")
            ->setAttribute('class', 'button button--round button--success')
        );
        
        //$formBuilder->bind((new UserRepository())->find(1));
        $formBuilder->bind(new User());

        return $formBuilder;
    }

}
