<?php namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WordpressController extends Controller {

	function __construct()
	{
		$this->middleware('guest');
	}

	function postIndex(Request $request)
	{
		$xml = simplexml_load_string($request->getContent());

		switch($xml->methodName) {

			//wordpress blog verification
			case 'mt.supportedMethods':
				self::success('metaWeblog.getRecentPosts');
				break;

			//first authentication request from ifttt
			case 'metaWeblog.getRecentPosts':
				//send a blank blog response
				//this also makes sure that the channel is never triggered
				self::success('<array><data></data></array>');
				break;

			case 'metaWeblog.newPost':
				self::newPost($xml);
				break;	
		}

	}
	

	private static function newPost($xml) {
		//@see http://codex.wordpress.org/XML-RPC_WordPress_API/Posts#wp.newPost
		$obj = new \stdClass;
		//get the parameters from xml
		$obj->user = (string)$xml->params->param[1]->value->string;
		$obj->pass = (string)$xml->params->param[2]->value->string;

		$targets=array();

		$content = $xml->params->param[3]->value->struct->member;
		foreach($content as $data) {

			switch((string)$data->name) {

				//neglect these sections of the request
				case 'post_status' ://publish status
				case 'mt_keywords': //tags
					break;

				//the passed categories are parsed into an array
				case 'categories':
					foreach($data->xpath('value/array/data/value/string') as $cat)
						array_push($targets,(string)$cat);
					break;
		
				case 'description':
					$obj->data = (string)$data->value->string;
					break;
			}
		}

		foreach ($targets as $target) {

			$client = new \GuzzleHttp\Client(['base_url' => $target]);
			$response = $client->post('/', [
				'headers' => ['Content-Type' => 'application/json'],
				'body' => json_encode($obj)
			]);

			$status = $response->getStatusCode();


			if ($status < 400) {
				self::success('<string>'.$status.'</string>');
			}

			else {
				self::failure($status);
			}

/*
			$headers = array('Content-Type' => 'application/json');
			$response = Requests::post($url, $headers, json_encode($obj));

			if($response->success)
				self::success('<string>'.$response->status_code.'</string>');
			else
				self::failure($response->status_code);
 */

		}

		if (count($targets) == 0) {
			//since the url was invalid, we return 400 (Bad Request)
			self::failure(400);
		}
	}

	function getIndex()	{
		return view('welcome');
	}


	private static function success($innerXML) {

		$xml =  <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <params>
    <param>
      <value>
      $innerXML
      </value>
    </param>
  </params>
</methodResponse>

EOD;
		self::output($xml);

	}


	private static function failure($status){

		$xml= <<<EOD
<?xml version="1.0"?>
<methodResponse>
  <fault>
    <value>
      <struct>
        <member>
          <name>faultCode</name>
          <value><int>$status</int></value>
        </member>
        <member>
          <name>faultString</name>
          <value><string>Request was not successful.</string></value>
        </member>
      </struct>
    </value>
  </fault>
</methodResponse>

EOD;
		self::output($xml);
	}

	private static function output($xml){
		$length = strlen($xml);
		header('Connection: close');
		header('Content-Length: '.$length);
		header('Content-Type: text/xml');
		header('Date: '.date('r'));
		echo $xml;
	}



}
