<?php

namespace App\Traits;

trait GeneralTrait
{

    public function returnError($errNum, $msg)
    {
        return response()->json([
            'status' => false,
            'errNum' => $errNum,
            'msg' => $msg
        ],401);
    }


    public function returnSuccessMessage($msg = "", $errNum = "S000")
    {
        return response()->json([
            'status' => true,
            'errNum' => $errNum,
            'msg' => $msg
        ]);
    }

    public function returnData($key, $value, $msg = "")
    {
        return response()->json([
            'status' => true,
            'errNum' => "S000",
            'msg' => $msg,
            $key => $value
        ]);
    }
    public function price($r1,$r2,$r3,$dis1,$dis2,$dis3,$days,$mainPrice){
        if ($days>$r1){
            return $mainPrice;
        }
        if ($days>$r2){
            $dis=100-$dis1;
            return $mainPrice*($dis/100);
        }
        if($days>$r3){
            $dis = 100-$dis2;
            return $mainPrice*($dis/100);
        }
        $dis = 100-$dis3;
        return $mainPrice*($dis/100);
    }

}
