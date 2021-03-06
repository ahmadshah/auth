<?php namespace Orchestra\Auth\Console;

use Illuminate\Console\Command;

class AuthCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'auth:migrate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run migration for orchestra/auth package.';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $this->call('migrate', array('--package' => 'orchestra/auth'));
    }
}
