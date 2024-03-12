<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

//
// Tulostaa HTML-sivun alkuosan valitun otsikon kanssa.
//
function luo_html_alku($otsikko) {
    echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\"";
    echo "\"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
    echo "<html>\n";
    echo "<head>\n";
    echo "  <meta http-equiv=\"Content-Type\"
      content=\"text/html;charset=utf-8\" />\n";
    echo "  <link rel=\"stylesheet\" type=\"text/css\"
      href=\"Library.css\" />\n";
    echo "  <title>".$otsikko."</title>\n";
    echo "</head>\n";
    echo "<body>\n\n";
    echo "<div id=\"sisalto\">\n\n";
}

//
// Tulostaa HTML-sivun loppuosan.
//
function luo_html_loppu() {
    echo "\n</div>\n\n";
    echo "</body>\n";
    echo "</html>\n";
}

//
// Tulostaa kirjautumissivun
//
function tulostetaan_kirjautumissivu() {

    echo "<form action=\"login.php\" method=\"post\" class=\"lomakesivu\">\n";
    echo "<fieldset>\n";
    echo "<legend>Kirjaudu kirjastoon</legend><br>";
    echo "<div><label for=\"username\">Käyttäjätunnus </label>";
    echo "<input type=\"text\" maxlength=\"20\" name=\"username\" id=\"username\" maxlength=\"20\" size=\"20\" value=\"\" /></div><br>\n";
    echo "<div><label for=\"password\">Salasana </label>";
    echo "<input type=\"text\" maxlength=\"20\" name=\"password\" id=\"password\" maxlength=\"20\" size=\"20\" value=\"\" /></div><br>\n";
    echo "<div><input type=\"submit\" name=\"kirjaudu\" value=\"Kirjaudu\" /></div>\n\n";
    echo "<div><input type=\"submit\" name=\"uusi_kayttaja\" value=\"Uusi käyttäjä\" /></div><br>\n";
    echo "Kirjaston käyttämä muoto on <a href =\"library.xsd\">täällä<a>";
    echo "</fieldset>\n";
    echo "</form>";
}

//
// Tulostaa kirjaston tuontikyselyn
//
function tulostetaan_kirjastontuonti() {
    
    echo "<form action=\"library.php\" method=\"post\" class=\"lomakesivu\" enctype=\"multipart/form-data\">";
    echo "<fieldset>\n";
    echo "<legend>Tuo kirjasto</legend><br>";
    echo "Lataa kirjasto-xml:<br><br>";
    echo "<input type=\"file\" name=\"fileToUpload\" id=\"fileToUpload\">";
    echo "<input type=\"submit\" value=\"Lataa XML\" name=\"getxml\"><br><br>";
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo "</fieldset>";
    echo "</form>";
    
} 
    
//
// Tulostaa käyttäjän lisäämissivun
//
function tulostetaan_lisaamissivu() {
    
    echo "<form action=\"adduser.php\" method=\"post\" class=\"lomakesivu\">\n";
    echo "<fieldset>\n";
    echo "<legend>Luo uusi käyttäjä</legend><br>";
    echo "<div><label for=\"username\">Käyttäjätunnus </label>";
    echo "<input type=\"text\" maxlength=\"20\" name=\"username\" id=\"username\" value=\"\"></div><br>\n";
    echo "<div><label for=\"password\">Salasana </label>";
    echo "<input type=\"text\" maxlength=\"20\" name=\"password\" id=\"password\" value=\"\"></div><br>\n";
    echo "<div><label for=\"password_check\">Salasana uudestaan</label>";
    echo "<input type=\"text\" maxlength=\"20\" name=\"password_check\" id=\"password_check\" value=\"\"></div><br>\n";
    echo "<div><input type=\"submit\" name=\"uusi_tili\" value=\"Lisää käyttäjä\" /></div><br>";
    echo "Palaa <a href=\"login.php\">kirjautumissivulle</a>.\n";
    echo "</fieldset>";
    echo "</form>";
    
}

