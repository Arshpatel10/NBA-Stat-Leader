import os
import requests
import json
import pandas as pd
import mysql.connector
from mysql.connector import Error
from dotenv import load_dotenv

load_dotenv()

#Environment variables for MySQL
HOST = os.getenv('HOST')
MYSQL_DATABASE = os.getenv('MYSQL_DATABASE')
MYSQL_USERNAME = os.getenv('MYSQL_USERNAME')
MYSQL_PASSWORD = os.getenv('MYSQL_PASSWORD')

RAPIDAPI_KEY = os.getenv('RAPIDAPI_KEY')

# Set up API request headers to authenticate requests
headers = {
    "x-rapidapi-host": "nba-api-free-data.p.rapidapi.com",
    'x-rapidapi-key': RAPIDAPI_KEY,
    "Content-Type": "application/json"
    }

url_player = "https://nba-api-free-data.p.rapidapi.com/nba-player-list"
url_stats = "https://nba-api-free-data.p.rapidapi.com/nba-player-stats"
player_info = []

def get_from_API(url,headers,params):
    try:
        response = requests.get(url, headers=headers, params=params)
        response.raise_for_status()
        return response.json()
    
    except requests.exceptions.HTTPError as http_error_message:
        print (f"❌ [HTTP ERROR]: {http_error_message}")

    except requests.exceptions.ConnectionError as connection_error_message:
        print (f"❌ [CONNECTION ERROR]: {connection_error_message}")

    except requests.exceptions.Timeout as timeout_error_message:
        print (f"❌ [TIMEOUT ERROR]: {timeout_error_message}")

    except requests.exceptions.RequestException as other_error_message:
        print (f"❌ [UNKNOWN ERROR]: {other_error_message}")

def process_player_ID(data):
    for player in data['response']['PlayerList']:
        playerID = player['id']
        salary = player.get('salary', 0)
        fullName = player['fullName']

        player_info.append({
            'Player ID' : playerID,
            'Salary': salary,
            'Fullname' : fullName
        })

def process_player_stats(data, playerID):
    
    categories = data.get('response', {}).get('stats', {}).get('categories', [])
    averages = categories[0].get('totals', []) if len(categories) > 0 else []

    points_avg = float(averages[17]) if averages != [] else 0.0
    assists_avg = float(averages[12]) if averages != [] else 0.0
    rebounds_avg = float(averages[11]) if averages != [] else 0.0
    steals_avg = float(averages[14]) if averages != [] else 0.0
    blocks_avg = float(averages[13]) if averages != [] else 0.0
    
    totals = categories[1].get('totals', []) if len(categories) > 1 else []

    points_total = float(totals[14]) if totals != [] else 0
    assists_total = float(totals[9]) if totals != [] else 0
    rebound_total = float(totals[8]) if totals != [] else 0
    steals_total = float(totals[11]) if totals != [] else 0
    blocks_total = float(totals[10]) if totals != [] else 0

    for player in player_info:
        if player['Player ID'] == playerID:
            player.update({
                'Average ppg' : points_avg,
                'Average apg' : assists_avg,
                'Average rpg' : rebounds_avg,
                'Average spg' : steals_avg,
                'Average bpg' : blocks_avg,
                'Total points' : points_total,
                'Total assists' : assists_total,
                'Total rebounds' : rebound_total,
                'Total steals' : steals_total,
                'Total blocks' : blocks_total
            })
    
def create_dataframe(player_info):
    df = pd.DataFrame(player_info)

    df.sort_values(by=['Fullname'], ascending=[True], inplace=True)

    df.reset_index(drop= True, inplace= True)

    df = df[['Player ID', 'Fullname', 'Salary', 'Average ppg', 'Average apg', 'Average rpg', 'Average spg', 'Average bpg', 'Total points', 'Total assists', 'Total rebounds', 'Total steals', 'Total blocks']]

    return df

