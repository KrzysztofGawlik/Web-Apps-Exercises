<?php
    require("connection.php");

    $id = intval($_GET["id"]);
    if($id){
        $query = "delete from subscribers where id = '$id'";
        $statement = $connection->prepare($query);
        $statement->execute();
        header("Location: viewsubscribers.php");
        exit;
    }
?>