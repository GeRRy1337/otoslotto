<?php
require "db.inc.php";

if(isset($_POST['gepi'])){
    $szamok="";
    $j=0;
    while($j!=5){
        $randnum=rand(1,10);
        if(strpos($szamok,strval($randnum))===false){
            $szamok.=$randnum." ";
            $j++;
        }
    }
    $num="";
    for($i=0;$i<10;$i++){
        $num.=rand(0,9);
    }
    $result=$conn->query("SELECT szelvenyszam from szelvenyek where szelvenyszam =".$num);
    while($result->num_rows>0){
        $num="";
        for($i=0;$i<10;$i++){
            $num.=rand(0,9);
        }
        $result=$conn->query("SELECT szelvenyszam from szelvenyek where szelvenyszam =".$num);
    }
    $resId=$conn->query("SELECT lottoszam+1 from lotto order by lottoszam desc limit 1");
    if($resId->num_rows>0){
        $id=$resId->fetch_assoc()['lottoszam+1'];
    }else{
        $id=1;
    }
    if ($conn->query("INSERT INTO szelvenyek(szelvenyszam,tippek,lottoszam) VALUES($num,'".$szamok."',".$id.")")){
        header("Location:index.php");
    }

}

if(isset($_POST['kuld'])){
    $num="";
    for($i=0;$i<10;$i++){   
        $num.=rand(0,9);
    }
    $result=$conn->query("SELECT szelvenyszam from szelvenyek where szelvenyszam =".$num);
    while($result->num_rows>0){
        $num="";
        for($i=0;$i<10;$i++){
            $num.=rand(0,9);
        }
        $result=$conn->query("SELECT szelvenyszam from szelvenyek where szelvenyszam =".$num);
    }
    $resId=$conn->query("SELECT lottoszam+1 from lotto order by lottoszam desc limit 1");
    if($resId->num_rows>0){
        $id=$resId->fetch_assoc()['lottoszam+1'];
    }else{
        $id=1;
    }
    if ($conn->query("INSERT INTO szelvenyek(szelvenyszam,tippek,lottoszam) VALUES($num,'".$_POST['tippek']."',".$id.")")){
        header("Location:index.php");
    }
}

if(isset($_POST['general'])){
    $szamok="";
    $j=0;
    while($j!=5){
        $randnum=rand(1,10);
        if(strpos($szamok,strval($randnum))===false){
            $szamok.=$randnum." ";
            $j++;
        }
    }
    if ($conn->query("INSERT INTO lotto(szamok) VALUES('".$szamok."')")){
        header("Location:index.php");
    }
}

function getNyero($conn){
    $result=$conn->query("Select szamok from lotto order by lottoszam desc limit 1");
    if($result->num_rows>0){
        return $result->fetch_assoc()['szamok'];
    }

    return false;
}

function getTalalatok($conn){
    $talalatok=array(
        "0"=>0,
        "1"=>0,
        "2"=>0,
        "3"=>0,
        "4"=>0,
        "5"=>0,
        "max"=>0,
    );
    $szamok="";
    $result=$conn->query("Select szamok from lotto order by lottoszam desc limit 1");
    if($result->num_rows>0){
        $szamok=explode(" ",$result->fetch_assoc()['szamok']);
    }
    $result=$conn->query("SELECT tippek from szelvenyek where lottoszam = (SELECT lottoszam from lotto order by lottoszam desc limit 1)");
    if($result->num_rows>0){
        while($row=$result->fetch_assoc()){
            $db=0;
            foreach(explode(" ",$row['tippek']) as $szam){
                if(in_array($szam,$szamok)){
                    $db++;
                }
            }
            $talalatok[$db]++;
            $talalatok['max']++;
        }
    }
    return $talalatok;
    
}
?>
<form method="POST" action="index.php">
    <p>A sz??mokat sz??k??zzel elv??lasztva kell be??rni!</p>
    <div>
    <label for="">Sz??mok:</label>
    <input type="text" name="tippek">
    <input type="submit" name="kuld">
    </div>
</form>
<form method="POST" action="index.php">
    <label for="">G??pi lott??:</label>
    <input type="submit" name="gepi">
</form>
<form method="POST" action="index.php">
    <label for="">Nyer??sz??mok gener??l??sa:</label>
    <input type="submit" name="general">
</form>
<?php
if(getNyero($conn)){
?>
<div>
    Nyer??sz??mok:<?=getNyero($conn)?><br>
    <?php 
        $talalatok=getTalalatok($conn);
        echo "5 tal??latos ".$talalatok["5"]." db<br>
        4 tal??latos ".$talalatok["4"]." db<br>
        3 tal??latos ".$talalatok["3"]." db<br>
        2 tal??latos ".$talalatok["2"]." db<br>
        ??sszes fogad??s: ".$talalatok["max"]." db<br>";
    ?>
</div>
<?php } ?>