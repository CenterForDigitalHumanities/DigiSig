<div class='header digisigHeader'>
    <img class='digisigImg' src='<?php echo $basePath; ?>digisig/images/digsig.jpg'/>
</div>
<?php
$logBtn = "";
$action = $_SERVER['PHP_SELF'];
<<<<<<< HEAD
if($_SESSION['userID'] === 1){
    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"$basePath/DigiSig/include/login.php\"' />";
    $_SESSION['userID'] = 1;
    $_SESSION['fk_access'] = 1;
    $_SESSION['fk_repository'] = 0;
    $_SESSION['user_email'] = "DigiSig";
}
else
{
    $logBtn = "<span class='login' style='margin-right: 50px;'>User: ".$_SESSION['user_email']."&nbsp;&nbsp;</span><input class='login' type='button' value='log out' onclick='window.location=\"$basePath/digisig/logout.php\"' />";
}

echo '<form name ="navigate" action="'.$action.'" method="post" class="header">
        <p class="navigation">
            <input class="navigate" type="submit" name ="submit" value ="HOME"/>
            <input class="navigate" type="submit" name ="submit" value ="ABOUT"/>
            <input class="navigate" type="submit" name ="submit" value ="ADVANCED SEARCH"/>
            <input class="navigate" type="submit" name ="submit" value ="CONTACT"/>
            '.$logBtn.'
        </p>
    </form>';
?>
