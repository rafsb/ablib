<?php
namespace abox;
include "../user.php";

if(aval()){
    
    $user = user();
    $user = qout("SELECT * FROM Users WHERE code='$user'",AB_OBJECT);
    
    if($user->status()){
        
        $user = $user->data();
        $conf = conf('ps');
        $in = in();
        $code = $user->code;
        $date = (new Date())->datetime();


        if(!isset($in['val0'])){
            echo '0';
        }else{
            $val0 = $in['val0'];
            $desc = isset($in["desc"])?$in["desc"]:"MANUAL INSERTION";
            $item = isset($in["code"])?$in["code"]:get_hash();
            $obj['token'] = $conf->token;
            $obj['email'] = $conf->mail;
            $obj['currency'] = 'BRL';
            $obj['itemId1'] = $item;
            $obj['itemDescription1'] = $desc;
            $obj['itemAmount1'] = \number_format($val0,2,".","");
            $obj['itemQuantity1'] = "1";

            $obj = http_build_query($obj);

            $curl = curl_init($conf->url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $obj);
            curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);

            $out = curl_exec($curl);
            curl_close($curl);

            $out = simplexml_load_string($out);
            if(!isset($out->error)){
                $move = $out->code;
                if(!qio("SELECT * FROM Accounts WHERE code='$code'")) qin("INSERT INTO Accounts VALUES('$code','$date','0.00','$date')");
                qin("INSERT INTO Movements(code,acct,type,val0,cdat,obs0) VALUES('$move','$code','5','$val0','$date','$desc')");
                echo $out->code;
            }else echo '0';
        }
    }
    
} else echo '0';?>