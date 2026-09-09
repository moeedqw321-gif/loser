<?php
$api_key = 'ec5a74bb083a1dc40b168abbfa6001d5'; // your api key

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




	function check($api_key,$inv_key){
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,'http://lamizshop.ir/pay/invoice/check/'.$inv_key);
        curl_setopt($ch,CURLOPT_POSTFIELDS,"api_key=$api_key");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
        $res = curl_exec($ch);
        curl_close($ch);
        return $res;
    }


echo $invoice_key = @$_POST['invoice_key'];
echo '<br>';



$result = check($api_key, $invoice_key);
$result = json_decode($result,1);



if($result['status'] == 1) {

    
 mysqli_query($connection,"UPDATE transactions SET status='1' WHERE savano='$invoice_key' ") or die (mysql_error());
   
    
 $row= mysqli_query($connection,"SELECT * FROM transactions WHERE savano='$invoice_key' ");
    while($res=mysqli_fetch_assoc($row)){
         $id=$res['user_id'];
         $pricee=$res['price'];
    }
    
    
   $row= mysqli_query($connection,"SELECT * FROM users WHERE id='$id' ");
    while($res=mysqli_fetch_assoc($row)){
         $price=$res['cash'];
        $sum= $price+$pricee;

mysqli_query($connection,"UPDATE users SET cash='$sum' WHERE id='$id' ") or die (mysql_error());

         
    }  
    
    
    echo 'عملیات پرداخت آنلاین موفقیت آمیز بوده است!';
    
    header( "refresh:3; url=/payment/credit" );
    
} else {
    
    echo 'عملیات پرداخت آنلاین موفقیت آمیز نبوده است!';
    
    header( "refresh:3; url=/payment/credit" );

    
}
?>