<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");

session_start();

//
//  Yhdistetään tietokantaan
//
function connect_to_database() {
    
    // Staattinen muuttuja ei katoa vaikka funktiosta palataankin.
    // Alustetaan muuttuja false-arvolla.
    static $connection = false;
    
    if($connection != false)
        return $connection;
        
        // Jos yhteyttä ei ole vielä avattu, avataan se.
        // Jos yhteyden avaaminen ei onnistu, heitetään
        // poikkeus, joten se pitää napata.
        try {
            $connection = new PDO("mysql:host=db1.n.kapsi.fi;dbname=magetsu","magetsu","FgQiYWtkRX");
        } catch (PDOException $e) {
            exit("Tietokantavirhe: " . $e->getMessage());
        }
        
        $connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $connection->exec("set names utf8");
        
        if (get_logging()) {
            
            printlog("Yhteys tietokantaan saatu<br>");
            
        }
        
        return $connection;
}

//
//   Tarkistetaan tietokanta
//
function is_database_ok($connection) {
    
    $sql = 'SHOW TABLES';
    $query = $connection->prepare($sql);
    $query->execute();
    if ($query->rowCount()!=0) {
        
        return true;

    } else {
        
        return false;
    }
}

//
//   Tarkistetaan tietokanta
//
function is_library_ok($connection) {
    
    if (get_logging()) {
        echo "Tarkistetaan tietokanta<br>";
    }
    
    $book_table = $_SESSION["user_id"].'book';
    $dvd_table = $_SESSION["user_id"].'dvd';
    
    $sql = 'SHOW TABLES';
    $query = $connection->prepare($sql);
    $query->execute();
    $array = $query->fetchAll(PDO::FETCH_COLUMN, 0);
    
    if (in_array($book_table,$array) && in_array($dvd_table,$array)) {
        
        IF (get_logging()) {
            echo "Tietokanta OK<br>";
        }
        return true;
    }
    
    if (get_logging()) {
        echo "Tietokantaa ei ole<br>";
    }
    
    return false;
}

//
// Luodaan kirjastotaulut tietokantaan
//
function create_library_tables($a_connection) {
    
    IF (get_logging()) {
        echo "Luodaan tietokanta<br>";
    }
    
    $table = $_SESSION["user_id"].'book';
    
    $sql = "DROP TABLE IF EXISTS $table";
    $query = $a_connection->prepare($sql);
    $query->execute();
    
    $sql="CREATE TABLE IF NOT EXISTS magetsu.$table ( surname VARCHAR(15) NOT NULL,
													forename VARCHAR(20) NOT NULL,
													bookname VARCHAR(100) NOT NULL,
													pubyear INT NOT NULL,
													isbn VARCHAR(20) NOT NULL,
													orgname VARCHAR(100) NOT NULL,
													orgyear INT NOT NULL,
													other VARCHAR(1000) NOT NULL)";
    
    $query = $a_connection->prepare($sql);
    
    IF (get_logging()) {
        echo "Luodaan kirjaston kirjat<br>";
    }
    
    $query->execute();
    
    $table = $_SESSION["user_id"].'dvd';
    $sql = "DROP TABLE IF EXISTS $table";
    $query = $a_connection->prepare($sql);
    $query->execute();
    
    $sql="CREATE TABLE IF NOT EXISTS magetsu.$table ( name VARCHAR(100) NOT NULL,
												   year INT NOT NULL,
												   orgname VARCHAR(100) NOT NULL,
												   barcode VARCHAR(20) NOT NULL,
												   director VARCHAR(100) NOT NULL,
												   music VARCHAR(100) NOT NULL,
												   other VARCHAR(1000) NOT NULL)";
    
    $query = $a_connection->prepare($sql);
    
    IF (get_logging()) {
        echo "Luodaan kirjaston DVD:t<br>";
    }
    
    $query->execute();
    
    IF (get_logging()) {
        echo "Luotiin kirjasto<br>";
    }
}

