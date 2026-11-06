<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestMail;

class MailController extends Controller
{
    public function basic_email()
    {
        try {
            Mail::raw('This is a test email from Laravel', function ($message) {
                $message->to('bestsolutions4017@gmail.com')
                        ->subject('Test Email')
                        ->from(config('mail.from.address'), 'Ali Raza');
            });
    
            return "Email sent";
    
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }
}