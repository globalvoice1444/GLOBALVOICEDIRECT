<?php
// Real SMS sending is OFF unless GVD_SMS_LIVE=true is set in .env. While off, "Send" only does a dry run.
return ['live' => filter_var(env('GVD_SMS_LIVE', false), FILTER_VALIDATE_BOOLEAN)];
