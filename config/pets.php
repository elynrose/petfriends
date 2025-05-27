<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Pet Availability Settings
    |--------------------------------------------------------------------------
    |
    | These settings define the valid time range for pet availability and bookings.
    | Hours are in 24-hour format.
    |
    */

    'availability_start_hour' => 6,  // Represents 06:00 AM. Bookings can start from this hour.
    'availability_end_hour'   => 22, // Represents 22:00 PM. Bookings must end before this hour.
                                     // So, the latest allowed end time is effectively 21:59.
];
