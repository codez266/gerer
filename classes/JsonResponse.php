<?php
class JsonResponse {
	public static function encode( $response, $data ) {
		$response->headers->set( 'Content-Type: application/json' );
		$response->setBody( json_encode( $data ) );
	}
}
