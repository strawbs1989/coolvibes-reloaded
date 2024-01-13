<?php 

    if(isset($_POST['btn-send']))
    {
       $YourName = $_POST['YName'];
       $Email = $_POST['Track'];
       $Subject = $_POST['Subject'];
       $Msg = $_POST['msg'];

       if(empty($UserName) || empty($Email) || empty($Subject) || empty($Msg))
       {
           header('location:index.php?error');
       }
       else
       {
           $to = "coolvibes1989@gmail.com";
           $headers = "From: requests@requests.cu.ma\r\n";



           if(mail($to,$Subject,$Msg,$Email) ) {
			   echo "The Email Has Been Sent!";
	   } else { 
	   echo "The email has failed!";
	   }
           {
               header("location:index.php?success");
           }
       }
    }
    else
    {
        header("location:index.php");
    }
?>