<?php
/**
 * Front controller fallback for hosts (e.g. cPanel) whose document root is
 * this repo's root rather than /public. Normally .htaccess rewrites every
 * request into public/ before PHP ever runs, but this covers the case where
 * mod_rewrite is unavailable or this file is requested directly.
 */

require __DIR__.'/public/index.php';
