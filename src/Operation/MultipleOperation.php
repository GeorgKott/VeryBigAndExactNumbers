<?php

namespace georgkott\verybigandexactnumbers\operation;

class MultipleOperation extends OperationAbstract
{
    private function mulN($str = '',$n = 0,$r = 0)
    {
        $number = '';
        $add = 0;

        for($i=strlen($str)-1;$i>=0;$i--){
            $sum = $str[$i]*$n + $add;

            if($sum >= 10){
                if($i == 0){
                    $number = strval($sum).$number;
                }
                else{
                    $add = intval($sum/10);
                    $number = strval($sum%10).$number;
                }
            }
            else{
                $add = 0;
                $number = strval($sum).$number;
            }
        }

        return $number.str_repeat('0',$r);
    }

    private function mul($n1 = '',$n2 = '')
    {
        $data = [];

        $r = 0;
        for($i=strlen($n2)-1;$i>=0;$i--){
            $data[] = $this->mulN($n1,$n2[$i],$r);
            $r++;
        }

        $mul = '0';

        foreach($data as $item){
            $mul = $this->mulSum($mul,$item);
        }

        $point = strlen($this->r1)+strlen($this->r2);

        $this->resultNatural = $this->trimNumbers(substr($mul,0,-$point),'l');
        $this->resultRational = $this->trimNumbers(substr($mul,-$point),'r');
    }

    private function checkOnlyZero($str = '')
    {
        if(preg_match('/^[0]+$/',$str)){
            return true;
        }

        return false;
    }

    private function trimNumbers($str = '',$trim = 'l')
    {
        if($this->checkOnlyZero($str)){
            return "0";
        }

        if($trim == 'l'){
            return ltrim($str,'0');
        }
        else{
            return rtrim($str,'0');
        }
    }

    private function mulSum($str1,$str2)
    {
        if(strlen($str1) > strlen($str2)){
            $n = strlen($str1) - strlen($str2);
            $str2 = str_repeat('0',$n).$str2;
        }
        else if(strlen($str1) < strlen($str2)){
            $n = strlen($str2) - strlen($str1);
            $str1 = str_repeat('0',$n).$str1;
        }

        $s = '';
        $add = 0;

        for($i=strlen($str1)-1;$i>=0;$i--){
            $sum = $str1[$i] + $str2[$i] + $add;
            if($sum >= 10){
                if($i == 0){
                    $s = strval($sum).$s;
                }
                else{
                    $add = 1;
                    $s = strval($sum%10).$s;
                }
            }
            else{
                $add = 0;
                $s = strval($sum).$s;
            }
        }

        return $s;
    }

    public function result()
    {
        $number1 = $this->n1.$this->r1;
        $number2 = $this->n2.$this->r2;

        $this->mul($number1,$number2);

        return [$this->resultNatural,$this->resultRational];
    }
}

