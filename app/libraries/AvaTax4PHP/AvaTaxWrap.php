<?php 

abstract class AvaTaxWrap {

	protected static $_config = null;
	protected static $_environment = null;
	protected static $_tax = null;
	protected static $_retriesNumber = 2;
	
	public static function __init($env,$config){
		self::registerAutoload();
		self::$_environment = $env;
		self::$_config = $config['avatax'];
		self::__getConfig();
	}
	
	public static function autoload ($class_name){

		$exists = false; 
		
		$path = dirname(__FILE__).'/classes/'.$class_name . '.class.php';
		if(file_exists($path)) { $exists= true;	}
		
		if(!$exists) {
			$path = dirname(__FILE__).'/classes/BatchSvc/'.$class_name . '.class.php';
			if(file_exists($path)) { $exists = true;	}
		}
		
		if($exists === true) { 
			require_once $path;
			//spl_autoload($class_name);
		}
	}
	
	public static function registerAutoload() {
    	spl_autoload_register(array('AvaTaxWrap', 'autoload'));
  	}
	
  	public static function cancelTax($invoice){
  		
  		$client = new TaxServiceSoap(self::$_environment);
  		$request= new CancelTaxRequest();
		$request->setDocCode($invoice);
		$request->setDocType('SalesInvoice');
		$request->setCompanyCode(self::$_config['companyCode']); 
		$request->setCancelCode( CancelCode::$DocDeleted );
		
		try {
			$result = $client->cancelTax($request);
		
			if ($result->getResultCode() == "Success") {
				$return = true;
	  		} else {
					$str = '';
					foreach($result->getMessages() as $msg) {
						$str.= $msg->getName().": ".$msg->getSummary()."\n";
					}
					throw new Exception ($str);
			}
	    } catch ( Exception $e ) {
			throw new Exception ('tax process (cancel) error',0,$e);
		}
		return $return;
  	}
  	
  	public static function getAndCommitTax($data){
  		//static::$_tax = null; // just in case ;)
  		$data['doctype'] = 'record';
  		$data['date'] = date('Y-m-d',time());
  		
  		self::getTax($data);  
  		self::postTax();
		
  	 	if (isset(self::$_tax['totalTax'])) return self::$_tax['totalTax'];
  		else return 0;
  	}
  	
  	public static function commitTax($invoice = null){
  		if (is_null($invoice)) $invoice = self::$_tax['DocCode'];
  		
		$client = new TaxServiceSoap(self::$_environment);
		$request= new CommitTaxRequest();
		$request->setDocCode($invoice);
		$request->setDocType('SalesInvoice');
		$request->setCompanyCode(self::$_config['companyCode']); 
		
		try{
			$result = $client->commitTax($request);
			if ($result->getResultCode() == SeverityLevel::$Success) {
				$return = true;
			} else {
				$str = '';
				foreach($result->getMessages() as $msg) {
					$str.= $msg->getName().": ".$msg->getSummary()."\n";
				}
				throw new Exception ($str,911);
				
			}
	    } catch ( Exception $e ) {
			throw new Exception ('tax process (commit) error',0,$e);
		}

		return $return;
  	}
  	
  	public static function postTax(){
  		$client = new TaxServiceSoap(self::$_environment);
  		$request= new PostTaxRequest();
  		
  		$request->setDocCode(self::$_tax['DocCode']);		
		$request->setDocType('SalesInvoice');
		$request->setCompanyCode(self::$_config['companyCode']);
		$request->setTotalAmount(self::$_tax['totalAmount']);
		$request->setTotalTax(self::$_tax['totalTax']);
	    $request->setDocDate(self::$_tax['date']);
	    $result = $client->postTax($request);
  		
	    try{    
	    	if ($result->getResultCode()==SeverityLevel::$Success){
		    	$return = true;	
		    } else {
				foreach($result->getMessages() as $msg) {
					$str.= $msg->getName().": ".$msg->getSummary()."\n";
				}
				throw new Exception ($result->getResultCode().': '.$str);
			}
			
	    } catch ( Exception $e ) {    	
			throw new Exception ('tax process (post) error: '.$e->getMessage(),0,$e);
		}		    
		return $return;
  	}
  	
