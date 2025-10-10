<?php

// app/Providers/EventServiceProvider.php

namespace App\Providers;

use App\Models\LeaveRequest;
use App\Models\Timesheet;
use App\Models\Schedule;
use App\Models\Certification;
use App\Observers\LeaveRequestObserver;
use App\Observers\TimesheetObserver;
use App\Observers\ScheduleObserver;
use App\Observers\CertificationObserver;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [

    ];

    public function boot(): void
    {
        Timesheet::observe(TimesheetObserver::class);
        Schedule::observe(ScheduleObserver::class);
        Certification::observe(CertificationObserver::class);
        LeaveRequest::observe(LeaveRequestObserver::class);
    }


}
