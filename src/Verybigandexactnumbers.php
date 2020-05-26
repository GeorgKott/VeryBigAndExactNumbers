<?php

namespace georgkott\verybigandexactnumbers;

class Verybigandexactnumbers
{
    public $naturalNumber;
    public $rationalNumber;
    public $sign;

    public $history = [];

    private function checkOnlyZero($str = '')
    {
        if(preg_match('/^[0]+$/',$str)){
            return true;
        }

        return false;
    }

    private function deconstructValue($str = '',$trim = 'l')
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

    private function constructNumber($number = '',$n = 0,$repeat = '')
    {
        if($repeat == 'l'){
            $number = str_repeat('0',$n).$number;
        }
        else if($repeat == 'r'){
            $number .= str_repeat('0',$n);
        }

        return $number;
    }

    private function constructNumbers($n1 = '',$n2 = '',$repeat = '')
    {
        if(strlen($n1) > strlen($n2)){
            $natural2 = $this->constructNumber($n2,strlen($n1)-strlen($n2),$repeat);
            $natural1 = $n1;
        }
        else if(strlen($n1) < strlen($n2)){
            $natural1 = $this->constructNumber($n1,strlen($n2)-strlen($n1),$repeat);
            $natural2 = $n2;
        }
        else{
            $natural2 = $n2;
            $natural1 = $n1;
        }

        return [$natural1,$natural2];
    }

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

                $natural = $this->deconstructValue($m[2][0],'l');

                if(!empty($m[5][0]) || $m[5][0] == "0"){
                    $rational = $this->deconstructValue($m[5][0],'r');
                }
                else{
                    $rational = "0";
                }

