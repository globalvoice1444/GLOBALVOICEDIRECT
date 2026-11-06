<?php

namespace App\Http\Controllers;

use App\Coupon;
use Illuminate\Http\Request;

class CouponsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $coupon = Coupon::where('code',$request->code)->where('end_date','>=',date('Y-m-d'))->where('start_date','<=',date('Y-m-d'))->where('is_active',1)->first();
        if (!$coupon) {
            $response = ['status'=>'error', 'message'=>'Invalid Coupon Code. Please try again.'];
            return response()->json($response);
        }
        $cartTotal =storeController::getTotal();
        $coupondiscount = $coupon->discount($cartTotal);
        session()->put('coupon',[
            'name' => $coupon->code,
            'discount' => $coupondiscount,
            'type' => $coupon->type,
            'total' => $cartTotal,
            'off' => $coupon->type=='fixed' ? $coupon->value : $coupon->percent_off,
            'downpayment' => number_format(($cartTotal-$coupondiscount)/2,2),
        ]);
        $data = session()->get('coupon');
        $response = ['status'=>'success', 'message'=>'Coupon has been applied.','cart'=>$data];
        return response()->json($response);
    }
    public static function updateTotal(){
      
      $couponcode = session()->get('coupon')['name'];
      $coupon = Coupon::where('code',$couponcode)->first();
      $cartTotal =storeController::getTotal();
      $coupondiscount = $coupon->discount($cartTotal);

      session()->put('coupon',[
          'name' => $coupon->code,
          'discount' => $coupondiscount,
          'type' => $coupon->type,
          'total' => $cartTotal,
          'off' => $coupon->type=='fixed' ? $coupon->value : $coupon->percent_off,
          'downpayment' => number_format(($cartTotal-$coupondiscount)/2,2),
      ]);
    }
    public function show(){
      $coupons = Coupon::all();
      return view('admin.couponmanagement',compact('coupons'));
    }
    public function get(Request $req){
        $this->validate($req, [
        'id' => 'required | exists:coupons,id',
        ]);
        $coupon = Coupon::find($req->id);
        return response()->json($coupon);
    }
    public function add(Request $req){
      $this->validate($req, [
        'code' => 'required|unique:coupons,code',
        'type' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
      ]);

      $coupon = new Coupon();
      $coupon->code = $req->code;
      $coupon->type = $req->type;
      if ($req->type == 'fixed') {
        $coupon->value = $req->value;
        $coupon->percent_off = null;
      }elseif ($req->type == 'percent'){
        $coupon->percent_off = $req->percent_off;
        $coupon->value = null;
      }
      $coupon->start_date = $req->start_date;
      $coupon->end_date = $req->end_date;
      $coupon->is_active = true;
      $coupon->save();

      return redirect()->back()->with('success','Coupon added successfully.');
    }

    public function update(Request $req,$id){

       $this->validate($req, [
        'code' => 'required',
        'type' => 'required',
        'start_date' => 'required',
        'end_date' => 'required',
      ]);

      $coupon = Coupon::find($id);

      $coupon->code = $req->code;
      $coupon->type = $req->type;
      if ($req->type == 'fixed') {
        $coupon->value = $req->value;
        $coupon->percent_off = null;
      }elseif ($req->type == 'percent'){
        $coupon->percent_off = $req->percent_off;
        $coupon->value = null;
      }
      $coupon->start_date = $req->start_date;
      $coupon->end_date = $req->end_date;
      $coupon->is_active = true;

      $coupon->update();

      return redirect()->back()->with('success','Coupon updated successfully.');
    }
    public function deactivate(Request $req,$id){
      $coupon = Coupon::find($id);
      if ($req->status == "true") {
        $coupon->is_active = 1;
      }elseif($req->status == "false"){
        $coupon->is_active = 0;
      }
      $coupon->update();
      return response()->json(['status'=>'success','message' => $req->status=="true"?'Coupon activated successfully':'Coupon deactivated successfully.']);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $coupon = Coupon::find($id);
        $coupon->delete();
        return redirect()->back()->with('success','Coupon deleted successfully.');
    }
}