//
// Lisää DVD
//
function tulostetaan_dvdn_tiedot() {
    
    // name, year, orgname, barcode, director, music, other
    echo "<fieldset>\n";
    echo "<legend>Muuta DVD</legend>";
    echo '<form action="dvd.php" method="post">';
    echo '<br><label for="surname">Elokuvan nimi:</label>';
    echo '<input type="text" id="name" name="name" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["name"].'" required><br>';
    echo '<br><label for="forename">Julkaisuvuosi:</label>';
    echo '<input type="text" id="year" name="year" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["year"].'" required><br>';
    echo '<br><label for="bookname">Alkuperäinen nimi:</label>';
    echo '<input type="text" id="orgname" name="orgname" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["orgname"].'"><br>';
    echo '<br><label for="pubyear">Viivakoodi:</label>';
    echo '<input type="text" id="barcode" name="barcode" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["barcode"].'"><br>';
    echo '<br><label for="isbn">Ohjaaja:</label>';
    echo '<input type="text" id="director" name="director" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["director"].'"><br>';
    echo '<br><label for="orgname">Musiikki:</label>';
    echo '<input type="text" id="music" name="music" size="50" value="'.$_SESSION["dvd_list"][$_POST['dvdselection']]["music"].'"><br>';
    echo '<br><label for="other">Muuta tietoa:</label>';
    echo '<textarea id="other" name="other" rows="2" cols ="50">'.$_SESSION["dvd_list"][$_POST['dvdselection']]["other"].'</textarea><br>';
    echo '<input type="submit" name="muutadvdtietokannassa" value="Muuta DVD"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Lisää kirja
//
function tulostetaan_kirjan_tiedot() {
  
    // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
    
    echo "<fieldset>\n";
    echo "<legend>Muuta kirjaa</legend>";
    echo '<form action="book.php" method="post">';
    echo '<br><label for="surname">Kirjailijan sukunimi:</label>';
    echo '<input type="text" id="surname" name="surname" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["surname"].'" required><br>';
    echo '<br><label for="forename">Kirjailijan etunimi:</label>';
    echo '<input type="text" id="forename" name="forename" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["forename"].'" required><br>';
    echo '<br><label for="bookname">Kirjan nimi:</label>';
    echo '<input type="text" id="bookname" name="bookname" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["bookname"].'" required><br>';
    echo '<br><label for="pubyear">Kirjan julkaisuvuosi:</label>';
    echo '<input type="number" id="pubyear" name="pubyear" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["pubyear"].'"><br>';
    echo '<br><label for="isbn">Kirjan isbn:</label>';
    echo '<input type="text" id="isbn" name="isbn" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["isbn"].'"><br>';
    echo '<br><label for="orgname">Kirjan alkukielinen nimi:</label>';
    echo '<input type="text" id="orgname" name="orgname" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["orgname"].'"><br>';
    echo '<br><label for="orgyear">Kirjan julkaisuvuosi:</label>';
    echo '<input type="number" id="orgyear" name="orgyear" size="50" value="'.$_SESSION["book_list"][$_POST['bookselection']]["orgyear"].'"><br>';
    echo '<br><label for="other">Muuta tietoa:</label>';
    echo '<textarea id="other" name="other" rows="2" cols ="50">'.$_SESSION["book_list"][$_POST['bookselection']]["other"].'</textarea><br>';
    echo '<input type="submit" name="muutakirjaatietokannassa" value="Muuta kirjaa"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Tiedostomuotoa ei tueta
//
function tulostetaan_tiedostomuoto_vaara() {
  
    echo "<fieldset>\n";
    echo "<legend>Tiedostomuoto on väärä</legend><br>";
    echo "Tiedostomuotoa ei tueta!<br><br>";
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo "</fieldset>";
    
}
    
//
// Tiedostomuotoilu on väärä
//
function tulostetaan_tiedostomuotoilu_vaara() {
    
    echo "<fieldset>\n";
    echo "Kirjaston tiedoston muotoa ei tueta!<br><br>\n";
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo "</fieldset>";
    
}

//
// Vie kirjasto
//
function tulostetaan_vie_kirjasto() {
    
    echo "<fieldset>\n";
    echo "<legend>Vie kirjasto</legend><br>";
    echo "Kirjasto on exportoitu tiedostoon ".$_SESSION["user_id"]."library.xml<br><br>";
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo "</fieldset>";
    
}

//
// Lisää kirja
//
function tulostetaan_lisaa_kirja() {
    
    // surname, forename, bookname, pubyear, isbn, orgname, orgyear, other
    echo "<fieldset>\n";
    echo "<legend>Lisää kirja</legend>";
    echo '<form action="book.php" method="post">';
    echo '<br><label for="surname">Kirjailijan sukunimi:</label>';
    echo '<input type="text" id="surname" name="surname" size="50" value="" required><br>';
    echo '<br><label for="forename">Kirjailijan etunimi:</label>';
    echo '<input type="text" id="forename" name="forename" size="50" value="" required><br>';
    echo '<br><label for="bookname">Kirjan nimi:</label>';
    echo '<input type="text" id="bookname" name="bookname" size="50" value="" required><br>';
    echo '<br><label for="pubyear">Kirjan julkaisuvuosi:</label>';
    echo '<input type="number" id="pubyear" name="pubyear" size="50" value=""><br>';
    echo '<br><label for="isbn">Kirjan isbn:</label>';
    echo '<input type="text" id="isbn" name="isbn" size="50" value=""><br>';
    echo '<br><label for="orgname">Kirjan alkukielinen nimi:</label>';
    echo '<input type="text" id="orgname" name="orgname" size="50" value=""><br>';
    echo '<br><label for="orgyear">Kirjan julkaisuvuosi:</label>';
    echo '<input type="number" id="orgyear" name="orgyear" size="50" value=""><br>';
    echo '<br><label for="other">Muuta tietoa:</label>';
    echo '<textarea id="other" name="other" rows="2" cols ="50"></textarea><br>';
    echo '<input type="submit" name="tallennakirja" value="Tallenna kirja"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Lisää DVD
//
function tulostetaan_lisaa_dvd() {
    
    // name, year, orgname, barcode, director, music, other
    echo "<fieldset>\n";
    echo "<legend>Lisää kirja</legend>";
    echo '<form action="dvd.php" method="post">';
    echo '<br><label for="surname">Elokuvan nimi:</label>';
    echo '<input type="text" id="name" name="name" size="50" value="" required><br>';
    echo '<br><label for="forename">Julkaisuvuosi:</label>';
    echo '<input type="text" id="year" name="year" size="50" value=""><br>';
    echo '<br><label for="bookname">Alkuperäinen nimi:</label>';
    echo '<input type="text" id="orgname" name="orgname" size="50" value=""><br>';
    echo '<br><label for="pubyear">Viivakoodi:</label>';
    echo '<input type="text" id="barcode" name="barcode" size="50" value=""><br>';
    echo '<br><label for="isbn">Ohjaaja:</label>';
    echo '<input type="text" id="director" name="director" size="50" value=""><br>';
    echo '<br><label for="orgname">Musiikki:</label>';
    echo '<input type="text" id="music" name="music" size="50" value=""><br>';
    echo '<br><label for="other">Muuta tietoa:</label>';
    echo '<textarea id="other" name="other" rows="2" cols ="50"></textarea><br>';
    echo '<input type="submit" name="tallennadvd" value="Tallenna DVD"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Poista kirja
//
function tulostetaan_kirjan_poisto($selection_list) {
   
    echo "<fieldset>\n";
    echo "<legend>Poista kirja</legend>";
    echo '<form action="book.php" method="post">';
    echo '<label for="books">Valitse poistettava kirja:</label>';
    echo '<select name="bookselection" id="books">';
    echo $selection_list;
    echo '</select>';
    echo '<br><br>';
    echo '<input type="submit" name="poistakirja" value="Poista kirja"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Muuta kirja
//
function tulostetaan_kirjan_muutos($selection_list) {
    
    echo "<fieldset>\n";
    echo "<legend>Muuta kirjaa</legend>";
    echo '<form action="book.php" method="post">';
    echo '<label for="books">Valitse muutettava kirja:</label>';
    echo '<select name="bookselection" id="books">';
    echo $selection_list;
    echo '</select>';
    echo '<br><br>';
    echo '<input type="submit" name="muutakirja" value="Valitse kirja"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Muuta DVD
//
function tulostetaan_dvdn_muutos($selection_list) {
    
    echo "<fieldset>\n";
    echo "<legend>Muuta DVD</legend>";
    echo '<form action="dvd.php" method="post">';
    echo '<label for="books">Valitse muutettava DVD:</label>';
    echo '<select name="dvdselection" id="dvds">';
    echo $selection_list;
    echo '</select>';
    echo '<br><br>';
    echo '<input type="submit" name="muutadvd" value="Valitse DVD"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}
//
// Poista DVD
//
function tulostetaan_dvdn_poisto($selection_list) {
    
    echo "<fieldset>\n";
    echo "<legend>Poista kirja</legend>";
    echo '<form action="dvd.php" method="post">';
    echo '<label for="dvds">Valitse poistettava kirja:</label>';
    echo '<select name="dvdselection" id="dvds">';
    echo $selection_list;
    echo '</select>';
    echo '<br><br>';
    echo '<input type="submit" name="poistadvd" value="Poista DVD"><br><br>';
    echo '</form>';
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo '</fieldset>';
}

//
// Toimintaa ei tueta
//
function tulostetaan_toimintoa_ei_tueta($otsikko) {
    
    echo "<fieldset>\n";
    echo "<legend>".$otsikko."</legend><br>";
    echo "Toimintoa ei tueta vielä<br><br>";
    echo "Palaa <a href=\"library.php\">etusivulle</a>.\n";
    echo "</fieldset>";
    
}

//
// Uudelleenohjaa käyttäjän toiselle sivulle.
//
function redirect($sivu)
{
    header("Location: http://" . $_SERVER["HTTP_HOST"]
        . dirname($_SERVER["PHP_SELF"])
        . "/" . $sivu);
}

//
// Tulostaa HTML-sivun yläpainikerivin.
//
function luo_html_painikkeet($is_database_ok, $is_book) {
    
    echo "<h1>".$_SESSION["user_name"].":n kirjasto</h1>\n";

    echo "<ul>";
    if ($is_database_ok) {
        
        echo "<li><a href=library.php?toiminto=vie>Vie kirjasto</a></li>";
        
    }
    
    echo "<li><a href=library.php?toiminto=tuo>Tuo kirjasto</a></li>";
    if ($is_book) {
        
        echo "<li><a href=library.php?toiminto=lisaakirja>Lisää kirja</a></li>";
        
    } else {
        
        echo "<li><a href=library.php?toiminto=lisaadvd>Lisää DVD</a></li>";
        
    }
    
    if ($is_database_ok) {
        if ($is_book) {
            
            echo "<li><a href=library.php?toiminto=poistakirja>Poista kirja</a></li>";
            echo "<li><a href=library.php?toiminto=muutakirja>Muuta kirjaa</a></li>";
            
        } else {
            
            echo "<li><a href=library.php?toiminto=poistadvd>Poista DVD</a></li>";
            echo "<li><a href=library.php?toiminto=muutadvd>Muuta DVD</a></li>";
            
        }
        
        if ($is_database_ok) {
            
            echo "<li><a href=\"#\" onclick= \"window.print(); return false;\">Tulosta kirjasto</a></li>";
        }
        
        if ($is_book) {
            echo "<li><a href=library.php?toiminto=vaihdaluettelo>Näytä DVD:t</a></li>";
        } else {
            echo "<li><a href=library.php?toiminto=vaihdaluettelo>Näytä kirjat</a></li>";
        }
        
        echo "<li><a href=library.php?toiminto=poistu>Kirjaudu ulos</a></li>\n";
        
        if ($is_book) {
            
            echo '<li class="searchbox">Etsi kirja: </li>';
            
        } else {
            
            echo '<li class="searchbox">Etsi DVD: </li>';
            
        }
        
        echo '<form action="library.php" method="post">';
        echo '<textarea class="searchbox" id="search" name="searchstring" rows="1" cols ="25"></textarea>';
        
        if (isset($_POST["etsi"])) {
            
            echo '<input type="submit" name="tyhjennä" value="Tyhjennä haku">';
            
        } else {
            
            if ($is_book) {
                
                echo '<input type="submit" name="etsi" value="Etsi kirja">';
                
            } else {
                
                echo '<input type="submit" name="etsi" value="Etsi DVD">';
                
            }
            
        }
        echo '</form>';
        
        echo "</ul><br>\n\n";
    }
}

?>