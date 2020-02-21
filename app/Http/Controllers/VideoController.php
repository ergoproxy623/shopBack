<?php

namespace App\Http\Controllers;

use App\Http\Requests\PurchaseRequest;
use App\Models\Purchase;
use App\Models\User;
use App\Models\UserVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use JWTAuth;
use App\Models\Video;
use LiqPay;

class VideoController extends Controller
{
    // list videos first block video
    public function showVideo1()
    {
        $user = auth()->user();
        $purchase = $user->purchase()
            ->where('status', Purchase::FIRST_BLOCK)
            ->where('pay_status', Purchase::PAY_SUCCESS)
            ->exists();
        if ($purchase || $user->role_id == 2) {
            $video = Video::where('status', Video::FIRST_BLOCK)->get();
            return response()->json(['success' => true, 'data' => $video]);
        }
        return response()->json(['success' => false, 'message' => trans('message.access')]);
    }

    // list videos second block video
    public function showVideo2()
    {
        $user = auth()->user();
        $purchase = $user->purchase()
            ->where('status', Purchase::SECOND_BLOCK)
            ->where('pay_status', Purchase::PAY_SUCCESS)
            ->exists();
        if ($purchase || $user->role_id == 2) {
            $video = Video::where('status', Video::SECOND_BLOCK)->get();
            return response()->json(['success' => true, 'data' => $video]);
        }
        return response()->json(['success' => false, 'message' => trans('message.access')]);
    }

    //user buy video
    public function purchase(PurchaseRequest $request)
    {
        $user = auth()->user();
        $purchase = Purchase::make($request->all());
        $purchase->user_id = $user->id;
        $purchase->save();
        if($purchase->status == Purchase::FIRST_BLOCK) {
            $liqpay = new LiqPay(config('liqpay.public_key'), config('liqpay.private_key'));
            $html = $liqpay->cnb_form(array(
                'action' => 'pay',
                'amount' => '1',
                'currency' => 'UAH',
                'description' => 'Video easy',
                'order_id' => $purchase->id,
                'version' => '3',
                'server_url' => url('/api/payment/success'),
            ));
        }

        if($purchase->status == Purchase::SECOND_BLOCK) {
            $liqpay = new LiqPay(config('liqpay.public_key'), config('liqpay.private_key'));
            $html = $liqpay->cnb_form(array(
                'action' => 'pay',
                'amount' => '2',
                'currency' => 'UAH',
                'description' => 'Video hard',
                'order_id' => $purchase->id,
                'version' => '3',
                'server_url' => url('/api/payment/success'),
            ));
        }
        return response()->json(['success' => true, 'html' => $html]);
    }

    //update user's status if payment is success
    public function paymentSuccess(Request $request)
    {
        $data = $request->input('data');
        $post_signature = $request->input('signature');

        $sign = base64_encode(sha1(
            config('liqpay.private_key') .
            $data .
            config('liqpay.private_key')
            , 1));

        if ($post_signature !== $sign) {
            return response()->json(['success' => false]);
        }

        $dataArr = json_decode(base64_decode($data), true);
        $purchase = Purchase::find($dataArr['order_id']);

        if ($dataArr['status'] === 'success') {
            $purchase->pay_status = Purchase::PAY_SUCCESS;
        } else {
            $purchase->pay_status = Purchase::PAY_FAIL;
        }

        $user = User::find($purchase->user_id);
        if ($purchase->status == Purchase::FIRST_BLOCK) {
            if($user->status == User::BUY_ANYTHING) {
                $user->status = User::BUY_ONE_BLOCK;
                $user->save();
                $purchase->save();
                return response()->json(['success' => true]);
            }
            $str = Purchase::where('user_id', $user->id)
                ->where('status', Purchase::SECOND_BLOCK)
                ->where('pay_status', Purchase::PAY_SUCCESS)
                ->count();
            if($str != 0) {
                $user->status = User::BUY_TWO_BLOCK;
                $user->save();
                $purchase->save();
                return response()->json(['success' => true]);
            }
        }
        if ($purchase->status == Purchase::SECOND_BLOCK) {
            if($user->status ==  User::BUY_ANYTHING) {
                $user->status = User::BUY_ONE_BLOCK;
                $user->save();
                $purchase->save();
                return response()->json(['success' => true]);
            }
            $str = Purchase::where('user_id', $user->id)
                ->where('status', Purchase::FIRST_BLOCK)
                ->where('pay_status', Purchase::PAY_SUCCESS)
                ->count();
            if($str != 0) {
                $user->status =  User::BUY_TWO_BLOCK;
                $user->save();
                $purchase->save();
                return response()->json(['success' => true]);
            }
        }
        return response()->json(['success' => false, 'message' => 'Status has not been changed']);
    }
}
