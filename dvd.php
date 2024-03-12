<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("sql.php");
require_once("html.php");

//
// Tämä suoritetaan kun lisätään DVD:tä tietokantaan
//
if(isset($_POST["tallennadvd"]))
{
    IF (get_logging()) {
        echo "Tallennetaan dvd :<br>";
        echo var_dump($_POST)."<br>";
    }
    
    insert_dvd($_POST);
    
    if (get_logging()) {
        
        echo "DVD lisättiin onnistuneesti tietokantaan<br>";
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        
    } else {
        
        redirect("library.php");
        
    }
}

//
// Tämä suoritetaan kun poistetaan DVD tietokannasta
//
if(isset($_POST["poistadvd"]))
{
    if (isset($_POST['dvdselection'])) {
        
        $connection = connect_to_database();
        
        if (get_logging()) {
            
            // name, year, organame
            echo "Poistetaan DVD : ".$_SESSION["dvd_list"][$_POST['dvdselection']]["name"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["year"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["orgname"]."<br>";
            delete_dvd($connection);
            echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
            exit;
            
        } else {
            
            delete_dvd($connection);
            redirect("library.php");
        }
    } else {
        
    }
    
}

//
// Tämä suoritetaan kun muutetaan DVD:tä tietokannasta
//
if(isset($_POST["muutadvd"]))
{
    if (isset($_POST['dvdselection'])) {
        
        // Otetaan talteen valittu kirja
        $_SESSION['modified_dvd'] = $_POST['dvdselection'];
        
        if (get_logging()) {
            
            // name, year, orgname, barcode, director, music, other
            echo "Muutetaan DVD:tä : ".$_SESSION["dvd_list"][$_POST['dvdselection']]["name"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["year"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["orgname"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["barcode"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["director"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["music"]." ";
            echo $_SESSION["dvd_list"][$_POST['dvdselection']]["other"]."<br>";
            
            luo_html_alku("Muuta DVD");
            tulostetaan_dvdn_tiedot();
            luo_html_loppu();
            
            echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
            exit;
            
        } else {
            
            luo_html_alku("Muuta kirjaa");
            tulostetaan_dvdn_tiedot();
            luo_html_loppu();
            exit;
        }
        
    } else {
        
    }
}

//
// Tämä suoritetaan kun muutetaan DVD:tä tietokannasta
//
if(isset($_POST["muutadvdtietokannassa"]))
{
    IF (get_logging()) {
        echo "Muutetaan DVD :<br>";
        echo var_dump($_POST)."<br>";
    }
    
    modify_dvd($_POST);
    
    if (get_logging()) {
        
        echo "DVD muutettiin onnistuneesti tietokannassa<br>";
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        
    } else {
        
        redirect("library.php");
        
    }
}

//
// Tulostaa kirjojen valintalistan
//
function create_dvd_selectionlist($a_library) {
    
    // name, year, orgname, barcode, director, music, other
    $elements = $a_library->getElementsByTagName('dvd');
    
    $id=0;
    
    $selection_list = null;
    $dvd_list = array();
    
    // data rows
    foreach ($elements as $node) {
        
        $name = $node->getElementsByTagName('name')->item(0)->nodeValue;
        $year = $node->getElementsByTagName('year')->item(0)->nodeValue;
        $orgname = $node->getElementsByTagName('orgname')->item(0)->nodeValue;
        $barcode = $node->getElementsByTagName('barcode')->item(0)->nodeValue;
        $director = $node->getElementsByTagName('director')->item(0)->nodeValue;
        $music = $node->getElementsByTagName('music')->item(0)->nodeValue;
        $other = $node->getElementsByTagName('other')->item(0)->nodeValue;
        
        $dvd_array = array('name' => $name, 'year' => $year, 'orgname' => $orgname, 'barcode' => $barcode, 'director' => $director, 'music' => $music, 'other' => $other);
        
        if (get_logging()) {
            
            echo var_dump($dvd_array)."<br>";
        }
        
        array_push($dvd_list, $dvd_array);
        
        $selection = '<option value="'.$id.'">';
        $selection .= $name.", ";
        $selection .= $year.", ";
        $selection .= $orgname;
        $selection .=  "</option>\n";
        
        $id++;
        
        $selection_list .= $selection;
    }
    
    // Otetaan talteen kirjojen taulukko
    $_SESSION["dvd_list"] = $dvd_list;
    
    if (get_logging()) {
        
        echo $selection_list."<br>";
        var_dump($dvd_list);
    }
    
    return $selection_list;
}

//
// Tulostaa dvdtaulukot
//
function create_html_dvdtable($a_library) {
    
    // name, year, orgname, barcode, director, music, other
    $elements = $a_library->getElementsByTagName('dvd');
    
    
    // start table
    echo "<table>\n";
    
    // header row
    echo "<tr>";
    echo "<th>ID</th><th>Elokuvan nimi</th><th>Julkaisuvuosi</th><th>Alkuperäinen nimi</th><th>Viivakoodi</th><th>Ohjaaja</th><th>Musiikki</th><th>Muuta</th>";
    
    echo "</tr>\n";
    $id=1;
    
    // data rows
    foreach ($elements as $node) {
        
        echo "<tr>\n";
        echo "<td>".$id."</td>\n";
        echo "<td>".$node->getElementsByTagName('name')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('year')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('orgname')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('barcode')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('director')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('music')->item(0)->nodeValue."</td>\n";
        echo "<td>".$node->getElementsByTagName('other')->item(0)->nodeValue."</td>\n";
        echo "</tr>\n";
        $id++;
    }
    
    // finish table and return it
    
    echo "</table>";
}

//
// Lisää DVD
//
function lisaa_dvd() {
    
    // name, year, orgname, barcode, director, music, other
    
    luo_html_alku("Lisää DVD");
    tulostetaan_lisaa_dvd();
    luo_html_loppu();
    exit;
}

//
// Poista DVD
//
function poista_dvd() {
    
    luo_html_alku("Poista DVD");
  
    // Haetaan kirjat tietokannasta
    $booksql = fetch_book_sql(null);
    $dvdsql = fetch_dvd_sql(null);
    
    // Luodaan XML-puu kirjastosta
    $library_tree = create_xml_tree($booksql, $dvdsql);
    $selection_list = create_dvd_selectionlist($library_tree);
    
    tulostetaan_dvdn_poisto($selection_list);
    //tulostetaan_toimintoa_ei_tueta("Poista kirja");
    luo_html_loppu();
    exit;
}

//
// Muuta DVD
//
function muuta_dvd() {
    
    luo_html_alku("Muuta DVD");
    
    // Haetaan kirjat tietokannasta
    $booksql = fetch_book_sql(null);
    $dvdsql = fetch_dvd_sql(null);
    
    // Luodaan XML-puu kirjastosta
    $library_tree = create_xml_tree($booksql, $dvdsql);
    $selection_list = create_dvd_selectionlist($library_tree);
    
    tulostetaan_dvdn_muutos($selection_list);
    luo_html_loppu();
    exit;
}

?>