  	public static function returnTax($data){
  		$data['doctype'] = 'return';
  		self::getTax($data);
		self::commitTax($data['order']['order_id']);
  	}
  	
	public static function sender(){
		return self::__addresser( array(
			'address' => '300 Nixon Line',
			'address_2' => '',
			'city' => "Edisson",
			'state' => 'NJ',
			'zip' => '08837'
		) );
	} 
	
	public static function getTax($data,$retry = 0){
		
		if (!is_array($data)) return 0;
		if (!array_key_exists('items',$data)) return 0;
		if (!is_array($data['items'])) return 0;
		if (count($data['items']) == 0) return 0;
		
		if(array_key_exists('shippingAddr',$data) )$shipping = $data['shippingAddr'];
		else if (is_array($data['order']) && array_key_exists('shipping',$data['order']) ) $shipping = $data['order']['shipping'];

		if (array_key_exists('doctype',$data) && $data['doctype'] == 'record' )
			$DocType = DocumentType::$SalesInvoice;
		else if (array_key_exists('doctype',$data) && $data['doctype'] == 'return' )
			$DocType = DocumentType::$ReturnInvoice;
		else $DocType = DocumentType::$SalesOrder;
		
		$client = new TaxServiceSoap(self::$_environment);
		$request= new GetTaxRequest();
		
		$request->setOriginAddress(self::sender()); // Origin Address
		$request->setDestinationAddress	(self::__addresser($shipping)); // Desctionation Address
		$request->setCompanyCode(self::$_config['companyCode']);
		$request->setDocType($DocType); 
		$request->setLines(self::__liner($data['items']));
		
		$dateTime=new DateTime();
		if (array_key_exists('order',$data)) {
			$docCode = $data['order']['order_id']; 
		} else {
			$docCode= "TOTSYTest-".date_format($dateTime,"dmyGis");
		}
		    
		//    invoice number
		$request->setDocCode($docCode);
		//date
		if (isset($data['date'])){
			$request->setDocDate($data['date']);
		} else {
			$request->setDocDate(date_format($dateTime,"Y-m-d"));
	    }
	    // string Optional
	    $request->setSalespersonCode("");
	    //string Required
	    $request->setCustomerCode("Totsy");
	    //string   Entity Usage        
	    $request->setCustomerUsageType("");
	    
	    if (isset($data['totalDiscount'])){
		    //decimal
		    $request->setDiscount($data['totalDiscount']);     
	    } else {
		    //decimal
		    $request->setDiscount(0.00);
	    } 
	    
	    //string Optional       
	    $request->setPurchaseOrderNo("");
	    //string   if not using ECMS which keys on customer code
	    $request->setExemptionNo("");         
	    //Summary or Document or Line or Tax or Diagnostic
	    $request->setDetailLevel(DetailLevel::$Tax);
	    $request->setLocationCode("");        //string Optional - aka outlet id for tax forms
	    
	    self::$_tax = null; // just in case ;)
	    
	    try{
			$getTaxResult = $client->getTax($request);
			
			if ($getTaxResult->getResultCode() == SeverityLevel::$Success) {
		        //throw new Exception ($getTaxResult->getTotalTax());
				self::$_tax['DocCode'] = $request->getDocCode();
				self::$_tax['totalAmount'] = $getTaxResult->getTotalAmount();
				self::$_tax['totalTax'] = $getTaxResult->getTotalTax();
				self::$_tax['shippingTax'] = 0;
				self::$_tax['date'] = date_format($dateTime,"Y-m-d");
				foreach($getTaxResult->getTaxLines() as $ctl)
				{
					if ( trim($ctl->getTaxCode()) == 'FR020100'){
						self::$_tax['shippingTax']  = $ctl->getTax();
					}
				}

				if ($shipping > 0 ){
					$return = self::$_tax['totalTax'] - self::$_tax['shippingTax'];
				} else { $return = self::$_tax['totalTax']; }
			} else {
				$str = '';
				foreach($getTaxResult->getMessages() as $msg) {
					$str.= $msg->getName().": ".$msg->getSummary()."\n";
				}
				$return = new Exception ($str);
			}
	    } catch ( SoapFault $s){
	    	$return = $s; 
		} catch ( Exception $e ) {
			$return = $e;
		}
		return $return;
	}
	
