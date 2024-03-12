<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 0);

require_once("sql.php");
require_once("book.php");
require_once("dvd.php");
require_once("logging.php");
require_once("html.php");
require_once("xml.php");

//
// Tämä suoritetaan kun saadaan valinta kirjaston toimenpiteestä
//
if(isset($_GET["toiminto"]))
{
    switch($_GET["toiminto"]) {
        case "vie" :
            vie_kirjasto();
            break;
        case "tuo" :
            tuo_kirjasto();
            break;
        case "lisaakirja" :
            lisaa_kirja();
            break;
        case "poistakirja" :
            poista_kirja();
            break;
        case "muutakirja" :
            muuta_kirja();
            break;
        case "lisaadvd" :
            lisaa_dvd();
            break;
        case "poistadvd" :
            poista_dvd();
            break;
        case "muutadvd" :
            muuta_dvd();
            break;
        case "tulosta" :
            tulosta_kirjasto();
            break;
        case "vaihdaluettelo" :
            vaihda_luettelo();
            break;
        case "poistu" :
            kirjaudu_ulos();
            break;
    }
}

//
// Tämä suoritetaan kun saadaan valinta tietokanta-XML:n lataamisesta palveluun
//
if(isset($_POST["getxml"])) {
    
    if (get_logging()) {
        echo "Filename: " . $_FILES['fileToUpload']['name']."<br>";
        echo "Type : " . $_FILES['fileToUpload']['type'] ."<br>";
        echo "Size : " . $_FILES['fileToUpload']['size'] ."<br>";
        echo "Temp name: " . $_FILES['fileToUpload']['tmp_name'] ."<br>";
        echo "Error : " . $_FILES['fileToUpload']['error'] . "<br>";
    }
    
    
    if ($_FILES["fileToUpload"]["type"] != "text/xml") {
        
        luo_html_alku("Tiedostomuoto väärin");
        tulostetaan_tiedostomuoto_vaara();
        luo_html_loppu();
        exit;
        
    }

    $xmlDoc = new DOMDocument('1.0', 'UTF-8');
    $xmlDoc->load($_FILES["fileToUpload"]["tmp_name"]);
    
    if (!$xmlDoc->schemaValidate("library.xsd")) {
        
        luo_html_alku("Tiedoston muotoilu väärin");
        tulostetaan_tiedostomuotoilu_vaara();
        luo_html_loppu();
        exit;
        
    }
     
    if (get_logging()) {
        echo var_dump($xmlDoc->saveXML().'<br>');
    }
    
    $connection = connect_to_database();
    create_library_tables($connection);
    populate_database($connection,$xmlDoc);
    
    if (get_logging()) {
        
        echo "Kirjasto ladattiin onnistuneesti tietokantaan<br>";
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        exit;
        
    } else {
        
        redirect("library.php");
        
    }
    
}

//
// Tämä suoritetaan kun saadaan valinta hausta tietokannasta
//
if(isset($_POST["etsi"])) {

    if (get_logging()) {
        
        echo "Hakusana on : ".$_POST["searchstring"]."<br>";
    }

    create_library_search($_SESSION["books"], $_POST["searchstring"]);
    
    if (get_logging()) {
        
        echo "Suoritettiin etsi onnistuneesti<br>";
        exit;
        
    } else {
        
        exit;
        
    }
}

// Oletuksena tietokanta ei ole OK
$is_database_ok = false;

// Tämä muuttuja on olemassa vain jos käyttäjä on kirjautunut.
if(isset($_SESSION["user_id"]))
{
    
    if (get_logging()) {
        
        echo "Käyttäjä ".$_SESSION["user_id"]." on kirjautunut<br>";
        
    }
    
    //
    //	Luodaan kirjasto
    //
    create_library($is_database_ok, $_SESSION["books"]);

} else {
    
    // Jos käyttäjä ei ole kirjautunut ohjataan käyttäjä kirjautumissivulle
    redirect("login.php");
}

