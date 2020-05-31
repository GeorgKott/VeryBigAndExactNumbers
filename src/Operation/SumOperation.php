<?php

namespace georgkott\verybigandexactnumbers\operation;

class SumOperation extends OperationAbstract
{
    private function sumN($n1 = '0',$n2 = '0',$add = '0')
    {
        $number = '';

        for($i=strlen($n1)-1;$i>=0;$i--){
            $sum = $n1[$i] + $n2[$i] + $add;
            if($sum >= 10){
                if($i == 0){
                    $number = strval($sum).$number;
                }
                else{
                    $add = 1;
                    $number = strval($sum%10).$number;
                }
            }
            else{
                $add = 0;
                $number = strval($sum).$number;
            }
        }

        return [$number,$add];
    }

    private function sum()
    {
        $add = 0;

        list($this->resultRational,$add) = $this->sumN($this->r1,$this->r2,$add);
        list($this->resultNatural,$add) = $this->sumN($this->n1,$this->n2,$add);
    }

    public function result()
    {
        $this->sum();

        return [$this->resultNatural,$this->resultRational];
    }
}

