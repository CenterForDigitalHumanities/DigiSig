<div class='pageWrap'>
    <form class="searchArea" name = "selectclass" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post" onsubmit="submitFormClass()">

        <select name="field"/>
        <?php
        $query = "SELECT printphrase_class, id_class, classnotation, cases, class_sortorder FROM classification_view ORDER BY class_sortorder";
        $searchfields = mysqli_query($link, $query);
        echo "<option value='holder' disabled selected = 'true'>Select Class</option>";
        while ($row = mysqli_fetch_array($searchfields)) {
            echo "<option value=" . $row['id_class'] . ">" . $row['classnotation'] . $row['printphrase_class'] . $row['cases'] . "</option>";
        }
        ?>
        </select>

        <div class="searchPiece">
            <div class="searchBtn_1" onclick="$(this)
                        .next()
                        .click();">GO</div>
            <input class="searchBtn_2" type="submit" name ="submit" value ="Advanced Search"/>
        </div>
    </form>


    <?php
    if (isset($motifClass)) {

//establish which part of the representation_view table to query
        $query1 = "SELECT pk_class, level, printphrase_class FROM classification_view WHERE id_class = '" . $motifClass . "'";
        $query1result = mysqli_query($link, $query1);
        $row = mysqli_fetch_assoc($query1result);
        $classnumber = $row['pk_class'];
        $classlevel = "level" . $row['level'];
        $classtitle = $row['printphrase_class'];

//create an array of the photographs and their metadata					
        $query2 = "SELECT * FROM representation_class_view WHERE " . $classlevel . " = " . $classnumber;
        $query2result = mysqli_query($link, $query2);
        $rowcount = mysqli_num_rows($query2result);

//Title for group of photographs					
        Echo "<div>" . $classtitle . "<br> Number of results:" . $rowcount . "<br></div>";


//if there are results then check to see if then assemble a list of the filename and establish if they are 'local'
        if ($rowcount > 0) {
            while ($row = mysqli_fetch_array($query2result)) {

                $val1_filename = $row['representation_thumbnail'];
                $val2_thumb = $row['thumb'];

                if ($val2_thumb == "local" || $val2_thumb == null) {
                    $val3_connection = $small;
                } else
                    ($val3_connection = $val2_thumb);

                $val4_id_seal = $row['id_seal'];
                $val5_id_item = $row['id_item'];
                $val6_label = $row['repository_fulltitle'] . " " . $row['shelfmark'];

                $seal_connection = $address . "/entity/" . $val4_id_seal;

                echo "<a target='_blank' href='" . $seal_connection . "' title= '" . $val6_label . "'>";
                echo "<img src='" . $val3_connection . $val1_filename . "' alt= '" . $val6_label . "'  height ='100'></a><br>";
            }
        }
    }
    ?>
    <script>
        function submitFormClass () {
            document.getElementById('search_term').value = document.getElementById('search_term_').value;
            var searchTerm = document.getElementById('search_term').value;
            document.getElementById('search_term').value = searchTerm.replace('/', '_');
        }
    </script>

</div>