<?php 
session_start();
//init
if(isset($_POST['to'])){$_SESSION['to']=$_POST['to'];}
if(isset($_POST['msg'])){$_SESSION['msg']=$_POST['msg'];}
if(isset($_POST['sbj'])){$_SESSION['sbj']=$_POST['sbj'];}
if(isset($_POST['frm'])){$_SESSION['frm']=$_POST['frm'];}
//unset($_SESSION['filez']);
if(isset($_POST['submitF'])){
    //clear
if($_POST['submitF']=='clear'){
    unset($_SESSION['filez']);
    $folder = 'uploaded';
    $files = glob($folder . '/*');
    foreach($files as $file){
        if(is_file($file)){
            unlink($file);
        }
    }
}else{
    //upload files
if(count($_FILES['upload']['name'])>0){

    for($i=0; $i<count($_FILES['upload']['name']); $i++) {
        //Get the temp file path
        $tmpFilePath = $_FILES['upload']['tmp_name'][$i];
        if($tmpFilePath != ""){
            $shortname = $_FILES['upload']['name'][$i];
            $filePath = "uploaded/" . date('d-m-Y-H-i-s').'-'.$_FILES['upload']['name'][$i];
            $fileType = $_FILES['upload']['type'][$i];
            $fileSize=$_FILES['upload']['size'][$i];
            if(move_uploaded_file($tmpFilePath, $filePath)) {
                $_SESSION['filez'][]= array($shortname,$filePath,$fileType,$fileSize);
            }
          }
    }
    unset($_FILES);    
}
}
}
//style
?>
<html>
    <head>
    <style>
       
body{
    background-color: #444;
color: #ddd;
}    
input,textarea,select {
            background-color: #2C3E50;
            border: none;
            color: #95A5A6;
        }
        .sbm::-webkit-file-upload-button {
            visibility: hidden;
        }

        .sbm::before {
            content: 'select file';
            color: #95A5A6;
            border-style: none;
        }
        .invalid { border-color: red!important; border: 1px solid;}
  #error { color: red!important; }
    </style>
    </head>
    <body>
    
