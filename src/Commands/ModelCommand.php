<?php

namespace Voyager\Admin\Commands;

use Illuminate\Foundation\Console\ModelMakeCommand;

class ModelCommand extends ModelMakeCommand
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'voyager:model';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the Voyager Admin package';

    protected function getStub()
    {
        return __DIR__.'/../../resources/stubs/model.stub';
    }
}
