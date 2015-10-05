<?php
   session_start();
   $msg = "";
   if (sessionExpired()) {
      $msg = "Sorry, session has expired. You have been logged out.";
      session_start();  // restart a new one
      $action = $_REQUEST['action']= "SHOWLOGIN";
   }
   global $dbCon;
   $dbCon = connectToDB("labs","labspwd","localhost","labsdb");
   if (isset($_REQUEST['nextFindOption'])) {
      nextFindOption($_REQUEST['nextFindOption'], $_REQUEST['value']);
      exit;
   } 

   function lastIndexOf($string,$item){  
      $index=strpos(strrev($string),strrev($item));  
      if ($index){  
         $index=strlen($string)-strlen($item)-$index;  
         return $index;  
      }  
      else return -1;  
   }  
   function sessionExpired() {
      if (!isset($_SESSION['username'])) return false;  // check expiry only if logged in
      if (!isset($_SESSION['lastTime'])) {
         $_SESSION['lastTime'] = time();
         return false;
      }

      $now = time();
      $last = $_SESSION['lastTime'];
      $diff = $now - $last;
      if ($diff > 1800) { // expire in 30 minutes
         unset($_SESSION['username']);
         session_unset();
         session_destroy();
         return true;
      }
      else $_SESSION['lastTime'] = time();
      return false;
   }

   if (isset($_REQUEST['action'])) {
      $action = strtoupper($_REQUEST['action']);
      if ($action=="OPEN") {
         open(); return;
         exit;
      }
      else if (($action=="ADDQUESTION") && isset($_REQUEST['prompt'])) {
         addQuestion(); exit;
      }
   }

?>


<html>
<head>
<link rel="Stylesheet" href="main.css" type="text/css">
<script src='main.js'></script>
</head><body style="margin-left: 0px; margin-top: 0px; margin-right: 0px;" topmargin="0" leftmargin="0" marginwidth="0" marginheight="0" bgcolor="#FFFFFF" 
 text="#000000" link="#006633" alink="#006633" vlink="#006633">
   <table class='table1' width="100%" cellspacing="0" style="table: fixed-width;">
     <tr class='tr1'> 
       <td class='td1' bgcolor="#006633" align="left" valign="bottom" height="47" width="611"> 
         <a href="http://www.emich.edu"><img src="http://www.emich.edu/compsci/images/tmpltimages/emu_head_sm.gif" alt="Eastern Michigan University" width="375" height="18" border="0"></a> 
       </td>

       <td class='td1' bgcolor="#006633" align="right"> 
          <form action="http://google.com/cse" style="padding-bottom: 1px; margin: 0px;" name="searchform">
    	   <input type="hidden" name="cx" value="015915423083034838823:uko0su6ebr4">
           <input type="hidden" name="cof" value="FORID:0">
           <input name="q" id="q" type="text" size="18" maxlength="50" value="" style="font-size: 11px; font-family: Verdana, Geneva, sans-serif;" onKeyPress="pressenter(event);"> 
           <input type='submit' value='Search CS Dept'>
        </form>					  
      </td>
    </tr>
  <tr class='tr1' height="1px"><td class='td1' colspan="2" style="padding:0px"></td></tr>
  <tr class='tr1' height="3px"><td class='td1' colspan="2" style="padding:0px;background-color:#000000"></td></tr>
  <tr class='tr1' height="1px"><td class='td1' colspan="2" style="padding:0px"></td></tr>
<tr class='tr1'>
  <td class='td1' colspan="2" style="padding:0px" background="http://www.emich.edu/compsci/images/tmpltimages/photo_negative.JPG"><a href="http://www.emich.edu/compsci/index.html"><img src="http://www.emich.edu/compsci/images/tmpltimages/head.JPG" border="0"></a></td>
  </tr>
</table>
<center>
<br />
<table class='table1' style="width:900px">
   <tr class='tr1'><th class='th1' width="100%" bgcolor="#006633" style="color:#FFFFFF;text-align:center;font-size:20px">24/7 TUTORIALS & LABS</th></tr>
  <tr class='tr1'>
     <td class='td1' width="100%" style="height:30px;background-color:#000000;color:#FFFFFF;font-weight:bold;text-align:center">
<?php
   if ($dbCon == null) {
      redFlag("<h4>Unable to perform initial setup. Try again later or contact <a href='mailto:aikeji@emich.edu'>aikeji@emich.edu</a></h4>");
      exit;
   }
   if (isset($_REQUEST['action'])) $action = strtoupper($_REQUEST['action']);
   else $action = "";
   if ($action=="PROCESSLOGIN") {
      $msg=processLogin();
      if (startsWith($msg, "ERROR ")) $action = "SHOWLOGIN"; // if error, retry login
   }
   else if ($action=="LOGOUT") {
      unset($_SESSION['username']);
      session_destroy();
      $msg = "Logout completed!";
   }
   echo "<a style='color:white' href='?action=home'>Home</a>&nbsp;&nbsp;|&nbsp;&nbsp<a style='color:white' href='?action=howtouse'>How to use this site</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
   if (!isset($_SESSION['username'])) {
      echo "<a style='color:white' href='?action=showLogin'>Login</a>&nbsp;&nbsp;|&nbsp;&nbsp;<a style='color:white' href='?action=signup'>Signup</a>";
   }
   else {
/*      echo "<select name='action' onChange=\"location.href='?action='+options[selectedIndex].value;\">
              <option value='More Options'>More Options</option>
              <option value='Add Adoption Information'>Add Adoption Information</option>
              <option value='Change Password'>Change Password</option>
           </select>"&nbsp;&nbsp;|&nbsp;&nbsp;";
*/
      echo "<a style='color:white' href='?action=logout'>Logout</a>";
      echo "&nbsp;&nbsp;|&nbsp;&nbsp;";
      echo "<a style='color:white;font-size:10px;' href='?action=ChangePassword'>Change Password</a>";
   }
   echo "</td>
      </tr>
      </table>";
   echo "<h4 style='color:red'>$msg</h4>";
   switch ($action) {
      case "SHOWLOGIN": showLogin(); break;
      case "SIGNUP": signup(); break;
      case "HOWTOUSE": howToUse(); break;
      case "CHANGEPASSWORD": changePassword(); break;
      case "FORGOT PASSWORD": forgotPassword(); break;
//      case "FIND": findAdoptionInformation(); break;
      case "OPEN": open(); return; //show(null, false); open(); break;
      case "SHOWALL": show(null, false); showAll(); break;
      
      default:
         if ((!isset($_SESSION['username'])) || ($_SESSION['username']=="")) {      
            redFlag("Sorry, session has expired. You have been logged out.");
            session_start();  // restart a new one
            $action = $_REQUEST['action']= "SHOWLOGIN";
            showLogin(); break;
         }
         switch ($action) {
            case "UPDATE":
            //case "DELETE": updateOrDelete($action); break;
            case "ADDSUBCATEGORY": addSubcategory(); break;
            case "DELETE":
            case "DELETEDESCENDANTS": delete($action); break;
            case "UPDATEPROPERTIES": updateProperties(); showProperties(); break;
            case "UPDATEGROUP": show(null, false); updateGroup(); break;
            case "ATTACHDOCUMENT": attachDocument(); break;
            case "ATTACHNOTE": attachNote(); break;
            case "ADDQUESTION": addQuestion(); break;
            case "REVIEWQUESTIONS": show(null, false); reviewQuestions(); break;
            case "GETPROPERTIES": show(null, false); showProperties(); break;
            case "QANDA": show(null, false); qAndA(); break;
            case "CHANGEDESCRIPTION":
            case "CHANGEOWNER": 
               updateDescriptionOrOwner();
//               show(null, false);
//               showProperties();
               break;
//            case "CHANGEPRIVILEGES": changePrivileges(); addGroups(); break;
            case "SEARCH": search(); break;
            default:
               show(null, true);
         }
   }
  
   echo "<p style='margin-top:100px;text-align:center;font-size:12px'>By <a href='mailto:aikeji@emich.edu'>aikeji@emich.edu</a></p>";

