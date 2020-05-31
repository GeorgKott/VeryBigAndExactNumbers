<?php

namespace georgkott\verybigandexactnumbers\operation;

class DiffOperation extends OperationAbstract
{
    private function diffN($n1 = '0',$n2 = '0',$del = '0')
    {
        $number = '';

        for($i=strlen($n1)-1;$i>=0;$i--){
            $sub = $n1[$i] - $n2[$i] - $del + 10;
            if($sub < 10){
                $del = 1;
                $number = strval($sub).$number;
            }
            else{
                $del = 0;
                $number = strval($sub-10).$number;
            }
        }

        return [$number,$del];
    }

    private function diff()
    {
        $del = 0;

        list($this->resultRational,$del) = $this->diffN($this->r1,$this->r2,$del);
        list($this->resultNatural,$del) = $this->diffN($this->n1,$this->n2,$del);
    }

    public function result()
    {
        $this->diff();

        return [$this->resultNatural,$this->resultRational];
    }
}

