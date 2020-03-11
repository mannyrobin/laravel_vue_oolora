<?php

namespace App\Http\Controllers;

use App\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VoucherController extends Controller
{

    public function index()
    {
        $vouchers = Voucher::all();
        return response()->json(['data'=> $vouchers]);

    }


    public function getVoucher()
    {

        return view('vouchers.voucher-form');
    }


    public function utilize(Request $request)
    {
        $this->validate( $request, [
           'code' => 'required',
        ]);


        $code = $request->code;
        $user = $request->user_id;

        $voucher = Voucher::where('code', $code)->first();

        if($voucher!==null) {

            $user_plan = DB::table('plan_subscriptions')->where('subscribable_id', '=', $user)->first();

            if($voucher->status=='used') {

                return back()->with('error', 'This code has already been used. Kindly contact the system administrator');

            }else{

                $voucher->where('code', $code)->update(['status' => 'used', 'user_id'=>$user]);
                $end_date = date('Y-m-d', strtotime('+'.''.$voucher->period.''.'years'));

               // dd($end_date);
                DB::table('plan_subscriptions')->where('subscribable_id', $user)
                    ->update([
                        'starts_at' =>date('Y-m-d'),
                        'ends_at'=>$end_date,
                        'trial_ends_at'=>$end_date,
                        'gateway'=> 'voucher',
                        'gateway_subscription_id'=>'voucher',
                        'plan_id' =>$voucher->plan_id
                    ]);
                return redirect()->to('/billing');
                //return back()->with('success', 'You have successfully used your redemption code');
            }
        }else{
            return back()->with('error', 'This code is invalid. Check and try again or kindly contact the system administrator');
        }




    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'count' => 'required|numeric',
            'plan_id' => 'required',
            'period' => 'required',
        ]);

        $count = $request->post("count");

        if($count!="" || $count!=0){
            $min =100000000000;
            $max = 999999999999999;
            $range = array();
            $i=0;
            while ($i++ < $count) {

                if(!in_array($num = substr(mt_rand($min, $max), 0, 12), $range)){

                    $range[] = $num;
                }else {
                    continue;
                }

            }
            $this->generate($request, $range) ;

            if ($i>0) {
                $msg = ['message'=> $count  . " Vouchers generated "] ;
                return response()->json($msg);
            }
        }
    }



    public function generate(Request $request, $pins){

        for ($i=0; $i < sizeof($pins); $i++) {
            $data['serial_no']= substr(strtoupper(str_shuffle(md5(microtime()))), 0, 7);
            $data['code'] =$pins[$i];
            $data['status'] = "new";
            $data['period'] = $request->period;
            $data['plan_id'] = $request->plan_id;
            $data['created_at'] = date("Y-m-d");
            $data['updated_at'] = date("Y-m-d");
            if (in_array($pins[$i], Voucher::all()->toArray())) {
                continue;
            }else {
                Voucher::firstOrCreate($data);
            }

        }

    }


    public function destroy($id)
    {
        $voucher = Voucher::find($id);

        if($voucher!==null){

            if($voucher->delete()){
                $msg = ['message'=> $voucher->code  ."  ".  "Voucher deleted "] ;

            }else{
                $msg = ['message'=> "Unable to delete voucher: " ." " .$voucher->code] ;
            }
            return response()->json($msg);
        }

    }



}