//
// Luodaan kirjastonäkymä
//
function create_library($is_database, $is_book) {
    
    $yhteys = connect_to_database();
    
    if (is_library_ok($yhteys)) {
        
        // Tietokanta on OK
        $is_database = true;
        
        // Haetaan kirjat ja dvd:t tietokannasta
        $booksql = fetch_book_sql(null);
        $dvdsql = fetch_dvd_sql(null);
        
        // Luodaan XML-puu kirjastosta
        $library_tree = create_xml_tree($booksql, $dvdsql);
        
    } else {
        
        // Tietokanta ei ole OK
        $is_database = false;
        
        // Luodaan tietokanta
        $connection = connect_to_database();
        create_library_tables($connection);
        
    }
    
    luo_html_alku("Kirjastoni");
    
    luo_html_painikkeet($is_database, $is_book);
    
    if ($is_database) {
        
        if ($is_book) {
            
            // Näytetään kirjat
            create_html_booktable($library_tree);
            
        } else {
            
            // Näytetään DVD:t
            create_html_dvdtable($library_tree);
        }
        
    } else {
        
        echo "<h2>Kirjastosi on tyhjä. Tuo kirjasto tai lisää kirjoja<h2><br>";
        
    }
    
    luo_html_loppu();
   
}

function create_library_search($is_book, $is_searchstring) {
    
    $yhteys = connect_to_database();
    
    if (is_library_ok($yhteys)) {
        
        // Tietokanta on OK
        $is_database = true;
        
        // Haetaan kirjat ja dvd:t tietokannasta
        $booksql = fetch_book_sql($is_searchstring);
        $dvdsql = fetch_dvd_sql($is_searchstring);
        
        // Luodaan XML-puu kirjastosta
        $library_tree = create_xml_tree($booksql, $dvdsql);
        
    } else {
        
        // Tietokanta ei ole OK
        $is_database = false;
        
        // Luodaan tietokanta
        create_library_tables($yhteys);
        
    }
    
    luo_html_alku("Kirjastoni");
    
    luo_html_painikkeet($is_database, $is_book);
    
    if ($is_database) {
        
        if ($is_book) {
            
            // Näytetään kirjat
            create_html_booktable($library_tree);
            
        } else {
            
            // Näytetään DVD:t
            create_html_dvdtable($library_tree);
        }
        
    } else {
        
        echo "<h2>Kirjastosi on tyhjä. Tuo kirjasto tai lisää kirjoja<h2><br>";
        
    }
    
    luo_html_loppu();
    
}
//
//   Vie kirjasto tiedostoon
//
function vie_kirjasto() {
    
    $yhteys = connect_to_database();
    
    if (is_library_ok($yhteys)) {
        
        // Tietokanta on OK
        $is_database_ok = true;
        
        // Haetaan kirjat ja dvd:t tietokannasta
        $booksql = fetch_book_sql(null);
        $dvdsql = fetch_dvd_sql(null);
        
        // Luodaan XML-puu kirjastosta
        $library_tree = create_xml_tree($booksql, $dvdsql);
        
    } else {
        
        // Tietokanta ei ole OK
        $is_database_ok = false;
        
    }
    
    $libraryfile = $_SESSION["user_id"].'library.xml';
        
    $library_tree->save($libraryfile);
    
    //$dir = "/PHP/Kirjastov3/";
        
    //header("Content-type: text/xml");
    //header('Content-Disposition: inline; filename="' . $dir . $libraryfile . '"');
    //header("Content-Transfer-Encoding: Text");
    //header("Content-length: " . filesize($dir . $libraryfile));
    //header('Content-Type: application/xml');
    //header('Content-Disposition: attachment; filename="' . $libraryfile . '"');
    //readfile("$dir$libraryfile");
    
    luo_html_alku("Vie kirjasto");
    tulostetaan_vie_kirjasto();
    luo_html_loppu();
    exit;
}

//
//   Hae kirjasto tiedostosta
//
function tuo_kirjasto() {
    
    luo_html_alku("Tuo kirjasto");
    tulostetaan_kirjastontuonti();
    luo_html_loppu();
    exit;
}

//
//   Vaihda luetteloa
//
function vaihda_luettelo() {
    
    luo_html_alku("Vaihda luetteloa");
    $_SESSION["books"] = !$_SESSION["books"];
    if (get_logging()) {
    
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
    
    } else {
        
        redirect("library.php");
        
    }
    luo_html_loppu();
    exit;
}

//
//   Kirjaudu ulos
//
function kirjaudu_ulos() {
    
    luo_html_alku("Kirjaudu ulos");
    session_unset();
    session_destroy();
    if (get_logging()) {
        
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        
    } else {
        
        redirect("library.php");
        
    }
    luo_html_loppu();
    exit;
}

?>