function getFileMimeType($file) {
    if (function_exists('finfo_file')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $type = finfo_file($finfo, $file);
        finfo_close($finfo);
    } else {
        require_once 'upgradephp/ext/mime.php';
        $type = mime_content_type($file);
    }
echo "a type=$type";
    if (!$type || in_array($type, array('application/octet-stream', 'text/plain'))) {
        $secondOpinion = exec('file -b --mime-type ' . escapeshellarg($file), $foo, $returnCode);
        if ($returnCode === 0 && $secondOpinion) {
            $type = $secondOpinion;
        }
    }
echo "b";
    if (!$type || in_array($type, array('application/octet-stream', 'text/plain'))) {
        require_once 'upgradephp/ext/mime.php';
        $exifImageType = exif_imagetype($file);
        if ($exifImageType !== false) {
            $type = image_type_to_mime_type($exifImageType);
        }
    }
echo "c";
    return $type;
}
   function open () {
      if (!isset($_REQUEST['pos'])) return;
      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select info from attachments where id=$id");
      if (!is_array($r)) return;
      if (isset($_REQUEST['b'])) { // must be a URL to an outside file
         $filename = $r[0][0];
      }
      else { // local file. Get from upload folder
         $ext = $r[0]['info'];
         $filename = "uploads/$pos"."_.$ext";
//         echo 33333;
//         exec ("cat $filename");
//         return;
//echo "Filename=$filename";         
//         echo 4;
      }

//      $type = getFileMimeType($filename);
      $mimeTypes = array (
         "xls"=>"application/vnd.ms-excel",
         "xlsx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
         "xltx"=>"application/vnd.openxmlformats-officedocument.spreadsheetml.template",
         "potx"=>"application/vnd.openxmlformats-officedocument.presentationml.template",
         "ppsx"=>"application/vnd.openxmlformats-officedocument.presentationml.slideshow",
         "pptx"=>"application/vnd.openxmlformats-officedocument.presentationml.presentation",
         "sldx"=>"application/vnd.openxmlformats-officedocument.presentationml.slide",
         "docx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.document",
         "dotx"=>"application/vnd.openxmlformats-officedocument.wordprocessingml.template",
         "xlam"=>"application/vnd.ms-excel.addin.macroEnabled.12",
         "xlsb"=>"application/vnd.ms-excel.sheet.binary.macroEnabled.12",
         "htm"=>"text/html"
      );
      $ext = strtolower($ext);
      if (isset($mimeTypes[$ext])) $type = $mimeTypes[$ext];
      else {
         $finfo = finfo_open(FILEINFO_MIME_TYPE);
         $type = finfo_file($finfo, dirname(__FILE__)."/".$filename);
      }
//echo "TYP=$type";
//exit;
      header("Content-type:$type");
//return;
//      $ext = strtolower(trim($r[0]['info']));
//      if ($ext=="txt") header('Content-type:text/txt');
//      else header('Content-type:application/$ext');
      readfile($filename);
//      $fp = fopen($filename, "r");
//      if (!$fp) return;
//echo 4444;      
//      $output = fopen('php://output', 'w');
//      while(!feof($fp)) {
//echo 44445555;      
//         fwrite($output, "$filename");
//         echo fread($fp, 4096);
//         echo $s;
//echo "S=$s len=".strlen($s);
//fwrite(output, fread($fp, 4096));
//      }
//      fclose($fp);
//      fclose($output);
   }



   function search() {
echo "Coming soon";
return;
      // Tokenize search keywords and formulate the search condition
      $keywords = $_REQUEST['keywords'];
      if (trim($keywords)=="") {
         redFlag("Keywords for search not specified!");
         return;
      }
      
      // If ref number is specified as search keyword, get the corresponding document
      $r = process("select id from categories where id=".mysqlReady($keywords));
      if (is_array($r)) {
            show($r[0][0]);
            return;
      }    

      $pos = $_REQUEST['pos'];
      $r = process("select id from categories where id=".mysqlReady($pos)); // validate pos
      if (!is_array($r)) {
         echo "Illegal op - contact web master";
         return;
      }   
      $pos = $r[0]['id'];
      $cond1 = "description like ".mysqlReady("%$keywords%");
      $tokens = preg_split("/[\s,;\n\t]+/", $keywords);
      $cond2="";
      //echo "CT=".count($tokens);
      for ($i=0; $i < count($tokens); $i++) {
         $token = $tokens[$i];
         if ($cond2!="") $cond2 .= " and ";
         $cond2 .= "(description like ".mysqlReady("%$token%").")";
echo "CND=$cond2 tok=$token";             
      }
      echo "C2=$cond2";
      // find the nodes to be searched i.e. pos and descendants of pos
      $r = process("select description, id from categories where (id REGEXP '".str_replace(".","\\\\.",$pos)."($|\\\\.)')");
      echo "IDS=";print_r($r);

      // find matches based on title or descriptions
      echo "select description, id from categories where (id REGEXP '".str_replace(".","\\\\.",$pos)."($|\\\\.)')
         and ($cond1 ".($cond2==""?"":" or ($cond2)").")";
      $match1 = process("select description, id from categories where (id REGEXP '".str_replace(".","\\\\.",$pos)."($|\\\\.)')
         and ($cond1 ".($cond2==""?"":" or ($cond2)").")");
      echo "MATCH=";print_r($match1);
      //for i in *; do printf "$i: ";grep -oi "o" $i | wc -l; done
/*for i in *; do 
    printf "$i: ";
    cat $i | tr '\r\n' ' '   | grep -oi  "done\|world" | wc -l
done



      $kwds = $_REQUEST['keywords'];
      $files = "$pos*_";
      // after upload if search allowed and no search file then
      // if filetype is text create alias text upload with "_" for the search file 
      
      // formulate regular expression for the search
      // grep -c -i -H "create socket a *.php

pdftoascii pdf a.txt
if a.txt is empty, try
   pdftohtml and place files in x.dir
   for each jpeg file in x.dir convert to text via a.sh and append to all.txt
   if all.txt is still empty, warn user that conversion may not be accurate so they may add a search kwd file

pdftojpeg & pipe to 
coneiform -o out.txt in.jpeg

      
*/
      // search files
   }
   
   
   
   function updateGroup() {
//print_r($_REQUEST);
      // validate - level 3 at this node needed to update or add group
      if (getPrivilege($_REQUEST['pos']) < 3) {
         redFlag("Illegal op - contact web master");
         return false;
      }

      // Update or show menu?
      if (isset($_REQUEST['localAction'])) { // Update
         $action = strtoupper($_REQUEST['localAction']);
         switch ($action) {
            case "ADDNEWGROUP":
               if (isset($_REQUEST['members']) && addOrUpdateGroup($_REQUEST['pos'], 0, $_REQUEST['gpName'],
                   $_REQUEST['privilege'], $_REQUEST['members'])) {
                   exit;
//                  showProperties();
//                  return true;
               }
               // Ask for information
               $r = process("select description from categories where id=".mysqlReady($_REQUEST['pos']));
               $desc = $r[0][0];
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='localAction' value='ADDNEWGROUP'>
                  <table style='width:600px;spacing:10px;padding:10px' class='t1'><tr class='tr1'><th colspan=2 class='th1'>ADD NEW GROUP<br /><small>Please enter group information and submit</small></th>
                  <tr class='tr1'><td class='td1' style='text-align:right'>Group's Name:</td>
                     <td class='td1' style='text-align:left;padding:10px'><input type='text' name='gpName' value=\"".$_REQUEST['gpName']."\" size=20></td></tr>
                  <tr class='tr1'><td class='td1' style='text-align:right'>Select Group's Privilege in $desc:</td>
                     <td class='td1' style='text-align:left;padding:10px'><select name='privilege'>
                         <option ".($_REQUEST['privilege']==0?" selected":"").">0
                         <option ".($_REQUEST['privilege']==1?" selected":"").">1
                         <option ".($_REQUEST['privilege']==2?" selected":"").">2
                         <option ".($_REQUEST['privilege']==3?" selected":"").">3
                     </td></tr>
                  <tr class='tr1'><td class='td1' style='text-align:right'>Group Members myEmich Email<br />addresses (comma separated):</td>
                     <td class='td1' style='text-align:left;padding:10px'><textarea name='members' rows=5 cols=35>".$_REQUEST['members']."</textArea></td></tr>
                  </table>
                  <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";
               return true;

            case "CHANGEGROUPPRIVILEGE":
               if (isset($_REQUEST['newPrivilege'])) {
                  $priv = $_REQUEST['newPrivilege'];
                  if ((!is_numeric($priv)) || ($priv < 0) || ($priv > 3)) return;
//                  echo("replace into itemPrivileges set itemId=".mysqlReady($_REQUEST['pos']).", groupId=".mysqlReady($_REQUEST['gpId']).", privilege=$priv");
                  process("replace into itemPrivileges set itemId=".mysqlReady($_REQUEST['pos']).", groupId=".mysqlReady($_REQUEST['gpId']).", privilege=$priv");
                  break;
               }
               $r = process("select privilege from itemPrivileges where itemId=".mysqlReady($_REQUEST['pos'])." and groupId=".mysqlReady($_REQUEST['gpId']));
               $priv = $r[0]['privilege'];
               $gpName = $_REQUEST['gpName'];
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
                  <input type='hidden' name='gpName' value='$gpName'>
                  <input type='hidden' name='localAction' value='CHANGEGROUPPRIVILEGE'>
                  <table style='width:400px' class='t1'><tr class='tr1'><th class='th1'>CHANGE GROUP PRIVILEGE FOR $gpName</th></tr>
                  <tr class='tr1'><td class='td1' style='text-align:center;padding:20px'>
                  <b>Select the new privilege and submit</b><br />
                  <select name='newPrivilege'>";
               for ($k=3; $k >= 0; $k--) {
                  if ($k==$priv) echo "<option selected>$k";
                  else echo "<option>$k";
               }
               echo "</select></td></tr>
                  </table>
                  <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";
               return true;


            case "CHANGEGROUPNAME":
               if (isset($_REQUEST['newGpName']) && addOrUpdateGroup($_REQUEST['pos'], $_REQUEST['gpId'], $_REQUEST['newGpName'], 0, "")) {
                  redFlag("Group name ".$_REQUEST['gpName']." has changed to ".$_REQUEST['newGpName']);
                  $_REQUEST['gpName'] = $_REQUEST['newGpName'];
                  break;
//                  showProperties();
//                  return true;
               }

               // Ask for information
               $r = process("select name from groups where id=".mysqlReady($_REQUEST['gpId'])." and categoryId=".mysqlReady($_REQUEST['pos']));
               $gpName = $r[0][0];
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
                  <input type='hidden' name='gpName' value='$gpName'>
                  <input type='hidden' name='localAction' value='CHANGEGROUPNAME'>
                  <table style='width:400px' class='t1'><tr class='tr1'><th class='th1'>CHANGE GROUP NAME FOR $gpName</th></tr>
                  <tr class='tr1'><td class='td1' style='text-align:center;padding:20px'>
                  <b>Please enter the new name and submit</b><br />
                  New Group Name:
                     <input type='text' name='newGpName' value=\"".$_REQUEST['newGpName']."\" size=20></td></tr>
                  </table>
                  <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";
               return true;

            case "VIEWGROUPMEMBERS":
               $r = process("select name from groups where id=".mysqlReady($_REQUEST['gpId'])." and categoryId=".mysqlReady($_REQUEST['pos']));
               $gpName = $r[0][0];
               $r = process("select username from groupMembers where groupId=".mysqlReady($_REQUEST['gpId'])." order by username");
               $members="";
               for ($i=0; $i < count($r); $i++) $members .= ($i+1).". ".$r[$i]['username']."<br />";
               echo "<table style='width:400px' class='t1'><tr class='tr1'><th colspan=2 class='th1'>GROUP MEMBERS FOR $gpName</th>
                  <tr class='tr1'><td class='td1' style='text-align:left;width:250px;height:150px'><div style='width:100%;height:100%;overflow:auto'><p style='margin-left:20px'>$members</p></div><br /></td></tr>
                  </table>";
               break;

            case "ADDNEWGROUPMEMBERS":
               if (isset($_REQUEST['members']) && addOrUpdateGroup($_REQUEST['pos'], $_REQUEST['gpId'], $_REQUEST['gpName'], 0, $_REQUEST['members'])) {
                  redFlag("New Member(s) Added to ".$_REQUEST['gpName']."!");
                  break;
               }
               // Ask for information
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='gpName' value='".$_REQUEST['gpName']."'>
                  <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
                  <input type='hidden' name='localAction' value='ADDNEWGROUPMEMBERS'>
                  <table style='width:400px' class='t1'><tr class='tr1'><th colspan=2 class='th1'>ADD NEW GROUP MEMBERS to ".$_REQUEST['gpName']."</th></tr>
                  <tr class='tr1'><td class='td1' style='text-align:center'>Enter new group members myEmich email addresses - separate with comma, semi-colon or white spaces<br />
                     <textarea name='members' rows=3 cols=35>".$_REQUEST['members']."</textArea><br /><br /></td></tr>
                  </table>
                  <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";
               return true;

            case "DELETEGROUP":
               if (isset($_REQUEST['confirm'])) {
                  process("delete from groupMembers where groupId=".mysqlReady($_REQUEST['gpId']));
                  process("delete from itemPrivileges where groupId=".mysqlReady($_REQUEST['gpId']));
                  process("delete from groups where id=".mysqlReady($_REQUEST['gpId']));
                  redFlag("Deleted ".$_REQUEST['gpName']." and the association with its members!<br /><br />");
                  showProperties();
                  return;
               }
               // Ask for information
               $r = process("select description from categories where id=".mysqlReady($_REQUEST['pos']));
               $desc = $r[0][0];
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='gpName' value='".$_REQUEST['gpName']."'>
                  <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
                  <input type='hidden' name='localAction' value='DELETEGROUP'>
                  <table style='width:400px' class='t1'><tr class='tr1'><th colspan=2 class='th1'>DELETE GROUP ".$_REQUEST['gpName']."</th></tr>
                  <tr class='tr1'><td class='td1' style='padding:20px'>If deleted, <ul>
                  <li>Association between this group and its members will be deleted too.
                  <li>The member accounts will not deleted as they may be used in other groups.
                  </ul>Are you sure you want to delete the group ".$_REQUEST['gpName']." under $desc?
                  </td></tr></table>
                  <br /><input name='confirm' type='submit' value='YES'>&nbsp;&nbsp;&nbsp;<a href=\"?action=updateGroup&pos=".$_REQUEST['pos']."&gpName=".$_REQUEST['gpName']."&gpId=".$_REQUEST['gpId']."\">quit</a></form>";
               return true;

            case "DELETESOMEGROUPMEMBERS":
               if ((isset($_REQUEST['members'])) && (trim($_REQUEST['members']) != "")) {
                  $members="";
                  $list = preg_split("/[\s,;\n\t]+/", $_REQUEST['members']);
                  for ($i=0; $i<count($list); $i++) {
                     $email = $list[$i];
                     if ($members=="") $members .= "(\"$email\"";
                     else $members.= ",\"$email\"";
                  }
                  $members .= ")";
                  $r=process("select count(*) from groupMembers where groupId=".mysqlReady($_REQUEST['gpId'])." and username in $members");
                  if (is_array($r) && ($r[0][0]>0)) {
                     process("delete from groupMembers where groupId=".mysqlReady($_REQUEST['gpId'])." and username in $members");
                     redFlag($r[0][0]." matching email address(es) deleted from ".$_REQUEST['gpName']." group!");
                  }
                  else {
                     redFlag("No match - $members not found in ".$_REQUEST['gpName']." group!");
                  }
                  break;
//                  showProperties();
//                  return;
               }
               // Ask for information
               echo "<form action=''>
                  <input type='hidden' name='action' value='updateGroup'>
                  <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
                  <input type='hidden' name='gpName' value='".$_REQUEST['gpName']."'>
                  <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
                  <input type='hidden' name='localAction' value='DELETESOMEGROUPMEMBERS'>
                  <table style='width:400px' class='t1'><tr class='tr1'><th colspan=2 class='th1'>DELETE SOME GROUP MEMBERS of ".$_REQUEST['gpName']."</th></tr>
                  <tr class='tr1'><td class='td1' style='text-align:center'>Enter myEmich email addresses to be deleted from group. Separate with comma, semi-colon or white spaces.<br />
                     <textarea style='margin:20px' name='members' rows=5 cols=35>".$_REQUEST['members']."</textArea></td></tr>
                  </table>
                  <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";
               return true;
         }
      }

      // Ask for information
      echo "<form action=''>
         <input type='hidden' name='action' value='updateGroup'>
         <input type='hidden' name='pos' value='".$_REQUEST['pos']."'>
         <input type='hidden' name='gpName' value='".$_REQUEST['gpName']."'>
         <input type='hidden' name='gpId' value='".$_REQUEST['gpId']."'>
         <table style='width:400px' class='t1'><tr class='tr1'><th class='th1'>GROUP ".$_REQUEST['gpName']."</th></tr>
         <tr class='tr1'><td class='td1' style='text-align:center'>
         <br />
         What would you like to do to ".$_REQUEST['gpName']."?<br />
         <select name='localAction'>
            <option value=''>Select from here
            <option value='CHANGEGROUPPRIVILEGE' ".($_REQUEST['localAction']=="CHANGEGROUPPRIVILEGE"?"selected":"").">Change Group Privilege";

      // if groupd was created at another node other than this one,
      //  then allow change to just the privilege and no other changes
      $r = process("select categoryId from groups where id=".mysqlReady($_REQUEST['gpId']));
//      if ($r[0]['categoryId'] == $_REQUEST['pos']) {
      if (getPrivilege($r[0]['categoryId']) == 3) {
         echo "
            <option value='CHANGEGROUPNAME' ".($_REQUEST['localAction']=="CHANGEGROUPNAME"?"selected":"").">Change Group Name
            <option value='VIEWGROUPMEMBERS' ".($_REQUEST['localAction']=="VIEWGROUPMEMBERS"?"selected":"").">View All Members
            <option value='ADDNEWGROUPMEMBERS' ".($_REQUEST['localAction']=="ADDNEWGROUPMEMBERS"?"selected":"").">Add New Members
            <option value='DELETESOMEGROUPMEMBERS' ".($_REQUEST['localAction']=="DELETESOMEGROUPMEMBERS"?"selected":"").">Delete Some Members
            <option value='DELETEGROUP' ".($_REQUEST['localAction']=="DELETEGROUP"?"selected":"").">Delete This Group";
      }

      echo "
         </select><br /><br />
         </td></tr></table>
         <br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?action=getProperties&pos=".$_REQUEST['pos']."'>quit</a></form>";

   }

   function updateDescriptionOrOwner() {
      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      if (isset($_SESSION['username'])) $user = $_SESSION['username'];
      else $user="";
      $modify = getPrivilege($id, $user);
      if ($modify != 3) return;
      switch (strtoupper($_REQUEST['action'])) {
         case "CHANGEDESCRIPTION":
            if (isset($_REQUEST['description']) && ($_REQUEST['description']!='')) {
               $r=process("select description from categories where id=$id");
               $description=$_REQUEST['description'];
               if (endsWith($r[0][0],"\b")) $description .= "\b";
               else if (endsWith($r[0][0],"\e")) $description .= "\e";
               process("update categories set description=".mysqlReady($description)." where id=$id");
               show(null, false);
               showProperties();
            }
            else {
               show(null, false);
               echo "<form>
                  <input type='hidden' name='action' value='CHANGEDESCRIPTION'>
                  <input type='hidden' name='pos' value='$pos'>
                  <table id='pTable' class='table1' style='width:500px'><th class='th1' colspan=2>Change Description for ".$_REQUEST['gpName']."</th></tr><tr><td style='font-weight:400;text-align:right'>Enter new Description:</td><td><input type='text' value=\"".$_REQUEST['gpName']."\" name='description' size=50></td></tr></table>
<br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?pos=$pos'>quit</a></form>";
            }
            break;
         case "CHANGEOWNER":
            if (isset($_REQUEST['owner']) && ($_REQUEST['owner']!='')) {
               process("update categories set owner=".mysqlReady($_REQUEST['owner'])." where id=$id");
               show(null, false);
               showProperties();
            }
            else {
               show(null, false);
               echo "<form>
                  <input type='hidden' name='action' value='CHANGEOWNER'>
                  <input type='hidden' name='pos' value='$pos'>
                  <table id='pTable' class='table1' style='width:500px'><th class='th1' colspan=2>Change Owner for ".$_REQUEST['gpName']."</th></tr><tr><td style='font-weight:400;text-align:right'>Enter new Owner:</td><td><input type='text' name='owner' size=30></td></tr></table>
<br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?pos=$pos'>quit</a></form>";
            }
            break;
      }
   }

   function qAndA() {
      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select description, owner, datetime from categories where id=$id");
      if (!is_array($r)) {
         redFlag("Illegal op - contact web master");
         return;
      }
      $label = $r[0]['description'];
      while (endsWith($label,"\b") || endsWith($label, "\e")) {
         $label = substr($label, 0, strlen($label)-2);
      }
       
//         "<h3 style='margin-bottom:0px'>"+description+"</h3>"+ajax("?action="+action+"&id="+id+"&prompt=yes");
      
      echo "<h3>$label Questions & Answers</h3>";
      
      if (isset($_REQUEST['prompt'])) {
         echo "<div id='d2' style='width:800px;margin-top:20px;border:0px'>";
         addQuestion();
         echo "</div>";
      }
      echo "<a href=\"?action=qAndA&pos=$pos&prompt=yes\">Add New Question</a><br />
         <a href=\"?action=reviewQuestions&pos=$pos\">Review Questions</a><br />
         <a href=\"?action=deleteAllQuestions&pos=$pos\">Delete All Questions</a>";
      exit;
      if (isset($_SESSION['username'])) $user = $_SESSION['username'];
      else $user="";
      $modify = getPrivilege($pos, $user);
      if ($modify == 3) {
         echo "<b>Properties for $label</b>
            <br />You may click on a property to change or update it.
            <form>
            <input type='hidden' name='action' value='updateProperties'>
            <input type='hidden' name='pos' value='$pos'>";
      }
      else {
         echo "<h3>Properties for $label</h3>";
      }
      echo "<table id='pTable' class='table1' style='width:600px'><th class='th1'>Property</th><th class='th1'>Value</th></tr>
            <tr><td style='font-weight:400'>";
      if ($modify==3) echo "<a href='?action=CHANGEDESCRIPTION&gpId=$gpId&pos=$pos&gpName=$label'>Description</a>";
      else echo "Description";
      echo "</td><td>$label</td></tr>
         <tr><td style='font-weight:400'>";
      if ($modify==3) echo "<a href='?action=CHANGEOWNER&gpId=$gpId&pos=$pos&gpName=$label'>Owner</a>";
      else echo "Owner";
      echo "</td><td>".$r[0]['owner']."</td></tr>";
//      echo "<tr><td style='font-weight:400'>Owner</td><td>";
//      if ($modify==3) {
//         echo "<input type='hidden' name='oldOwner' value=\"".$r[0]['owner']."\">
//            <input size=20 type='text' name='owner' value=\"".$r[0]['owner']."\"></td>";
//      }
//      else echo $r[0]['owner']."</td>";
      echo "<tr><td style='font-weight:400'>Date/Time Created</td><td>".$r[0]['datetime']."</td>
            <tr><th colspan=2 style='font-weight:400'><br />Privileges<br />3=delete/add-item/read; 2=add-item/read; 1=read; 0=None
            <br />
               Click on a group to view or update the properties or members.</th></tr>";
      $allowed = getPrivilegedGroups($pos, $user);
      // Include groups with their privileges revoked (set to 0) at this position.
      // This is so the user can change the privilege if they so choose
      $r = process("select groupId, privilege,itemId from itemPrivileges where itemId=\"$pos\" and privilege=0");
      for ($i=0; $i <count($r); $i++) $allowed[] = $r[$i];
//echo "ALLOWED=";print_r($allowed); echo "OK";   
//print_r($r);
      // Of the remaining allowed groups, pick the one with the highest privilege to this node.
      // It so happens that the closest privileges are towards the top of the array since we
      // selected and added to the array from the node, to its parent, then grand-parent, etc.
      $closestIds = array ();
      $closestIndexes = array();
      for ($i=0; $i < count($allowed); $i++) {
         if (($allowed[$i]['privilege'] >= 0) && (!in_array($allowed[$i]['groupId'], $closestIds))) {
//         echo "AA".$allowed[$i]['groupId'];
            $closestIds[] = $allowed[$i]['groupId'];
            $closestIndexes[] = $i;
         }
      }


      // Show the group or owner and privilege.
      // Revocable (non-owner) privileges are modifyable
      for ($i=0; $i < count($closestIds); $i++) {
         $gpId = $closestIds[$i];
         $itemId = $allowed[$closestIndexes[$i]]['itemId'];
         $priv = $allowed[$closestIndexes[$i]]['privilege'];
         $r = process("select name, categoryId from groups where id=\"$gpId\"");
         if (!is_array($r)) { // must be an owner of a node, and not a group
            echo "<tr><td style='padding-left:10px'>$gpId</td><td style='text-align:center'>3</td></tr>";
         }
//         else if ($itemId != $pos) { // irrevocable privilege from ancestor node
//            echo "<tr><td style='padding-left:10px'>".$gpName[0][0]."</td><td style='text-align:center'>$priv   I:$itemId P:$pos</td></tr>";
//         }
         else { // revocable privilege at the present node
            if ($modify) {
               $gpName = $r[0]['name'];
               $categoryId = $r[0]['categoryId'];
               echo "<tr><td style='padding-left:10px'>";
                  echo "<a href='?action=updateGroup&gpId=$gpId&pos=$pos&gpName=$gpName'>$gpName</a>";
               echo "</td><td style='text-align:center'>$priv";
/*
               if ($pos!=$categoryId) echo $gpName;
               else {
                  echo "<a href='?action=updateGroup&gpId=$gpId&pos=$pos&gpName=$gpName'>$gpName</a>";
               }
               echo "</td><td style='text-align:center'><input 
                  type='hidden' name='gps[]' value='$gpId'>
                  <input type='hidden' name='oldprvs[]' value='$priv'>
                  <select name='prvs[]'>";
               for ($k=3; $k >= 0; $k--) {
                  if ($k==$priv) echo "<option selected>$k";
                  else echo "<option>$k";
               }
               echo "</select>";
*/
               echo "</td></tr>";
            }
            else {
               echo "<tr><td style='padding-left:10px'>".$gpName[0][0]."</td><td style='text-align:center'>$priv</td></tr>";
            }
         }
      }

//      if ($modify) echo "<tr><td colspan=2 style='text-align:center'><a href='javascript:addPrivilege()'>Click</a> to Add New Group and Privilege</td></tr>";
      if ($modify) echo "<tr><td colspan=2 style='text-align:center'><a href='?action=updateGroup&localAction=ADDNEWGROUP&pos=$pos'>Click</a> to Add New Group and Privilege</td></tr>";
      echo "</table>";
//      if ($modify) {
//        echo "<br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?pos=$pos'>quit</a></form>";
//      }
   }   
   
   
   function showProperties() {
      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select description, owner, datetime from categories where id=$id");
      if (!is_array($r)) {
         redFlag("Illegal op - contact web master");
         return;
      }
      $label = $r[0]['description'];
      while (endsWith($label,"\b") || endsWith($label, "\e")) {
         $label = substr($label, 0, strlen($label)-2);
      }
      
      if (isset($_SESSION['username'])) $user = $_SESSION['username'];
      else $user="";
      $modify = getPrivilege($pos, $user);
      if ($modify == 3) {
         echo "<b>Properties for $label</b>
            <br />You may click on a property to change or update it.
            <form>
            <input type='hidden' name='action' value='updateProperties'>
            <input type='hidden' name='pos' value='$pos'>";
      }
      else {
         echo "<h3>Properties for $label</h3>";
      }
      echo "<table id='pTable' class='table1' style='width:600px'><th class='th1'>Property</th><th class='th1'>Value</th></tr>
            <tr><td style='font-weight:400'>";
      if ($modify==3) echo "<a href='?action=CHANGEDESCRIPTION&gpId=$gpId&pos=$pos&gpName=$label'>Description</a>";
      else echo "Description";
      echo "</td><td>$label</td></tr>
         <tr><td style='font-weight:400'>";
      if ($modify==3) echo "<a href='?action=CHANGEOWNER&gpId=$gpId&pos=$pos&gpName=$label'>Owner</a>";
      else echo "Owner";
      echo "</td><td>".$r[0]['owner']."</td></tr>";
//      echo "<tr><td style='font-weight:400'>Owner</td><td>";
//      if ($modify==3) {
//         echo "<input type='hidden' name='oldOwner' value=\"".$r[0]['owner']."\">
//            <input size=20 type='text' name='owner' value=\"".$r[0]['owner']."\"></td>";
//      }
//      else echo $r[0]['owner']."</td>";
      echo "<tr><td style='font-weight:400'>Date/Time Created</td><td>".$r[0]['datetime']."</td>
            <tr><th colspan=2 style='font-weight:400'><br />Privileges<br />3=delete/add-item/read; 2=add-item/read; 1=read; 0=None
            <br />
               Click on a group to view or update the properties or members.</th></tr>";
      $allowed = getPrivilegedGroups($pos, $user);
      // Include groups with their privileges revoked (set to 0) at this position.
      // This is so the user can change the privilege if they so choose
      $r = process("select groupId, privilege,itemId from itemPrivileges where itemId=\"$pos\" and privilege=0");
      for ($i=0; $i <count($r); $i++) $allowed[] = $r[$i];
//echo "ALLOWED=";print_r($allowed); echo "OK";   
//print_r($r);
      // Of the remaining allowed groups, pick the one with the highest privilege to this node.
      // It so happens that the closest privileges are towards the top of the array since we
      // selected and added to the array from the node, to its parent, then grand-parent, etc.
      $closestIds = array ();
      $closestIndexes = array();
      for ($i=0; $i < count($allowed); $i++) {
         if (($allowed[$i]['privilege'] >= 0) && (!in_array($allowed[$i]['groupId'], $closestIds))) {
//         echo "AA".$allowed[$i]['groupId'];
            $closestIds[] = $allowed[$i]['groupId'];
            $closestIndexes[] = $i;
         }
      }


      // Show the group or owner and privilege.
      // Revocable (non-owner) privileges are modifyable
      for ($i=0; $i < count($closestIds); $i++) {
         $gpId = $closestIds[$i];
         $itemId = $allowed[$closestIndexes[$i]]['itemId'];
         $priv = $allowed[$closestIndexes[$i]]['privilege'];
         $r = process("select name, categoryId from groups where id=\"$gpId\"");
         if (!is_array($r)) { // must be an owner of a node, and not a group
            echo "<tr><td style='padding-left:10px'>$gpId</td><td style='text-align:center'>3</td></tr>";
         }
//         else if ($itemId != $pos) { // irrevocable privilege from ancestor node
//            echo "<tr><td style='padding-left:10px'>".$gpName[0][0]."</td><td style='text-align:center'>$priv   I:$itemId P:$pos</td></tr>";
//         }
         else { // revocable privilege at the present node
            if ($modify) {
               $gpName = $r[0]['name'];
               $categoryId = $r[0]['categoryId'];
               echo "<tr><td style='padding-left:10px'>";
                  echo "<a href='?action=updateGroup&gpId=$gpId&pos=$pos&gpName=$gpName'>$gpName</a>";
               echo "</td><td style='text-align:center'>$priv";
/*
               if ($pos!=$categoryId) echo $gpName;
               else {
                  echo "<a href='?action=updateGroup&gpId=$gpId&pos=$pos&gpName=$gpName'>$gpName</a>";
               }
               echo "</td><td style='text-align:center'><input 
                  type='hidden' name='gps[]' value='$gpId'>
                  <input type='hidden' name='oldprvs[]' value='$priv'>
                  <select name='prvs[]'>";
               for ($k=3; $k >= 0; $k--) {
                  if ($k==$priv) echo "<option selected>$k";
                  else echo "<option>$k";
               }
               echo "</select>";
*/
               echo "</td></tr>";
            }
            else {
               echo "<tr><td style='padding-left:10px'>".$gpName[0][0]."</td><td style='text-align:center'>$priv</td></tr>";
            }
         }
      }

//      if ($modify) echo "<tr><td colspan=2 style='text-align:center'><a href='javascript:addPrivilege()'>Click</a> to Add New Group and Privilege</td></tr>";
      if ($modify) echo "<tr><td colspan=2 style='text-align:center'><a href='?action=updateGroup&localAction=ADDNEWGROUP&pos=$pos'>Click</a> to Add New Group and Privilege</td></tr>";
      echo "</table>";
//      if ($modify) {
//        echo "<br /><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?pos=$pos'>quit</a></form>";
//      }
   }

   function getPrivilegedGroups($pos, $user) {   
      // Get groupId & privilege of all groups with access privilege to this node
      $allowed = array();
      $revoked = array();
      $r = process("select id from groups where name='Everyone' and categoryId='1'");
      $everyone = $r[0][0];
      $tempPos = $pos;
      // Get groups user belongs to with privilege or revoked privilege to this node

      // if groupMembers is empty, then select from only itemPrivileges table
 //     $gpMembers=process("select 1 from groupMembers limit 1");
      do {
//echo "POS=$tempPos";      
         // If the user is the owner of any node in the path from root to this node, it
         // Add owners of nodes along the path from root to this node.
         $r = process("select owner from categories where id=\"$tempPos\"");
         $allowed[] = array('groupId'=>$r[0][0], 'privilege'=>3, 'itemId'=>$tempPos);

//         if (is_array($gpMembers)) {
            // !=1 below elimites the -root- group
            $query = "select itemPrivileges.groupId, itemPrivileges.privilege, \"$tempPos\" as itemId
               from itemPrivileges, groupMembers where itemPrivileges.groupId != 1 and
               itemPrivileges.itemId=\"$tempPos\" and itemPrivileges.groupId=groupMembers.groupId";
//         }
         $r = process($query);      
//echo "Q=$query"; print_r($r);
         if (is_array($r)) {
            for ($i=0; $i < count($r); $i++) {
               if ($r[$i]['privilege'] > 0) $allowed[] = $r[$i];
               else $revoked[] = $r[$i];
            }
         }
//         else {
            $query = "select groupId, privilege, \"$tempPos\" as itemId
               from itemPrivileges where groupId !=1 and
               itemId=\"$tempPos\"";
//         }
         $r = process($query);      
//echo "Q=$query"; print_r($r);
         if (is_array($r)) {
            for ($i=0; $i < count($r); $i++) {
               if ($r[$i]['privilege'] > 0) $allowed[] = $r[$i];
               else $revoked[] = $r[$i];
            }
         }
         if (($i=lastIndexOf($tempPos,"."))<0) break;
         $tempPos = substr($tempPos, 0, $i);
      } while (true);
     
      // remove all allowed groups with revoked privileges
      for ($i=0; $i < count($revoked); $i++) {
         for ($j =0; $j < count($allowed); $j++) {
            if ($allowed[$j]['groupId']==$revoked[$i]['groupId']) {
               // remove this groupId from the allowed list since it is revoked along the path from root to this node
               $allowed[$j]['privilege'] = 0;
            }
         }
      }
      return $allowed;
   }   
   

   function updateGroupPrivileges() {
print_r($_REQUEST);

      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $username = $_SESSION['username'];
      if (getPrivilege($pos) != 3) {
         redFlag("Error, it seems you don't have the permission for this action!");
         return;
      }

      // update existing privileges
      if (isset($_REQUEST['gps']) && isset($_REQUEST['prvs'])) {
         $gps = $_REQUEST['gps'];
         $prvs = $_REQUEST['prvs'];
         $oldprvs = $_REQUEST['oldprvs'];
         for ($i=0; $i < count($gps); $i++) {
            $priv = $prvs[$i];
            if ($priv == $oldprvs[$i]) continue;
            $gp = $gps[$i];
            if (is_numeric($priv) && is_numeric($gp) && ($priv>=0)&&($priv<=3)) {
//               if ($priv=='0') {
//                  process("delete from itemPrivileges where itemId=$id and groupId=$gp");
//               }
//               else {
                  process("replace into itemPrivileges set itemId=$id, groupId=$gp, privilege=$priv");
//               }
            }
         }
      }


      if (!(isset($_REQUEST['newGps']) || isset($_REQUEST['newPrvs']) || isset($_REQUEST['members']))) return;
      $gps = $_REQUEST['newGps'];
      $prvs = $_REQUEST['newPrvs'];
      $members = $_REQUEST['members'];
      for ($i=0; $i < count($gps); $i++) {
         addOrUpdateGroup($pos, 0, $gps[$i], $prvs[$i], $members[$i]);
      }
   }

   function addOrUpdateGroup($pos, $gpId, $gpName, $gpPriv, $emails) {
      if (trim($gpName)=="") {
         redFlag("Group with blank name not allowed. It was skipped.");
         return false;
      }

      if (!(is_numeric($gpPriv) && $gpPriv>=0 && $gpPriv <=3)) { // invalid op
         redFlag("Invalid operation!");
         return false;
      }

      // validate that each member has access to this node
      $list = preg_split("/[\s,;\n\t]+/", $emails);
      $y = 0;
      for ($i=0; $i<count($list); $i++) {
         $email = $list[$i];
         $temp = normalize($list[$i]);
         if ( (($temp=='')||(indexOf($temp,'@')>=0)) ||
              ((!filter_var($mail, FILTER_VALIDATE_EMAIL)) && (($y=getPrivilege($pos, $email))<=0))) {
            if ($gpId == 0) {
               redFlag("Group $gpName not added b/c member $email does not have access to all the ancestor nodes");
               return false;
            }
            else {
               $r = process("select description from categories where id=".mysqlReady($pos));
               redFlag("Group privilege operation failed b/c email \"$email\" does not have access to ".$r[0][0]);
               return false;
            }
         }
         $list[$i] = $temp;
      }

      // if adding a new group or changing the name of an existing group,
      // make sure it doesn't already exist
      if ($gpId != 0) { // updating an existing group
         $r = process("select name from groups where id=".mysqlReady($gpId));
         if (!is_array($r)) {
            redFlag("Invalid op - quiting!"); return false;
         }
 
         // If we have a new name, make sure it does not already exist
         $oldName = $r[0][0];
         if (strcasecmp($oldName, $gpName)) {
            $r = process("select 1 from groups where categoryId=".mysqlReady($gpId)." and name=$gpName");
            if (is_array($r) && ($r[0][0]==1)) {
               redFlag("Group $oldName can not be renamed $gpName b/c the new name already exists.<br />Update group aborted!");
               return false;
            }
            process("update groups set name=".mysqlReady($gpName)." where id=".mysqlReady($gpId));
         }
         
      }
      else { // adding new group
         $r = process("select 1 from groups where categoryId=".mysqlReady($pos)." and name=".mysqlReady($gpName));
//         echo "RRR=";print_r($r);
         if (is_array($r) && ($r[0][0]==1)) {
            redFlag("Group \"$gpName\" not added b/c it already exists. Add group aborted!<br />");
            return false;
         }
         process("insert into groups set name=".mysqlReady($gpName).", categoryId=".mysqlReady($pos));
         $r = process("select LAST_INSERT_ID()");
         $gpId = $r[0][0];
         // update privileges
         process("replace into itemPrivileges set itemId=".mysqlReady($pos).", groupId=".mysqlReady($gpId).", privilege=".mysqlReady($gpPriv));
      }
         
      // Add group members
 //     $list = preg_split("/[\s,;\n\t]+/", $emails); 
      for ($k=0; $k < count($list); $k++) {
         $email = $list[$k];
         process("replace into groupMembers set groupId=".mysqlReady($gpId).", username=".mysqlReady($email));
      }
      redFlag("Group $gpName operation successfully completed. ");
      return true;
   }

   // Can user access node?
   // Verify user can read all nodes starting from parent of node back up to root.
   function accessible ($node, $user) {
      do {
         if (getPrivilege($node, $user)<1) return 0;
         // move up to the parent node
         if (($i=indexOf($node, ".")) > 0) $node = substr($node, 0, $i);
      } while ($i > 0);
      return 1; // access path determined
   }
   
   function getPrivilege($pos, $user=null) {
      if ($user==null) {
         if (!isset($_SESSION['username'])) $user="";
         else $user = $_SESSION['username'];
      }
//      // superuser?
//      if (strtolower($user)=="aikeji") return 3;
      // If the user is the owner of any node in the path from root to this node, it
      // means user has irrevocable r/w/d privilege (3) to this node?

      $list = explode(".",$pos);
      $next = str_replace('\'','',$list[0]);
      $set = "(".mysqlReady($next);
      for ($i=1; $i < count($list); $i++) {
         $next = "$next".".".str_replace('\'','',$list[$i]);
         $set .= ",".mysqlReady($next);
      }
      $set .=")";
      $r = process("select 1 from categories where id in $set and owner=\"$user\"");
      if (is_array($r)) return 3;

      // Since user is not the owner of any nodes in the path, find and return
      // the highest privilege by user based on groups they belong to.
      
      $r = process("select id from groups where name='Everyone' and categoryId='1'");
      $everyone = $r[0][0];
      $r = process("select itemId, privilege, itemPrivileges.groupId as groupId from itemPrivileges, groups, groupMembers where itemId in $set and (itemPrivileges.groupId=$everyone or (itemPrivileges.groupId=groups.id and groups.id=groupMembers.groupId and username=\"$user\"))");
      // Get allowed privileges
      $allowed = array();
      for ($i=0; $i<count($r); $i++) {
         $priv = $r[$i]['privilege'];
         if ($priv > 0) {
            $gpId = $r[$i]['groupId'];
            if (!isset($allowed[$gpId])) $allowed[$gpId] = $priv;
            else if ($priv > $allowed[$gpId]) $allowed[$gpId] = $priv;
         }
      }
      // remove revoked privileges;
      for ($i=0; $i<count($r); $i++) {
         $priv = $r[$i]['privilege'];
         if ($priv == 0) {
            $gpId = $r[$i]['groupId'];
            $allowed[$gpId] = $priv;
         }
      }
      return max($allowed);
   }


   function updateProperties() {
//print_r($_REQUEST);
      $pos = $_REQUEST['pos'];
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);

      $priorityLevel = getPrivilege($pos);
      if ($priorityLevel != 3) {
         redFlag("Illegal op - contact web master");
         return;
      }
      if (isset($_REQUEST['desc']) && ($_REQUEST['desc']!="")) {
         $desc = mysqlReady($_REQUEST['desc']);
         process("update categories set description=$desc where id=$id");
      }

      if (isset($_REQUEST['owner']) && ($_REQUEST['owner']!="") &&
           strcasecmp($_REQUEST['oldOwner'],$_REQUEST['owner'])) {
         if (strcasecmp($_REQUEST['owner'],$_SESSION['username'])) {
            redFlag("Error, you can only change the owner to your email address. Owner not changed!<br />");
         }
         else {
            $owner = mysqlReady($_REQUEST['owner']);
            process("update categories set owner=$owner where id=$id");
         }
      }

      updateGroupPrivileges();
         
      echo "<h3>Update completed</h3>";
      show($pos);
   }

   function delete($request) {
//echo 1;
      $pos = $_REQUEST['pos'];
      if (getPrivilege($pos)<3) {
         redFlag("Illegal op - contact web master");
         return;
      }
      // Root should never be deleted
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
//echo 2; 
      $r = process("select description from categories where id=$id");
//      echo("select description from categories where id=$id");
      if ((!is_array($r)) || (strtoupper($request)=="DELETE" && strtolower($r[0][0])=="all documents")) return;
      // zap descendants, then parent if necessary
      $name = $r[0][0];
      $count=0;
      $r = process("select count(*) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+')");
//echo 3;
      if (is_array($r) && ($r[0][0]>0)) {
      echo 3.5;
         process("delete from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+')");
         process("delete from attachments where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+')");
         process("delete from itemPrivileges where itemId regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+')");
         $count = $r[0][0];
//         exec ("find uploads -regex 'uploads/$pos.[0-9]+_.*' -print0 | xargs -0 rm -f");
         exec ("rm -fr uploads/$pos.*_*");
//echo "find uploads -regex 'uploads/1.[0-9]+_.*' -print0 | xargs -0 rm -f";
      }
//echo 4;
      if (strtoupper($request) == "DELETEDESCENDANTS") {
//echo 5;      
         echo "<h3> All $count descendant(s) of [$name] deleted.</h3>";
         show($pos);
      }
      else {
//echo 6;
//         exec ("find uploads -regex 'uploads/$pos_.*' -print0 | xargs -0 rm -f");
//         echo ("rm -fr uploads/$pos"."_*");
         exec ("rm -fr uploads/$pos"."_*");
         process("delete from categories where id=$id");
         process("delete from attachments where id=$id");
         process("delete from itemPrivileges where itemId=$id");
         $i = lastIndexOf($pos, ".");
         $pos = substr($pos, 0, $i);
         echo "<h3> [$name] and all $count descendant(s) deleted.</h3>";
         show($pos);
      }
   }


   function addSubcategory() {
      $pos = $_REQUEST['pos'];
      // make sure pos exists
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select 1 from categories where id=$id");
      if (!is_array($r)) return;
      $r = process("select max(count) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$')");
      if (is_array($r) && ($r[0][0] > 0)) {
         $r = process("select id from categories where count=".$r[0][0]);
         $list = explode(".",$r[0][0]);
         $next = "$pos.".($list[count($list)-1]+1);
      }
      else $next = $pos.".1";
      $label = $_REQUEST['label'];
      // Remove any ending \b or \e in catgeories as they are used to id
      // attachments (documents) instead of categories
      while (endsWith($label,"\b") || endsWith($label, "\e")) {
         $label = substr($label, 0, strlen($label)-1);
      }
      process("insert into categories set id='$next', owner=\"".$_SESSION['username']."\", description=".mysqlReady($label));
      echo "<h3>".$_REQUEST['label']." added.</h3>";

      // Grand father in parent privileges
      $j = lastIndexOf($itemId, ".");
      if ($j > 0) {  // not Adam?
         $parentId = substr($itemId, 0, $j);
         $priv = process("select groupId, privilege, name from itemPrivileges, groups where itemId='$parentId' and groupId=id");
         for ($i=0; $i < count($priv); $i++) {
            process("insert into itemPrivileges set itemId='$next', groupId='".$priv[$i]['groupId'].", privilege=".$priv[$i]['privilege']);
         }
      }
      //showPrivileges($next);
      show($pos);
   }


   function makeSet($delimiter, $itemId) {
      $list = explode ($delimiter, $itemId);
      if (!is_array($list)) return "";
      $pos = $list[0];
      $s = "(\"$pos\"";
      for ($i=1; $i < count($list); $i++) {
         $pos .= $delimiter.$list[$i];
         $s .= ",\"$pos\"";
      }
      $s .=")";
      return $s;
   }


   function attachDocument() {
      $pos = $_REQUEST['pos'];
      // make sure pos exists
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select 1 from categories where id=$id");
      if (!is_array($r)) return;

      // validate attachment
      $postMax = ini_get('post_max_size');
      if (endsWith($postMax, "K")) $postMax = substr($postMax, 0, strlen($postMax)-1)*1024;
      elseif (endsWith($postMax, "M")) $postMax = substr($postMax, 0, strlen($postMax)-1)*1024*1024;
      if (endsWith($postMax, "G")) $postMax = substr($postMax, 0, strlen($postMax)-1)*1024*1024*1024;
      
      if (($_REQUEST['docType']=='url') && (trim($_REQUEST['url'])!= "")) {
         $url = $_REQUEST['url'];
         if (!startsWith($url, "http")) $url = "http://".$url;
//         $infile = fopen($url, "rb") or false;
//         if (!$infile) {
//            echo "Error, unable to verify $url";
//            return;
//         }
      }
      else if ($_REQUEST['docType']=='file') {
         if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            redFlag("Error, document attachments larger than $postMax can not be uploaded");
            return;
         }
         else if ($_FILES['uploadFile']['error'] > 0) {
            redFlag("Error, unable to upload attachment file");
//            redFlag("Error, unable to upload attachment file".$_FILES['uploadFile']['error']);
            return;
         }
      }
      else { redFlag("Error, illegal upload type!"); return;}

      // validate any search keyword file
