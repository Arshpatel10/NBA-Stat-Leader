<?php
require_once 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['HOST'];
$username = $_ENV['MYSQL_USERNAME'];
$password = $_ENV['MYSQL_PASSWORD'];
$dbname = $_ENV['MYSQL_DATABASE'];


$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "SELECT Fullname, Average_ppg FROM players ORDER BY Average_ppg DESC LIMIT 10";
$result = $conn->query($sql);

echo "<div class='containers'>";
    echo "<div class='card'><h3>Points</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Average_ppg"], 1) . " PPG</li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";

    $sql = "SELECT Fullname, Average_rpg FROM players ORDER BY Average_rpg DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Rebounds</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Average_rpg"], 1) . " RPG</li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";

    $sql = "SELECT Fullname, Average_apg FROM players ORDER BY Average_apg DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Assists</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Average_apg"], 1) . " APG</li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";

echo "</div>";

echo "<div class='containers'>";
    $sql = "SELECT Fullname, Average_spg FROM players ORDER BY Average_spg DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Steals</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Average_spg"], 1) . " SPG</li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";

    $sql = "SELECT Fullname, Average_bpg FROM players ORDER BY Average_bpg DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Blocks</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Average_bpg"], 1) . " BPG</li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";


echo "</div>";

$conn->close();
?>