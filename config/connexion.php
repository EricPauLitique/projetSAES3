
    <?php

    class Connexion {
        // Les attributs static caractristiques de la connexion
        static private $hostname = 'localhost';
        /*static private $database = 'saes3-ese';
        static private $login = 'saes3-ese';
        static private $password = 'kU4Ny1JywAGjEybF';*/

        static private $database = 'eric';
        static private $login = 'root';
        static private $password = 'root';


        static private $tabUTF8 = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");


        // L'atribut static qui matérialisera la connexion
        static private $pdo;


        // Le getter public de cet attribut
        static public function pdo() {return self::$pdo;}

        // la fonction static de connexion qui initialise $pdo et lance la tentative de connecxion

        static public function connect() {
            $h= self::$hostname; $d=self::$database; $l = self::$login;
            $p = self::$password; $t=self::$tabUTF8;

            try {
                self::$pdo = new PDO("mysql:host=$h;dbname=$d",$l,$p,$t);
                self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            } catch (PDOException $e) {
                echo "erreur de connexion : ".$e->getMessage()."<br>";
            }
        }
    }



    ?>