//print_r($_FILES);
//print_r($_REQUEST);
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
         if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            redFlag("Error, keyword file attachments larger than $postMax can not be uploaded");
            return;
         }
         else if ($_FILES['searchFile']['error'] > 0) {
            redFlag("Error, unable to upload search file");
         }
      }
      
      $r = process("select max(count) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$')");
      if (is_array($r) && ($r[0][0] > 0)) {
         $r = process("select id from categories where count=".$r[0][0]);
         $list = explode(".",$r[0][0]);
         $next = "$pos.".($list[count($list)-1]+1);
      }
      else $next = $pos.".1";
      $label = $_REQUEST['label'];
      
      // create the path if it doesn't exist
      $dir = "uploads/";
      exec("mkdir $dir");
      $filename = "$dir$next";
      if ($_REQUEST['docType']=='url') {
         // save link in db
         process("replace into attachments set id='$next', info=".mysqlReady($url));
         // Make sure label for url attachments ends with a "\b" as it
         // is used to id url attachments (documents) instead of others
         if (!endsWith($label,"\b")) $label .= "\b";
/*         if (indexOf($url,".")<0) $ext="..txt";
         else $ext = end(explode(".", $url));
         $outfile = fopen("$filename.$ext", "w");
         $count = 0;
         while (!feof($infile)) {
            if ($count > $postMax) {
               redFlag("Error, attachments larger than $postMax can not be uploaded");
               fclose(infile); fclose($outfile);
               return;
            }
            
            fwrite($outfile, fread($infile, 8192));
            $count += 8192;
         }
         fclose ($outfile); fclose($infile);
*/
      }
      else { // assume uploaded file
         $name = $_FILES["uploadFile"]["name"];
         if (indexOf($name,".")<0) $ext="";
         else $ext = end(explode(".", $name));
         move_uploaded_file($_FILES['uploadFile']['tmp_name'],"$filename"."_.$ext");    
         exec("chmod 700 $filename"."_.$ext");    
         // save ext in db
         process("replace into attachments set id='$next', info='$ext'");

         // Make sure label for file attachments ends with a "\e" as it
         // is used to id file attachments (documents) instead of others
         if (!endsWith($label,"\e")) $label .= "\e";
      }

      // grab any search keyword files
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
echo 111;
         $name = $_FILES["searchFile"]["name"];
