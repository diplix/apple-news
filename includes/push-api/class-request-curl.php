<?php
namespace Push_API;

/**
 * Helper class to handle CURL configuration.
 *
 * @since 0.0.0
 */
class Request_CURL {

	/**
	 * @var CURL Handle
	 * @since 0.0.0
	 */
	private $curl;

	function __construct( $url, $debug ) {
		// Set up CURL
		$this->curl = curl_init( $url );

		// If we want to debug using a reverse proxy, like Charles.
		if ( $debug ) {
			curl_setopt( $this->curl, CURLOPT_PROXY, '127.0.0.1' );
			curl_setopt( $this->curl, CURLOPT_PROXYPORT, 8888 );
		}

		// The HTTPS certificate does not seem to be validated, this is probably
		// becaues it's just a test endpoint for now. This should be removed once
		// the endoint is stable, or at least be able to toggle it on and off.
		curl_setopt( $this->curl, CURLOPT_SSL_VERIFYPEER, 0);
		// Not sure if this is required. Leave it off if possible.
		//curl_setopt( $this->curl, CURLOPT_INFILESIZE, strlen( $this->article ) );
		// Make curl_exec return the request result rather than just true.
		curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );
	}

	public function post( $content, $boundary, $signature ) {
		curl_setopt( $this->curl, CURLOPT_HTTPHEADER, array(
			'Content-Length: ' . strlen( $content ),
			'Content-Type: multipart/form-data; boundary=' . $boundary,
			$signature
		) );
		curl_setopt( $this->curl, CURLOPT_POST, true );
		curl_setopt( $this->curl, CURLOPT_POSTFIELDS, $content );

		return $this->send();
	}

	public function get( $signature ) {
		curl_setopt( $this->curl, CURLOPT_HTTPHEADER, array( $signature ) );
		return $this->send();
	}

	private function send( $json_response = true ) {
		$response = curl_exec( $this->curl );

		if ( false === $response ) {
			$error = curl_error( $this->curl );
			curl_close( $this->curl );
			throw new Request_CURL_Exception( "CURL request failed: $error" );
		}
		curl_close( $this->curl );

		return $json_response ? json_decode( $response ) : $response;
	}

}

class Request_CURL_Exception extends \Exception {}