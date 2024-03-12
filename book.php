<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("sql.php");
require_once("html.php");

//
// Tämä suoritetaan kun lisätään kirjaa tietokantaan
//
if(isset($_POST["tallennakirja"]))
{
    IF (get_logging()) {
        echo "Tallennetaan kirja :<br>";
        echo var_dump($_POST)."<br>";
    }
    
    insert_book($_POST);
    
    if (get_logging()) {
        
        echo "Kirja lisättiin onnistuneesti tietokantaan<br>";
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        
    } else {
        
        redirect("library.php");
        
    }
}

//
// Tämä suoritetaan kun poistetaan kirja tietokannasta
//
if(isset($_POST["poistakirja"]))
{
    if (isset($_POST['bookselection'])) {
                
        if (get_logging()) {
            
            echo "Poistetaan kirja : ".$_SESSION["book_list"][$_POST['bookselection']]["surname"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["forename"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["bookname"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["pubyear"]."<br>";
            delete_book();
            echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
            exit;
            
        } else {
            
            delete_book();
            redirect("library.php");
        }
    } else {
        
    } 
}

//
// Tämä suoritetaan kun muutetaan kirjaa tietokannasta
//
if(isset($_POST["muutakirja"]))
{
    if (isset($_POST['bookselection'])) {

        // Otetaan talteen valittu kirja
        $_SESSION['modified_book'] = $_POST['bookselection'];
        
        if (get_logging()) {
            
            // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
            echo "Muutetaan kirjaa : ".$_SESSION["book_list"][$_POST['bookselection']]["surname"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["forename"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["bookname"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["pubyear"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["isbn"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["orgname"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["orgyear"]." ";
            echo $_SESSION["book_list"][$_POST['bookselection']]["other"]."<br>";
            
            luo_html_alku("Muuta kirjaa");
            tulostetaan_kirjan_tiedot();
            luo_html_loppu();
            
            echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
            exit;
            
        } else {
            
            luo_html_alku("Muuta kirjaa");
            tulostetaan_kirjan_tiedot();
            luo_html_loppu();
            exit;
        }
        
    } else {
        
    }
}

//
// Tämä suoritetaan kun muutetaan kirjaa tietokannasta
//
if(isset($_POST["muutakirjaatietokannassa"]))
{
    IF (get_logging()) {
        echo "Muutetaan kirjaa :<br>";
        echo var_dump($_POST)."<br>";
    }
    
    modify_book($_POST);
    
    if (get_logging()) {
        
        echo "Kirja muutettiin onnistuneesti tietokannassa<br>";
        echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
        
    } else {
        
        redirect("library.php");
        
    }
}

//
// Tulostaa kirjojen valintalistan
//
function create_book_selectionlist($a_library) {

    // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
    $elements = $a_library->getElementsByTagName('book');

    $id=0;
    
    $selection_list = null;
    $book_list = array();
    
    // data rows
    foreach ($elements as $node) {
        
        $surname = $node->getElementsByTagName('surname')->item(0)->nodeValue;
        $forename = $node->getElementsByTagName('forename')->item(0)->nodeValue;
        $bookname = $node->getElementsByTagName('bookname')->item(0)->nodeValue;
        $pubyear = $node->getElementsByTagName('pubyear')->item(0)->nodeValue; 
        $isbn = $node->getElementsByTagName('isbn')->item(0)->nodeValue;
        $orgname = $node->getElementsByTagName('orgname')->item(0)->nodeValue;
        $orgyear = $node->getElementsByTagName('orgyear')->item(0)->nodeValue;
        $other = $node->getElementsByTagName('other')->item(0)->nodeValue;
        
        $book_array = array('surname' => $surname, 'forename' => $forename, 'bookname' => $bookname, 'pubyear' => $pubyear, 'isbn' => $isbn, 'orgname' => $orgname, 'orgyear' => $orgyear, 'other' => $other);
            
        if (get_logging()) {
            
            echo var_dump($book_array)."<br>";
        }
        
        array_push($book_list, $book_array); 
        
        $selection = '<option value="'.$id.'">';
        $selection .= $surname.", ";
        $selection .= $forename.", ";
        $selection .= $bookname.", ";
        $selection .= $pubyear;
        $selection .=  "</option>\n";
                
        $id++;
        
        $selection_list .= $selection;
    }
    
    // Otetaan talteen kirjojen taulukko
    $_SESSION["book_list"] = $book_list;
        
    if (get_logging()) {
        
        echo $selection_list."<br>";
        var_dump($book_list);
    }
    
    return $selection_list;
}

//
// Tulostaa kirjataulukot
//
function create_html_booktable($a_library) {
    
    // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
    $elements = $a_library->getElementsByTagName('book');
    
    // start table
    echo "<table>\n";
    
    // header row
    echo "<tr>";
    echo "<th>ID</th><th>Sukunimi</th><th>Etunimi</th><th>Kirjan nimi</th><th>Julkaisuvuosi</th><th>ISBN</th><th>Alkuperäinen nimi</th><th>Alkuperäinen vuosi</th><th>Muuta</th>";
    
    echo "</tr>\n";
    $id=1;
    
    // data rows
    foreach ($elements as $node) {
        
        echo "<tr>";
        echo "<td>".$id."</td>";
        echo "<td>".$node->getElementsByTagName('surname')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('forename')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('bookname')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('pubyear')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('isbn')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('orgname')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('orgyear')->item(0)->nodeValue."</td>";
        echo "<td>".$node->getElementsByTagName('other')->item(0)->nodeValue."</td>";
        echo "</tr>\n";
        $id++;
    }
    
    // finish table and return it
    
    echo "</table>";
}

function lisaa_kirja() {
    
    luo_html_alku("Lisää kirja");
    tulostetaan_lisaa_kirja();
    luo_html_loppu();
    exit;
}

function poista_kirja() {
    
    luo_html_alku("Poista kirja");
 
    // Haetaan kirjat tietokannasta
    $booksql = fetch_book_sql(null);
    $dvdsql = fetch_dvd_sql(null);
    
    // Luodaan XML-puu kirjastosta
    $library_tree = create_xml_tree($booksql, $dvdsql);
    $selection_list = create_book_selectionlist($library_tree);
    
    tulostetaan_kirjan_poisto($selection_list);
    luo_html_loppu();
    exit;
}

function muuta_kirja() {
    
    luo_html_alku("Muuta kirjaa");
    
    // Haetaan kirjat tietokannasta
    $booksql = fetch_book_sql(null);
    $dvdsql = fetch_dvd_sql(null);
    
    // Luodaan XML-puu kirjastosta
    $library_tree = create_xml_tree($booksql, $dvdsql);
    $selection_list = create_book_selectionlist($library_tree);
    
    tulostetaan_kirjan_muutos($selection_list);
    luo_html_loppu();
    exit;
}

?>