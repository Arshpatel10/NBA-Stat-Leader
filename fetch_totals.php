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

$sql = "SELECT Fullname, Total_points FROM players ORDER BY Total_points DESC LIMIT 10";
$result = $conn->query($sql);

echo "<div class='containers'>";
echo "<div class='card'><h3>Points</h3><ul>";

if ($result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
        echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Total_points"]) . " </li>";
        $rank++;
    }
} else {
    echo "<li>No data available</li>";
}

echo "</ul></div>";

$sql = "SELECT Fullname, Total_rebounds FROM players ORDER BY Total_rebounds DESC LIMIT 10";
$result = $conn->query($sql);
echo "<div class='card'><h3>Rebounds</h3><ul>";

if ($result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
        echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Total_rebounds"]) . " </li>";
        $rank++;
    }
} else {
    echo "<li>No data available</li>";
}

echo "</ul></div>";


$sql = "SELECT Fullname, Total_assists FROM players ORDER BY Total_assists DESC LIMIT 10";
$result = $conn->query($sql);
echo "<div class='card'><h3>Assists</h3><ul>";

if ($result->num_rows > 0) {
    $rank = 1;
    while ($row = $result->fetch_assoc()) {
        $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
        echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Total_assists"]) . " </li>";
        $rank++;
    }
} else {
    echo "<li>No data available</li>";
}

echo "</ul></div>";

echo "</div>";

echo "<div class='containers'>";
    $sql = "SELECT Fullname, Total_steals FROM players ORDER BY Total_steals DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Steals</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Total_steals"]) . " </li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";

    $sql = "SELECT Fullname, Total_blocks FROM players ORDER BY Total_blocks DESC LIMIT 10";
    $result = $conn->query($sql);

    echo "<div class='card'><h3>Blocks</h3><ul>";

    if ($result->num_rows > 0) {
        $rank = 1;
        while ($row = $result->fetch_assoc()) {
            $class = ($rank == 1) ? "first" : (($rank == 2) ? "second" : (($rank == 3) ? "third" : "regular"));
            echo "<li class='$class'>$rank. " . $row["Fullname"] . " - " . number_format($row["Total_blocks"]) . " </li>";
            $rank++;
        }
    } else {
        echo "<li>No data available</li>";
    }

    echo "</ul></div>";


echo "</div>";

$conn->close();
?>