<?php

//$logBtn2 = "";
//if(!isset($_SESSION['userID'])){
//    $logBtn = "<input class='login' type='button' value='log in' onclick='window.location=\"include/login.php\"' />";
//    $_SESSION['userID'] = 1;
//    $_SESSION['fk_access'] = 1;
//    $_SESSION['fk_repository'] = 0;
//}
//else{
//    $logBtn = "<input class='login' type='button' value='log out' onclick='' />";
//}
$action = $_SERVER['PHP_SELF'];

/*
 * This belongs on the about us page.
 <p>
          DigiSig is a new resource for the study of sigillography, particularly medieval 
          seals from the British Isles.  Based at the centre for Digital Humanities at St Louis University, Missouri, 
          it aims to foster sigillographic research by linking and matching sigillographic 
          datasets and making that information available.
        </p>
 */
echo '<div class="footer">
        <form name ="navigate" action="'.$action.'" method="post">
            <input class="footNav" type="submit" name ="submit" value ="HOME"/>
            <input class="footNav" type="submit" name ="submit" value ="ABOUT"/>
            <input class="footNav" type="submit" name ="submit" value ="ADVANCED SEARCH"/>
            <input class="footNav" type="submit" name ="submit" value ="CONTACT"/>
        </form>
      </div>';
?>

