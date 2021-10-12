<?php

namespace App\Http\Controllers\Api\Web;

use App\Models\Invoice;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NotificationHandlerController extends Controller
{
    public function index(Request $request){
        $payload = $request->getContent();
        $notification = json_decode($payload);//json_decode merupakan fungsi php yang berguna untuk mengubah data dengan format json ke dalam object php/array assosiatif

        $validSignatureKey = hash("sha256", $notification->order_id . $notification->status_code . $notification->gross_amount . config('services.midtrans.serverKey'));
        if ($notification->signature_key != $validSignatureKey) {
            return response(['message' => 'Invalid signature'], 403);
        }

        $transaction = $notification->transaction_status;
        $type = $notification->payment_type;
        $orderId = $notification->order_id;

        //data transaction
        $data_transaction = Invoice::where('invoice',$orderId)->first();

        if($transaction == 'capture'){
            //for credit card transaction, we need to check whether transaction is challenge by FDS of not
            if($type == 'credit_card'){
                //nothing
            }elseif($type == 'settlement'){
                $data_transaction->update([
                    'status' => 'success'
                ]);
    
                //update stock product 
                foreach($data_transaction->orders()->get() as $order) {
    
                    $product = Product::whereId($order->product_id)->first();
                    $product->update([
                        'stock' => $product->stock - $order->qty
                    ]);
                }
            }elseif($transaction=='pending'){
                $data_transaction->update([
                    'status' =>'pending',
                ]);
            }elseif($transaction=='deny'){
                $data_transaction->update([
                    'status' =>'failde',
                ]);
            }elseif($transaction=='expire'){
                $data_transaction->update([
                    'status' =>'expired',
                ]);
            }elseif($transaction =='cancel'){
                $data_transaction->update([
                    'status' =>'failed',
                ]);
            }
        }
    }
}
