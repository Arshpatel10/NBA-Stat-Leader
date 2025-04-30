<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NBA Stat Leaders</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        NBA Stat Leaders
    </header>
    
    <div class="intro">
        <h2> Welcome to NBA Stat Leaders page</h2>
        <h3 style="color:rgba(61, 71, 94, 0.832)"> This page displays the most current NBA player data. You may choose to view the regular season carrer leaders among active players in each major stastical category by either average or totals, or you can choose to search for an individual player and view their data.</h3>
        <h3 style="color:rgba(61, 71, 94, 0.832)">The stats update every morning at 7:00 A.M EST</h3>
    </div>
    

    <div class="button b2" id="button-10">
        <input type="checkbox" class="checkbox" />
        <div class="knobs">
            <span style="top:15px" >Average</span>
        </div>
        <div class="layer"></div>
    </div>

    

    <div class="temp">
    </div>

    <section id="current">
        <h2>Search for player</h2>
        <h4>Manually search for an active NBA player from the 2024-2025 season</h4>
        <div class="search-box">
            <input type="text" id="searchInput" placeholder="Search players..." autocomplete="off">
        </div>
        <div class="search-results">
            
            <div id="searchResults" class="search-results-box"></div>
        </div>
       
    </section>
    
    
    
    
    <script>
        document.addEventListener("DOMContentLoaded", function (){
            const toggleButton = document.querySelector("#button-10 .checkbox");
            const statsContainer = document.querySelector(".temp")

            function loadStats(type) {
                const url = type === "average" ? "fetch_points.php" : "fetch_totals.php";
                fetch(url)
                    .then(response => response.text())
                    .then(data => {
                        statsContainer.innerHTML = data;
                    })
                    .catch(error => console.error("Error fetching stats:",error ));
            }
            loadStats("average");
            toggleButton.addEventListener("change", function(){
                loadStats(this.checked ? "total" : "average");
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
            const input = document.getElementById("searchInput");
            const results = document.getElementById("searchResults");

            input.addEventListener("keyup", function () {
                const query = this.value.trim();

                if (query.length === 0) {
                    results.innerHTML = "";
                    return;
                }

                fetch("search_players.php?query=" + encodeURIComponent(query))
                    .then(response => response.text())
                    .then(data => {
                        results.innerHTML = data;
                    })
                    .catch(err => console.error("Error fetching search results:", err));
            });
        });
    </script>


</body>
</html>
