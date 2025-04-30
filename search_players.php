<?php

require_once 'vendor/autoload.php';

// Load the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$servername = $_ENV['HOST'];
$username = $_ENV['MYSQL_USERNAME'];
$password = $_ENV['MYSQL_PASSWORD'];
$dbname = $_ENV['MYSQL_DATABASE'];

if (!isset($_GET['query'])) {
    exit;
}

$search = trim($_GET['query']);

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = $conn->real_escape_string($search);
$sql = "SELECT * FROM players WHERE Fullname LIKE '%$search%' ";
$result = $conn->query($sql);

echo "<table>";
echo "<tr>";
echo "<th>Player</th>";
echo "<th>Salary</th>";
echo "<th>Average PPG</th>";
echo "<th>Average RPG</th>";
echo "<th>Average APG</th>";
echo "<th>Average SPG</th>";
echo "<th>Average BPG</th>";
echo "<th>Total Points</th>";
echo "<th>Total Rebounds</th>";
echo "<th>Total Assists</th>";
echo "<th>Total Steals</th>";
echo "<th>Total Blocks</th>";
echo "</tr>";

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td style= width:10% >" . htmlspecialchars($row['Fullname']) .  "</td>";
        echo "<td>" . htmlspecialchars(number_format($row['Salary'])) . "</td>";
        echo "<td>" . htmlspecialchars($row['Average_ppg']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Average_rpg']) . "</td>";
        echo "<td>" . htmlspecialchars($row['Average_apg']) . "</td>";
        echo "<td>" . htmlspecialchars($row["Average_spg"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Average_bpg"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Total_points"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Total_rebounds"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Total_assists"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Total_steals"]) . "</td>";
        echo "<td>" . htmlspecialchars($row["Total_blocks"]) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<div>No players found</div>";
}

echo "</table>";
$conn->close();
?>