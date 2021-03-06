<?php
/**
 *
 *
 *   ______                        _____           __
 *  /_  __/__  ____ _____ ___     / ___/__  ______/ /___
 *   / / / _ \/ __ `/ __ `__ \    \__ \/ / / / __  / __ \
 *  / / /  __/ /_/ / / / / / /   ___/ / /_/ / /_/ / /_/ /
 * /_/  \___/\__,_/_/ /_/ /_/   /____/\__,_/\__,_/\____/
 *
 *
 *
 * Filename->TutorialPurchased.php
 * Project->lexue
 * Description->The event fired after a tutorial is purchased by a student.
 *
 * Created by DM on 16/10/4 上午12:05.
 * Copyright 2016 Team Sudo. All rights reserved.
 *
 */
namespace App\Events;

use App\Models\Course\Order;
use App\Events\Event;
use Illuminate\Queue\SerializesModels;

class TutorialPurchased extends Event
{
    use SerializesModels;

    public $order;

    /**
     * Create a new event instance.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }
}