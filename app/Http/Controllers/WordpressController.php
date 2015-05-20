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
				self::success('<array><data></data></array>');
				break;

			case 'metaWeblog.newPost':
				self::newPost($xml);
				break;	
		}
	}
	
	private static function newPost($xml) {

		$message = new App\Message;
		$message->user = (string)$xml->params->param[1]->value->string;
		$message->password = (string)$xml->params->param[2]->value->string;

		$targets=[];
		$tags=[];

		$content = $xml->params->param[3]->value->struct->member;
		foreach($content as $data) {

			switch((string)$data->name) {

				case 'mt_keywords': //tags
					foreach($data->xpath('value/array/data/value/string') as $tag) {
					  array_push($tags,(string)$tag);
					}
					$message->tags = implode("\n",tags);
					break;

				case 'title':
					$message->subject = (string)$data->value->string;
					break;	  
		
				case 'description':
					$message->body = (string)$data->value->string;
					break;

				case 'categories':
					foreach($data->xpath('value/array/data/value/string') as $cat) {
					  array_push($targets,(string)$cat);
					}
					break;
			}
		}

		foreach ($targets as $target) {

			if(preg_match("/^[a-zA-Z0-9]+$/", $target) == 1) {

			  $url = config('app.target');
			  $url .= '/'.trim($target,'/');
			
			  $client = new \GuzzleHttp\Client();
			  $response = $client->post($url, [
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

			  // TODO process multiple Targets
			}

			else {
				// TODO May be we should allow full urls here ?
				self::failure(400);
			}
			break;
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
