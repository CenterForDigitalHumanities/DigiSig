<html>
    <head>
        <script src="http://localhost:8080/DigiSig/include/lightbox/js/lightbox-plus-jquery.min.js"></script>
        <link rel="stylesheet" href="http://localhost:8080/DigiSig/css/digisigSkin.css">	
        
    </head>
    <body>
      
<?php
// ALPHA version: July 2015


#functions

//my functions
include "include/function.php";
//functions copied from other people
include "include/function_parsepath.php";

#connection details
require_once("config/config.php");

#constants and default values
include "include/constants.php";

session_start();
include "header.php";
//user login
if(isset($_POST['user_email']) && isset($_POST['password']))
{
    $email = $_POST['user_email'];
    $pwd = $_POST['password'];
    $login = "select * from user_digisig where user_email = '".$email."' and password='".$pwd."'";
    
    $queryresults = pg_query($login);
    $count = pg_num_rows($queryresults);
    if($count > 0){
        $row = pg_fetch_array($queryresults);
        $_SESSION['userID'] = $row['pk_user'];
        $_SESSION['user_email'] = $row['user_email'];
        $_SESSION['fk_access'] = $row['fk_access'];
        $_SESSION['fk_repository'] = $row['fk_repository'];
    }
    else{
        echo 'User email or password error, cannot log in. ';
    }
}

