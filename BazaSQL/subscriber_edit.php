<?php
    require_once("navigator.php");
    require("connection.php");

    $id = intval($_GET["id"]);
    $query = "select fname,email from subscribers where id='$id'";
    $result = $connection->query($query);
    if($result->num_rows > 0){
        $row = $result->fetch_assoc();
    }

    $fname = $row["fname"];
    $email = $row["email"];
    $statusMessage = "";
    if(isset($_POST["submit"])){
        $fname = $_POST['fname'];
        $email = $_POST['email'];

        $query = "update subscribers set fname='$fname', email='$email' where id='$id' ";
        $statement = $connection->prepare($query);
        if($statement->execute()){
            $statusMessage = "Pomyślnie zmodyfikowano użytkownika!";
            $fname = "";
            $email = "";
        } else {
            $statusMessage = "Nie udało się zmodyfikować użytkownika...";
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Subscriber</title>
    <meta charset="utf-8">
</head>
<body>
    <h1>Edytuj użytkownika</h1>
    <form name="add_user" method="post">
    Imię:<br>
    <input type="text" placeholder="Imię" name="fname" value="<?php echo $fname; ?>" required><br><br>
    Email:<br>
    <input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required><br><br>
    <br>
    <input type="submit" value="Zaktualizuj" name="submit">
    <?php echo $statusMessage; ?>
    </form>
    <br><br>
</body>
</html>