def create_db_connection(host_name, user_name, user_password, db_name):
    """
    Establish a connection to the MySQL database
    """
    db_connection = None
    try:
        db_connection = mysql.connector.connect(
            host=host_name,
            user=user_name,
            passwd=user_password,
            database=db_name
        )
        print("MySQL Database connection successful ✅")

    except Error as e:
        print(f"❌ [DATABASE CONNECTION ERROR]: '{e}'")

    return db_connection
        
def create_table(db_connection):
    CREATE_TABLE_SQL_QUERY = """
    CREATE TABLE IF NOT EXISTS players (
        `Player_id` INT,
        `Fullname` VARCHAR(255),
        `Salary` INT,
        `Average_ppg` FLOAT,
        `Average_apg` FLOAT,
        `Average_rpg` FLOAT,
        `Average_spg` FLOAT,
        `Average_bpg` FLOAT,
        `Total_points` INT,
        `Total_assists` INT,
        `Total_rebounds` INT,
        `Total_steals` INT,
        `Total_blocks` INT,
        PRIMARY KEY (`Player_id`)
    );
    """

    try:
        cursor = db_connection.cursor()
        cursor.execute(CREATE_TABLE_SQL_QUERY)
        db_connection.commit()
        print("Table created successfully ✅")

    except Error as e:
        print(f"❌ [CREATING TABLE ERROR]: '{e}'")

def insert_into_table(db_connection,df):
    cursor = db_connection.cursor()

    INSERT_DATA_SQL_QUERY = """
    INSERT INTO players (`Player_id`,`Fullname`,`Salary`,`Average_ppg`,`Average_apg`,`Average_rpg`, `Average_spg`, `Average_bpg`, `Total_points`, `Total_assists`, `Total_rebounds`, `Total_steals`, `Total_blocks`)
    VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
    ON DUPLICATE KEY UPDATE
        `Fullname` = VALUES(`Fullname`),
        `Salary` = VALUES(`Salary`),
        `Average_ppg` = VALUES(`Average_ppg`),
        `Average_apg` = VALUES(`Average_apg`),
        `Average_rpg` = VALUES(`Average_rpg`),
        `Average_spg` = VALUES(`Average_spg`),
        `Average_bpg` = VALUES(`Average_bpg`),
        `Total_points` = VALUES(`Total_points`),
        `Total_assists` = VALUES(`Total_assists`),
        `Total_rebounds` = VALUES(`Total_rebounds`),
        `Total_steals` = VALUES(`Total_steals`),
        `Total_blocks` = VALUES(`Total_blocks`);
    """
    # Create a list of tuples from the dataframe values
    data_values_as_tuples = [tuple(x) for x in df.to_numpy()]

    # Execute the query
    cursor.executemany(INSERT_DATA_SQL_QUERY, data_values_as_tuples)
    db_connection.commit()
    print("Data inserted or updated successfully")

def run_data_pipeline():
    check_rate_limits()

    for i in range(1,31):
        querystring = {"teamid": str(i)}
        response = get_from_API(url_player,headers,querystring)
        process_player_ID(response)


    for player in player_info:
        querystring = {"playerid": str(player['Player ID'])}
        response = get_from_API(url_stats,headers,querystring)
        process_player_stats(response, player['Player ID'])


    df = create_dataframe(player_info)
    

    db_connection = create_db_connection(HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE)
    

    # If connection is successful, proceed with creating table and inserting data
    if db_connection is not None:
        create_table(db_connection)  
        df = create_dataframe(player_info) 
        insert_into_table(db_connection, df)

def check_rate_limits():
    """
    Check the API quota allocated to your account
    """
    response = requests.get(url_player, headers=headers)
    response.raise_for_status()

    daily_limits = response.headers.get('x-ratelimit-requests-limit')
    daily_remaining = response.headers.get('x-ratelimit-requests-remaining')
    calls_per_min_allowed = response.headers.get('X-RateLimit-Limit')
    calls_per_min_remaining = response.headers.get('X-RateLimit-Remaining')
    
    rate_limits = {
        'daily_limit': daily_limits,
        'daily_remaining': daily_remaining,
        'minute_limit': calls_per_min_allowed,
        'minute_remaining': calls_per_min_remaining
    }

    print(rate_limits)

run_data_pipeline()


 