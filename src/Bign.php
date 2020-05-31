<?php

namespace georgkott\verybigandexactnumbers;

use georgkott\verybigandexactnumbers\operation\OperationDecorator;

class BigN
{
    public $naturalNumber;
    public $rationalNumber;
    public $sign;

    private $history = [];

    public function __construct($str = '0')
    {
        if(is_string($str)){
            preg_match_all('/^(-?)([0-9]+)((\.)([0-9]+))?(([eE])([+-])?([0-9]+))?$/',$str,$m);

            if(!empty($m[0][0]) || $m[0][0] == "0"){
                if(!empty($m[1][0])){
                    $this->sign = 0;
                }
                else{
                    $this->sign = 1;
                }

                $natural = $this->trimNumbers($m[2][0],'l');

                if(!empty($m[5][0]) || $m[5][0] == "0"){
                    $rational = $this->trimNumbers($m[5][0],'r');
                }
                else{
                    $rational = "0";
                }

                if(!empty($m[9][0]) || $m[9][0] == "0"){
                    $exp = $this->trimNumbers($m[9][0],'l');

                    if(empty($m[8][0]) || $m[8][0] == '+'){
                        if($rational == '0'){
                            if($natural !== '0'){
                                $natural .= str_repeat('0',$exp);
                            }
                        }
                        else{
                            $add = substr($rational,0,$exp);
                            $natural .= $add.str_repeat('0',$exp-strlen($add));
                            $rational = substr($rational,$exp) == ''?'0':substr($rational,$exp);
                        }
                    }
                    else{
                        if($natural == '0'){
                            if($rational !== '0'){
                                $rational = str_repeat('0',$exp).$rational;
                            }
                        }
                        else{
                            $add = strrev(substr(strrev($natural),0,$exp));
                            $natural = strlen($natural) <= $exp ? '0':strrev(substr(strrev($natural),$exp));
                            $rational = str_repeat('0',$exp-strlen($add)).$add.$rational;
                        }
                    }
                }

                $this->naturalNumber = $this->trimNumbers($natural,'l');
                $this->rationalNumber = $this->trimNumbers($rational,'r');

                if($this->naturalNumber === "0" && $this->rationalNumber === "0" && $this->sign === 0){
                    $this->sign = 1;
                }
            }
            else{
                throw new \InvalidArgumentException(
                    'Passed value wrong format'
                );
            }

        }
        else{
            throw new \InvalidArgumentException(
                'Passed value should be a string'
            );
        }
    }

    public function show()
    {
        $sign = '';

        if($this->sign === '0'){
            $sign = '-';
        }

        echo $sign.$this->naturalNumber.'.'.$this->rationalNumber;
    }

    /*
     * a + b
     */

    public function sum(BigN $n)
    {
        list($natural1,$natural2) = $this->padNumbers($this->naturalNumber,$n->naturalNumber,'l');
        list($rational1,$rational2) = $this->padNumbers($this->rationalNumber,$n->rationalNumber,'r');

        $sum = new OperationDecorator(__FUNCTION__,$natural1,$rational1,$natural2,$rational2,$this->sign,$n->sign);
        list($natural,$rational,$sign) = $sum->result();

        $this->naturalNumber = $this->trimNumbers($natural,'l');
        $this->rationalNumber = $this->trimNumbers($rational,'r');
        $this->sign = $sign;
    }

    /*
    * a - b
    */

    public function diff(BigN $n)
    {
        list($natural1,$natural2) = $this->padNumbers($this->naturalNumber,$n->naturalNumber,'l');
        list($rational1,$rational2) = $this->padNumbers($this->rationalNumber,$n->rationalNumber,'r');

        $diff = new OperationDecorator(__FUNCTION__,$natural1,$rational1,$natural2,$rational2,$this->sign,$n->sign);
        list($natural,$rational,$sign) = $diff->result();

        $this->naturalNumber = $this->trimNumbers($natural,'l');
        $this->rationalNumber = $this->trimNumbers($rational,'r');
        $this->sign = $sign;
    }

    /*
    * a * b
    */

    public function mul(BigN $n)
    {
        $mul = new OperationDecorator(__FUNCTION__,$this->naturalNumber,$this->rationalNumber,$n->naturalNumber,$n->rationalNumber,$this->sign,$n->sign);
        list($natural,$rational,$sign) = $mul->result();

        $this->naturalNumber = $this->trimNumbers($natural,'l');
        $this->rationalNumber = $this->trimNumbers($rational,'r');
        $this->sign = $sign;
    }

    /*
    * a / b
    */

    public function div(BigN $n,$accuracy = 20)
    {
        $div = new OperationDecorator(__FUNCTION__,$this->naturalNumber,$this->rationalNumber,$n->naturalNumber,$n->rationalNumber,$this->sign,$n->sign,['accuracy' => $accuracy]);
        list($natural,$rational,$sign) = $div->result();

        $this->naturalNumber = $this->trimNumbers($natural,'l');
        $this->rationalNumber = $this->trimNumbers($rational,'r');
        $this->sign = $sign;
    }

    /*
    * a ** b
    */

    public function pow(BigN $n)
    {

    }

    /*
    * ln(a)
    */

    public function ln(BigN $n)
    {

    }

    /*
    * a > b
    */

