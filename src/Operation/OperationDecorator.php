<?php

namespace georgkott\verybigandexactnumbers\operation;

class OperationDecorator
{
    private $namingOperation;

    private $resultNatural;
    private $resultRational;
    private $resultSign;

    public function __construct($operation,$n1 = '', $r1 = '', $n2 = '', $r2 = '',$s1 = 1, $s2 = 1,$param = [])
    {
        if($operation == 'sum'){
            if($s1 === $s2){
                $sum = new SumOperation($n1,$r1,$n2,$r2);
                list($this->resultNatural,$this->resultRational) = $sum->result();
                $this->resultSign = $s1;
            }
            else{
                if(($n1 === $n2)&&($r1 === $r2)){
                    $this->resultNatural = '0';
                    $this->resultRational = '0';
                    $this->resultSign = '1';
                }
                else{
                    if($s1 === '0'){
                        if($this->moreAbs($n1,$n2,$r1,$r2)){
                            $sum = new DiffOperation($n1,$r1,$n2,$r2);
                            list($this->resultNatural,$this->resultRational) = $sum->result();
                            $this->resultSign = '0';
                        }
                        else{
                            $sum = new DiffOperation($n2,$r2,$n1,$r1);
                            list($this->resultNatural,$this->resultRational) = $sum->result();
                            $this->resultSign = '1';
                        }
                    }
                    else{
                        if($this->moreAbs($n1,$n2,$r1,$r2)){
                            $sum = new DiffOperation($n1,$r1,$n2,$r2);
                            list($this->resultNatural,$this->resultRational) = $sum->result();
                            $this->resultSign = '1';
                        }
                        else{
                            $sum = new DiffOperation($n2,$r2,$n1,$r1);
                            list($this->resultNatural,$this->resultRational) = $sum->result();
                            $this->resultSign = '0';
                        }
                    }
                }
            }
        }
        else if($operation == 'diff'){
            if($s1 === $s2){
                if(($n1 === $n2)&&($r1 === $r2)){
                    $this->resultNatural = '0';
                    $this->resultRational = '0';
                    $this->resultSign = '1';
                }
                else{
                    if($s1 === '1'){
                        if($this->moreAbs($n1,$n2,$r1,$r2)){
                            $diff = new DiffOperation($n1,$r1,$n2,$r2);
                            list($this->resultNatural,$this->resultRational) = $diff->result();
                            $this->resultSign = '1';
                        }
                        else{
                            $diff = new DiffOperation($n2,$r2,$n1,$r1);
                            list($this->resultNatural,$this->resultRational) = $diff->result();
                            $this->resultSign = '0';
                        }
                    }
                    else{
                        if($this->moreAbs($n1,$n2,$r1,$r2)){
                            $diff = new DiffOperation($n1,$r1,$n2,$r2);
                            list($this->resultNatural,$this->resultRational) = $diff->result();
                            $this->resultSign = '0';
                        }
                        else{
                            $diff = new DiffOperation($n2,$r2,$n1,$r1);
                            list($this->resultNatural,$this->resultRational) = $diff->result();
                            $this->resultSign = '1';
                        }
                    }
                }
            }
            else{
                $sum = new SumOperation($n1,$r1,$n2,$r2);
                list($this->resultNatural,$this->resultRational) = $sum->result();

                if($s1 === '1'){
                    $this->resultSign = '1';
                }
                else{
                    $this->resultSign = '0';
                }
            }
        }
        else if($operation == 'mul'){
            $mul = new MultipleOperation($n1,$r1,$n2,$r2);
            list($this->resultNatural,$this->resultRational) = $mul->result();

            if($s1 === $s2){
                $this->resultSign = '1';
            }
            else{
                $this->resultSign = '0';
            }
        }
        else if($operation == 'div'){
            if($n2 === '0' && $r2 === '0'){
                throw new Exception('Division by zero');
            }

            if($param['accuracy'] < max(strlen($r1),strlen($r2))){
                $param['accuracy'] = max(strlen($r1),strlen($n->rationalNumber));
            }

            $div = new DivideOperation($n1,$r1,$n2,$r2,$param);
            list($this->resultNatural,$this->resultRational) = $div->result();

            if($s1 === $s2){
                $this->resultSign = '1';
            }
            else{
                $this->resultSign = '0';
            }
        }
    }

    private function moreAbs($n1,$n2,$r1,$r2):bool
    {
        if($n1 === $n2){
            for($i=0;$i<strlen($r1);$i++){
                if($r1[$i]>$r2[$i]){
                    return true;
                }
                else if($r1[$i]<$r2[$i]){
                    return false;
                }
            }

            return false;
        }
        else{
            for($i=0;$i<strlen($n1);$i++){
                if($n1[$i]>$n2[$i]){
                    return true;
                }
                else if($n1[$i]<$n2[$i]){
                    return false;
                }
            }

            return false;
        }
    }

    public function result()
    {
        return [$this->resultNatural,$this->resultRational,$this->resultSign];
    }
}