	protected static function __addresser ($address){
		//Add Address
		$origin = new Address();
		
		if (is_object($address)){	
			$origin->setLine1($address->address);
			$origin->setLine2($address->address_2);
			$origin->setCity($address->city);
			$origin->setRegion($address->state);
			$origin->setPostalCode($address->zip);
		} else if (is_array($address)){	
			$origin->setLine1($address['address']);
			$origin->setLine2($address['address_2']);
			$origin->setCity($address['city']);
			$origin->setRegion($address['state']);
			$origin->setPostalCode($address['zip']);
		}
		return $origin;
	}
	
	protected static function __eventLiner ($cartByEvent){
		$lines = array();

		$iterator = 0;
		foreach ($cartByEvent as $key => $event){

			foreach ($event as $item){
				
			    $lines[$iterator] = new Line();
			    //string  // line Number of invoice
			    $lines[$iterator]->setNo ($iterator)
			    	  //string SKU
			    	  //->setItemCode($item['sku_details'][$item['size']])
			    	  ->setItemCode($item['item_id'])
			    	  //string
			    	  ->setDescription($item['description'])
			    	  // System or Custom Tax Code
			    	  ->setTaxCode("")
			    	  //decimal // The quantity represented by this line
			    	  ->setQty($item['quantity'])
			    	  //decimal // TotalAmmount
			    	  ->setAmount(number_format($item['sale_retail'] * $item['quantity'] ,2))
			    	  //boolean
			    	  ->setDiscounted(isset($item['discount_exempt'])?$item['discount_exempt']:false)
			    	  //Revenue Account.
			    	  ->setRevAcct("")
			    	  // Client specific reference field.
			    	  ->setRef1("")
			    	  // Client specific reference field.
			    	  ->setRef2("")
			    	  //string
			    	  ->setExemptionNo("")
			    	  // boolean
			    	  ->setTaxIncluded( isset($item['taxIncluded'])?$item['taxIncluded']:false) 
			    	  //string
			    	  ->setCustomerUsageType("");  
			   $iterator++;
			}
		}
		return $lines;
	}

	protected static function __liner ($items){
		$lines = array();

		$iterator = 0;
		
			foreach ($items as $item){
				$item = EnsureIsArray($item);
			    $lines[$iterator] = new Line();
			    //string  // line Number of invoice
			    $lines[$iterator]->setNo ($iterator)
			    	  //string SKU
			    	  //->setItemCode($item['sku_details'][$item['size']])
			    	  ->setItemCode( (string) $item['item_id'] )
			    	  //string
			    	  ->setDescription($item['description'])
			    	  // System or Custom Tax Code
			    	  ->setTaxCode(($item['_id'] == 'Shipping' || $item['_id'] == 'OverShipping')?"FR020100":"")
			    	  //decimal // The quantity represented by this line
			    	  ->setQty($item['quantity'])
			    	  //decimal // TotalAmmount
			    	  ->setAmount(number_format($item['sale_retail'] * $item['quantity'] ,2))
			    	  //boolean
			    	  ->setDiscounted(isset($item['discount_exempt'])?$item['discount_exempt']:false)
			    	  //Revenue Account.
			    	  ->setRevAcct("")
			    	  // Client specific reference field.
			    	  ->setRef1("")
			    	  // Client specific reference field.
			    	  ->setRef2("")
			    	  //string
			    	  ->setExemptionNo("")
			    	  //string
			    	  ->setCustomerUsageType("");
					  // boolean
			    $lines[$iterator]->setTaxIncluded( isset($item['taxIncluded'])?$item['taxIncluded']:false); 
			    	  
			    	   
			   $iterator++;
		}

		return $lines;
	}
	
	
	protected static function __getConfig (){
		new ATConfig(self::$_environment, 
			array(
				'url' => self::$_config['url'],
				'account' => self::$_config['account'],
				'license' => self::$_config['license'],
				'trace' => self::$_config['trace']
			)
		);
	}
}

function EnsureIsArray( $obj ) 
{
    if( is_object($obj)) 
	{
        $item[0] = $obj;
    } 
	else 
	{
        $item = (array)$obj;
    }
    return $item;
}

?>