<?php

namespace georgkott\verybigandexactnumbers\operation;

class DivideOperation extends OperationAbstract
{
    private $accuracy;

    public function __construct($n1 = '', $r1 = '', $n2 = '', $r2 = '',$param = [])
    {
        if(!isset($param['accuracy']) || !is_int($param['accuracy'])){
            throw new Exception('Not correct accuracy. Need a number');
        }
        else{
            $this->accuracy = $param['accuracy'];
        }

        parent::__construct($n1, $r1, $n2, $r2);
    }

    public function result()
    {
        if(strlen($this->r1)>strlen($this->r2)){
            $number1 = $this->n1.$this->r1;
            $number2 = $this->n2.$this->r2.str_repeat('0',strlen($this->r1)-strlen($this->r2));
        }
        else if(strlen($this->r1)<strlen($this->r2)){
            $number1 = $this->n1.$this->r1.str_repeat('0',strlen($this->r2)-strlen($this->r1));
            $number2 = $this->n2.$this->r2;
        }
        else{
            $number1 = $this->n1.$this->r1;
            $number2 = $this->n2.$this->r2;
        }
        
        $this->div($number1,$number2,$this->accuracy);

        return [$this->resultNatural,$this->resultRational];
    }

    private function moreEqualDiv($n1 = '',$n2 = '')
    {
        if(strlen($n1) > strlen($n2)){
            $n2 = str_repeat('0',strlen($n1)-strlen($n2)).$n2;
        }
        else if(strlen($n1) < strlen($n2)){
            $n1 = str_repeat('0',strlen($n2)-strlen($n1)).$n1;
        }

        for($i=0;$i<strlen($n1);$i++){
            if($n1[$i]>$n2[$i]){
                return true;
            }
            else if($n1[$i]<$n2[$i]){
                return false;
            }
        }

        return true;
    }

    private function div($number1 = '',$number2 = '',$accuracy = 20)
    {
        $natural = [];

        $str = substr($number1,0,strlen($number2));
        
        $pflag = 0;
        $pn = 0;
        $offset = strlen($number2);

        while($pn < $accuracy){
            if($this->moreEqualDiv($str,$number2)){
                if(!isset($natural[$pn])){
                    $natural[$pn] = 1;
                }
                else{
                    $natural[$pn]++;
                }

                $str = $this->divSub($str,$number2);
            }
            else{
                if(substr($number1,$offset,1) === false || substr($number1,$offset,1) === ''){
                    $str = $str.'0';
                    $pflag++;
                }
                else{
                    $str = $str.substr($number1,$offset,1);
                    $offset++;
                }

                if(!isset($natural[$pn])){
                    $natural[$pn] = 0;
                }

                $pn++;
            }
        }
        
        $itog = '';

        for($i=0;$i<=$pn;$i++){
            if(!isset($natural[$i])){
                $itog .= '0';
            }
            else{
                $itog .= $natural[$i];
            }
        }

        $this->resultNatural = $this->trimNumbers(substr($itog,0,-$pflag),'l');
        $this->resultRational = $this->trimNumbers(substr($itog,-$pflag),'r');
    }

    private function divSub($n1 = '',$n2 = '')
    {
        if(strlen($n1)>strlen($n2)){
            $n2 = str_repeat('0',strlen($n1)-strlen($n2)).$n2;
        }

        $natural = '';

        $del = 0;

        for($i=strlen($n1)-1;$i>=0;$i--){
            $sub = $n1[$i] - $n2[$i] - $del + 10;
            if($sub < 10){
                $del = 1;
                $natural = strval($sub).$natural;
            }
            else{
                $del = 0;
                $natural = strval($sub-10).$natural;
            }
        }

        $natural = $this->trimNumbers($natural,'l');

        return $natural;
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
}