//if user doesn't log in, show the log in part. 
//if(!isset($_SESSION['userID']))
//{
//    echo "<input type='button' value='log in' onclick='window.location=\"include/login.php\"' />";
//    $_SESSION['userID'] = 1;
//    $_SESSION['fk_access'] = 1;
//    $_SESSION['fk_repository'] = 0;
//}else{
//    echo 'Hi, '.$_SESSION['user_email'];
//}
    /* If the page has received instructions via the 'post' method this 
    * code captures the 'post' and interprets it as a new header.*/

   if ($_POST['submit']) {

       $page = "/" . strtolower($_POST['submit']);

           IF ($_POST['field']) {
           $field = "/" . strtolower($_POST['field']);}

           If ($_POST['index']) {
           $index = "/" . strtolower($_POST['index']);}

           If ($_POST['term']) {
           $term = "/" . ($_POST['term']);}

           If ($_POST['exact']) {
           $exact = "/e";}

       $url = ($address . $page . $field . $index . $term . $exact);

       // reload the page with the new header
       header ( 'Location:' .$url);       
   }

   // reset the post array to clear any lingering data
   $_POST = array();

   /* If the page has NOT received instructions via 'post' 
   * check to see if header contains search instructions
   */

   $path_info = parse_path();

   if ($path_info[call_parts][0] == "search") {
       $field = ($path_info[call_parts][1]);
       $index = ($path_info[call_parts][2]);
       $term = ($path_info [call_parts][3]);
       $exact = ($path_info [call_parts][4]);
       $title = "RESULTS";   
   }

   if ($path_info[call_parts][0] == "entity") {
       $id= ($path_info[call_parts][1]);
       //find the last digit in the id number because it indicates the type of entity
       $entity = substr($id, -1);
       $title = $id;
   }

   //Dataset statistics

   $query = "SELECT COUNT (*) FROM seal";
               $queryresults = pg_query($query);
               $row = pg_fetch_assoc($queryresults);
               $sealcount = $row[count];


   /* this file loads the header which is consistent on on all pages
    * It has these parts:
    * 1) Banner / Title
    * 2) Navigation bar
    * 3) Introduction text
    * 4) Basic Search bar
    */

   include "include/page.php";

   // load the optional extra parts of the page depending on the header

   switch($path_info['call_parts'][0]) {

       case 'search':

       //test to see if the search string has more than 1 character
       if (strlen($term) > 0) {
           $term = str_replace("_", "/", $term);
       // if someone searches 'all fields' run the query for all possible searches
       // otherwise, just run the query on the specified field
       if ($field == "all_fields") {
           $query12 = "SELECT field_url FROM field";
           $query12result = pg_query($query12);
           while ($row = pg_fetch_array($query12result)) {
               $searchfield = $row[field_url];    
               queryResult($searchfield, $index, $term, $address, $exact);
               }
       } else {
          queryResult($field, $index, $term, $address, $exact);
       }
   }
              break;

       case 'entity':

   # show information about a specific entity

   // first test that we have an entity number and proceed if yes
   if ($id > 0) {

   # 1) determine what view to query using the entity number
   $query6 = "SELECT * FROM entity WHERE entity_code = $entity";
   $query6result = pg_query($query6);
   $row = pg_fetch_object($query6result);
   $column = $row->entity_column;
   $view = $row->entity_view;

   # 2) formulate and return the basic search string
   $query8 = "SELECT * FROM $view WHERE $column = $id";
   $query8result = pg_query($query8);

   //start rowcounter for table output
   $rowcount = 1;

   #the format for each version of the output depends on the nature of the data

   //for shelfmarks 
       If ($entity == 0) {
       $row = pg_fetch_array($query8result);
       $value1 = $row[repository_fulltitle];
       $value2 = $row[shelfmark];
       $value10 = $row[repository_startdate];
       $value11 = $row[repository_enddate];
       $value12 = $row[repository_location];
       $value13 = $row[repository_description];
       $value14 = $row[connection];
       $value15 = $row[ui_event_repository];

       echo "ITEM";
       echo "<br> DIGISIG ID:" . $id;
       echo "<br> Permalink: http://digisig.org/entity/".$id;

       echo "<br><br>" . $value1 . ": " . $value2;
       echo '<a href="' . $value14 . $value15 . '">external link</a>';
       echo "<br> dated:" . $value10 . " to " . $value11;
       echo "<br> Location:" . $value12;
       echo "<br> Description:" . $value13;

       $query12 = "SELECT * FROM shelfmark_view WHERE id_item = $id ORDER BY position_latin";
       $query12result = pg_query($query12);

       // table detailing which seal impressions are associated with this item
       echo '<table border = 1><tr><td></td><td>Examples</td></tr><tr><td></td><td>nature</td><td>number</td><td>position</td><td>shape</td></tr>';
       while ($row = pg_fetch_array($query12result)) {      
       $value3 = $row[nature];
       $value4 = $row[number];
       $value5 = $row[position_latin];
       $value6 = $row[shape];
       $value7 = $row[id_seal];
       $value8 = $row[representation_filename];
       $value9 = $row[name_first] . " " . $row[name_last];
       echo '<tr><td>' . $rowcount . '</td>';
       echo '<td>' . $value3 . '</td>';
       echo '<td>' . $value4 . '</td>';
       echo '<td>' . $value5 . '</td>';
       echo '<td>' . $value6 . '</td>';
       echo '<td><a href=' . $address . '/entity/' .$value7.'>view seal</a></td>'; 
       if(1 == $row['fk_access']){
           echo '<td><a href="'. $manifestation . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="'. $manifestation . $value8 . '" </img></a></td></tr>';
       }else if(isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])){
           echo '<td><a href="'. $manifestation . $value8 . '" data-lightbox="example-1" data-title="' . $value2 . '<br>photo: ' . $value9 . '"><img src="'. $manifestation . $value8 . '" </img></a></td></tr>';
       }else{
           echo '<td><a href=""><img src="" </img></a></td></tr>';
       }

       $rowcount++;
       }
       echo '</table>';
       }

   //for seal descriptions
   If ($entity == 3) {
       $row = pg_fetch_array($query8result);

       //assign variables
       $value1 = $row[index];
       $value2 = $row[catalogue_volume];
       $value3 = $row[catalogue_pagenumber];
       $value4 = $row[sealdescription_identifier];
       $value5 = $row[realizer];
       $value6 = $row[motif_obverse];        
       if (isset($row[motif_reverse])) {
           $value6 = "obverse: ". $value6 . "<br>reverse: " . $row[motif_reverse];
       }

       $value7 = $row[legend_obverse];
       if (isset($row[legend_reverse])) {
           $value6 = "obverse: ". $value6 . "<br>reverse: " . $row[legend_reverse];
       }

       $value8 = $row[shape];
       $value9 = $row[sesalsize_vertical];
       $value10 = $row[sealsize_horizontal];
       $value11 = $row[id_seal];
       $value12 = $row[representation_filename];

       //formulate header
       echo "SEAL DESCRIPTION";
       echo "<br> DIGISIG ID: " . $id;
       echo "<br> Permalink: http://digisig.org/entity/" . $id . "<br>";


       // title
           echo $value1 .":".$value4;
               if (isset($value2)) {
                   echo ", vol." . $value2;
               }
               if (isset($value3)) {
                   if (strpos($value3,'-') !== false) {
                       echo ", p." . $value3;
                       }
                   else {
                   echo ", pp." . $value3;}
               }

       //output entry -- only output variables with values

       if (isset($value5)) {    
       echo '<br><br> Name:'. $value5. '<br>';
       }

       if (isset($value6)) {
       echo '<br> Motif:'.$value6. '<br>';
       }

       if (isset($value7)) {
       echo '<br> Legend:'.$value7. '<br>';
       }

       if (isset($value8 )) {
       echo '<br> Shape:'.$value8. '<br>';
       }

       if (isset($value9)) {
       echo '<br> Size Vertical:'.$value9. '<br>';
       }

       if (isset($value10)) {
       echo '<br> Size Horizontal:'.$value10. '<br>';
       }

       //prepare the photograph -- if it is available
       if (isset($value12)) {
           if(1 == $row['fk_access']){
               echo '<a href="'. $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="'. $description . $value12 . '" height=200></img></a><br>';
           }else if(isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])){
               echo '<a href="'. $description . $value12 . '" data-lightbox="example-1" data-title=""><img src="'. $description . $value12 . '" height=200></img></a><br>';
           }else{
               echo '<a href="" ><img src="" height=200></img></a><br>';
           }
       }

       //link to seal page
       echo '<br><a href=' . $address . '/entity/' .$value11.'>view seal</a><br>';

       //check for other seal descriptions

       $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $value11";
       $query12result = pg_query($query12);

       $count = pg_num_rows($query12result);
       if ($count > 1) {
           echo "other descriptions";
           $duplicate = $id;
       sealdescription($query12result, $address, $duplicate);
       }
   }

   //for a seal
   If ($entity == 1) {

       echo "SEAL";
       echo "<br> DIGISIG ID:" . $id;
       echo "<br> Permalink: http://digisig.org/entity/".$id;

       echo '<table border = 1><tr><td>Shape</td><td>Height</td><td>Width</td></tr>';

       // note that a seal can have two faces but I am going to assume that the double side ones are the same
       $row = pg_fetch_array($query8result);
       $value3 = $row[shape];
       $value4 = $row[face_vertical];
       $value5 = $row[face_horizontal];

       echo '<td>' . $value3 . '</td>';
       echo '<td>' . $value4 . '</td>';
       echo '<td>' . $value5 . '</td></tr>';     
       $id_seal = $row[id];

       echo "</table><br>";

       // call seal description function to make list of associated seal descriptions 

       $query12 = "SELECT * FROM sealdescription_view WHERE id_seal = $id";
       $query12result = pg_query($query12);
       sealdescription($query12result, $address);


   // list of associated seal impressions 
   $query10 = "SELECT * FROM shelfmark_view WHERE id_seal = $id";
   $query10result = pg_query($query10);

   echo '<table border = 1><tr><td>Examples</td></tr><tr><td></td><td>';
   $rowcount = 1;

   while ($row = pg_fetch_array($query10result)) {

       $value1 = $row[nature];
       $value2 = $row[number];
       $value3 = $row[position_latin];
       $value4 = $row[shape];
       $value5 = $row[shelfmark];
       $value6 = $row[id_item];
       $value7 = $row[representation_filename];
       $value8 = $row[name_first] . " " . $row[name_last];
       $value9 = $row[repository_startdate];
       $value10 = $row[repository_enddate];
       echo '<tr><td>' . $rowcount . '</td>';
       echo '<td>' . $value1 . '</td>';
       echo '<td>' . $value2 . '</td>';
       echo '<td>' . $value3 . '</td>';
       echo '<td>' . $value4 . '</td>';
       echo '<td> dated:' . $value9 . ' to ' . $value10;
       echo '<td><a href=' . $address . '/entity/' .$value6.'>'. $value5 . '</a></td>';
       if(1 == $row['fk_access']){
           echo '<td><a href="'. $manifestation . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="'. $manifestation . $value7 . '" height=50></img></a></td>';
       }else if(isset($_SESSION['userID']) && ($_SESSION['fk_access'] == $row['fk_access'] || $_SESSION['fk_repository'] == $row['fk_repository'])){
           echo '<td><a href="'. $manifestation . $value7 . '" data-lightbox="example-1" data-title="' . $value5 . '<br>photo: ' . $value8 . '"><img src="'. $manifestation . $value7 . '" height=50></img></a></td>';
       }else{
           echo '<td><a href=""><img src="'.$_SESSION['fk_access'].'" height=50></img></a></td>';
       }
       

       echo '</tr>';   
       $rowcount++;
   }
       echo "</table><br>";
   }
   }
       break;

       case 'about': {
       echo "<br>Text about the project";}
       break;


       case 'advanced search': {
       echo "<br>Text about forthcoming search options";}
       break;


       case 'contact': {
       echo "<br>Where to contact us";}
       break;


       default:
               echo "<div class='searchResults'>";
               echo "<div class='resultsTitle'>Results</div>";
               echo "<span class='separator'>publications and projects</span><br>";
               
               $query = "SELECT DISTINCT title, uri_catalogue FROM search_view WHERE title NOT IN ('Public Index') ORDER BY title";
               $queryresults = pg_query($query);
               while ($row = pg_fetch_assoc($queryresults)){
                   echo '<a href="' . $row[uri_catalogue] . '" target="_blank">' . $row[title] . '</a>';
                  echo "<br>";
               }

               echo "<span class='separator'>repositories</span><br>";
               $query = "SELECT DISTINCT repository_fulltitle, id_archoncode FROM shelfmark_view ORDER BY repository_fulltitle";
               $queryresults = pg_query($query);
               while ($row = pg_fetch_assoc($queryresults)){
                   echo '<a href="'. $archonsearch . "?_ref=" . $row[id_archoncode] . '" target="_blank">' . $row[repository_fulltitle] . '</a>';
                   echo "<br>";
               }
               echo "</div>";
       }    



    include "include/footer.php";
    
?>


</body></html>



