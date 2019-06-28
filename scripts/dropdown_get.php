<script type="text/javascript">

    function office_names() {

        var select = document.form.office_name;
        select.options[0] = new Option("Valitse toimisto");
        select.options[0].value = '';

        <?php
        @$office_name = $_GET['officename'];
        $query = tc_query("SELECT * FROM offices");
        $cnt=1;
        if($query != FALSE){
            while ($row = mysqli_fetch_array($query)) {
            if (isset($abc)) {
            echo "select.options[$cnt] = new Option(\"".$row['officeName']."\");\n";
            echo "select.options[$cnt].value = \"".$row['officeName']."\";\n";
            } elseif ("".$row['officeName']."" == stripslashes($office_name)) {
            echo "select.options[$cnt] = new Option(\"".$row['officeName']."\",\"".$row['officeName']."\", true, true);\n";
            } else {
            echo "select.options[$cnt] = new Option(\"".$row['officeName']."\");\n";
            echo "select.options[$cnt].value = \"".$row['officeName']."\";\n";
            }
            $cnt++;
            }
        }
        ?>
    }

    function group_names() {
        var offices_select = document.form.office_name;
        var groups_select = document.form.group_name;
        groups_select.options[0] = new Option("Valitse ryhmä");
        groups_select.options[0].value = '';

        if (offices_select.options[offices_select.selectedIndex].value != '') {
            groups_select.length = 0;
        }

        <?php
        $query = tc_query("SELECT * FROM offices");
        if($query != FALSE){        
            while ($row = mysqli_fetch_array($query)) {
                $office_row = addslashes("".$row['officeName']."");
                ?>

                if (offices_select.options[offices_select.selectedIndex].text == "<?php echo $office_row; ?>") {
                    <?php

                    $query2 = tc_query("SELECT * FROM groups NATURAL JOIN offices WHERE officeName = '$office_row'");

                    echo "groups_select.options[0] = new Option(\"...\");\n";
                    echo "groups_select.options[0].value = '';\n";
                    $cnt = 1;

                    if($query2 != FALSE){
                        while ($row2 = mysqli_fetch_array($query2)) {
                            echo "groups_select.options[$cnt] = new Option(\"".$row2['groupName']."\");\n";
                            echo "groups_select.options[$cnt].value = \"".$row2['groupID']."\";\n";
                            $cnt++;
                        }
                    }
                    ?>
                }

            <?php
            }
        }
        ?>
        
        
        if (groups_select.options[groups_select.selectedIndex].value != '') {
            groups_select.length = 0;
        }
    }

</script>