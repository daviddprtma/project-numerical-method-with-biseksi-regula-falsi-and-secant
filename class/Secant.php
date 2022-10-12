<?php
require_once('class/check.php');

class secant{
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

    function checkStop(secant $prev, check $check){
        $res = $check($this->iteration, $this->mid, $prev->mid);
        return $res;
    }

    function setUpInitial(check $check, float $initVal1, float $initVal2){
        $this->min = $initVal1;
        $this->max = $initVal2;

        $this->iteration = 1;
        $this->fillInitRes($check);
        $this->getMid();
        $this->fillIterationRes($check);
        $this->iterationError = 1;
    }

    function nextIteration(secant $prev, check $check){
        $this->min = $prev->max;
        $this->max = $prev->mid;

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