//
// Tietokanta t�ytet��n tiedolla joka on saatu XML:st�
//
function populate_database($a_connection, $a_library) {
    
    $i=0;
    
    $table = 'magetsu.'.$_SESSION["user_id"].'book';
    
    $sql = "INSERT INTO $table (surname, forename, bookname, pubyear, isbn, orgname, orgyear, other) values ( ?, ?, ?, ?, ?, ?, ?, ?)";
    
    while(is_object($books = $a_library->getElementsByTagName("book")->item($i))) {
        
        $array = array();
        
        foreach($books->childNodes as $nodename) {
            
            if ($nodename->nodeType==XML_ELEMENT_NODE) {
                
                array_push($array,$nodename->nodeValue);
                
            }
        }
        
        If (get_logging()) {
            
            echo '<br>Suoritetaan INSERT INTO '.$table.' ('.$array[0].', '.$array[1].', '.$array[2].', '.$array[3].', '.$array[4].', '.$array[5].', '.$array[6].', '.$array[7].')<br>';
            
        }
        
        $query = $a_connection->prepare($sql);
        $query->execute([$array[0],$array[1],$array[2],$array[3],$array[4],$array[5],$array[6],$array[7]]);
        $i++;
    }
    
    $i=0;
    $table = 'magetsu.'.$_SESSION["user_id"].'dvd';
    $sql = "INSERT INTO $table (name, year, orgname, barcode, director, music, other) values(?, ?, ?, ?, ?, ?, ?)";
    
    while(is_object($dvds = $a_library->getElementsByTagName("dvd")->item($i))) {
        
        $array = array();
        
        foreach($dvds->childNodes as $nodename) {
            
            if ($nodename->nodeType==XML_ELEMENT_NODE) {
                
                array_push($array,$nodename->nodeValue);
            }
        }
        
        If (get_logging()) {
            
            echo '<br>Suoritetaan INSERT INTO '.$table.' ('.$array[0].', '.$array[1].', '.$array[2].', '.$array[3].', '.$array[4].', '.$array[5].', '.$array[6].')<br>';
        }
        
        $query = $a_connection->prepare($sql);
        $query->execute([$array[0],$array[1],$array[2],$array[3],$array[4],$array[5],$array[6]]);
        $i++;
    }
    
    IF (get_logging()) {
        echo "Kirjasto lisätty onnistuneesti tietokantaan<br>";
    }
    
}

//
// Luodaan käyttäjätaulu tietokantaan
//
function create_accounttable() {
    
    $connection = connect_to_database();
    
    $sql="CREATE TABLE IF NOT EXISTS magetsu.accounts ( user_id int(11) NOT NULL auto_increment,
													   username VARCHAR(20) NOT NULL,
													   password VARCHAR(40) NOT NULL,
													   PRIMARY KEY (user_id),
													   UNIQUE KEY username (username))";
    
    $query = $connection->prepare($sql);
    $query->execute();
}

//
//   Lisätään uusi käyttäjä tietokantaan
//
function add_account($username, $password) {
    
    $connection = connect_to_database();
    
    $account = true;
    
    try {
        
        $user = array($username, $password);
        $sql = "INSERT INTO accounts (username, password) VALUES (?, ?)";
        
        $query = $connection->prepare($sql);
        
        $query->execute($user);
    } catch(Exception $e) {
        
        /*** check if the username already exists ***/
        if( $e->getCode() == 23000) {
            
            $account = false;
            echo 'Käyttäjänimi on jo käytössä<br>';
        } else {
            
            $account = false;
            /*** if we are here, something has gone wrong with the database ***/
            echo 'We are unable to process your request. Please try again later';
        }
    }
    return $account;
}

//
// Haetaan kirjojen tietokanta
//
function fetch_book_sql($searchstring) {
    
    $yhteys = connect_to_database();
    
    if (get_logging()) {
        echo "Haetaan kirja-SQL<br>";
    }
    
    $table_book = 'magetsu.'.$_SESSION["user_id"].'book';
    
    if ($searchstring) {
        
        // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
        $sql = "SELECT * FROM $table_book WHERE surname LIKE '%";
        $sql .= $searchstring."%' OR forename LIKE '%";
        $sql .= $searchstring."%' OR bookname LIKE '%";
        $sql .= $searchstring."%' OR pubyear LIKE '%";
        $sql .= $searchstring."%' OR isbn LIKE '%";
        $sql .= $searchstring."%' OR orgname LIKE '%";
        $sql .= $searchstring."%' OR orgyear LIKE '%";
        $sql .= $searchstring."%' OR other LIKE '%";
        $sql .= $searchstring."%'";
        
    } else {
        
        $sql = "SELECT * FROM $table_book ORDER BY surname";
        
    }
    
    if (get_logging()) {
        
        echo "Lauseke on ".$sql."<br>";
    }
    
    $query = $yhteys->prepare($sql);
    $query->execute();
    $array = $query->fetchAll();
    
    if (get_logging()) {
        
        foreach ($array as $arrays) {
            echo $arrays['forename']." ".$arrays['surname']." ".$arrays['bookname'].'<br>';
        }
    }
    
    return $array;
}

