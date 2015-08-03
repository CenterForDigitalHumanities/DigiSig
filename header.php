<?php
echo "<div class='header digisigHeader'>
        <img class='digisigImg' src='http://localhost:8080/DigiSig/images/digsig.jpg'/>
    </div> ";
$logBtn = "";
$action = $_server['PHP_SElF'];
 if(!isset($_SESSION['userID']) || $_SESSION['userID']===1 ){
$action = $_SERVER['PHP_SELF'];
if(!isset($_SESSION['userID'])){
    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"include/login.php\"' />";
    $_SESSION['userID'] = 1;
    $_SESSION['fk_access'] = 1;
    $_SESSION['fk_repository'] = 0;
}
else if(isset($_SESSION['userID']) && $_SESSION['userID'] == 1)
{
    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"include/login.php\"' />";
}
else
{
    $_SESSION['user_email'] = "DigiSig";
    $logBtn = "<span class='login'>".$_SESSION['user_email']."</span>&nbsp;&nbsp;&nbsp;<input class='login' type='button' value='log out' onclick='window.location=\"logout.php\"' />";
}
echo '<form name ="navigate" action="'.$action.'" method="post" class="header">
        <!--<div class="navTitle">Navigation</div>-->
        <p class="navigation">
            <input class="navigate" type="submit" name ="submit" value ="HOME"/>
            <input class="navigate" type="submit" name ="submit" value ="ABOUT"/>
            <input class="navigate" type="submit" name ="submit" value ="ADVANCED SEARCH"/>
            <input class="navigate" type="submit" name ="submit" value ="CONTACT"/>
            '.$logBtn.'
        </p>
    </form>';
//echo "<div class='userInfo'>User Information:<br>
//      userID: ".$_SESSION['userID']."<br>
//      username: ".$_SESSION['user_email']."
//      </div>";
?>
