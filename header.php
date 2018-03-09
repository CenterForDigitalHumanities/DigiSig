<div class='header digisigHeader'>
    <img class='digisigImg' src='<?php echo $basePath; ?>/digisig/images/digsig.jpg'/>
    <div id="headerCredit" href="<?php echo $basePath; ?>/digisig/entity/thisentity"></div>
</div>
<?php
$logBtn = "";
$action = $_SERVER['PHP_SELF'];
if(!isset($_SESSION['userID']) || $_SESSION['userID'] === 1){
    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"$basePath/digisig/include/login.php\"' />";
}
else
{
    $email = "";
    if(isset($_SESSION['user_email'])){
        $email = $_SESSION['user_email'];
    }
    else{
        $email = "NOT FOUND";
    }
    $logBtn = "<span class='login email' >User: ".$_SESSION['user_email']."&nbsp;&nbsp;</span><input class='login' type='button' value='log out' onclick='window.location=\"$basePath/digisig/logout.php\"' />";
}


echo '<form name ="navigate" action="'.$action.'" method="post" class="theheader">
        <p class="navigation">
            <input class="navigate" type="submit" name ="submit" value ="HOME"/>
            <input class="navigate" type="submit" name ="submit" value ="ABOUT"/>
            <input class="navigate" type="submit" name ="submit" value ="RTI GALLERY"/>
            <input class="navigate" type="submit" name ="submit" value ="ADVANCED SEARCH"/>
            <input class="navigate" type="submit" name ="submit" value ="CONTACT"/>
            '.$logBtn.'
        </p>
    </form>';
?>
