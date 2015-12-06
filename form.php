<?php
include "top.php";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1 Initialize variables
//
// SECTION: 1a.
// variables for the classroom purposes to help find errors.

$debug = false;

if (isset($_GET["debug"])) { // ONLY do this in a classroom environment
    $debug = true;
}

if ($debug)
    print "<p>DEBUG MODE IS ON</p>";

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1b Security
//
// define security variable to be used in SECTION 2a.
$yourURL = $domain . $phpSelf;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1c form variables
//
// Initialize variables one for each form element
// in the order they appear on the form
$userID = "";
$fldPassword = "";
$fldName = "";
$fldEmail = "";
$fldState = "";
$fldCity = "";


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1d form error flags
//
// Initialize Error Flags one for each form element we validate
// in the order they appear in section 1c.
$firstNameERROR = false;
$emailERROR = false;

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
// SECTION: 1e misc variables
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();

// array used to hold form values that will be written to a CSV file
$dataRecord = array();

$mailed=false; // have we mailed the information to the user?
//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
// SECTION: 2 Process for when the form is submitted
//
if (isset($_POST["btnSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2a Security
    // 
    
    if (!formSecurityCheck(true)) {
        $msg = "<p>Sorry you cannot access this page. ";
        $msg.= "Security breach detected and reported</p>";
        die($msg);
    }
    
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2b Sanitize (clean) data 
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.

    $userID = htmlentities($_POST["txtUserID"], ENT_QUOTES, "UTF-8");
    $dataRecord[] = $userID;
    
    $fldPassword = filter_var($_POST["txtFldPassword"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $fldPassword;
    
    $fldName = filter_var($_POST["txtFldName"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $fldName;
    
    $fldEmail = filter_var($_POST["txtFldEmail"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $fldEmail;
    
    $fldState = filter_var($_POST["txtFldState"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $fldState;
    
    $fldCity = filter_var($_POST["txtFldCity"], FILTER_SANITIZE_EMAIL);
    $dataRecord[] = $fldCity;
    

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2c Validation
    //
    // Validation section. Check each value for possible errors, empty or
    // not what we expect. You will need an IF block for each element you will
    // check (see above section 1c and 1d). The if blocks should also be in the
    // order that the elements appear on your form so that the error messages
    // will be in the order they appear. errorMsg will be displayed on the form
    // see section 3b. The error flag ($emailERROR) will be used in section 3c.

    
    if ($userID == "") {
        $errorMsg[] = "Please enter a User ID";
        $firstNameERROR = true;
    } elseif (!verifyAlphaNum($userID)) {
        $errorMsg[] = "Your User ID appears to contain a non-alphanumerical character.";
        $firstNameERROR = true;
    }

    if ($fldEmail == "") {
        $errorMsg[] = "Please enter your email";
        $emailERROR = true;
    } elseif (!verifyEmail($fldEmail)) {
        $errorMsg[] = "Incorrect email address";
        $emailERROR = true;
    }

   
    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    // SECTION: 2d Process Form - Passed Validation
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //
    if (!$errorMsg) {
        if ($debug)
            print "<p>Form is valid</p>";


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2f Create message
        //
        // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).

        $message = '';

        foreach ($_POST as $key => $value) {

            $message .= "<p>";

            $camelCase = preg_split('/(?=[A-Z])/', substr($key, 3));

            foreach ($camelCase as $one) {
                $message .= $one . " ";
            }
            $message .= " = " . htmlentities($value, ENT_QUOTES, "UTF-8") . "</p>";
        }


        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        // SECTION: 2g Mail to user
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $fldEmail; // the person who filled out the form
        $cc = "";
        $bcc = "";
        $from = "keohara@uvm.edu";

        // subject of mail should make sense to your form
        $todaysDate = strftime("%x");
        $subject = "Research Study: " . $todaysDate;

        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
        
    } // end form is valid
    
} // ends if form was submitted.

//#############################################################################
//
// SECTION 3 Display Form
//
?>

<article id="main">

    <?php
    //print $firstName;
    //####################################
    //
    // SECTION 3a.
    // 
    // If its the first time coming to the form or there are errors we are going
    // to display the form.
    if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) { // closing of if marked with: end body submit
        
//        print "<h1>Values garnered from user input... ";
//        print $userID;
//        print "<br>";
//        print $fldName;
//        print "<br>";
//        print $fldEmail;
//        print "<br>";
//        print $fldState;
//        print "<br>";
//        print $fldCity;
//        print "<br>";
//        $default = "default";
        $userIDInsert =  'INSERT INTO tblUser (pmkUserID, fldPassword, fldName, fldEmail, fldState, fldCity) ';
        $userIDInsert .= 'VALUES (\'' . $userID . '\', \'' . $fldPassword . '\', \'' . $fldName . '\', \'' . $fldEmail . '\', \'' . $fldState . '\', ';
        $userIDInsert .= '' . '\'' . $fldCity . '\')';
        
        
        $dbArray = $thisDatabaseWriter->insert($userIDInsert, "", 0, 0, 12, 0, false, false);
        
        if (!$mailed) {
            print "not ";
        }

        print "<br><h3>Welcome to SocialSport!</h3><br>";
        print "<p>A message containing your account information has ";
        if (!$mailed) {
            print "not ";
        }
        print "been sent</p>";
        print "<p>To: " . $fldEmail . "</p>";
        print "<p>ACCOUNT INFORMATION</p>";

        print $message;
        ?>
        <div class="container text-center">
            <a href="#" class="btn btn-info" role="button">Log In</a>
        </div>
        <?php
        } 
    else {
        


        //####################################
        //
        // SECTION 3b Error Messages
        //
        // display any error messages before we print out the form

        if ($errorMsg) {
            print '<div id="errors">';
            print "<ol>\n";
            foreach ($errorMsg as $err) {
                print "<li>" . $err . "</li>\n";
            }
            print "</ol>\n";
            print '</div>';
        }


        //####################################
        //
        // SECTION 3c html Form
        //
        /* Display the HTML form. note that the action is to this same page. $phpSelf
          is defined in top.php
          NOTE the line:

          value="<?php print $email; ?>

          this makes the form sticky by displaying either the initial default value (line 35)
          or the value they typed in (line 84)

          NOTE this line:

          <?php if($emailERROR) print 'class="mistake"'; ?>

          this prints out a css class so that we can highlight the background etc. to
          make it stand out that a mistake happened here.

         */
        ?>

        <form action="<?php print $phpSelf; ?>"
              method="post"
              id="frmRegister">

            <fieldset class="wrapper">
                <p>Welcome to SocialSport, fill out the form to create an account.</p>

                <fieldset class="wrapperTwo">
                    <legend>Sign-up form</legend>

                    <fieldset class="contact">
                        
                        <label for="txtUserID" class="required">User ID
                            <input type="text" id="txtUserID" name="txtUserID"
                                   value="<?php print $userID; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter your userID"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtPassword" class="required">Password
                            <input type="text" id="txtFldPassword" name="txtFldPassword"
                                   value="<?php print $fldPassword; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter a password"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtFldName" class="required">Name
                            <input type="text" id="txtFirstName" name="txtFldName"
                                   value="<?php print $firstName; ?>"
                                   tabindex="100" maxlength="45" placeholder="Enter your name"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()"
                                   autofocus>
                        </label>
                        
                        <label for="txtFldEmail" class="required">Email
                            <input type="text" id="txtEmail" name="txtFldEmail"
                                   value="<?php print $fldEmail; ?>"
                                   tabindex="120" maxlength="45" placeholder="Enter your email address"
                                   <?php if ($emailERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   >
                        </label>
                        
                        <label for="txtFldState" class="required">State
                            <select name="txtFldState">
                                    <option value="CA">CA</option>
                                    <option value="MA">MA</option>
                                    <option value="NH">NH</option>
                                    <option value="NY">NY</option>
                                    <option value="OR">OR</option>
                                    <option value="PA">PA</option>
                                    <option value="VT">VT</option>
                            </select>
                        </label>
                        
                        <label for="txtFldCity" class="required">Email
                            <input type="text" id="txtEmail" name="txtFldCity"
                                   value="<?php print $fldCity; ?>"
                                   tabindex="120" maxlength="45" placeholder="city"
                                   <?php if ($firstNameERROR) print 'class="mistake"'; ?>
                                   onfocus="this.select()" 
                                   >
                        </label>
                        
                    </fieldset> <!-- ends contact -->
                    
                </fieldset> <!-- ends wrapper Two -->
                
                <fieldset class="buttons">
                    <legend></legend>
                    <input type="submit" id="btnSubmit" name="btnSubmit" value="Register" tabindex="900" class="button">
                </fieldset> <!-- ends buttons -->
                
            </fieldset> <!-- Ends Wrapper -->
        </form>

    <?php
    } // end body submit
    ?>

</article>

<?php include "footer.php"; ?>

</body>
</html>
