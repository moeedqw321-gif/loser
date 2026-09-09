<?php
$api_key = 'ec5a74bb083a1dc40b168abbfa6001d5'; // your api key
$amount = $_POST['price'];
$user_id = $_POST['id'];
$created_at=date("Y-m-d h:i:sa");
$callback = 'http://i-winner.tk/pay/get.php'; // callback url

    function config(){
        $server='localhost';
        $user="admin_tw";
        $spassword='sTkpqAvo5';
        $db="admin_tw";
        $connect=mysqli_connect($server,$user,$spassword,$db);
        mysqli_set_charset($connect, "utf8");
        mysqli_query($connect,"SET NAMES 'utf8'");
        return $connect;
    }


$connection=config();



    function request($api_key,$amount,$redirect){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://lamizshop.ir/pay/invoice/request');
        curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key&amount=$amount&return_url=$redirect");
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }
    


$result = request($api_key,$amount,urlencode($callback));

$result = json_decode($result,1);


if($result['status'] == 1) {
    
   echo $invoice_key = $result['invoice_key']; // you may save this in DB and check it in get.php for more security
    
$amount = $amount/10;
mysqli_query($connection,"INSERT INTO `transactions` VALUES('', '$amount', '','افزایش موجودی حساب', '$user_id','', '$created_at','', '','', '0','$invoice_key')" ) or die (mysql_error());




  
    $go = "http://lamizshop.ir/pay/invoice/pay/".$invoice_key;
    header('location:'.$go);

  
    

} else {
    echo 'error : '.$result['errorCode'].' - '.$result['errorDescription'];
    die('');
}
?>