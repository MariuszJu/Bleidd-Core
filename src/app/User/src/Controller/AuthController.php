<?php

namespace Bleidd\App\User\Controller;

use Bleidd\Facade\Auth;
use Bleidd\Facade\Config;
use Bleidd\Util\FileReader;
use Bleidd\View\ViewModel;
use Bleidd\App\User\Model\User;
use Bleidd\App\User\Form\AuthForm;
use Bleidd\Authorization\Authorization;
use Bleidd\Controller\AbstractController;
use Carbon\Carbon;

class AuthController extends AbstractController
{

    /**
     * AuthController constructor
     */
    public function __construct()
    {

    }

    /**
     * @return void
     */
    public function loginAction()
    {
        if (Auth::isAuthorized(Authorization::AREA_SYSTEM)) {
            return $this->redirect()
                ->toRoute('admin');
        }

        $view = new ViewModel();
        $form = (new AuthForm())
            ->build();

        $fileReader = new FileReader();
        $files = $fileReader->readLocation(sprintf('%s/public/assets/admin/img/login_backgrounds', ROOT_DIR));

        if ($this->request()->isPost()) {
            $data = $this->request()->post();

            $form->setData($data);
            /** @var User $user */
            $user = $form->getObject();
            
            Auth::authorize(Authorization::AREA_SYSTEM, $user->getEmail(), $user->getPassword());
        }

        $view->setLayout(Config::configKey('user.view.layout.login'));
        $view->setVariables([
            'form' => $form,
            'bg'   => $files[rand(0, count($files) - 1)]
        ]);

        return $view;
    }
    
}
