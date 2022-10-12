<!DOCTYPE html>
<html>
<?php
    require_once("class/check.php");
    require_once("class/Biseksi.php");
    require_once("class/Regula.php");
    require_once("class/Secant.php");
?>
<head>
    <style>
        table, th, td{
            border : 1px solid black;
            min-width:100px;
        }
        .pageIndex li{
            display:inline-block;
            width: 10px;
        }
        .tableList{
            display:inline-block;
            border : 1px solid black;
            margin:auto;
            padding : 30px;
        }
        .formDiv{
            margin:auto;
        }
    </style>
</head>
<body>
    <div class='tableList'>
        <div class="formDiv">
            <form method='POST'>
                <p>Nilai Awal 1 : <input type="number" name="startVal1" required
                    <?php
                        if(isset($_POST["startVal1"])){
                            $val = $_POST["startVal1"];
                            echo " value = '$val'";
                        }
                    ?>
                ></p>
                <p>Nilai Awal 2 : <input type="number" name="startVal2" required
                    <?php
                        if(isset($_POST["startVal2"])){
                            $val = $_POST["startVal2"];
                            echo " value = '$val'";
                        }
                    ?>
                ></p>
                <br>
                <p>Kriteria Berhenti</p>
                <p>Angka Signifikan : <input type="number" min="1" step="1" name="sigFig"
                    <?php
                        if(isset($_POST["sigFig"])){
                            $val = $_POST["sigFig"];
                            echo " value = '$val'";
                        }
                    ?>
                > (Default : 2, Minimum angka signifikan : 1)</p>
                <p>Maksimum Iterasi : <input type="number" min="1" step="1" name="maxIteration"
                    <?php
                        if(isset($_POST["maxIteration"])){
                            $val = $_POST["maxIteration"];
                            echo " value = '$val'";
                        }
                    ?>
                > (Default : -, Iterasi Minimum : 1)</p>
                <p>Hasil f(x) : 1 > 10^(<input type="number" max="-1" step="1" name="FunResTarget"
                <?php
                    if(isset($_POST["FunResTarget"])){
                        $val = $_POST["FunResTarget"];
                        echo " value = '$val'";
                    }
                ?>
                >) > f(x) (Default : -, maximum target hasil : 10^(-1))</p>
                <input type="submit" name="submit" from="index" value="Start Calculation">
            </form>
        </div>
        <br>

        <?php
            if(isset($_POST["submit"])){
                //set the stopping criteria
                $check = new check();
                if(isset($_POST["sigFig"])){
                    $sigFig = (int) $_POST["sigFig"];
                    if($sigFig>0){
                        $check->sigFig = $_POST["sigFig"];
                    } else {
                        $check->sigFig = 2;
                    }
                } else { $check->sigFig = 2; }

                if(isset($_POST["maxIteration"])){
                    $maxIteration = (int) $_POST["maxIteration"];
                    if($maxIteration>0){
                        $check->maxIteration = $maxIteration;
                    } else {
                        $check->maxIteration = -1;
                    }
                } else { $check->maxIteration = -1; }

                if(isset($_POST["FunResTarget"])){
                    $FunResTarget = (int) $_POST["FunResTarget"];
                    if($FunResTarget<=0){
                        $check->funResTarget = $FunResTarget;
                    } else {
                        $check->funResTarget = 1;
                    }
                } else { $check->funResTarget = 1; }

                //check the initial condition
                $initVal1 = $_POST["startVal1"]*1.0;
                $initVal2 = $_POST["startVal2"]*1.0;
                $init = $check->checkStartVal($initVal1, $initVal2);
            }
        ?>

        <br>
        <table>
            <tr><th colspan='7'>Biseksi</th></tr>
            <tr>
                <th>x<sub>l</sub></th>
                <th>x<sub>u</sub></th>
                <th>x<sub>middle</sub></th>
                <th>f(x<sub>l</sub>)</th>
                <th>f(x<sub>u</sub>)</th>
                <th>f(x<sub>middle</sub>)</th>
                <th>&epsilon;<sub>a</sub></th>
            </tr>
            <?php
                if(isset($_POST["submit"])){
                    if($init == "target"){
                        echo "<tr><td colspan='7'>Salah satu nilai awal adalah akar dari persamaan</td></tr>";
                    }
                    else if($init == "n"){
                        echo "<tr><td colspan='7'>Nilai awal tidak mengapit akar</td></tr>";
                    }
                    else{
                        $biseksi = new biseksi();
                        $biseksi->setUpInitial($check, $initVal1, $initVal2);
                        $biseksiRes = false;

                        echo "<tr>";
                        echo "<td>$biseksi->min</td>";
                        echo "<td>$biseksi->max</td>";
                        echo "<td>$biseksi->mid</td>";
                        echo "<td>$biseksi->minRes</td>";
                        echo "<td>$biseksi->maxRes</td>";
                        echo "<td>$biseksi->midRes</td>";
                        echo "<td>-</td>";
                        echo "</tr>";

                        $biseksiPrev = $biseksi;
                        while(!$biseksiRes){
                            $biseksiNow = new biseksi();
                            $biseksiRes = $biseksiNow->nextIteration($biseksiPrev, $check);
                            
                            echo "<tr>";
                            echo "<td>$biseksiNow->min</td>";
                            echo "<td>$biseksiNow->max</td>";
                            echo "<td>$biseksiNow->mid</td>";
                            echo "<td>$biseksiNow->minRes</td>";
                            echo "<td>$biseksiNow->maxRes</td>";
                            echo "<td>$biseksiNow->midRes</td>";
                            echo "<td>$biseksiNow->iterationError</td>";
                            echo "</tr>";

                            $biseksiPrev = $biseksiNow;
                        }
                    }
                }
            ?>
        </table>
        <br><br>

        <table>
            <tr><th colspan='7'>Regula-Falsi</th></tr>
            <tr>
                <th>x<sub>l</sub></th>
                <th>x<sub>u</sub></th>
                <th>x<sub>middle</sub></th>
                <th>f(x<sub>l</sub>)</th>
                <th>f(x<sub>u</sub>)</th>
                <th>f(x<sub>middle</sub>)</th>
                <th>&epsilon;<sub>a</sub></th>
            </tr>
            <?php
                if(isset($_POST["submit"])){
                    if($init == "target"){
                        echo "<tr><td colspan='7'>Salah satu nilai awal adalah akar dari persamaan</td></tr>";
                    }
                    else if($init == "n"){
                        echo "<tr><td colspan='7'>Nilai awal tidak mengapit akar</td></tr>";
                    }
                    else{
                        $regula = new regula();
                        $regula->setUpInitial($check, $initVal1, $initVal2);
                        $regulaRes = false;
    
                        echo "<tr>";
                        echo "<td>$regula->min</td>";
                        echo "<td>$regula->max</td>";
                        echo "<td>$regula->mid</td>";
                        echo "<td>$regula->minRes</td>";
                        echo "<td>$regula->maxRes</td>";
                        echo "<td>$regula->midRes</td>";
                        echo "<td>-</td>";
                        echo "</tr>";

                        $regulaPrev = $regula;
                        while(!$regulaRes){
                            $regulaNow = new regula();
                            $regulaRes = $regulaNow->nextIteration($regulaPrev, $check);
                            
                            echo "<tr>";
                            echo "<td>$regulaNow->min</td>";
                            echo "<td>$regulaNow->max</td>";
                            echo "<td>$regulaNow->mid</td>";
                            echo "<td>$regulaNow->minRes</td>";
                            echo "<td>$regulaNow->maxRes</td>";
                            echo "<td>$regulaNow->midRes</td>";
                            echo "<td>$regulaNow->iterationError</td>";
                            echo "</tr>";

                            $regulaPrev = $regulaNow;
                        }
                    }
                }
            ?>
        </table>
        <br><br>

        <table>
            <tr><th colspan='7'>Secant</th></tr>
            <tr>
                <th>x<sub>(i-1)</sub></th>
                <th>x<sub>i</sub></th>
                <th>x<sub>(i+1)</sub></th>
                <th>f(x<sub>(i-1)</sub>)</th>
                <th>f(x<sub>i</sub>)</th>
                <th>f(x<sub>(i+1)</sub>)</th>
                <th>&epsilon;<sub>a</sub></th>
            </tr>
            <?php
                if(isset($_POST["submit"])){
                    if($init == "target"){
                        echo "<tr><td colspan='7'>Salah satu nilai awal adalah akar dari persamaan</td></tr>";
                    }
                    else{
                        $secant = new secant();
                        $secant->setUpInitial($check, $initVal1, $initVal2);
                        $secantRes = false;
    
                        echo "<tr>";
                        echo "<td>$secant->min</td>";
                        echo "<td>$secant->max</td>";
                        echo "<td>$secant->mid</td>";
                        echo "<td>$secant->minRes</td>";
                        echo "<td>$secant->maxRes</td>";
                        echo "<td>$secant->midRes</td>";
                        echo "<td>-</td>";
                        echo "</tr>";
                        
                        $secantPrev = $secant;
                        while(!$secantRes){
                            $secantNow = new secant();
                            $secantRes = $secantNow->nextIteration($secantPrev, $check);
                            
                            echo "<tr>";
                            echo "<td>$secantNow->min</td>";
                            echo "<td>$secantNow->max</td>";
                            echo "<td>$secantNow->mid</td>";
                            echo "<td>$secantNow->minRes</td>";
                            echo "<td>$secantNow->maxRes</td>";
                            echo "<td>$secantNow->midRes</td>";
                            echo "<td>$secantNow->iterationError</td>";
                            echo "</tr>";

                            $secantPrev = $secantNow;
                        }
                    }
                }
            ?>
        </table>
    </div>
</body>
</html>