<?php
//init 2
if(!isset($_POST['cap'])){
    $_POST['cap']='';
}
if(!isset($_POST['frm'])){
    $_SESSION['frm']='noreply';
}
//validations
if ((!isset($_SESSION['to']))||(!filter_var($_SESSION['to'], FILTER_VALIDATE_EMAIL))){echo "<div id='error'>email wrong!</div>";}else{ 
if ((!isset($_SESSION['sbj']))||((strlen($_SESSION['sbj'])<1))){echo "<div id='error'>subject must be defined!</div>";}else{ 
if ($_SESSION['random_code']!==$_POST['cap']){echo "<div id='error'>capcha wrong!</div></br>";}else{
if((isset($_POST['action']))&&($_POST['action']=='send')){
    $separator = md5(time());
    $eol = "\r\n";
   // if(!isset($_POST['sbj'])){$_POST['sbj']='object not set';}
    // $headers="From: noreply\r\n";
    // $headers.="Reply-to: noreply\r\n";
    // $headers.="Content-type: text/plain\r\n";
    // $headers.="";
//headers
    $from=$_SESSION['frm'];
    $from = '=?utf-8?B?'.base64_encode($from).'?=';
    // main header (multipart mandatory)
    // $headers = "From: name <test@test.com>" . $eol;
    $headers = "From: ".$from . $eol;
    //if(isset($_SESSION('frm')))
    $headers .= "MIME-Version: 1.0" . $eol;
    $headers .= "Content-Type: multipart/mixed; boundary=\"" . $separator . "\"" . $eol;
    $headers .= "Content-Transfer-Encoding: 7bit" . $eol;
    $headers .= "This is a MIME encoded message." . $eol;
    $mailto = $_SESSION['to'];
    $subject = $_SESSION['sbj'];
    $subject = '=?utf-8?B?'.base64_encode($subject).'?=';
    $message = $_SESSION['msg'];
    //text
    $body = "--" . $separator . $eol;
    // $body .= "Content-Type: text/plain; charset=\"iso-8859-1\"" . $eol;
    $body .= "Content-Type: text/plain; charset=\"utf-8\"" . $eol;
    $body .= "Content-Transfer-Encoding: 8bit" . $eol;
    $body .= $message . $eol.$eol;
//files
   if(isset( $_SESSION['filez'])){
        foreach( $_SESSION['filez'] as $file){
       // echo"adding file ".$file[1];
    $content = file_get_contents($file[1]);
    $content = chunk_split(base64_encode($content));
    $body .= "--" . $separator . $eol;
    $body .= "Content-Type: application/octet-stream; name=\"" .$file[0]. "\"" . $eol;
    $body .= "Content-Description: ".$file[0]. $eol;
    $body .= "Content-Disposition: attachment;" . $eol;
    $body .= " filename=\"".($file[0])."\"; size=".($file[3]).";" .$eol;
    $body .= "Content-Transfer-Encoding: base64" . $eol;    
    $body .=  $eol.$content. $eol . $eol;
    }}
    
    $body .= "--" . $separator . "--";
//sending
    if(mail($mailto,$subject,$body,$headers)){echo"Sent succesefully to ".$mailto."!";
        unset($_SESSION['filez']);
        $folder = 'uploaded';
        $files = glob($folder . '/*');
        foreach($files as $file){
            if(is_file($file)){
                unlink($file);
            }
        }
        //unset($_POST['unset']);
       ?>  <form method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
       <input class="sbm" type="submit" name='action' value="back">
   </form>
   <?php 
        exit;
    }else{echo"Error sending mail!";}
    
    // exit;
}}}}
//init 33
if(!isset($_SESSION['to'])){
    $_SESSION['to']='anon@somewhere.smth';
}
if(!isset($_SESSION['msg'])){
    $_SESSION['msg']='type something here';
}
if(!isset($_SESSION['sbj'])){
    $_SESSION['sbj']='';
}
//file-manipulations form
?><form  action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
<input class="sbm" type="submit" value="clear" name="submitF">
<?php
//file list show
if(isset( $_SESSION['filez'])){
    echo "Uploaded:</br>";
    echo "<ul>";
    foreach( $_SESSION['filez'] as $file){
        echo "<li>".$file[0]."</li>";
        // var_dump($file);
    }
    echo "</ul>";
}
?>
<input class="sbm" type="file" multiple="multiple" name="upload[]" id="upload"> 
 <input class="sbm" type="submit" value="Upload file" name="submitF">
 </form>
//main form
<form method="post" enctype='multipart/form-data' action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
<input type="text" id="frm" name="frm"value="<?php echo $_SESSION['frm'];?>">
    <label id = "frml" for="head">from</label><br>    
<input type="text" id="rec" name="to"value="<?php echo $_SESSION['to'];?>">
    <label id = "recl" for="head">to</label><br>
    <input type="text" name="sbj"value="<?php echo $_SESSION['sbj'];?>">
    <label for="head">subject</label><br>
    <textarea rows="20" cols="50" name="msg"><?php echo $_SESSION['msg'];?></textarea></br>
    <img src="capcha.php"> <label for="head">send empty to refresh</label></br>
   
    <input type="text" name="cap" value="">
    <input class="sbm" type="submit" name='action' value="send">
</form>
<script>
    rec.onblur = function() {
  if (!rec.value.includes('@')) { // не email
    rec.classList.add('invalid');
    //error.innerHTML = 'Пожалуйста, введите правильный email.'
    document.getElementById("recl").innerHTML = "receiver not set";
  }
};

rec.onfocus = function() {
  if (this.classList.contains('invalid')) {
    this.classList.remove('invalid');
    //error.innerHTML = "";
    document.getElementById("recl").innerHTML = "receiver";
  }
};
</script>
    </body>
</html>
