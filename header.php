<?php
echo "<div class='digisigHeader'>
        <img class='digisigImg' src='http://localhost:8080/DigiSig/images/digsig.jpg'/>
    </div> ";
$logBtn = "";
$action = $_server['PHP_SElF'];
 if(!isset($_SESSION['userID'])){
    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"include/login.php\"' />";
    $_SESSION['userID'] = 1;
    $_SESSION['fk_access'] = 1;
    $_SESSION['fk_repository'] = 0;
}
else{
    $logBtn = "<input class='login' type='button' value='log out' onclick='' />";
}
echo '<form name ="navigate" action="'.$action.'" method="post">
        <!--<div class="navTitle">Navigation</div>-->
        <p class="navigation">
            <input class="navigate" type="submit" name ="submit" value ="HOME"/>
            <input class="navigate" type="submit" name ="submit" value ="ABOUT"/>
            <input class="navigate" type="submit" name ="submit" value ="ADVANCED SEARCH"/>
            <input class="navigate" type="submit" name ="submit" value ="CONTACT"/>
            '.$logBtn.'
        </p>
    </form>';
?>
