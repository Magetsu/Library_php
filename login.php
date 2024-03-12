<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("logging.php");
require_once("html.php");
require_once("sql.php");

// Tarkistetaan kirjautumisyritys
if(isset($_POST["kirjaudu"])) {
        
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
       
    //$password = sha1( $password );
    
    $user_id = check_login($username,$password);
    
    if($user_id == 0) {
        
        if (get_logging()) {
            
            printlog("Kirjautuminen epäonnistui<br>");
            printlog("<p>Palaa <a href=\"login.php\">kirjautumissivulle</a>.</p>\n");
            
        } else {
            
            redirect("login.php");
            
        }
        exit;
        
    } else {
        
        // Kirjautuminen onnistui, löytyi tunnuksia vastaava rivi.
        
        // Aloitetaan istunto
        session_start();
        
        // Otetaan talteen käyttäjän user id ja user name
        $_SESSION["user_id"] = $user_id;
        $_SESSION["user_name"] = $username;
        
        // Mikä kirjasto näytetään ensin. true = kirjat, false = DVD:t
        $_SESSION["books"] = true;
         
        if (get_logging()) {
            
            printlog("Kirjautuminen onnistui<br>");
            printlog("Käyttäjä id : ".$_SESSION["user_id"]."<br>");
            printlog("<p>Palaa <a href=\"library.php\">pääsivulle</a>.</p>\n");
            
        } else {
            
            redirect("library.php");
        }
        exit;
    }
    
} elseif (isset($_POST["uusi_kayttaja"]))  {

    if (get_logging()) {

        printlog("Luodaan uusi käyttäjä<br>");
        printlog("<p>Mennään <a href=\"adduser.php\">luomaan</a> uusi käyttäjä.</p>\n");
        
    } else {
        
        // Mennään uuden käyttäjän lisäämissivulle
        redirect("adduser.php");
        
    }
    exit;
}

luo_html_alku("Kirjasto - sisäänkirjautuminen");

tulostetaan_kirjautumissivu();

luo_html_loppu();

//
//   Kirjautumisen tarkistaminen tietokannasta
//
function check_login($username, $password) {
    
    $connection = connect_to_database();
    
    $user_id = 0;
    
    try {
        $user = array($username,$password);
        
        $sql = "SELECT user_id, username, password FROM accounts WHERE username = ? AND password = ?";
        
        printlog("SELECT user_id, username, password FROM accounts WHERE username = ".$username." AND password = ".$password."<br>");
        
        $query = $connection->prepare($sql);
        
        $query->execute($user);
        
        $user_id = $query->fetchColumn();
        
    } catch(Exception $e) {
        /*** if we are here, something has gone wrong with the database ***/
        echo 'We are unable to process your request. Please try again later';
    }
    
    return $user_id;
}

?>