<?php
    require_once("navigator.php");
    require("connection.php");

    $statusMessagge = "";
    $resultArray = array();
    $query = "select id,fname,email from subscribers";
    $result = $connection->query($query);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            array_push($resultArray, $row);
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Subscribers</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Wyświetl użytkowników</h1>
    <h3>
        Delete - powoduje usunięcie użytkownika oraz uruchominie wyzwalacza po usunięciu.<br>
        Edit - po edycji użytkownika zostanie uruchomiony wyzwalacz
    </h3>
    <table>
        <tr>
            <th>Lp.</th>
            <th>Imię</th>
            <th>Email</th>
            <th colspan=2>Akcja</th>
        </tr>

        <?php for($i = 0; $i < count($resultArray); $i++) { ?>
            <tr>
                <td><?php echo $i+1 ?></td>
                <td><?php echo $resultArray[$i]['fname'] ?></td>
                <td><?php echo $resultArray[$i]['email'] ?></td>

                    <td width=100px><a href="
                    subscriber_edit.php?id=<?php echo $resultArray[$i]["id"] ?>
                        ">Edytuj</a></td>

                    <td width=100px><a href="
                    subscriber_del.php?id=<?php echo $resultArray[$i]["id"] ?>
                        ">Usuń</a></td>
            </tr>
        <?php } ?>
    </table>
</body>
</html>