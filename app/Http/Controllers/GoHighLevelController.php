<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use Illuminate\Http\Request;
use App\User;
use Auth;
use Hash;
use Date;
use App\AffiliateUser;
use App\AffiliateReferral;
use App\Coupon;
use App\AdditionalCreditAppInfo;
class GoHighLevelController extends Controller
{

    public function authorizationCode($client_id, $client_secret, $grant_type){
        $client = new \GuzzleHttp\Client();
         
        try {
            $response = $client->request('POST', 'https://services.leadconnectorhq.com/oauth/token', [
                'form_params' => [
                    'client_id' => $client_id,
                    'client_secret' => $client_secret,
                    'grant_type' => 'authorization_code'
                ],
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
            ]);
    
            return (string) $response->getBody();
        } catch (\Throwable $th) {
            \Log::warning('GHL authorization failed: '.substr($th->getMessage(),0,200));
            return null;
        }
    }
    public function workflow(){
        
        //Guzzle
        
        $token= $this->authorizationCode('65fda6b133000f0b17407784-lu2v3dvy', '76d4385f-42d5-4690-8250-690345ec28ec', 'authorization_code');
        $ghlNotice = $token ? null : 'Go High Level is not connected yet. Workflows will appear here once the Go High Level account is authorized.';
        return view('admin.ghlworkflow', compact('ghlNotice'));
        // $client = new \GuzzleHttp\Client();

         
    }

    public function webhookurl(Request $request){
        dd($request);
    }
}