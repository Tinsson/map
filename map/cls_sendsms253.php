<?php

class sendSMS253
{
            
    var $account;
    var $pswd;
    var $mobile = '';
    var $msg = '';
    var $needstatus = true;
    var $url='http://222.73.117.169/msg/HttpBatchSendSM';
    
    function __construct($data=array())
    {
        $this->account = 'N1793614';
        $this->pswd= 'Pscf26cc';
        $this->mobile = $data['mobile'] ? $data['mobile'] : '';
        $this->msg = $data['msg'] ? $data['msg'] : '';
        
        
    }
    
    function send()
    {
        $post_data = array();
        $post_data['account'] = $this->account;
        $post_data['pswd'] = $this->pswd;
        $post_data['msg'] = $this->msg; 
        $post_data['mobile'] = $this->mobile;
        $post_data['needstatus'] = $this->needstatus;
        $res = $this->http_request($this->url, http_build_query($post_data));
        return $res;
    }
    
    function http_request($url,$data = null)
    {
        if (function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
           
            if (!empty($data)) {
                curl_setopt($curl, CURLOPT_POST, 1);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            }
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            $output = curl_exec($curl);
            curl_close($curl);
                    
            $result=preg_split("/[,\r\n]/",$output);

            if ($result[1]==0) {
                //return "curl success";
                return 1;
            } else {
                  return "curl error".$result[1];
            }
        } elseif (function_exists('file_get_contents')) {
            
            $output=file_get_contents($url.$data);
            $result=preg_split("/[,\r\n]/",$output);
        
            if ($result[1]==0) {
                //return "success";
                return 1;
            } else {
                  return "error".$result[1];
            }
        } else {
            return false;
        } 
        
    }

}