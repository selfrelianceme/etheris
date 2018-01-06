<?php

namespace Selfreliance\Etheris;

use Illuminate\Http\Request;
use Config;
use Route;
use Log;

use Illuminate\Foundation\Validation\ValidatesRequests;

use Selfreliance\Etheris\Events\EtherisPaymentIncome;
use Selfreliance\Etheris\Events\EtherisPaymentCancel;

use Selfreliance\Etheris\EtherisInterface;
use Selfreliance\Etheris\Exceptions\EtherisException;
use GuzzleHttp\Client;

class Etheris implements EtherisInterface
{
	use ValidatesRequests;
	public $client;

	public function __construct(){
		$this->client = new Client([
		    'base_uri' => 'https://etheris.io/api/',
			'headers' => [
		        'auth-token' => Config::get('etheris.token')
		    ]		    
		]);
	}

	function balance($currency = 'ETH'){
		if($currency != 'ETH'){
			throw new \Exception('Only currency ETH');	
		}
		try{
			$response = $this->client->request('GET', 'wallet/balance');
			$body     = $response->getBody();
			$code     = $response->getStatusCode();
			$resp     = json_decode($body->getContents());
		}catch(\GuzzleHttp\Exception\ClientException $e){
			throw new \Exception($e->getMessage());
		}
		
		if($resp->code != 200){
			throw new \Exception($resp->message);
		}
		return $resp->balance_eth;
	}

	function form($payment_id, $sum, $units='ETH'){
		$PassData = new \stdClass();

		try{
			$response = $this->client->request('GET', 'wallet/income_payment', [
				'query' => [
					'user_fields' => [
						'payment_id' => $payment_id,
						'hash_pay'   => md5($payment_id.Config::get('etheris.secret_key'))
					]
			    ]
			]);
			$body     = $response->getBody();
			$code     = $response->getStatusCode();
			$resp     = json_decode($body->getContents());
		}catch(\GuzzleHttp\Exception\ClientException $e){
			$PassData->error = $e->getMessage();
			return PassData;
		}

		if($resp->code != 200){
			$PassData->error = $resp->message;
			return PassData;
		}

		$PassData->address = $resp->address;
		$PassData->another_site = false;

		return $PassData;
	}

	public function check_transaction(array $request, array $server, $headers = []){
		Log::info('Etheris IPN', [
			'request' => $request,
			'headers' => $headers,
			'server'  => array_intersect_key($server, [
				'PHP_AUTH_USER', 'PHP_AUTH_PW'
			])
		]);
	}

	public function validateIPN(array $post_data, array $server_data){
		
	}

	public function validateIPNRequest(Request $request) {
        return $this->check_transaction($request->all(), $request->server(), $request->headers);
    }

	public function send_money($payment_id, $amount, $address, $currency){
		if($currency != 'ETH'){
			throw new \Exception('Only currency ETH');	
		}
		
		try{
			$response = $this->client->request('POST', 'wallet/sendfunds', [
				'form_params' => [
					'amount'   => $amount,
					'to'       => $address,
					'gasprice' => 4
			    ]
			]);
			$body     = $response->getBody();
			$code     = $response->getStatusCode();
			$resp     = json_decode($body->getContents());
		}catch(\GuzzleHttp\Exception\ClientException $e){
			throw new \Exception($e->getMessage());
		}

		if($resp->code != 200){
			throw new \Exception($resp->message);
		}

		$PassData              = new \stdClass();
		$PassData->transaction = $resp->tx;
		$PassData->sending     = true;
		$PassData->add_info    = [
			"full_data" => $resp
		];
		return $PassData;
	}

	function cancel_payment(Request $request){
		// $PassData     = new \stdClass();
		// $PassData->id = $request->input('PAYMENT_ID');
		
		// event(new EtherisPaymentCancel($PassData));

		// return redirect()->route('personal.index');
	}
}