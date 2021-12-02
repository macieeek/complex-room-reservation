<!DOCTYPE html>
<html lang="pl">
<head>

    <title>Strona główna</title>

    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" type="text/css" href="index.css">
    <link rel="stylesheet" type="text/css" href="nav.css">
    
</head>
<body>
    

<div id="container">

    <div id="nav">

        <?php include_once("nav.php"); ?>

    </div>
    
    <div id="content">

        <div id="welcome">
            Witaj na stronie głównej hotelu!<br>
            Aby przejść do rezerwacji pokoju kliknij w zakładkę 
            <b><a href="reserve.php" class="link">Zarezerwuj pokój</a></b>
        </div>
        
    </div>
    

</div>



</body>
</html>