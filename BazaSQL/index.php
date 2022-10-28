<?php
    require_once("navigator.php");
    require("connection.php");

    $fname = "";
    $email = "";
    $statusMessage = "";
    if(isset($_POST["submit"])){
        $fname = $_POST['fname'];
        $email = $_POST['email'];

        $query = "insert into subscribers(fname, email) values ('$fname', '$email')";
        $statement = $connection->prepare($query);
        if($statement->execute()){
            $statusMessage = "Pomyślnie dodano użytkownika!";
            $fname = "";
            $email = "";
        } else {
            $statusMessage = "Nie udało się dodać użytkownika...";
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register Subscriber</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Dodaj użytkownika</h1>
    <form name="add_user" method="post">
    Imię:<br>
    <input type="text" placeholder="Imię" name="fname" required><br><br>
    Email:<br>
    <input type="email" placeholder="Email" name="email" required><br><br>
    <br>
    <input type="submit" value="Dodaj" name="submit">
    <?php echo $statusMessage; ?>
    </form>
    <br><br>
</body>
</html>