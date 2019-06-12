<?php

namespace App\Jobs;

use App\Models\WechatMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Redis;

class AddUserCommentMessageCount implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $comment;
    protected $redis_key = 'klinson:user_comment_message_count';
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($comment)
    {
        $this->comment = $comment;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Redis::hincrby($this->redis_key, $this->comment->post->user_id, 1);
        if ($this->comment->to_user_id) {
            Redis::hincrby($this->redis_key, $this->comment->to_user_id, 1);
        }
    }

}
