<?php
/*
 * Sample-data login for how-to videos (added 2026-11-01). Only the wpgc_users row with this ID sees fixture data (App\Portal\Demo).
 * To turn it off: set 'enabled' => false (then php artisan config:clear). To remove completely, see docs in /root/demo-account-20261101.txt.
 */
return [
    'enabled' => true,
    'user_id' => 4752, // wpgc_users.ID for login "demobusiness"
];
