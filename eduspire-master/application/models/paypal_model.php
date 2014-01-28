<?php
/*
@Page/Module Name/Class:                        Paypal_model.php
@Author Name:			 		janet rajani
@Date:					 	Sept, 23  2013
@Purpose:		        		Payment through paypal
@Table referred:				
@Table updated:					
@Most Important Related Files	NIL
*/

class Paypal_model extends CI_Model 
{
	protected $_environment  = PAYPAL_MODE ;
	protected $_user_name    = PAYPAL_USERNAME ;
	protected $_password     = PAYPAL_PASSWORD ;
	protected $_signature    = PAYPAL_SIGNATURE;
	protected $_endpoint     = PAYPAL_ENDPOINT_LIVE ;
	protected $_version      = PAYPAL_VERSION ;
	
	public function __construct()
	{
		parent::__construct();
	}
	
        /*
               @Function Name:	_process_request(The API method name,The POST Message fields in &name=value pair format)
               @Author Name:	Janet Rajani 
               @Date:		Sept 16, 2013
               @Purpose:	Send HTTP POST Request to PayPal 
        */
       protected function _process_request($methodName_, $nvpStr_) 
       {
            $errors=array();	
            $environment =$this->_environment;
            // Set up your API credentials, PayPal end point, and API version.
            $API_UserName = urlencode($this->_user_name);
            $API_Password = urlencode($this->_password);
            $API_Signature = urlencode($this->_signature);
            $API_Endpoint = $this->_endpoint;
            if('sandbox'==$this->_environment)
            {
                    $API_Endpoint = PAYPAL_ENDPOINT_TEST;
            }
          
            $version = urlencode($this->_version);
            // Set the API operation, version, and API signature in the request.

            $nvpreq = "METHOD=$methodName_&VERSION=$version&PWD=$API_Password&USER=$API_UserName&SIGNATURE=$API_Signature$nvpStr_";

            // Set the curl parameters.
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $API_Endpoint);
            curl_setopt($ch, CURLOPT_VERBOSE, 1);
            // Turn off the server and peer verification (TrustManager Concept).

            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            // Set the request as a POST FIELD for curl.

            curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);
            // Get response from the server.
            $httpResponse = curl_exec($ch);
            if(!$httpResponse) 
            {
                    $errors['error']="$methodName_ failed: ".curl_error($ch).'('.curl_errno($ch).')';
                    return $errors;
            }

            // Extract the response details.
            $httpResponseAr = explode("&", $httpResponse);
            $httpParsedResponseAr = array();

            foreach ($httpResponseAr as $key => $value) 
            {
                    $tmpAr = explode("=", $value);
                    if(sizeof($tmpAr) > 1) 
                    {
                            $httpParsedResponseAr[$tmpAr[0]] = $tmpAr[1];
                    }
            }

            if((0 == sizeof($httpParsedResponseAr)) || !array_key_exists('ACK', $httpParsedResponseAr)) 
            {
                    $errors['error'] = "Invalid HTTP Response for POST request($nvpreq) to $API_Endpoint.";
                    return $errors;
            }

            return $httpParsedResponseAr;
    }
	/*
		@Function Name:	pay
		@Author Name:	Janet Rajani 
		@Date:		Sept 16, 2013
		@Purpose:	Send url and method to paypal procession function and then display response 
	
	*/
	function pay($method='DoDirectPayment',$url_string='')
        {
		$result      = array();
		$response    = $this->_process_request($method,$url_string);
		if("SUCCESS" == strtoupper($response["ACK"]) || "SUCCESSWITHWARNING" == strtoupper($response["ACK"])) 
                {
                    $result  = $response;
                } 
                else  
                {
                    $result  = '';
                }
                    return $result;	
	}

}//end of class

//end of file 