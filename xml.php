<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");

// Luodaan tietokannan sis�ll�st� kirjojen XML
function create_xml_tree($a_bookarray, $a_dvdarray) {
    
    IF (get_logging()) {
        echo "Luodaan kirjaston XML<br>";
    }
    
    $library_tree = new DOMDocument('1.0', 'UTF-8');
    $xmlRoot = $library_tree->createElement("library");
    $xmlRoot = $library_tree->appendChild($xmlRoot);
    
    // Sijoitetaan kirjat XML:ään
    for($i=0; $i<count($a_bookarray); $i++) {
        
        $bookSurname = $a_bookarray[$i]['surname'];
        $bookForename = $a_bookarray[$i]['forename'];
        $bookBookname = $a_bookarray[$i]['bookname'];
        $bookPubyear = $a_bookarray[$i]['pubyear'];
        $bookIsbn = $a_bookarray[$i]['isbn'];
        $bookOrgname = $a_bookarray[$i]['orgname'];
        $bookOrgyear = $a_bookarray[$i]['orgyear'];
        $bookOther = $a_bookarray[$i]['other'];
        
        $currentBook = $library_tree->createElement('book');
        $currentBook->appendChild($library_tree->createElement('surname',htmlspecialchars($bookSurname)));
        $currentBook->appendChild($library_tree->createElement('forename',htmlspecialchars($bookForename)));
        $currentBook->appendChild($library_tree->createElement('bookname',htmlspecialchars($bookBookname)));
        $currentBook->appendChild($library_tree->createElement('pubyear',htmlspecialchars($bookPubyear)));
        $currentBook->appendChild($library_tree->createElement('isbn',htmlspecialchars($bookIsbn)));
        $currentBook->appendChild($library_tree->createElement('orgname',htmlspecialchars($bookOrgname)));
        $currentBook->appendChild($library_tree->createElement('orgyear',htmlspecialchars($bookOrgyear)));
        $currentBook->appendChild($library_tree->createElement('other',htmlspecialchars($bookOther)));
        
        $xmlRoot->appendChild($currentBook);
    }
    
    // Sijoitetaan DVD:t XML:ään
    for($j=0; $j<count($a_dvdarray); $j++) {
        
        $dvdName = $a_dvdarray[$j]['name'];
        $dvdYear = $a_dvdarray[$j]['year'];
        $dvdOrgname = $a_dvdarray[$j]['orgname'];
        $dvdBarcode = $a_dvdarray[$j]['barcode'];
        $dvdDirector = $a_dvdarray[$j]['director'];
        $dvdMusic = $a_dvdarray[$j]['music'];
        $dvdOther = $a_dvdarray[$j]['other'];
        
        $currentDvd = $library_tree->createElement('dvd');
        $currentDvd->appendChild($library_tree->createElement('name',htmlspecialchars($dvdName)));
        $currentDvd->appendChild($library_tree->createElement('year',htmlspecialchars($dvdYear)));
        $currentDvd->appendChild($library_tree->createElement('orgname',htmlspecialchars($dvdOrgname)));
        $currentDvd->appendChild($library_tree->createElement('barcode',htmlspecialchars($dvdBarcode)));
        $currentDvd->appendChild($library_tree->createElement('director',htmlspecialchars($dvdDirector)));
        $currentDvd->appendChild($library_tree->createElement('music',htmlspecialchars($dvdMusic)));
        $currentDvd->appendChild($library_tree->createElement('other',htmlspecialchars($dvdOther)));
        
        $xmlRoot->appendChild($currentDvd);
    }
    
    IF (get_logging()) {
        echo $library_tree->saveXML();
        echo "<br>";
    }
    
    // Palautetaan kirjaston XML
    return $library_tree;
}

?>