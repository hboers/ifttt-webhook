<?php namespace App;

class Message extends Eloquent 

	protected $table = 'messages';
	protected $fillable = ['user', 'password', 'body', 'subject'];

function toMessageArray() {
		return [
			'user' => $this->user,
			'password' => $this->password,
			'subject' => $this->subject,
			'body' => $this->body,
			'tags' => explode("\n",$this->tags);
		];
}

function toMessageJson() { return json_encode($this->toMessageArray()); }

}
