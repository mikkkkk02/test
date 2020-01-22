<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SearchableRefresh extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'search:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "Refresh all object's searchable array value";

    protected $models = null;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        /* Fetch all models */
        $this->models = $this->fetchModels();

        $this->info(PHP_EOL . "Refreshing searchable array values" . PHP_EOL);


        /* Loop through each php files */
        foreach ($this->models as $key => $model) {

            /* Fetch model name base on filename, add in "App\" string for it to work w/o the USE function */
            $modelName = "App\\" . $this->removeStrings([app_path(), 'app/', '.php'], $model);

            /* Check if class name has been defined */
            if(class_exists($modelName, true)) {
                /* Create one temp class */
                $model = new $modelName();

                /* Check if class name is an instance of a model */
                if($model instanceof \Illuminate\Database\Eloquent\Model) {
                    /* Check if class is searchable */
                    if(method_exists($model, 'toSearchableArray')) {

                        $this->info("<fg=yellow;>Refreshing: <fg=white;>" . $modelName . "...");

                        /* Run searchable */
                        $modelName::get()->searchable();
                    }
                }
            }
        }


        $this->info(PHP_EOL . "Searchable array values successfully refreshed!" . PHP_EOL);        
    }

    private function removeStrings(array $strings, $word) {
        foreach ($strings as $string) {
            $word = str_replace($string, '', $word);
        }

        return $word;
    }

    /**
     * Fetch all declared model classes
     * 
     * @return [type] [description]
     */
    private function fetchModels()
    {
        return \File::files(basename(app_path()));
    }
}
