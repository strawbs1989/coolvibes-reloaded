<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="bootstrap.css">
    <title>Requests</title>
</head>
<body>

    <div class="container">
        <div class="row">
            <div class="col-lg-6 m-auto">
                <div class="card mt-5">
                    <div class="card-title">
                        <h2 class="text-center py-2"> Track Information </h2>
                        <hr>
                        <?php 
                            $Msg = "";
                            if(isset($_GET['error']))
                            {
                                $Msg = " Please Fill in the Blanks ";
                                echo '<div class="alert alert-danger">'.$Msg.'</div>';
                            }

                            if(isset($_GET['success']))
                            {
                                $Msg = " Request successful and should play shortly ";
                                echo '<div class="alert alert-success">'.$Msg.'</div>';
                            }
                        
                        ?>
                    </div>
                    <div class="card-body">
                        <form action="process.php" method="post">
                            <input type="text" name="YName" placeholder="Your Name" class="form-control mb-2">
                            <input type="track" name="Track" placeholder="Track Name" class="form-control mb-2">
                            <input type="text" name="Artist" placeholder="Artist Name" class="form-control mb-2">
                            <textarea name="msg" class="form-control mb-2" placeholder="Dedication Message"></textarea>
                            <button class="btn btn-success" name="btn-send"> Dedicate it! </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>