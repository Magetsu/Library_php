<?php

header("Content-Type: text/html; charset=utf-8");
ini_set("error_reporting", E_ALL | E_STRICT);
ini_set("display_errors", 1);

require_once("html.php");
require_once("sql.php");

if(isset($_POST["uusi_tili"]) && ($_POST["username"]) && isset($_POST["password"]) && isset($_POST["password_check"])) {

    $connection = connect_to_database();
    
    if (!is_database_ok($connection)) {
        
        create_accounttable();
    }
    
    if (strlen($_POST['username']) > 20 || strlen($_POST['username']) < 4) {
        
        echo 'Liian lyhyt tai liian pitkä käyttäjänimi<br>';
    } elseif (strlen( $_POST['password']) > 20 || strlen($_POST['password']) < 4) {
        
        echo 'Liian lyhyt tai liian pitkä salasana<br>';
    } elseif (ctype_alnum($_POST['username']) != true) {
        
        echo "Käytä vain aakkosia ja numeroita käyttäjänimessä<br>";
    } elseif (ctype_alnum($_POST['password']) != true) {
        
        echo "Käytä vain aakkosia ja numeroita salasanassa<br>";
    } elseif ($_POST['password'] != $_POST['password_check']) {
        
        echo "Salasanat eivät täsmää<br>";
    } else {
        
        $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
        $password = filter_var($_POST['password'], FILTER_SANITIZE_STRING);
        
        //$password = sha1( $password );
        
        $account = add_account($username,$password);
                
        if ($account) {
            
            echo "Käyttäjätunnuksen luominen onnistui<br>";
            echo "<p>Palaa <a href=\"library.php\">etusivulle</a>.</p>\n";
            exit;
        }
    }
}

luo_html_alku("Kirjasto - uusi käyttäjä");

tulostetaan_lisaamissivu();

luo_html_loppu();

?>