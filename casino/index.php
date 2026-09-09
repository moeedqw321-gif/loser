<?php
// TkFreamwork - Dedicated Freamwork Client Applications \\
###########################################################
###########################################################
###########################################################
#####              Freamwork Destraction              #####
#####-------------------------------------------------#####
#####                   TkFreamWork                   #####
#####             WebSite : www.tkstar.ir             #####
#####           PhoneNumber : +989017735378           #####
#####         Code and Idea by AmirAli Esteki         #####
#####  TkFreamWork   For TkStar Clients   Dedicateds  #####
#####          Freamwork Version : 0.0.00001          #####
#####      Concessionaire Freamwork : TkStar Co.      #####
#####      Lunar Constructions Date : 1438/06/02      #####
#####     Helical Constructions Date : 1395/12/11     #####
#####    Gregorian Constructions Date : 2017/03/01    #####
#####-------------------------------------------------#####
#####                End Destraction .                #####
###########################################################
###########################################################
###########################################################
include_once("config.php");
$uri=(uri("p1")==""||uri("p1")=="index")?"index":((uri("pages")=="card")?uri("p1"):uri("p1"));
$address=scriptdir."pages".ds.$uri.".php";
if(is_file($address)&&file_exists($address)):require_once($address);else:require_once(scriptdir."pages".ds."404.php");exit();endif;
?>