<?php
/**
 *	Class represents the PayUMoney payment gateway.
 *
 *	@since 1.0.0
 */
class PayUMoney_Payment_Gateway {
	/**
	 *	Holds the server url.
	 *
	 *	@since	1.0.0
	 */
	private $request_url;
	
	/**
	 *	Holds the callback success url.
	 *
	 *	@since	1.0.0
	 */
	private $success_url;
	
	/**
	 *	Holds the callback failure url.
	 *
	 *	@since	1.0.0
	 */
	private $failure_url;
	
	/**
	 *	Holds the merchant key, provided by PayUMoney.
	 *
	 *	@since	1.0.0
	 */
	private $merchant_key;
	
	/**
	 *	Holds the hash salt, provided by PayUMoney.
	 *
	 *	@since	1.0.0
	 */
	private $salt;
	
	/**
	 *	Holds the service provider value.
	 *
	 *	@since	1.0.0
	 */
	private $service_provider;
		
	/**
	 *	Class constructor.
	 *	
	 *	Constructs the class object, sets the run mode, server url, merchant key 
	 *	and salt, all pasedd as parameters.
	 *
	 *	@since	1.0.0
	 */
	public function __construct( $merchant_key, $salt, $success_url, $failure_url, $test_mode = false ) {
		$this->merchant_key = $merchant_key;
		$this->salt = $salt;
		$this->service_provider = 'payu_paisa';
		$this->success_url = $success_url;
		$this->failure_url = $failure_url;
		$this->request_url = ( $test_mode ) ? 'https://test.payu.in/' : 'https://secure.payu.in/';
	}
		
	/**
	 *	Request payment.
	 *	
	 *	Request payment to the PayUMoney payment gateway using the WordPress
	 *	HTTP api.
	 *
	 *	@since	1.0.0
	 */
	public function request_payment( $params ) {
		// Validate the payment request parameters.
		$params = $this->validate_request_payment( $params );
		
		if( is_wp_error( $params ) ) {
			bppc_log( 'Parameters are invalid.' );
			return $params;
		}
		
		// Get the payment request hash value.
		$params['hash'] = $this->generate_hash( $params );
		
		// Build the query URL.
		$payment_url = $this->request_url . '_payment';
		
		// Echo the form & submit.
		$form_elements = '';
		foreach( $params as $key => $value ) {
			$form_elements .= '<input type="hidden" name="' . $key . '" value="' . $value . '">';
		}
		
		echo 
			'<div id="payu-form">' .
				'<p>Redirecting you to a secure payment page &hellip;</p>' .
				'<form action="' . $payment_url . '" method="post" id="payu_form">' .
					$form_elements .
				'</form>' .
				'<script type="text/javascript">' .
					'document.getElementById("payu_form").submit();' .
				'</script>' .
			'</div>';
		
		exit;
	}

	/**
	 *	Validate payment request params.
	 *	
	 *	Checks if the required payment request parameters are all there.
	 *
	 *	@since	1.0.0
	 */
	private function validate_request_payment( $params ) {
		if(  
			empty( $params['txnid'] ) ||
			empty( $params['amount'] ) ||
			empty( $params['firstname'] ) ||
			empty( $params['email'] ) ||
			empty( $params['phone'] ) ||
			empty( $params['productinfo'] )
		) {
			return new WP_Error( 'param_data_missing', __( 'Invalid payment request parameters.' ) );
		}
		
		$params['key'] = $this->merchant_key;
		// $params['service_provider'] = $this->service_provider;
		$params['surl'] = add_query_arg( 'pucb', 1, $this->success_url );
		$params['furl'] = add_query_arg( 'pucb', 1, $this->failure_url );
		
		return $params;
	}
		
	/**
	 *	Generate the hash for a payment request.
	 *
	 *	@since	1.0.0
	 */
	private function generate_hash( $params ) {
		$hash_vars = explode( '|', 'key|txnid|amount|productinfo|firstname|email|udf1|udf2|udf3|udf4|udf5|udf6|udf7|udf8|udf9|udf10' );
		
		$hash = '';
		
		foreach( $hash_vars as $hash_var ) {
			$hash .= isset( $params[$hash_var] ) ? $params[$hash_var] : '';
			$hash .= '|';
		}
		
		$hash .= $this->salt;
		
		return strtolower( hash( 'sha512', $hash ) );
	}
}