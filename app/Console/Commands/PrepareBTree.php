<?php

namespace App\Console\Commands;

use App\Models\Tree;
use App\Services\TreeService;
use Illuminate\Console\Command;

class PrepareBTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'preparebtree';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prepare BTree';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    /**
     * @var TreeService
     */
    private $treeService;


    public function __construct(TreeService $treeService)
    {
        parent::__construct();
        $this->treeService = $treeService;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $count = $this->ask("Specify the number of tree elements");
        try {
            Tree::truncate();
            $data = range(1, $count);
            shuffle($data);
            $this->treeService->init($data);
            $this->info("BTree data has been prepared");
        } catch (\Exception $e) {
            $this->error("We cannot prepare BTree data");
            $this->error($e->getMessage());
        }
        return true;
    }
}
