<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\User;
use Auth;
use Hash;
use Date;

class BulkvsController extends Controller
{

    public function bulkvsWebook(Request $request){
        Log::info('Response' . json_encode($request));
    }
}