<?php

namespace HessamDev\Hessam\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use HessamDev\Hessam\Models\HessamComment;

/**
 * Class CommentWillBeDeleted
 * @package HessamDev\Hessam\Events
 */
class CommentWillBeDeleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /** @var  HessamComment */
    public $comment;

    /**
     * CommentWillBeDeleted constructor.
     * @param HessamComment $comment
     */
    public function __construct(HessamComment $comment)
    {
        $this->comment=$comment;
    }

}
