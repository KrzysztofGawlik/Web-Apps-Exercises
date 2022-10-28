<?php
require_once("navigator.php");
require("connection.php");

    $statusMessage = "";
    $resultArray = array();
    $query = "show tables";
    $result = $connection->query($query);

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            array_push($resultArray, $row);
        }
    }

    if(isset($_POST['submitForm'])){
        $finalQuery = "";
        // GET TABLE NAME - $dstTable
        if(!empty($_POST['tables'])){
            $dstTable = $_POST['tables'];
        } else {
            $statusMessage = "Brak wystarczających informacji! [tabela]";
        }
        // GET CHECKBOXES ARRAY - $columnsSelected
        $dstColumns = array();
        if(!empty($_POST['colName'])){
            foreach($_POST['colName'] as $selected){
                array_push($dstColumns, $selected);
            }
        } else {
            $dstColumns = array();
            $query = "show columns from $dstTable";
            $result = $connection->query($query);

            if($result->num_rows > 0){
                while($row = $result->fetch_assoc()){
                    array_push($dstColumns, $row['Field']);
                }
            }
        }
        // BUILD QUERY
        $finalQuery = "SELECT ";
        for($i = 0; $i < count($dstColumns); $i++){
            if($i == 0){ $finalQuery .= $dstColumns[$i]; }
            else{ $finalQuery .= ",".$dstColumns[$i];}
        }
        $finalQuery .= " FROM ".$dstTable;
        if(!empty($_POST['condition_1'])){
            $condition = $_POST['condition_1'];
            $operator = $_POST['condition_1_operator'];
            if(empty($_POST['condition_1_value'])){
                $finalQuery .= " WHERE ".$condition."=".$condition;
            } else if($operator == "like" || $operator == "not like") {
                $finalQuery .= " WHERE ".$condition." ".$operator."'%".$_POST['condition_1_value']."%'";
            } else {
                $finalQuery .= " WHERE ".$condition." ".$operator." '".$_POST['condition_1_value']."'";
            }
        }
        $finalArray = array();
        $result = $connection->query($finalQuery);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                array_push($finalArray, $row);
            }
        }
    }
    function selectOptionTable($resultArray){
        for($i = 0; $i < count($resultArray); $i++){
            $tableName = $resultArray[$i]['Tables_in_test'];
            if(!empty($_POST['tables']) && $tableName == $_POST['tables']) {
                echo "<option value='$tableName' selected>".$tableName."</option>";
            } else {
                echo "<option value='$tableName' >".$tableName."</option>";
            }
        }
    }
    function checkboxColumns($connection, $tab){
        $resultArray = array();
        $query = "show columns from $tab";
        $result = $connection->query($query);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                array_push($resultArray, $row);
            }
        }

        for($i = 0; $i < count($resultArray); $i++){
            $columnName = $resultArray[$i]['Field'];
            echo "<input type='checkbox' name='colName[]' value='$columnName'>".$columnName."<br>";
        }
    }
    function selectOptionColumns($connection, $tab){
        $resultArray = array();
        $query = "show columns from $tab";
        $result = $connection->query($query);

        if($result->num_rows > 0){
            while($row = $result->fetch_assoc()){
                array_push($resultArray, $row);
            }
        }

        // Display all options
        for($i = 0; $i < count($resultArray); $i++){
            $columnName = $resultArray[$i]['Field'];
            echo "<option value='$columnName'>".$columnName."</option>";
        }
    }
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Custom Filters</title>
        <link rel="stylesheet" type="text/css" href="style.css">
    </head>
    <body>
    <h1>Dostosuj widok za pomocą filtrów</h1>

    <form name="customFilters" method="post">
        <table class="customFiltersTable">
            <tr>
                <td>Tabela </td>
                <td colspan=3>
                    <select name="tables" id="tables" onchange="this.form.submit();">
                        <option></option>
                        <?php selectOptionTable($resultArray); ?>
                    </select>
                </td>
            </tr>
            <tr>
                <td>Kolumny</td>
                <td colspan=3>
                        <?php 
                        if(!empty($_POST['tables'])) {
                            checkboxColumns($connection, $_POST['tables']);
                        }
                        ?>
                </td>
            </tr>
            <tr>
                <td>Warunek</td>
                <td>
                    <select name="condition_1" id="condition_1">
                        <option></option>
                        <?php
                        selectOptionColumns($connection, $_POST['tables']);
                        ?>
                    </select>
                </td>
                <td>
                    <select style="width: 150px" name="condition_1_operator" id="condition_1_operator">
                        <option value="=">równe</option>
                        <option value="!=">różne</option>
                        <option value="<">mniejsze</option>
                        <option value="<=">mniejsze/równe</option>
                        <option value=">">większe</option>
                        <option value=">=">większe/równe</option>
                        <option value="like">zawiera</option>
                        <option value="not like">nie zawiera</option>
                    </select> 
                </td>
                <td>
                    <input type="text" name="condition_1_value" id="condition_1_value>" placeholder="Wartość..."></input>
                </td>
            </tr>
            <tr><td colspan=4><input type="submit" name="submitForm" value="Pokaż wyniki"></td></tr>
        </table>
    </form>
    <?php 
        echo $statusMessage;
        //echo "<br>".$finalQuery; 
        if(!empty($finalQuery)){
            echo "<table class='tableAutoFit'><tr><th>#</th>";
            for($i = 0; $i < count($dstColumns); $i++){
                echo "<th>".$dstColumns[$i]."</th>";
            }
            echo "</tr>";
            for($i = 0; $i < count($finalArray); $i++){
                echo "<tr><td>".($i+1)."</td>";
                for($j = 0; $j < count($dstColumns); $j++){
                    echo "<td>".$finalArray[$i][$dstColumns[$j]]."</td>";
                }
                echo "</tr>";
            }
        }
    ?>
    </body>
</html>