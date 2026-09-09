<?php
	function check($api_key,$inv_key){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://domain.com/invoice/check/'.$inv_key);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    function request($api_key,$amount,$redirect){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://domain.com/invoice/request');
        curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key&amount=$amount&return_url=$redirect");
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
?>