    public static function more(BigN $n1,BigN $n2):bool
    {
        if($n1->sign > $n2->sign){
            return true;
        }
        else if($n1->sign < $n2->sign){
            return false;
        }
        else{
            if(strlen($n1->naturalNumber) > strlen($n2->naturalNumber)){
                return $n1->sign == 0?false:true;
            }
            else if(strlen($n1->naturalNumber) < strlen($n2->naturalNumber)){
                return $n1->sign == 0?true:false;
            }
            else{
                if($n1->naturalNumber === $n2->naturalNumber){
                    $rational1 = $n1->rationalNumber;
                    $rational2 = $n2->rationalNumber;

                    if(strlen($rational1) > strlen($rational2)){
                        $rational2 .= str_repeat('0',strlen($rational1)-strlen($rational2));
                    }
                    else if(strlen($rational1) < strlen($rational2)){
                        $rational1 .= str_repeat('0',strlen($rational2)-strlen($rational1));
                    }

                    for($i=0;$i<strlen($rational1);$i++){
                        if($rational1[$i]>$rational2[$i]){
                            return $n1->sign == 0?false:true;
                        }
                        else if($rational1[$i]<$rational2[$i]){
                            return $n1->sign == 0?true:false;
                        }
                    }

                    return $n1->sign == 0?true:false;
                }
                else{
                    for($i=0;$i<strlen($n1->naturalNumber);$i++){
                        if($n1->naturalNumber[$i]>$n2->naturalNumber[$i]){
                            return $n1->sign == 0?false:true;
                        }
                        else if($n1->naturalNumber[$i]<$n2->naturalNumber[$i]){
                            return $n1->sign == 0?true:false;
                        }
                    }

                    return $n1->sign == 0?true:false;
                }
            }
        }
    }

    /*
    * a < b
    */

    public static function less(BigN $n1,BigN $n2):bool
    {
        if($n1->sign > $n2->sign){
            return false;
        }
        else if($n1->sign < $n2->sign){
            return true;
        }
        else{
            if(strlen($n1->naturalNumber) > strlen($n2->naturalNumber)){
                return $n1->sign == 0?true:false;
            }
            else if(strlen($n1->naturalNumber) < strlen($n2->naturalNumber)){
                return $n1->sign == 0?false:true;
            }
            else{
                if($n1->naturalNumber === $n2->naturalNumber){
                    $rational1 = $n1->rationalNumber;
                    $rational2 = $n2->rationalNumber;

                    if(strlen($rational1) > strlen($rational2)){
                        $rational2 .= str_repeat('0',strlen($rational1)-strlen($rational2));
                    }
                    else if(strlen($rational1) < strlen($rational2)){
                        $rational1 .= str_repeat('0',strlen($rational2)-strlen($rational1));
                    }

                    for($i=0;$i<strlen($rational1);$i++){
                        if($rational1[$i]>$rational2[$i]){
                            return $n1->sign == 0?true:false;
                        }
                        else if($rational1[$i]<$rational2[$i]){
                            return $n1->sign == 0?false:true;
                        }
                    }

                    return $n1->sign == 0?false:true;
                }
                else{
                    for($i=0;$i<strlen($n1->naturalNumber);$i++){
                        if($n1->naturalNumber[$i]>$n2->naturalNumber[$i]){
                            return $n1->sign == 0?true:false;
                        }
                        else if($n1->naturalNumber[$i]<$n2->naturalNumber[$i]){
                            return $n1->sign == 0?false:true;
                        }
                    }

                    return $n1->sign == 0?false:true;
                }
            }
        }
    }

    /*
    * a >= b
    */

    public static function moreEqual(BigN $n1,BigN $n2)
    {

    }

    /*
    * a <= b
    */

    public static function lessEqual(BigN $n1,BigN $n2)
    {

    }

    /*
    * a == b
    */

    public static function equal(BigN $n1,BigN $n2)
    {
        if(($n1->sign === $n2->sign)&&($n1->naturalNumber === $n2->naturalNumber)&&($n1->rationalNumber === $n2->rationalNumber))
            return true;

        return false;
    }

    /*
    * a != b
    */

    public static function notEqual(BigN $n1, BigN $n2)
    {
        if(($n1->sign !== $n2->sign)||($n1->naturalNumber !== $n2->naturalNumber)||($n1->rationalNumber !== $n2->rationalNumber))
            return true;

        return false;
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

    private function padNumber($number = '',$n = 0,$repeat = '')
    {
        if($repeat == 'l'){
            $number = str_repeat('0',$n).$number;
        }
        else if($repeat == 'r'){
            $number .= str_repeat('0',$n);
        }

        return $number;
    }

    private function padNumbers($n1 = '',$n2 = '',$repeat = '')
    {
        if(strlen($n1) > strlen($n2)){
            $natural2 = $this->padNumber($n2,strlen($n1)-strlen($n2),$repeat);
            $natural1 = $n1;
        }
        else if(strlen($n1) < strlen($n2)){
            $natural1 = $this->padNumber($n1,strlen($n2)-strlen($n1),$repeat);
            $natural2 = $n2;
        }
        else{
            $natural2 = $n2;
            $natural1 = $n1;
        }

        return [$natural1,$natural2];
    }
}

