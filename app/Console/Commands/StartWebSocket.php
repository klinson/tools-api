<?php

namespace App\Console\Commands;

use App\Handlers\WebSocketHandler;
use App\Models\User;
use Illuminate\Console\Command;

class StartWebSocket extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'websocket:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'web-socket 启动';

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
        $server = new WebSocketHandler();
//        $server->open(function ($server, $request) {
//            $this->info('No.'.$request->fd.'接入');
//        });

        $server->start();
    }
}
