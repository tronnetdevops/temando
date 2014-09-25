<?php

class TemandoWebServices {
	
	public $client;
	
	function getQuotesByRequest($requestParameter,$uname,$password,$wsdl){ 
		ini_set("soap.wsdl_cache_enabled", "0");
		try{
			$this->client = new SoapClient($wsdl,array("features" => SOAP_SINGLE_ELEMENT_ARRAYS));
			$this->buildSoapHeader($uname,$password);
		} catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
		try{
			$response = $this->client->__soapCall('getQuotesByRequest',array($requestParameter));
			if( !isset($response) && empty($response) ){
				throw new exception("Unable to Connect to the Temando Services. For Further Details Contact Admin");
			} else {
				return obj2array($response);
			}
		} catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
	}
	
	function makeBookingByRequest($requestParameter,$uname,$password,$wsdl){ 
		ini_set("soap.wsdl_cache_enabled", "0");		
		try{
			$this->client = new SoapClient($wsdl);	
			$this->buildSoapHeader($uname,$password);
		} catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
		try{
			$response = $this->client->__soapCall('makeBookingByRequest',array($requestParameter));
				print_r($this->client);
				echo 'test';
			if( !isset($response) && empty($response) ){
				throw new exception("Unable to Connect to the Temando Services. For Further Details Contact Admin");
			} else {
				return obj2array($response);
			}
		}catch( exception $e){
				print_r($this->client);
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
	}
	
	function createClient($requestParameter,$uname,$password,$wsdl){ 
		ini_set("soap.wsdl_cache_enabled", "0");		
		try{
			$this->client = new SoapClient($wsdl);	
			$this->buildSoapHeader($uname,$password);
		} catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
		try{
			$response = $this->client->__soapCall('createClient',array($requestParameter));
			if( !isset($response) && empty($response) ){
				throw new exception("Unable to Connect to the Temando Services. For Further Details Contact Admin");
			} else {
				return obj2array($response);
			}
		}catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
	}
	
	function getClient($requestParameter,$uname,$password,$wsdl){ 
		ini_set("soap.wsdl_cache_enabled", "0");		
		try{
			$this->client = new SoapClient($wsdl);	
			$this->buildSoapHeader($uname,$password);
		} catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
		try{
			$response = $this->client->__soapCall('getClient',array($requestParameter));
			if( !isset($response) && empty($response) ){
				throw new exception("Unable to Connect to the Temando Services. For Further Details Contact Admin");
			} else {
				return obj2array($response);
			}
		}catch( exception $e){
			$error_msg = $e->getmessage();
			return obj2array($error_msg);
		}
	}
	
	function buildSoapHeader($uname,$password){   
		$kaeSoapHeader_NameSpace = "http://schemas.xmlsoap.org/ws/2003/06/secext"; 
		$kaeUsername = new SoapVar($uname, XSD_STRING, null, null, 'Username', $kaeSoapHeader_NameSpace);
		$kaePassword = new SoapVar($password, XSD_STRING, null, null, 'Password', $kaeSoapHeader_NameSpace); 
		$kaeUPCombo = new UPCombo($kaeUsername, $kaePassword); 
		$kaeUsernameToken = new UPToken($kaeUPCombo); 
		$kaeHdr = new SoapVar($kaeUsernameToken, SOAP_ENC_OBJECT, null, null, 'UsernameToken', $kaeSoapHeader_NameSpace); 
		$kaeSoapHeader_Name = "Security"; 
		$kaeSoapHeader_Data = $kaeHdr; 
		$kaeSoapHeader_MustUnderstand = false; 
		$kaeSoapHeader = new SoapHeader($kaeSoapHeader_NameSpace, $kaeSoapHeader_Name, $kaeSoapHeader_Data,$kaeSoapHeader_MustUnderstand); 
		$this->client->__setSoapHeaders(array($kaeSoapHeader));
	}
	
	function wdays($strTime = NULL){
		$arrWeekend = array("6","7"); // saturday and sunday
		$strTime = (is_null($strTime)) ? strtotime("+10 days") : $strTime;
		if(in_array(date("N", $strTime), $arrWeekend)){
			return $this->wdays(strtotime("+1 day", $strTime));
		} else {
			return $strTime;
		}
	
	}
		
}

class UPCombo { 
	public function __construct($NewUsername, $NewPassword){ 
		$this->Username = $NewUsername; 
		$this->Password = $NewPassword; 
	} 
} 

class UPToken { 
	private $UsernameToken;
	public function __construct($NewUPCombo){ 
		$this->UsernameToken = $NewUPCombo; 
	}
}


function obj2array($obj) {
  $out = array();
  if(!is_object($obj) && !is_array($obj)){ return $obj; }
  foreach ($obj as $key => $val) {
    switch(true) {
        case is_object($val):
         $out[$key] = obj2array($val);
         break;
      case is_array($val):
         $out[$key] = obj2array($val);
         break;
      default:
        $out[$key] = $val;
    }
  }
  return $out;
}

?>