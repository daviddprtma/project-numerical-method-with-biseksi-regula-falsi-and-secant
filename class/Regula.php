<?php
require_once('class/check.php');

class regula{
    public $min;
    public $max;
    public $mid;
    public $minRes;
    public $maxRes;
    public $midRes;
    public $iteration;
    public $iterationError;

    function getMid(){
        $low = $this->min;
        $up = $this->max;
        $fl = $this->minRes;
        $fu = $this->maxRes;

        $this->mid = $up-(($fu*($low-$up))/($fl-$fu));
    }

    function fillInitRes(check $check){
        $this->minRes = $check->calcFunction($this->min);
        $this->maxRes = $check->calcFunction($this->max);
    }

    function fillIterationRes(check $check){
        $this->midRes = $check->calcFunction($this->mid);
    }

    function checkStop(regula $prev, check $check){
        $res = $check($this->iteration, $this->mid, $prev->mid);
        return $res;
    }

    function setUpInitial(check $check, float $initVal1, float $initVal2){
        $a = $check->calcFunction($initVal1);
        if($a>0){
            $this->max = $initVal1;
            $this->min = $initVal2;
        } else{
            $this->min = $initVal1;
            $this->max = $initVal2;
        }

        $this->iteration = 1;
        $this->fillInitRes($check);
        $this->getMid();
        $this->fillIterationRes($check);
        $this->iterationError = 1;
    }

    function nextIteration(regula $prev, check $check){
        $prevRes = $prev->midRes;
        $res = false;
        if($prevRes > 0){
            $this->max = $prev->mid;
            $this->min = $prev->min;
        }
        else{
            $this->min = $prev->mid;
            $this->max = $prev->max;
        }

        $this->iteration = $prev->iteration + 1;
        $this->fillInitRes($check);
        $this->getMid();
        $this->fillIterationRes($check);
        $this->iterationError = (float) $check->calcIterationError($this->mid, $prev->mid);

        $res = $check->checkStop($this->iteration, $this->iterationError, $this->midRes);
        return $res;
    }
}
?>