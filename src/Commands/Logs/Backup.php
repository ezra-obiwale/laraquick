<?php

namespace Laraquick\Commands\Logs;

use Illuminate\Console\Command;

class Backup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'log:backup 
        { name=laravel : The name of the log file to backup } 
        { --clear : Clear the log file after backup }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Backups up the log file';

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
        $logName = $this->argument('name');
        $logPath = $this->logPath($logName);
        $clear = $this->option('clear');

        if (!file_exists($logPath)) {
            return $this->error('Log file does not exist');
        }

        $count = 0;
        $newLogName = $logName  . '-' . now()->toDateString();
        $newLogPath = $this->logPath($newLogName, $count);
        while (file_exists($newLogPath)) {
            $count++;
            $newLogPath = $this->logPath($newLogName, $count);
        }
        if (copy($logPath, $newLogPath)) {
            $this->info("Backed " . basename($logPath) . " up to " . basename($newLogPath));
            if ($clear) {
                if (!file_put_contents($logPath, '')) {
                    $this->info(basename($logPath) . ' has been cleared');
                } else {
                    $this->error('Clear failed');
                }
            }
        }
    }

    protected function logPath($name, $count = 0) {
        $logName = $name;
        if ($count) {
            $logName .= '-' . $count;
        }
        return storage_path('logs' . DIRECTORY_SEPARATOR . $logName . '.log');
    }
}
