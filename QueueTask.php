<?php

namespace App\Commands;

use Blocks\Models\QueueModel;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class QueueTask extends BaseCommand
{
    
    protected $group = 'queue';
    protected $name = 'queue';
    protected $description = 'My scheduled task';

    
    public function run(array $params)
    {
        $queueModel = new QueueModel();
        $queueModel->processPendingTasks();
    }
}
