<!-- ####  Main Navigation, where login, search tabs will be  ##### -->
<nav>
    
     
    <ol>
        <?php
        print "<br>";
        print "</br>";
        // This sets the current page to not be a link. Repeat this if block for
        //  each menu item 
        if ($path_parts['filename'] == "index") {
            print '<li class="activePage">Home</li>';
        } else {
            print '<li><a href="index.php">Home</a></li>';
        }
        
        if ($path_parts['filename'] == "form") {
            print '<li class="activePage">Sign Up</li>';
        } else {
            print '<li><a href="form.php">Sign Up</a></li>';
        }
        
//        if ($path_parts['filename'] == "populate-table.php") {
//            print '<li class="activePage">Generate Plan</li>';
//        } else {
//            print '<li><a href="planGenerator.php">Generate Plan</a></li>';   
//        }
//        
//        if ($path_parts['filename'] == "populate-table.php") {
//            print '<li class="activePage">Form</li>';
//        } else {
//            print '<li><a href="form.php">Form</a></li>';   
//        }
        
        ?>
    </ol>
</nav>
<!-- #################### Ends Main Navigation    ########################## -->

<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

