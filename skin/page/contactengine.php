<?php

$EmailFrom = "badgerequest@fluxster1.x10.mx";
$EmailTo = "nicklovdogs@yahoo.com";
$Subject = "Fluxster Badge Request";
$username = Trim(stripslashes($_POST['username'])); 
$Email = Trim(stripslashes($_POST['email'])); 
$badgetype = Trim(stripslashes($_POST['badgetype']));
$creatortype = Trim(stripslashes($_POST['creatortype']));
$customtype = Trim(stripslashes($_POST['customtype']));  
$Message = Trim(stripslashes($_POST['Message'])); 

// validation
$validationOK=true;
if (!$validationOK) {
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
  exit;
}

// prepare email body text
$Body = "";
$Body .= "Username: ";
$Body .= $username;
$Body .= "\n";
$Body .= "\n";
$Body .= "Email: ";
$Body .= $Email;
$Body .= "\n";
$Body .= "Badge Type: ";
$Body .= $badgetype;
$Body .= "\n";
$Body .= "If Creator type explain: ";
$Body .= $creatortype;
$Body .= "\n";
$Body .= "If Custom type explain: ";
$Body .= $customtype;
$Body .= "\n";
$Body .= "Brief explanation on badge request: ";
$Body .= $Message;
$Body .= "\n";

// send email 
$success = mail($EmailTo, $Subject, $Body, "From: <$EmailFrom>");

// redirect to success page 
if ($success){
  print "<meta http-equiv=\"refresh\" content=\"0;URL=/skin/page/contactthanks.php\">";
}
else{
  print "<meta http-equiv=\"refresh\" content=\"0;URL=error.htm\">";
}
?>