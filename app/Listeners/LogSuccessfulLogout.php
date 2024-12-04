<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogSuccessfulLogout
{
    public function handle(Logout $event)
    {
        // Log audit for user logout
        $event->user->auditEvent('logout');
    }
}
