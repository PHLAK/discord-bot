<?php

use Illuminate\Support\Facades\Schedule;

Schedule::command('horizon:snapshot')->everyFiveMinutes();

Schedule::command('telescope:prune --hours=120')->daily();
