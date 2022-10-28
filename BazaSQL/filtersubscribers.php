<?php
    require_once("navigator.php");
    require("connection.php");

    $statusMessage = "";

    if(isset($_GET['action'])){
        $action = intval($_GET['action']);
    } else {
        $action = 0;
    }
    
    switch($action){
        case 1: {
            $statusMessage = "Tabela: Dodani";
            $query = "select subscriber_name, date_added from audit_subscribers where action_performed='Insert a new subscriber'";
            break;
        }
        case 2: {
            $statusMessage = "Tabela: Usunięci";
            $query = "select subscriber_name, date_added from audit_subscribers where action_performed='Deleted a subscriber'";
            break;
        }
        case 3: {
            $statusMessage = "Tabela: Edytowani";
            $query = "select subscriber_name, date_added from audit_subscribers where action_performed='Updated a subscriber'";
            break;
        }
        case 4: {
            $statusMessage = "Tabela: Dodani/usunięci";
            $query = "select * from audit_subscribers where subscriber_name in (select subscriber_name from audit_subscribers 
            where action_performed='Deleted a subscriber') and action_performed in 
            ('Deleted a subscriber', 'Insert a new subscriber')";
            break;
        }
        case 5: {
            $statusMessage = "Tabela: Aktywni";
            $query = "select subscriber_name from audit_subscribers where action_performed=
            'Insert a new subscriber' and subscriber_name not in (select subscriber_name 
            from audit_subscribers where action_performed='Deleted a subscriber');";
            break;
        }
        default: {
            $statusMessage = "Tabela: <i>wybierz filtr</i>";
            $query = "select * from audit_subscribers where id = -1";
        }
    }

    $resultArray = array();
    $result = $connection->query($query);
    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            array_push($resultArray, $row);
        }
    }

    function twoCols($resultArray){
        echo "<tr><th>Lp.</th><th>Imię</th>";
        for($i = 0; $i < count($resultArray); $i++){
            echo "<tr><td>".($i+1)."</td>
                    <td>".$resultArray[$i]['subscriber_name']."</td>";
        }
        echo "</tr>";
    }
    function threeCols($resultArray){
        echo "<tr><th>Lp.</th><th>Imię</th><th>Data</th>";
        for($i = 0; $i < count($resultArray); $i++){
            echo "<tr><td>".($i+1)."</td>
                    <td>".$resultArray[$i]['subscriber_name']."</td>
                    <td>".$resultArray[$i]['date_added']."</td>";
        }
        echo "</tr>";
    }
    function fourCols($resultArray){
        echo "<tr><th>Lp.</th><th>Imię</th><th>Dodano</th><th>Usunięto</th>";
        for($i = 0; $i < count($resultArray); $i++){
            $userName = "";

            // Przypisz userName jeśli dodano użytkownika
            if($resultArray[$i]['action_performed'] == "Insert a new subscriber"){
                $userName = $resultArray[$i]['subscriber_name'];
            }

            // Ignoruj jeśli nie znaleziono insert
            if($userName == "") continue; 

            // Znajdz datę usunięcia analizując tabelę jeszcze raz
            for($j = 0; $j < count($resultArray); $j++){
                if($resultArray[$j]['subscriber_name'] == $userName && $resultArray[$j]['action_performed'] == "Deleted a subscriber"){
                    $dateDeleted = $resultArray[$j]['date_added']; break;
                }
            }

            // Wyświetl wynik
            echo "<tr><td>".($i+1)."</td>
                    <td>".$resultArray[$i]['subscriber_name']."</td>
                    <td>".$resultArray[$i]['date_added']."</td>
                    <td>".$dateDeleted."</td>";
        }
        echo "</tr>";
    }

?>
<!DOCTYPE html>
<html>
    <head>
        <title>Filter Subscribers</title>
        <meta charset="utf-8">
        <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Filtruj użytkowników</h1>
    <h3>
        [1] Dodani - widok wyświetlający nazwę użytkowników oraz datę ich dodania<br>
        [2] Usunięci - widok wyświetlający nazwę użytkowników oraz datę ich usunięcia<br> 
        [3] Edytowani - widok wyświetlający nazwę użytkowników oraz datę ich edycji<br>
        [4] Dodani/usunięci - widok wyświetlający nazwę już usuniętych użytkowników oraz daty ich dodania i usunięcia<br>
        [5] Aktywni - widok wyświetlający tylko istniejących użytkowników (bez korzystania z tabelki subscribers)
    </h3>
    <br>

    <table id="menuTable"><tr>
    <td><a href="filtersubscribers.php?action=1">Dodani</a></td>
    <td><a href="filtersubscribers.php?action=2">Usunięci</a></td>
    <td><a href="filtersubscribers.php?action=3">Edytowani</a></td>
    <!-- jedna kolumna więcej -->
    <td><a href="filtersubscribers.php?action=4">Dodani/usunięci</a></td>
    <!-- jedna kolumna mniej -->
    <td><a href="filtersubscribers.php?action=5">Aktywni</a></td>
    </tr></table>

    <br>
    <h4><?php echo $statusMessage; ?></h4>
    <!-- Stworzyć w PHP -->
    <form name="viewForm" method="post">
        <table>
    <?php
        switch($action){
            case 1: threeCols($resultArray); break;
            case 2: threeCols($resultArray); break;
            case 3: threeCols($resultArray); break;
            case 4: fourCols($resultArray); break;
            case 5: twoCols($resultArray); break;
            default: break;
        }
    ?>
        </table>
    </form>