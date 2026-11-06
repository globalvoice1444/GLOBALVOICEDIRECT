<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

class HomeController extends Controller
{
  /**
   * Create a new controller instance.
   *
   * @return void
   */
  public function __construct()
  {
    $this->middleware('auth')->except(['memberShipCardReview', 'getData', 'getRateCenter', 'maintainSession', 'contactUs', 'searchlocalareacode', 'singleProduct', 'search', 'getAllAvailableNumbersbyLocalcode']);
  }
  /**
   * Show the application dashboard.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    return view('home');
  }
  public function singleProduct(Request $req)
  {

    $productDet = DB::table('products')->where('ProductID', '=', $req->id)->first();

    return view('singleProduct', compact('productDet'));
  }
  //return view of member ship card during registration

  public function search(Request $req)
  {

    $pageProductlimit = 9;
    $searched = $req->search;
    if (!isset($_GET['page'])) {
      $page = 1;
    } else {
      $page = max(1, (int)$_GET['page']);
    }

    $startLimit = ($page - 1) * $pageProductlimit;

    $query = "SELECT pro.*,pr.* from products pro LEFT JOIN productad_on pr ON pro.ProductID = pr.product_id WHERE ProductName LIKE ? OR ProductCartDesc LIKE ? OR ProductPrice LIKE ? and ProductLive = 0 LIMIT ".(int)$startLimit." , ".(int)$pageProductlimit;

    $execute = DB::select($query, ['%'.$searched.'%', '%'.$searched.'%', '%'.$searched.'%']);

    $productListsCount = count($execute);
    $totalPages = ceil($productListsCount / $pageProductlimit);

    return view('search', compact(
      'productListsCount',
      'execute',
      'totalPages',
      'page',
      'searched'
    ));
  }
  public function memberShipCardReview(Request $req)
  {

    if (!isset($req->val) or !isset($req->package) or $req->val > 2 or $req->val < 0) {

      return redirect()->back();
    } else {

      //$response=$this->getAllStates();

      return view('numbers.index');
    }
  }
  public function searchlocalareacode(Request $req)
  {
    $response = array();
    $response['success'] = 0;
    $response['message'] = '';
    $response['data'] = [];

    $customer_login = config('services.vitelity.login');
    $customer_password = config('services.vitelity.pass');


    $state = $req->state;
    $rateCenter = $req->ratecenter;

    if ($customer_login and $customer_password) {

      $result = file_get_contents("https://api.vitelity.net/api.php?login=" . $customer_login . "&pass=" . $customer_password . "&cmd=listlocal&state=" . $state . "&ratecenter=" . $rateCenter . "&xml=yes");

      $xml = simplexml_load_string($result);
      $num = $xml->numbers;
      if ($xml->status == 'ok') {
        $response['success'] = 1;
        for ($i = 0; $i < sizeof($num->did); $i++) {
          array_push($response['data'], $num->did[$i]->number);
        }
      } else {
        $response['message'] = $xml->error;
      }
    }
    die(json_encode($response));
  }

  //BULK Api for DID services

  //Search inventory of available telephone numbers by npa, nxx
  // public function getAllAvailableNumbersbyLocalcode(Request $request)
  // {
  //   $response=array();
  //   $response['success']= 0;
  //   $response['message']='';
  //   $response['data']=[];

  //   $npa=$request->tollNum;
  //   $client = new \GuzzleHttp\Client();
  //   $headers = [
  //     'Authorization' => 'Basic aW5mb0BnbG9iYWx2b2ljZWRpcmVjdC5jb206OGNmMTM2NGJiN2JkNDgyOGNhZjBkNDE2M2RhNDkxZTQ='
  //   ];
  //   $request = $client->request('GET', 'https://portal.bulkvs.com/api/v1.0/orderTn?Npa='.$npa.'&Lca=true&Limit=100', ['headers' => $headers]);

  //   $numbers = json_decode($request->getBody());
  //   foreach ($numbers as $number) {
  //     array_push($response['data'], $number->TN);
  //   }
  //   $response['success']= 1;
  //   die(json_encode($response));

  // }


  public function getData(Request $req)
  {
    $response = [
      'success' => 0,
      'message' => '',
      'dids'    => [],
      'resultSet' => ''
    ];

    $username = config('services.commio.username');
    $token    = config('services.commio.token');

    $input = trim($req->search);

    if (!$username || !$token || !$input) {
      $response['message'] = "Missing API credentials or search input.";
      return response()->json($response);
    }

    $tollFreePrefixes = ['800', '833', '844', '855', '866', '877', '888'];

    if (strlen($input) == 3 && in_array($input, $tollFreePrefixes)) {
      // Toll-free → Only difference is searchType=tollfree
      $response['resultSet'] = 'tollfree';
      $url = "https://api.thinq.com/inbound/get-numbers?" . http_build_query([
        'searchType'   => 'tollfree',
        'searchBy'     => 'npa',
        'quantity'     => 100,
        'contiguous'   => 'false',
        'npa'          => $input,
        'related'      => 'true',
        'onNetOnly'    => 'true'
      ]);
    } elseif (strlen($input) == 3 && is_numeric($input)) {
      // Local → Uses the exact API structure you want
      $response['resultSet'] = 'local';
      $url = "https://api.thinq.com/inbound/get-numbers?" . http_build_query([
        'searchType'   => 'domestic',
        'searchBy'     => 'npa',
        'quantity'     => 100,
        'contiguous'   => 'false',
        'npa'          => $input,
        'related'      => 'true',
        'onNetOnly'    => 'true'
      ]);
    } else {
      $response['message'] = "Invalid input. Must be a 3-digit area code.";
      return response()->json($response);
    }

    // CURL request
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => $url,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_HTTPHEADER => [
        "Authorization: Basic " . base64_encode("$username:$token"),
        "Content-Type: application/json",
      ],
    ]);

    $result = curl_exec($curl);
    $error  = curl_error($curl);
    curl_close($curl);

    if ($error) {
      $response['message'] = "Request Error: $error";
      return response()->json($response);
    }

    $json = json_decode($result, true);

    if (!empty($json['dids'])) {
      $response['success'] = 1;

      foreach ($json['dids'] as $num) {
        $response['dids'][] = [
          'didSummary' => $num['didSummary'] ?? '',
          'id'         => $num['id'] ?? '',
          'npanxx'     => $num['npanxx'] ?? '',
          'carrierName' => $num['carrierName'] ?? ''
        ];
      }
    } else {
      $response['message'] = "No numbers found.";
    }

    return response()->json($response);
  }




  public function getAllStates()
  {
    $response = array();
    $response['success'] = 0;
    $response['message'] = '';
    $response['data'] = [];

    $customer_login = config('services.vitelity.login');
    $customer_password = config('services.vitelity.pass');

    //$state=$_GET['tollNum'];

    if ($customer_login and $customer_password) {

      $result = file_get_contents("https://api.vitelity.net/api.php?login=" . $customer_login . "&pass=" . $customer_password . "&cmd=liststates&xml=yes");

      $xml = simplexml_load_string($result);
      if ($xml->status == 'ok') {
        $response['success'] = 1;
        foreach ($xml->states as $number) {
          $response['data'] = $number;
        }
      }
      return $response;
    }
  }
  // public function getData(Request $req)
  // {
  //   $response = array();
  //   $response['success'] = 0;
  //   $response['message'] = '';
  //   $response['data'] = [];

  //   $customer_login = config('services.vitelity.login');
  //   $customer_password = config('services.vitelity.pass');

  //   $state = $req->tollNum;

  //   if ($customer_login and $customer_password and $state) {

  //     $result = file_get_contents("https://api.vitelity.net/api.php?login=" . $customer_login . "&pass=" . $customer_password . "&cmd=searchtoll&did=" . $state . "&xml=yes");

  //     $xml = simplexml_load_string($result);
  //     if ($xml->status == 'ok') {
  //       $response['success'] = 1;
  //       foreach ($xml->numbers as $number) {
  //         $response['data'] = $number;
  //       }
  //     } else {
  //       $result = file_get_contents("https://api.vitelity.net/api.php?login=" . $customer_login . "&pass=" . $customer_password . "&cmd=listtollfree&limit=10&xml=yes");

  //       $xml = simplexml_load_string($result);

  //       if ($xml->status == 'ok') {
  //         $response['success'] = 1;
  //         foreach ($xml->numbers as $number) {
  //           $response['data'] = $number;
  //         }
  //       } else {
  //         $response['message'] = $xml->error;
  //       }
  //     }
  //     die(json_encode($response));
  //   }
  // }
  public function getRateCenter(Request $req)
  {
    $response = array();
    $response['success'] = 0;
    $response['message'] = '';
    $response['data'] = [];

    $customer_login = config('services.vitelity.login');
    $customer_password = config('services.vitelity.pass');

    $state = $req->state;

    if ($customer_login and $customer_password) {

      $result = file_get_contents("https://api.vitelity.net/api.php?login=" . $customer_login . "&pass=" . $customer_password . "&cmd=listratecenters&state=" . $state . "&xml=yes");

      $xml = simplexml_load_string($result);
      if ($xml->status == 'ok') {
        $response['success'] = 1;
        foreach ($xml->ratecenters as $number) {
          $response['data'] = $number;
          //array_push($response['data'],$number);
        }
      } else {
        $response['message'] = $xml->error;
      }
    }
    //var_dump($response);
    die(json_encode($response));
  }
  public function maintainSession(Request $req)
  {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $number =  $_POST['number'];
    // $state = $_POST['state'];
    // $ratecenter = $_POST['ratecenter'];
    $pkg = $_POST['package'];
    $_SESSION["SelectNumberInfo"] = ['number' => $number, 'package' => $pkg];
  }

  public function contactUs(Request $req)
  {
    $comment = "";
    $message = "";
    if (isset($_POST['g-recaptcha-response'])) {
      $captcha = $_POST['g-recaptcha-response'];
      $response = json_decode(file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LduP20UAAAAAJkEdwQFjXY1wzmO96yLchWbaRmj&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']), true);
      if ($response['success'] == false) {
        $comment = "spammer";
        $message = "You are a Spammer...Get out from the Site.";
        return redirect()->route('contact-us', ['error' => $message]);
      } else {
        if (isset($_POST['email'])) {
          $htmlStr = "";
          $htmlStr .= "Hi ," . "<br /><br /><strong>Here is the Sender Message</strong><br/>";

          $htmlStr .= $_POST['message'] . "<br/><br/>";

          $htmlStr .= "<strong>Here is the Sender Email: </strong>" . $_POST['email'];


          $name = "Contact Us " . $_POST['name'];
          $email_sender = $email_sender = "no-reply@globalvoicedirect.com";;
          $subject = $_POST['subject'];
          $recipient_email = "support@globalvoicedirect.com";

          $body = $htmlStr;

        $__mailOk = true;
        try {
          \Mail::send(array(), array(), function ($message) use ($htmlStr, $recipient_email, $req) {
            $message->to($recipient_email)
              ->subject($req->subject)
              ->from(config('mail.from.address'))
              ->html($htmlStr);
          });
        } catch (\Throwable $__e) {
            $__mailOk = false;
            \Log::error('Mail send failed in ' . __METHOD__ . ': ' . $__e->getMessage());
        }

          if ($__mailOk) {
            $message = "You information sent to support center successfully. Thanks";
            return redirect()->route('contact-us', ['message' => $message]);
          } else {
            $message = "Something Went Wrong...Please try again.";
            return redirect()->route('contact-us', ['error' => $message]);
          }
        }
      }
    } else {
      $message = "Recaptcha required.";
      return redirect()->route('contact-us', ['error' => $message]);
    }
  }

  public function indexGVD()
  {

    return view('index');
  }
}