echo "NAME=$filename AA";
         move_uploaded_file($_FILES['searchFile']['tmp_name'],"$filename"."_txt");    
      }

      process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
//      echo("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      echo "<h3>".$_REQUEST['label']." attached.</h3>";
      show($pos);
   }

   function attachNote() {
      $pos = $_REQUEST['pos'];
      // make sure pos exists
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select 1 from categories where id=$id");
      if (!is_array($r)) return;

      // validate any search keyword file
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
         if(empty($_FILES) && empty($_POST) && isset($_SERVER['REQUEST_METHOD']) && strtolower($_SERVER['REQUEST_METHOD']) == 'post'){
            redFlag("Error, keyword file attachments larger than $postMax can not be uploaded");
            return;
         }
         else if ($_FILES['searchFile']['error'] > 0) {
            redFlag("Error, unable to upload search file");
         }
      }

      $note = $_REQUEST['note'];
      $label = trim($_REQUEST['label']);
      if (strlen($note)>100000) {
         redFlag("Error, maximum size of 100000 characters exceeded for notes");
         return;
      }
      else if ((strlen(trim($note))==0) ||
        (strlen($label)==0)) {
         redFlag("Error, Empty/blank note not allowed.");
         return;
      }
     
      // get next position 
      $r = process("select max(count) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$')");
      if (is_array($r) && ($r[0][0] > 0)) {
         $r = process("select id from categories where count=".$r[0][0]);
         $list = explode(".",$r[0][0]);
         $next = "$pos.".($list[count($list)-1]+1);
      }
      else $next = $pos.".1";
      $label = $label."\t";
      
      // grab any search keyword files
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
         $name = $_FILES["searchFile"]["name"];
         move_uploaded_file($_FILES['searchFile']['tmp_name'],"$filename"."_txt");    
      }

      process("replace into notes set id='$next', note=".mysqlReady($note));
      process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      echo "<h3 style='color;red'>".$_REQUEST['label']." attached.</h3>";
      show($pos);
   }

   function addQuestion() {
      $pos = $_REQUEST['pos'];   
      if (getPrivilege($pos)<3) { // no privilege to add question
         echo "Access denied. Please contact system manager for the right privilege";
         exit;
      }      
      
      // prompt for question?
      if (!isset($_REQUEST['label'])) {
         echo
            "<b>Add New Question</b><form method='post' enctype='multipart/form-data'><div style='border:1px solid black'>
            <input type='hidden' name='pos' value='$pos'>
            <table class='table1' style='border:0px;width:90%'>
            <tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>1. Enter a label or title for this question (<font style='color:red;font-weight:bold'>optional</font>):
            <input type='text' name='label' size='20'><br />
            </td></tr>
            <tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>2. How would you like to enter the question?<br />
            <input type='radio' name='qType' value='typed' style='margin-left:50px' onClick=\"document.getElementById('textQ').style.display='block';document.getElementById('uploadQ').style.display='none';\">Type or paste in the question&nbsp;&nbsp;&nbsp;&nbsp
            <input type='radio' name='qType' value='file' onClick=\"document.getElementById('textQ').style.display='none';document.getElementById('uploadQ').style.display='block';\">Upload a file with the question<br />

            <div id='textQ' style='display:none'>
               <span style='font-weight:bold;text-align:center'>Enter the question below</span>
               <textArea rows=10 cols=80 name='questionText'></textArea>
            </div>
            <div id='uploadQ' style='display:none'>
               <span style='font-weight:bold;text-align:center'>Select file to upload</span>
               <input type='file' name='questionUpload' size='30'>
            </div>
            </td></tr>
            <tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>3. Do you want others to use this question?
<input type='radio' name='public' checked='checked'>Yes&nbsp;&nbsp;&nbsp<input type='radio' name='public' >No
</td></tr>
      <tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>4. What form of response or answer do you want for this question?
<select name='responseType' onChange='responseFunc(this.options[this.selectedIndex].value)'>
<option value=''>Select</option>
<option value='multipleChoice'>Multiple choice</option>
<option value='essay'>Essay or written words</option>
<option value='program'>Computer program</option>
</select>
<div id='responseTypeDiv' style='display:block'></div>

            </td></tr>
            </table></div>
            <input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href=\"?action=qAndA&pos=$pos\">cancel</a></form>";
//            javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></form>";


         exit;
      }
print_r($_REQUEST);
      $pos = $_REQUEST['pos'];
      // make sure pos exists
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select 1 from categories where id=$id");
      if (!is_array($r)) return;

      if (strtoupper($_REQUEST['qType']) != "FILE") {
         $question = $_REQUEST['questionText'];
         if (strlen($question)>1000000) {
            redFlag("Error, maximum size of 1000000 characters exceeded for question");
            return;
         }
         else if ((strlen(trim($question))==0)) {
            redFlag("Error, Empty/blank question not allowed.");
            return;
         }         
      }
      else $question = "";
      
      $title = trim(mysqlReady($_REQUEST['label']));
      $responseType = strtoupper(($_REQUEST['responseType']));
      
      if ($responseType == "MULTIPLECHOICE") {
         $correctChoice=" ";
         for ($i=0; $i < count($_REQUEST['tamultipleChoice']); $i++) {
            $question .= "\b".$_REQUEST['tamultipleChoice'][$i];
echo "<br />ONE=".$_REQUEST['correctResponseList'][$i];            
            if (in_array($_REQUEST['correctResponseList'][$i], $_REQUEST['correctResponse'])) $correctChoice .="$i ";
         }
         $question .= "\b$correctChoice";
echo "CORR CHO=$correctChoice";         
      }
      process("replace into questions set title=$title, question=".mysqlReady($question).", owner='".$_SESSION['username']."', subcategory='$pos',
         privileges=".mysqlReady($_REQUEST['public']) );
echo("replace into questions set title=$title, question=".mysqlReady($question).", owner='".$_SESSION['username']."', subcategory='$pos',
         privileges=".mysqlReady($_REQUEST['public']) );
echo "<br />"; print_r($_SESSION);
      //     process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      
      echo "done";
      exit;
      // get next position 
      $r = process("select max(count) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$')");
      if (is_array($r) && ($r[0][0] > 0)) {
         $r = process("select id from categories where count=".$r[0][0]);
         $list = explode(".",$r[0][0]);
         $next = "$pos.".($list[count($list)-1]+1);
      }
      else $next = $pos.".1";
      $label = $label."\t";
      
      // grab any search keyword files
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
         $name = $_FILES["searchFile"]["name"];
         move_uploaded_file($_FILES['searchFile']['tmp_name'],"$filename"."_txt");    
      }

      process("replace into question set id='$next', question=".mysqlReady($question));
      process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      echo "<h3 style='color;red'>".$_REQUEST['label']." attached.</h3>";
      show($pos);
   }

   function reviewQuestions() {
      $pos = $_REQUEST['pos'];   
      if (getPrivilege($pos)<3) { // no privilege to add question
         echo "Access denied. Please contact system manager for the right privilege";
         exit;
      }      
      echo "Page: 1 2 3 ... Next Prev Go to:[] &nbsp;&nbsp;&nbsp;&nbsp;Filter Questions:<select name='filter'>
         <option value=''></option>
         <option value='All'>Show All Questions</option>
         <option value='byUser'>Show Questions by ...</option>
         <option value='tagged'>Show Tagged Questions</option>
         </select><br />";
      $r=process("select title, question, owner, privileges from questions where subcategory='$pos'");
      print_r($r);
      
      echo 123; exit;
      // prompt for question?
      if (!isset($_REQUEST['label'])) {
         echo
            "<b>Add New Question</b><form method='post' enctype='multipart/form-data'><div style='border:1px solid black'>
            <input type='hidden' name='pos' value='$pos'>
            <table class='table1' style='border:0px;width:90%'>
            <tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>1. Enter a label or title for this question (<font style='color:red;font-weight:bold'>optional</font>):
            <input type='text' name='label' size='20'><br />
            </td></tr>
            <tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>2. How would you like to enter the question?<br />
            <input type='radio' name='qType' value='typed' style='margin-left:50px' onClick=\"document.getElementById('textQ').style.display='block';document.getElementById('uploadQ').style.display='none';\">Type or paste in the question&nbsp;&nbsp;&nbsp;&nbsp
            <input type='radio' name='qType' value='file' onClick=\"document.getElementById('textQ').style.display='none';document.getElementById('uploadQ').style.display='block';\">Upload a file with the question<br />

            <div id='textQ' style='display:none'>
               <span style='font-weight:bold;text-align:center'>Enter the question below</span>
               <textArea rows=10 cols=80 name='questionText'></textArea>
            </div>
            <div id='uploadQ' style='display:none'>
               <span style='font-weight:bold;text-align:center'>Select file to upload</span>
               <input type='file' name='questionUpload' size='30'>
            </div>
            </td></tr>
            <tr class='tr2'><td class='td1' style='text-align:left;padding:15px;'>3. Do you want others to use this question?
<input type='radio' name='public' checked='checked'>Yes&nbsp;&nbsp;&nbsp<input type='radio' name='public' >No
</td></tr>
      <tr class='tr2' style='background-color:#F0F0F0'><td class='td1' style='text-align:left;padding:15px'>4. What form of response or answer do you want for this question?
<select name='responseType' onChange='responseFunc(this.options[this.selectedIndex].value)'>
<option value=''>Select</option>
<option value='multipleChoice'>Multiple choice</option>
<option value='essay'>Essay or written words</option>
<option value='program'>Computer program</option>
</select>
<div id='responseTypeDiv' style='display:block'></div>

            </td></tr>
            </table></div>
            <input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href=\"?action=qAndA&pos=$pos\">cancel</a></form>";
//            javascript:void()\" onClick=\"document.getElementById('d2').innerHTML='';document.getElementById('select1').options[0].selected='selected';return false;\">cancel</a></form>";


         exit;
      }
print_r($_REQUEST);
      $pos = $_REQUEST['pos'];
      // make sure pos exists
      if (is_numeric($pos)) $id="'$pos'";
      else $id = mysqlReady($pos);
      $r = process("select 1 from categories where id=$id");
      if (!is_array($r)) return;

      if (strtoupper($_REQUEST['qType']) != "FILE") {
         $question = $_REQUEST['questionText'];
         if (strlen($question)>1000000) {
            redFlag("Error, maximum size of 1000000 characters exceeded for question");
            return;
         }
         else if ((strlen(trim($question))==0)) {
            redFlag("Error, Empty/blank question not allowed.");
            return;
         }         
      }
      else $question = "";
      
      $title = trim(mysqlReady($_REQUEST['label']));
      $responseType = strtoupper(($_REQUEST['responseType']));
      
      if ($responseType == "MULTIPLECHOICE") {
         $correctChoice=" ";
         for ($i=0; $i < count($_REQUEST['tamultipleChoice']); $i++) {
            $question .= "\b".$_REQUEST['tamultipleChoice'][$i];
echo "<br />ONE=".$_REQUEST['correctResponseList'][$i];            
            if (in_array($_REQUEST['correctResponseList'][$i], $_REQUEST['correctResponse'])) $correctChoice .="$i ";
         }
         $question .= "\b$correctChoice";
echo "CORR CHO=$correctChoice";         
      }
      process("replace into questions set title=$title, question=".mysqlReady($question).", owner='".$_SESSION['username']."', subcategory='$pos',
         privileges=".mysqlReady($_REQUEST['public']) );
echo("replace into questions set title=$title, question=".mysqlReady($question).", owner='".$_SESSION['username']."', subcategory='$pos',
         privileges=".mysqlReady($_REQUEST['public']) );
echo "<br />"; print_r($_SESSION);
      //     process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      
      echo "done";
      exit;
      // get next position 
      $r = process("select max(count) from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$')");
      if (is_array($r) && ($r[0][0] > 0)) {
         $r = process("select id from categories where count=".$r[0][0]);
         $list = explode(".",$r[0][0]);
         $next = "$pos.".($list[count($list)-1]+1);
      }
      else $next = $pos.".1";
      $label = $label."\t";
      
      // grab any search keyword files
      if (isset($_REQUEST['search']) && ($_REQUEST['search']=="yes")) {
         $name = $_FILES["searchFile"]["name"];
         move_uploaded_file($_FILES['searchFile']['tmp_name'],"$filename"."_txt");    
      }

      process("replace into question set id='$next', question=".mysqlReady($question));
      process("insert into categories set id='$next', description=".mysqlReady($label).", owner='".$_SESSION['username']."'");
      echo "<h3 style='color;red'>".$_REQUEST['label']." attached.</h3>";
      show($pos);
   }


   


   function showAll() {
      if ($pos==null) {
         if (isset($_REQUEST['pos'])) $pos=$_REQUEST['pos'];
         else $pos=1;
      }

      // Show descendants of pos that user can read
      $r = process("select description from categories where id=".mysqlReady($pos));
      echo "<div id='d1' style='margin-left:200px;text-align:left;width:800px;margin-top:50px;border:0px'><h3>SHOWING ALL DOCUMENTS UNDER {$r[0][0]}</h3>";
      $r = process("select id, description from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.') order by datetime desc");
      if (!is_array($r)) echo "Nothing under this category";
      else {
         for ($i=0; $i < count($r); $i++) {
            $id = $r[$i]['id'];
            // Display if client has privilege to see it
            if (getPrivilege($id)<1) continue;
            
            $desc = $r[$i][1];
            if (endsWith($desc, "\b")) {
               $desc=substr($desc, 0, strlen($desc)-2);
            }
            else if (endsWith($desc, "\e")) {
               $desc=substr($desc, 0, strlen($desc)-2);
            }
//            else continue;
            showPath(strlen($pos),$id, true); echo "<br />";
         }
      }
   }

   function showPath($cutOff, $pos, $full=false) {
      $front = substr($pos, 0, $cutOff);
      if ($cutOff==0) $rear = $pos;
      else $rear = substr($pos, $cutOff+1);
      if ($rear=="") return "";
      $list = explode(".",$rear);
      if ($front !="") $front .= ".";
      $next = "$front{$list[0]}";
      $set = "(".mysqlReady($next);
      for ($i=1; $i < count($list); $i++) {
         $next = "$next.{$list[$i]}";
         $set .= ",".mysqlReady($next);
      }
      $set .=")";
      if ($full) $r = process("select id, description, datetime from categories where id in $set order by id");
      else $r = process("select id, description from categories where id in $set order by id");
      for ($i=0; $i<count($r); $i++) {
         $id = $r[$i]['id']; 
         $desc = $r[$i]['description']; 
         $href = "";
         if (endsWith($desc, "\b")) {
            $desc2=substr($desc, 0, strlen($desc)-2);
            $r2 = process("select info from attachments where id='$id'");
            if (is_array($r2)) $href=$r2[0][0];
//            if (is_array($r2)) $href="?action=OPEN&pos=$id&b=b";
            else $href="javascript:alert('url-not-found')";
         }
         else if (endsWith($desc, "\e")) {
            $desc2=substr($desc, 0, strlen($desc)-2);
            $r2 = process("select info from attachments where id='$id'");
            if (is_array($r2)) $href="?action=OPEN&pos=$id";
//            if (is_array($r2)) $href="uploads/$id"."_.{$r2[0]['info']}";
            else $href="javascript:alert('file-not-found');return false";
         }
         else {
            $desc2=$desc;
         }
         if ((($i+1) < count($r))||($full)) {
            echo ">>&nbsp;<a href='?pos=$id' style='color:black'>".replace($desc2, " ", "&nbsp;")."</a>";
         }
         else echo ">>&nbsp;<span style='color:black'>".replace($desc2," ","&nbsp;")."</span>";
         if ($href != "") echo "<a href=\"$href\" style='color:red;font-size:8pts'>(open)</a>";
         if (($full) && (($i+1)==count($r))) echo "&nbsp;".$r[$i]['datetime'];
      }
      return $desc2;
   }
   
   function show($pos=null, $header=true) {
      if ($pos==null) {
         if (isset($_REQUEST['pos'])) $pos=$_REQUEST['pos'];
         else $pos=1;
      }
      echo "<div style='width:900px;text-align:right'>
      <form style='margin-bottom:0px'>
      <input type='hidden' name='pos' value='$pos'>
      <input type='hidden' name='action' value='SEARCH'>
      <input type='text' size='30' name='keywords' style='color:gray' value='Enter Reference # or keywords' onClick=\"if (this.value=='Enter Reference # or keywords') {this.value='';this.style.color='black';}\">";
//      if (isset($desc) && (strlen($desc)>20)) $sz=8;
//      else $sz=10;
      $sz=10;
      echo "<input id='searchButton' type=submit style='font-size:$sz' value='Search '></form></div>";

      echo "<table class='table1' style='border:0px;width:900px'><tr class='tr1'><td class='td1' style='background-color:#0FFFFFF;text-align:left;font-weight:bold'>";
      $desc2 = showPath(0, $pos);
      if (isset($_SESSION['username'])) {
         $desc = $desc2;
         $p=getPrivilege($pos);
         if ($p>=2) {
            echo "<div style='float:right'><form id='f1' style='margin-bottom:0px'>
               <input type='hidden' name='pos' value='$pos'>
               <input type='hidden' name='desc' value='$desc2'>
               <select name='action' id='select1' onChange='getInfo(this.options[this.selectedIndex].value, \"$pos\", this.options[this.selectedIndex].innerHTML);'>
               <option value=''>Additional Options</opton>";
            echo "<option value='addsubcategory'>Add Subcategory to [$desc]</option> 
                 <option value='attachdocument'>Attach Document to [$desc]</option>
                 <option value='attachnote'>Attach Note to [$desc]</option>
                 <option value='qAndA'>[$desc] Q & A</option>
                 <option value='tests'>[$desc] Tests/Asgs/Surveys and related</option>
                 <option value='showall'>Show All [$desc] & Descendants</option>";
            if ($p==3) {
               if (trim($pos) != 1) echo "<option value='delete'>Delete [$desc] & descendants</opton>";
                echo "<option value='deletedescendants'>Delete descendants of [$desc]</opton>
                   <option value='getproperties'>Get Properties of [$desc]</option>";
            }
            echo "</select></form></div>";
         }
      }
      echo "</td></tr></table>
         </div><div id='d2' style='width:800px;margin-top:20px;border:0px'></div>";
if (isset($desc)) {
   if (endsWith($desc,"\b") || endsWith($desc, "\e"))
      $desc = substr($desc, 0, strlen($desc)-2);
echo "<script>document.getElementById('searchButton').value += \"$desc\"</script>";
}
      if (!$header) return;
      echo "<div id='d1' style='width:500px;margin-top:50px;border:0px;text-align:left'>";
      $r = process("select id, description from categories where id regexp concat(".mysqlReady(str_replace('.','\\.','^'.$pos)).",'\\.[0-9]+$') order by description");
      if (!is_array($r)) {
         if (endsWith($desc, "\t")) {
            $r=process("select note from notes where id='$pos'");
            if (is_array($r)) echo "<h3>$desc</h3>".replace($r[0]['note'], "\n","<br />");
         }
         else echo "Nothing under this category";
      }
      else {
         for ($i=0; $i < count($r); $i++) {
            $id = $r[$i][0];
            // Display if client has privilege to see it
            if (getPrivilege($id)<1) {
               if ($pos!="1.1.3") continue;  // Allow access to Courses ... - 1.1.3 w/o privilege

//               if (!(startsWith($pos,"1.1.3.")||($pos!="1.1.3"))) continue;  // Allow access to "Courses ... - 1.1.3 w/o privilege
//else echo "reject $pos<br />";
            }
            
            $desc = $r[$i][1];
            if (endsWith($desc, "\b")) {
               $desc=substr($desc, 0, strlen($desc)-2);
            }
            else if (endsWith($desc, "\e")) {
               $desc=substr($desc, 0, strlen($desc)-2);
            }
            $href="?pos=$id";
            echo "<a href=\"$href\" style='color:black'>$desc</a><br />";
         }
      }
   }



   function normalize($username) {
      if (endsWith(strtoupper($username), "@EMICH.EDU")) {
          $username = substr($username, 0, strlen($username)-10);
      }
      return $username;
   }

   function endsWith( $str, $sub ) {
      return ( substr( $str, strlen( $str ) - strlen( $sub ) ) === $sub );
   }


   function replace($src, $old, $new) {
    return str_replace($old, $new, $src); // this is the right sequence
   }



   function howToUse() {
      echo "
         <table><tr><th>How to use this site</th></tr>
             <tr><td>
                <ul>
                 <li style='margin-bottom:8px'>To Signup:
                        <ol><li>Click on signup
                            <li>Enter your my.emich username and click submit
                            <li>An email will be sent to your my.emich account with your temporary password.
                        </ol>
                     </li>
                <li style='margin-bottom:8px'>Some items are privileged. It is recommend you login while using this site so you can view all items for your user groups.
                <li style='margin-bottom:8px'>To Read A Document:
                        <ol><li>Navigate to the document location until it appears in the row with the double angles (>>)</li>
                            <li>Documents that may be opened or downloaded will have \"";redFlag("(open)");echo"\" next to them.
Click on ";redFlag("(open)"); echo ".
                        </ol>
                     </li>
                     <li style='margin-bottom:8px'>To Add a category, attach document or link to a resource:
                        <ol><li>Login.
                           <li>Navigate to the location where the item is to be added.
                            <li>After the double angles (>>) and to the far right you will see <b>Additional Options.</b>
                            <li>Select the desired option. Availale options may include:
                                <ul><li>Attach Subcategory: to create a subcategory under the last item in the double angles row.
                                <li>Attach Document, question or Note: to attach a note, question, document file or URL/link to a resource under the last item in the double angles row.
                                <li>Delete Descendants: to delete all the subcategories, documents, URL/links under under the last item in the double angles row.
                                <li>Get Properties: to view or change the properties for the item.<br />This will also allow you to revoke, delete or add new privileges for the item
                                </ul>
                        </ol>
                     </li>
                     <li style='margin-bottom:8px'>To Change your password:
                        <ol><li>Login.
                            <li>You will see Change Password towards the top of the page. Click on it and follow the instructions.
                        </ol>
                     </li>
                 </ul>
             </td></tr>
         </table>";

      
   }

   function showLogin() {
      echo "<form method='post'>
         <input type='hidden' name='action' value='processLogin'>
         <table><tr><th colspan='2'>Enter Login Information</th></tr>
                <tr><td align='right'>MyEmich Email Address:</td><td><input type='text' name='username' size='30'></td></tr>
                <tr><td align='right'>Password:</td><td><input type='password' name='password' size='30'></td></tr>
                <tr><td colspan='2' align='center'><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?'>quit</a>
</td></tr>
         </table>
         </form>";
         redFlag("<small>If you forgot your password <a href='?action=FORGOT PASSWORD'>click here</a> or contact <a href='mailto:aikeji@emich.edu'>aikeji@emich.edu</a> for help.</small>");
   }

   function changePassword() {
      if (isset($_REQUEST['pwd1']) && isset($_REQUEST['pwd2'])) {
         $pwd = mysqlReady($_REQUEST['pwd']);
         $pwd1 = mysqlReady($_REQUEST['pwd1']);
         $pwd2 = mysqlReady($_REQUEST['pwd2']);
         $username = mysqlReady($_SESSION['username']);
         $r = process("select 1 from users where username=$username and pwd=password($pwd)");
         if (!is_array($r)) {
            redFlag("Current password and username not matched. You may retry.");
         }
         else if ($pwd1 != $pwd2) {
            redFlag("Password and confirmed password must match, retry!");
         }
         else {
            process("update users set pwd=password($pwd1) where username=".mysqlReady($_SESSION['username']));
            redFlag("<h4>Password changed. Please select the next operation.</h4>");
            show();
            return;
         }
      }
      echo "<form method='post'>
         <input type='hidden' name='action' value='CHANGEPASSWORD'>
         <table><tr><th colspan='2'>CHANGE PASSWORD</th></tr>
                <tr><td align='right'>Current Password:</td><td><input type='password' name='pwd' size='30'></td></tr>
                <tr><td align='right'>New Password:</td><td><input type='password' name='pwd1' size='30'></td></tr>
                <tr><td align='right'>Confirm New Password:</td><td><input type='password' name='pwd2' size='30'></td></tr>
                <tr><td colspan='2' align='center'><input type='submit' value='submit'>&nbsp;&nbsp;&nbsp;<a href='?'>quit</a>
                </td></tr>
         </table>
         </form>";
   }

   function signup() {
      if (isset($_REQUEST['username'])) {
         $username = normalize($_REQUEST['username']);
         if (($username=='')||(indexOf($username,'@')>=0)) {
            redFlag("Invalid my.emich username. Retry!");
         }
         else {
            $r = process("select email from users where username=".mysqlReady($username));
            if (is_array($r)) {
               redFlag("User already signed up. You may Login!");
            }
            else {
               $newPwd = substr(md5(rand().rand()), 0, 9);
               process("insert into users set pwd=password(".mysqlReady($newPwd)."), username=".mysqlReady($username).", email=".mysqlReady($username."@emich.edu"));
               mail($username."@emicgggh.edu", "Temporary signup password for The Document Repository Website",
"Hello,

You temporary password is: $newPwd
Please login to the Document Repository website and change it to your desired password.


Thanks.", "From: aikeji@emich.edu\r\nReply-To: aikeji@emich.edu");
            echo "Your temporary password has been mailed to ".$username."@emich.edu";
            return;
            }
         }
      }

      echo "<form method='post'>
         <input type='hidden' name='action' value='SIGNUP'>
         <table><tr><th colspan='2'>SIGNUP<br>Enter your my.emich username and then submit</th></tr>
                <tr><td align='right'>Username:</td><td><input type='text' name='username' size='30'></td></tr>
                <tr><td colspan='2' align='center'><input type='submit' value='SUBMIT'></td></tr>
         </table>
         </form>";
   }

   function forgotPassword() {
      if (isset($_REQUEST['username'])) {
         $username = normalize($_REQUEST['username']);
         $r = process("select email from users where username=".mysqlReady($username));
         if (!is_array($r)) {
            redFlag("No such user found. You may retry!");
         }
         else {
            $newPwd = substr(md5(rand().rand()), 0, 9);
            process("update users set pwd=password(".mysqlReady($newPwd).") where username=".mysqlReady($username));
            mail($r[0]['email'], "New Password from Textbook Adoption website",
"Hello,

You new password is: $newPwd
Please login to the Document Repository website and change it to your desired password.


Thanks.", "From: aikeji@emich.edu\r\nReply-To: aikeji@emich.edu");
            redFlag("Your new password has been mailed to ".$r[0]['email']);
            return;
         }
      }
      echo "<form method='post'>
         <input type='hidden' name='action' value='FORGOT PASSWORD'>
         <table><tr><th colspan='2'>FORGOT PASSWORD<br>Enter username and then submit</th></tr>
                <tr><td align='right'>Username:</td><td><input type='text' name='username' size='30'></td></tr>
                <tr><td colspan='2' align='center'><input type='submit' value='SUBMIT'></td></tr>
         </table>
         </form>";
   }

   function processLogin() {
      if (!(isset($_REQUEST['username'])&&isset($_REQUEST['password']))) {
         return "Username & password required";
      }
      $username = mysqlReady(normalize($_REQUEST['username']));
      $pwd = mysqlReady($_REQUEST['password']);
      if (!is_array(process("select 1 from users where username=$username and pwd=password($pwd)"))) {
         return "ERROR - Invalid username or password, retry";
      }
      $_SESSION['username'] = normalize($_REQUEST['username']);
   }
function mysqlReady($x) {
   if (get_magic_quotes_gpc()) {
      $x = stripslashes($x);
   }
//   if (is_numeric($x)) return $x;
   return "'".mysql_real_escape_string($x)."'";
}
function startsWith( $str, $sub ) {
   return ( substr( $str, 0, strlen( $sub ) ) === $sub );
}

function process($query, $con=null) {
   global $dbCon;
   if ($con==null)$con=$dbCon;
   $result = mysql_query($query,$con);
   if (mysql_error() != '') {
      return "ERROR - ".mysql_errno().": ".mysql_error().": query= $query";
   }
   // Does this query result in tuples?
   if ((indexOf(trim($query), "SELECT") == 0) ||
      (indexOf(trim($query), "DESCRIBE") == 0) ||
      (indexOf(trim($query), "SHOW") == 0)) {
      // Extract the rows of the result and make an array of the rows
      // Note: This may be a 2D array if each row has multiple columns
      // i.e.
      // if more than one column is in the result.
      $r=0;
      while ($myrow = mysql_fetch_array($result)) {
         // get one row at a time
         $rows[$r++] = $myrow;
      }
      if ($r == 0) $rows = null;
   }
   else $rows = null;
   return $rows;
}

function indexOf($src, $x) {
    if (strpos(strtoupper($src), strtoupper($x)) === false) return -1;
    else return strpos(strtoupper($src), strtoupper($x));
}

function connectToDb($username, $pwd, $hostname, $db) {
   // the @ before function name suppresses auto display of
   // PHP warning/error messages. I prefer to show my own messages
   $dbCon = @mysql_pconnect($hostname, $username, $pwd);
   @mysql_select_db($db, $dbCon);
   if (mysql_error() != '') return null;
   return $dbCon;
}

function redFlag($x) {
   echo "<font style='color:red'>$x</font>";
}

?>
</center>
