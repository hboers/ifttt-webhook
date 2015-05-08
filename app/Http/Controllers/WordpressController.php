<?php namespace App\Http\Controllers;

class WordpressController extends Controller {

	function __construct()
	{
		$this->middleware('guest');
	}

	function postIndex()
	{
		return ['success' => 'true'];
	}

	function getIndex() 
	{
		return view('welcome');
	}

}
