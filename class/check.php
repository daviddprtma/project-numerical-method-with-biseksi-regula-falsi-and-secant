<?php
class check{
    public $sigFig;
    public $maxIteration;
    public $funResTarget;

    function calcFunction(float $x){
        //calculate result of the function
        $result = exp(-1*$x)-$x;
        return $result;
    }

    function calcIterationError(float $now, float $prev){
        $iterationError = (float) abs((($now-$prev)/$now)*100.0);
        return $iterationError;
    }

    function checkStartVal(float $x1, float $x2){
        //calulate the result of the function
        $val1 = $this->calcFunction($x1);
        $val2 = $this->calcFunction($x2);
        //set up initial value
        $pos1 = false;
        $pos2 = false;
        $res = "y";
        //check the sign. if + then true if - then false
        if($val1>0){$pos1 = true;}
        if($val2>0){$pos2 = true;}
        //check if the sign of init value is the same
        if($pos1 == $pos2){
            $res = "n";
        }
        //check in case the val get is 0. which is the target
        if($val1 == 0 || $val2 == 0){
            $res = "target";
        }
        return $res;
    }

    function checkStop(int $iteration, float $iterationError, float $x){
        $res = false;

        $errorCheck = 5.0*(10**(1-$this->sigFig));
        if($errorCheck>$iterationError){
            $res = true;
        }
        if($this->maxIteration > 0 && $iteration >= $this->maxIteration){
            $res = true;
        }
        if($x != 0){
            if($this->funResTarget<0){
                $funTarget = 10**($this->funResTarget);
                if($funTarget>abs($x)){
                    $res = true;
                }
            }
        } else {
            $res=0;
        }

        return $res;
    }
}
?>