//
//  Lisätään kirja tietokantaan
//
function insert_book($an_array) {
    
    $connection = connect_to_database();
    
    $table = 'magetsu.'.$_SESSION["user_id"].'book';
    
    $sql = "INSERT INTO $table (surname, forename, bookname, pubyear, isbn, orgname, orgyear, other) values ( ?, ?, ?, ?, ?, ?, ?, ?)";
    
    IF (get_logging()) {
        echo 'Suoritetaan INSERT INTO '.$table.' ('.$an_array["surname"].', '.$an_array["forename"].', '.$an_array["bookname"].', '.$an_array["pubyear"].', '.$an_array["isbn"].', '.$an_array["orgname"].', '.$an_array["orgyear"].', '.$an_array["other"].')<br>';
    }
    
    $query = $connection->prepare($sql);
    $query->execute([$an_array["surname"],
                    $an_array["forename"],
                    $an_array["bookname"],
                    $an_array["pubyear"],
                    $an_array["isbn"],
                    $an_array["orgname"],
                    $an_array["orgyear"],
                    $an_array["other"]]);
    
    IF (get_logging()) {
        
        echo "Kirja lisättiin onnistuneesti tietokantaan<br>";
    }
}

//
//  Poistetaan kirja tietokannasta
//
function delete_book() {

    $connection = connect_to_database();
    
    $table = 'magetsu.'.$_SESSION["user_id"].'book';

    $sql = "DELETE FROM $table WHERE surname = :surname AND forename = :forename AND bookname = :bookname AND pubyear = :pubyear AND isbn = :isbn AND orgname = :orgname AND orgyear = :orgyear AND other = :other";

    if (get_logging()) {
        echo 'Suoritetaan DELETE FROM '.$table.' WHERE surname = '
            .$_SESSION["book_list"][$_POST['bookselection']]["surname"]
            .' AND forename = '
            .$_SESSION["book_list"][$_POST['bookselection']]["forename"]
            .' AND bookname = '
            .$_SESSION["book_list"][$_POST['bookselection']]["bookname"]
            .' AND pubyear = '
            .$_SESSION["book_list"][$_POST['bookselection']]["pubyear"]
            .' AND isbn = '
            .$_SESSION["book_list"][$_POST['bookselection']]["isbn"]
            .' AND orgname = '
            .$_SESSION["book_list"][$_POST['bookselection']]["orgname"]
            .' AND orgyear = '
            .$_SESSION["book_list"][$_POST['bookselection']]["orgyear"]                        
            .' AND other = '
            .$_SESSION["book_list"][$_POST['bookselection']]["other"]
            .'<br>';
    }

    $query = $connection->prepare($sql);
    $query->execute($_SESSION["book_list"][$_POST['bookselection']]);

    if (get_logging()) {
        
        echo "Kirja poistettiin onnistuneesti tietokannasta<br>";
    }
}