                if(!empty($m[9][0]) || $m[9][0] == "0"){
                    $exp = $this->deconstructValue($m[9][0],'l');

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

                $this->naturalNumber = $this->deconstructValue($natural,'l');
                $this->rationalNumber = $this->deconstructValue($rational,'r');

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

    private function sumTwo($n1 = '',$r1 = '',$n2 = '', $r2 = '')
    {
        $natural = '';
        $rational = '';

        $add = 0;

        for($i=strlen($r1)-1;$i>=0;$i--){
            $sum = $r1[$i] + $r2[$i] + $add;
            if($sum >= 10){
                if($i == 0){
                    $rational = strval($sum).$rational;
                }
                else{
                    $add = 1;
                    $rational = strval($sum%10).$rational;
                }
            }
            else{
                $add = 0;
                $rational = strval($sum).$rational;
            }
        }

        for($i=strlen($n1)-1;$i>=0;$i--){
            $sum = $n1[$i] + $n2[$i] + $add;
            if($sum >= 10){
                if($i == 0){
                    $natural = strval($sum).$natural;
                }
                else{
                    $add = 1;
                    $natural = strval($sum%10).$natural;
                }
            }
            else{
                $add = 0;
                $natural[$i] = strval($sum).$natural;
            }
        }

        $natural = $this->deconstructValue($natural,'l');
        $rational = $this->deconstructValue($rational,'r');

        return [$natural,$rational];
    }

    private function subTwo($n1 = '',$r1 = '',$n2 = '', $r2 = '')
    {
        $natural = '';
        $rational = '';

        $del = 0;

        for($i=strlen($r1)-1;$i>=0;$i--){
            $sub = $r1[$i] - $r2[$i] - $del + 10;
            if($sub < 10){
                $del = 1;
                $rational = strval($sub).$rational;
            }
            else{
                $del = 0;
                $rational = strval($sub-10).$rational;
            }
        }

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

        $natural = $this->deconstructValue($natural,'l');
        $rational = $this->deconstructValue($rational,'r');

        return [$natural,$rational];
    }

    /*
     * a + b
     */

    public function sum(Verybigandexactnumbers $n)
    {
        list($natural1,$natural2) = $this->constructNumbers($this->naturalNumber,$n->naturalNumber,'l');
        list($rational1,$rational2) = $this->constructNumbers($this->rationalNumber,$n->rationalNumber,'r');

        if($this->sign === $n->sign){
            list($this->naturalNumber,$this->rationalNumber) = $this->sumTwo($natural1,$rational1,$natural2,$rational2);
        }
        else{
            if(($this->naturalNumber === $n->naturalNumber)&&($this->rationalNumber === $n->rationalNumber)){
                $this->naturalNumber = '0';
                $this->rationalNumber = '0';
                $this->sign = 1;
            }
            else{
                if($this->sign === 0){
                    if(self::moreAbs($this,$n)){
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural1,$rational1,$natural2,$rational2);
                        $this->sign = 0;
                    }
                    else{
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural2,$rational2,$natural1,$rational1);
                        $this->sign = 1;
                    }
                }
                else{
                    if(self::moreAbs($this,$n)){
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural1,$rational1,$natural2,$rational2);
                        $this->sign = 1;
                    }
                    else{
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural2,$rational2,$natural1,$rational1);
                        $this->sign = 0;
                    }
                }
            }
        }
    }

    /*
    * a - b
    */

    public function sub(Verybigandexactnumbers $n)
    {
        list($natural1,$natural2) = $this->constructNumbers($this->naturalNumber,$n->naturalNumber,'l');
        list($rational1,$rational2) = $this->constructNumbers($this->rationalNumber,$n->rationalNumber,'r');

        if($this->sign === $n->sign){
            if(($this->naturalNumber === $n->naturalNumber)&&($this->rationalNumber === $n->rationalNumber)){
                $this->naturalNumber = '0';
                $this->rationalNumber = '0';
                $this->sign = 1;
            }
            else{
                if($this->sign === 1){
                    if(self::moreAbs($this,$n)){
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural1,$rational1,$natural2,$rational2);
                        $this->sign = 1;
                    }
                    else{
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural2,$rational2,$natural1,$rational1);
                        $this->sign = 0;
                    }
                }
                else{
                    if(self::moreAbs($this,$n)){
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural1,$rational1,$natural2,$rational2);
                        $this->sign = 0;
                    }
                    else{
                        list($this->naturalNumber,$this->rationalNumber) = $this->subTwo($natural2,$rational2,$natural1,$rational1);
                        $this->sign = 1;
                    }
                }
            }
        }
        else{
            if($this->sign === 1){
                list($this->naturalNumber,$this->rationalNumber) = $this->sumTwo($natural1,$rational1,$natural2,$rational2);
            }
            else{
                list($this->naturalNumber,$this->rationalNumber) = $this->sumTwo($natural1,$rational1,$natural2,$rational2);
                $this->sign = 0;
            }
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

    /*
    * a * b
    */

    public function mul(Verybigandexactnumbers $n)
    {
        $data = [];
        $multiple = $n->naturalNumber.$n->rationalNumber;

        $r = 0;
        for($i=strlen($multiple)-1;$i>=0;$i--){
            $data[] = $this->mulN($this->naturalNumber.$this->rationalNumber,$multiple[$i],$r);
            $r++;
        }

        $mul = '0';

        foreach($data as $item){
            $mul = $this->mulSum($mul,$item);
        }

        $point = strlen($this->rationalNumber)+strlen($n->rationalNumber);

        $this->naturalNumber = $this->deconstructValue(substr($mul,0,-$point),'l');
        $this->rationalNumber = $this->deconstructValue(substr($mul,-$point),'r');

        if($this->sign === $n->sign){
            $this->sign = 1;
        }
        else{
            $this->sign = 0;
        }
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

        $natural = $this->deconstructValue($natural,'l');

        return $natural;
    }

    private function divTwo($number1 = '',$number2 = '',$accuracy = 20)
    {
        $natural = [];

        $str = substr($number1,0,strlen($number2));
        $pflag = 0;
        $pn = 0;
        $offset = strlen($number2);
        $oflag = 0;

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

                    $pn++;
                }
                else{
                    $str = $str.substr($number1,$offset,1);
                    $offset++;

                    if($oflag == 0){
                        $oflag = 1;
                    }
                    else{
                        $pn++;
                    }
                }

                if(!isset($natural[$pn])){
                    $natural[$pn] = 0;
                }
            }
        }

        $itog = '';

        for($i=0;$i<=$pflag;$i++){
            if(!isset($natural[$i])){
                $itog .= '0';
            }
            else{
                $itog .= $natural[$i];
            }
        }

        $n = $this->deconstructValue(substr($itog,0,-$pflag),'l');
        $r = $this->deconstructValue(substr($itog,-$pflag),'r');

        return [$n,$r];
    }

    /*
    * a / b
    */

    public function div(Verybigandexactnumbers $n,$accuracy = 20)
    {
        if($n->naturalNumber === '0' && $n->rationalNumber === '0'){
            throw new Exception('Division by zero');
        }

        if(strlen($this->rationalNumber)>strlen($n->rationalNumber)){
            $number1 = $this->naturalNumber.$this->rationalNumber;
            $number2 = $n->naturalNumber.$n->rationalNumber.str_repeat('0',strlen($this->rationalNumber)-strlen($n->rationalNumber));
        }
        else if(strlen($this->rationalNumber)<strlen($n->rationalNumber)){
            $number1 = $this->naturalNumber.$this->rationalNumber.str_repeat('0',strlen($n->rationalNumber)-strlen($this->rationalNumber));
            $number2 = $n->naturalNumber.$n->rationalNumber;
        }
        else{
            $number1 = $this->naturalNumber.$this->rationalNumber;
            $number2 = $n->naturalNumber.$n->rationalNumber;
        }

        if($accuracy < max(strlen($this->rationalNumber),strlen($n->rationalNumber))){
            $accuracy = max(strlen($this->rationalNumber),strlen($n->rationalNumber));
        }

        list($this->naturalNumber,$this->rationalNumber) = $this->divTwo($number1,$number2,$accuracy);

        if($this->sign === $n->sign){
            $this->sign = 1;
        }
        else{
            $this->sign = 0;
        }
    }

    /*
    * a ** b
    */

    public function pow(Verybigandexactnumbers $n)
    {

    }

    /*
    * ln(a)
    */

    public function ln(Verybigandexactnumbers $n)
    {

    }

    /*
    * |a| > |b|
    */

    public static function moreAbs(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2):bool
    {
        if(strlen($n1->naturalNumber) > strlen($n2->naturalNumber)){
            return true;
        }
        else if(strlen($n1->naturalNumber) < strlen($n2->naturalNumber)){
            return false;
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
                        return true;
                    }
                    else if($rational1[$i]<$rational2[$i]){
                        return false;
                    }
                }

                return false;
            }
            else{
                for($i=0;$i<strlen($n1->naturalNumber);$i++){
                    if($n1->naturalNumber[$i]>$n2->naturalNumber[$i]){
                        return true;
                    }
                    else if($n1->naturalNumber[$i]<$n2->naturalNumber[$i]){
                        return false;
                    }
                }

                return false;
            }
        }
    }

    /*
    * a > b
    */

    public static function more(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2):bool
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

    public static function less(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2):bool
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

    public static function moreEqual(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2)
    {

    }

    /*
    * a <= b
    */

    public static function lessEqual(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2)
    {

    }

    /*
    * a == b
    */

    public static function equal(Verybigandexactnumbers $n1,Verybigandexactnumbers $n2)
    {
        if(($n1->sign === $n2->sign)&&($n1->naturalNumber === $n2->naturalNumber)&&($n1->rationalNumber === $n2->rationalNumber))
            return true;

        return false;
    }

    /*
    * a != b
    */

    public static function notEqual(Verybigandexactnumbers $n1, Verybigandexactnumbers $n2)
    {
        if(($n1->sign !== $n2->sign)||($n1->naturalNumber !== $n2->naturalNumber)||($n1->rationalNumber !== $n2->rationalNumber))
            return true;

        return false;
    }
}

