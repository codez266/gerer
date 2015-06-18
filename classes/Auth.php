<?php
/**
 * Class for hash based message authentication
 * Intercept incoming requests and grant access only to those with conventional login or providing
 * authentication in requests
 */
use Slim\Slim;
class Auth extends \Slim\Middleware {
	private $_encoding;
	/**
	 * urls which are bypassed without check
	 * @var array
	 */
	private static $_allowedUrls = array( '/login', '/signup', '/profile', '/logout' );
	public function call() {
		session_start();
		$app = $this->app;
		$request = $app->request;
		$reqType = $request->getMethod();
		$contentType = $request->getContentType();
		$uri = $request->getPath();
		$auth = $request->headers->get( 'Authorization' );
		if ( in_array( $request->getPathInfo(), self::$_allowedUrls ) ) {
			$this->next->call();
		} else {
			/**
			 * For native handling, browser based
			 */
			if ( isset( $_SESSION['user'] ) ) {
				$token = $_SESSION['user']->getToken();
				$this->next->call();
			}
			else if ( $auth != null ) {
				/**
				 * For requests that may have been sent by apps
				 */
				$token = "";
				$keys = $this->parseAuthorization( $auth );
				$sig = $this->createSignature( $reqType, $uri, $contentType, $token );
				//var_dump($request->headers->all());
				if ( $sig == $keys[1] ) {
					$this->next->call();
				} else {
					echo "Access Denied\n".$sig."\n".$keys[1];
				}
			} else {
				echo "Access Denied\n";
			}
		}
	}
	public function construct( $encoding ) {
		$this->_encoding = $encoding;
	}
	public function verify( $sig, $stringToSign ) {

	}

	/**
	 * Create a signature from request
	 * @param  string $requestType
	 * @param  string $contentType
	 * @param  string $uri
	 * @param  string $privateKey
	 * @return string
	 */
	public function createSignature( $requestType, $contentType, $uri, $privateKey ) {
		if ( $requestType == null ) {
			$requestType = "";
		}
		if ( $contentType == null ) {
			$contentType = "";
		}
		if ( $uri == null ) {
			$uri = "";
		}
		$content = $requestType . $contentType . $uri;
		$content = utf8_encode( $content );
		$signature = base64_encode( hash_hmac( 'sha256', $content, $privateKey, true ) );
		//$signature = hash_hmac( 'sha256', $content, $privateKey );
		//echo $content;
		return $signature;
	}

	/**
	 * break Authorization header into public key and signature about ':'
	 * @param  string $auth Authorization header value
	 * @return string array of the public key and signature
	 */
	public function parseAuthorization( $auth ) {
		$array = explode( ':', $auth );
		return $array;
	}
}