function modify_book($an_array) {
    
    $connection = connect_to_database();

    $table = 'magetsu.'.$_SESSION["user_id"].'book';

    $sql = "UPDATE $table SET surname=?, forename=?, bookname=?, pubyear=?, isbn=?, orgname=?, orgyear=?, other=? WHERE surname=? AND forename=? AND bookname=? AND pubyear=? AND isbn=? AND orgname=? AND orgyear=? AND other=?";
    
    $query = $connection->prepare($sql);
    $query->execute([$an_array["surname"],
                    $an_array["forename"],
                    $an_array["bookname"],
                    $an_array["pubyear"],
                    $an_array["isbn"],
                    $an_array["orgname"],
                    $an_array["orgyear"],
                    $an_array["other"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["surname"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["forename"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["bookname"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["pubyear"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["isbn"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["orgname"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["orgyear"],
                    $_SESSION["book_list"][$_SESSION['modified_book']]["other"]]);

    if (get_logging()) {
        
        echo "Kirjaa muokattiin onnistuneesti tietokannasta<br>";
    }
}

//
// Haetaan DVD:n tietokanta
//
function fetch_dvd_sql($searchstring) {
    
    $yhteys = connect_to_database();
    
    if (get_logging()) {
        echo "Haetaan DVD-SQL<br>";
    }
    
    $table_dvd = 'magetsu.'.$_SESSION["user_id"].'dvd';
    
    if ($searchstring) {
        
        // name, year, orgname, barcode, director, music, other
        $sql = "SELECT * FROM $table_dvd WHERE name LIKE '%";
        $sql .= $searchstring."%' OR year LIKE '%";
        $sql .= $searchstring."%' OR orgname LIKE '%";
        $sql .= $searchstring."%' OR barcode LIKE '%";
        $sql .= $searchstring."%' OR director LIKE '%";
        $sql .= $searchstring."%' OR music LIKE '%";
        $sql .= $searchstring."%' OR other LIKE '%";
        $sql .= $searchstring."%'";
        
    } else {
        
        $sql = "SELECT * FROM $table_dvd ORDER BY name";
        
    }
    
    $query = $yhteys->prepare($sql);
    $query->execute();
    $array = $query->fetchAll();
    
    if (get_logging()) {
        foreach ($array as $arrays) {
            echo $arrays['name'].'<br>';
        }
    }
    
    return $array;
}

//
//  Lisätään kirja tietokantaan
//
function insert_dvd($an_array) {
    
    $connection = connect_to_database();
    
    $table = 'magetsu.'.$_SESSION["user_id"].'dvd';
    
    // name, year, orgname, barcode, director, music, other
    $sql = "INSERT INTO $table (name, year, orgname, barcode, director, music, other) values ( ?, ?, ?, ?, ?, ?, ?)";
    
    IF (get_logging()) {
        echo 'Suoritetaan INSERT INTO '.$table.' ('.$an_array["name"].', '.$an_array["year"].', '.$an_array["orgname"].', '.$an_array["barcode"].', '.$an_array["director"].', '.$an_array["music"].', '.$an_array["other"].')<br>';
    }
    
    $query = $connection->prepare($sql);
    $query->execute([$an_array["name"],$an_array["year"],$an_array["orgname"],$an_array["barcode"],$an_array["director"],$an_array["music"],$an_array["other"]]);
    
    IF (get_logging()) {
        
        echo "DVD lisättiin onnistuneesti tietokantaan<br>";
    }
}

//
//  Poistetaan dvd tietokannasta
//
function delete_dvd() {
    
    $connection = connect_to_database();
    
    $table = 'magetsu.'.$_SESSION["user_id"].'dvd';
    
    // name, year, orgname, barcode, director, music, other
    $sql = "DELETE FROM $table WHERE name = :name AND year = :year AND orgname = :orgname AND barcode = :barcode AND director = :director AND music = :music AND other = :other";
    
    IF (get_logging()) {
        echo 'Suoritetaan DELETE FROM '.$table.' WHERE name = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["name"].
            ' AND year = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["year"].
            ' AND orgname = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["orgname"].
            ' AND barcode = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["barcode"].
            ' AND director = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["director"].
            ' AND music = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["music"].
            ' AND other = '
            .$_SESSION["dvd_list"][$_POST['dvdselection']]["other"].
            '<br>';
    }
    
    $query = $connection->prepare($sql);
    $query->execute($_SESSION["dvd_list"][$_POST['dvdselection']]);
    
    IF (get_logging()) {
        
        echo "DVD poistettiin onnistuneesti tietokannasta<br>";
    }
}

//
//  Muutetaan dvd tietokannasta
//
function modify_dvd($an_array) {
    
    $connection = connect_to_database();
    
    $table = 'magetsu.'.$_SESSION["user_id"].'dvd';
    
    // name, year, orgname, barcode, director, music, other
    $sql = "UPDATE $table SET name=?, year=?, orgname=?, barcode=?, director=?, music=?, other=? WHERE name=? AND year=? AND orgname=? AND barcode=? AND director=? AND music=? AND other=?";
    
    $query = $connection->prepare($sql);
    $query->execute([$an_array["name"], 
                    $an_array["year"],
                    $an_array["orgname"],
                    $an_array["barcode"],
                    $an_array["director"],
                    $an_array["music"],
                    $an_array["other"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["name"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["year"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["orgname"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["barcode"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["director"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["music"],
                    $_SESSION["dvd_list"][$_SESSION['modified_dvd']]["other"]]);
    
    if (get_logging()) {
        
        echo "Kirjaa muokattiin onnistuneesti tietokannasta<br>";
    }
}

?>