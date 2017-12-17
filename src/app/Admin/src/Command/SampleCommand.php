<?php

namespace Bleidd\App\Admin\Command;

use Bleidd\Console\AbstractModuleCommand;

class SampleCommand extends AbstractModuleCommand
{

    /** @var string */
    public $name = 'page:sample';

    /** @var string */
    public $signature = 'page:sample {arg}';

    /**
     * @return void
     */
    public function fire()
    {
        echo '<pre>';
        print_r($this->params('arg'));
        echo '</pre>'; die('');
    }

}
