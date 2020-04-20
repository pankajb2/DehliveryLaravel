<?php
namespace App\Library\Services;
use GuzzleHttp\Client;
class Delhivery
{
    private $_API = array();
    private $_url;
    private $_token;
    public function __construct($data = FALSE)
    {
        if (is_array($data))
        {
            $this->setup($data);
        }

    }

    public function setup($data = array()){
        try {
            $this->_API = new Client();
            //$this->_url='https://test.delhivery.com/';
            $this->_url=$data['API_URL'];
            //https://track.delhivery.com/
            $this->_token=$data['TOKEN'];
        } catch(\Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    /**
     * Calls API and The Delivery PinCode(s) API retrieves list of all serviceable pin codes where they deliver .
     * @param string comma seperated pincodes
     * @return json

     Response format :
        {
          "delivery_codes": [
            {
              "postal_code": {
                "pin": "pincode",
                "pre_paid": "'Y' if serviceable else 'N'",
                "repl": "'Y' if serviceable else 'N'",
                "is_oda": "oda",
                "state_code": "state code",
                "max_weight": "max weight available on this pincode",
                "district": "district",
                "max_amount": "max amount available on this pincode",
                "cash": "'Y' if serviceable else 'N'",
                "pickup": "'Y' if serviceable else 'N'",
                "cod": "'Y' if serviceable else 'N'",
                "sort_code": "sort code"
              }
            }
          ]
        }
     */
    public function fetchPincode($pincode){
        try {
            $array=array();
            $array['type']="GET";
            $array['url']=$this->_url.'/c/api/pin-codes/json/?token='.$this->_token.'&filter_codes='.$pincode;
            $r = $this->_API->request($array['type'], $array['url']);
            $data = $r->getBody()->getContents();
            return json_decode($data,true);
        } catch(\Exception $ex) {
            return false;
            // echo $ex->getMessage();
        }
    }

    /**
     * Calls API and The Delivery PinCode(s) API retrieves list of all serviceable pin codes where they deliver .
     * @param string comma seperated pincodes
     * @return json
     */
    public function getAwb($count)
    {
        try {
            $array=array();
            $array['type']="GET";
            $array['url']=$this->_url.'/api/wbn/bulk.json?count='.$count;
            $array['header']= array(
                'Accept'=> 'application/json',
                'Authorization'=>'Token ' . $this->_token
            );
            $r = $this->_API->request($array['type'], $array['url'],['headers'=>$array['header']]);
            $data = $r->getBody()->getContents();
            return json_decode($data,true);
        } catch(\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
    * Create package in Delhivery portal
    * @param array data
    * @return json
    */

    public function createOrder($data)
    {
        try{
            $array = array();
            $array['type'] = "POST";
            $array['url'] = $this->_url.'/api/cmu/create.json';
            $array['header']= array(
                'Accept'=> 'application/json',
                'Authorization'=>'Token ' . $this->_token
            );
            $formdata = "format=json&data=".json_encode($data);
            $r = $this->_API->request($array['type'],$array['url'],['headers' => $array['header'],'form_params' => $formdata]);
            $response = $r->getBody()->getContents();
            return json_decode($response,true);
        }
        catch(\Exception $e){
            echo $ex->getMessage();
        }
    }

    /**
     * Packing slip API allows you to desing a packing slip for the corresponding waybill no. The response of this API would be in JSON format. You can further design the packing slip in HTML using this JSON input
     * @param string comma seperated awb codes
     * @return json
     */
    public function getPackingslip($awb)
    {
        try {
            $array=array();
            $array['type']="GET";
            $array['url']=$this->_url.'/api/p/packing_slip?wbns='.$awb;
            $array['header']= array(
                'Accept'=> 'application/json',
                'Authorization'=>'Token ' . $this->_token
            );
            $r = $this->_API->request($array['type'], $array['url'],['headers'=>$array['header']]);
            $data = $r->getBody()->getContents();
            return json_decode($data,true);
        } catch(\Exception $ex) {
            echo $ex->getMessage();
        }
    }

    /**
     * Packing slip API allows you to desing a packing slip for the corresponding waybill no. The response of this API would be in JSON format. You can further design the packing slip in HTML using this JSON input
     * {
     *"pickup_location_name": "pickup location name",
     *"client_name": "client name",
     *"pickup_time": "pickup time",
     *"pickup_id": "pickup id",
     *"incoming_center_name": "incoming center",
     *"expected_package_count": "expected package count",
     * "pickup_date": "pickup date"
     *}
     * @param array sample data
     * @return json
     */
     public function createPickup($data)
     {
         try {
             $array=array();
             $array['type']="POST";
             $array['url']=$this->_url.'/fm/request/new/';
             $array['header']= array(
                 'Accept'=> 'application/json',
                 'Authorization'=>'Token ' . $this->_token
             );
             $r=$this->_API->request($array['type'],$array['url'],['headers'=>$array['header'],'form_params'=>$data]);
             $response = $r->getBody()->getContents();
             return json_decode($response,true);
         } catch(\Exception $ex) {
             echo $ex->getMessage();
         }
     }

     /**
      * API for tracking awb 
      * @param string comma seperated awb codes
      * @return json
      */
    public function trackingAwb($awb,$order_id){
         try {
             $verbose = 2; 
             // Verbose (0 for Minimal info of package(meta). 1 - Meta info and all scan info of package. 2 - Meta info, all scan info and consignee details of package. Allowed values: 0, 1, 2)
             $array=array();
             $array['type']="GET";
             // $array['url']=$this->_url.'api/packages/json/';
             $array['url']=$this->_url.'/api/packages/json/?token='.$this->_token.'&waybill='.$awb.'&ref_nos='.$order_id.'&verbose='.$verbose;
             $r = $this->_API->request($array['type'], $array['url']);
             $data = $r->getBody()->getContents();
             return json_decode($data,true);
         } catch(\Exception $ex) {
             echo $ex->getMessage();
         }
    }
}
