<?php
namespace Selfreliance\Etheris;
use Illuminate\Http\Request;
interface EtherisInterface {
   public function balance();
   public function form($payment_id, $amount, $units);
   public function check_transaction(array $request, array $server, $headers = []);
   public function send_money($payment_id, $amount, $address, $currency);
   public function cancel_payment(Request $request);
}