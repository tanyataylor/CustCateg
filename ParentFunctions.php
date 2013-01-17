<?php

error_reporting(E_ALL|E_STRICT);
ini_set("display_errors",1);

class ParentFunctions
{
    public function __init(){
        $xml = $this->createXmlElement();
        $dbCredentials = $this->dbCredentials($xml);
        $connectCredentials = $this->dbConnect($dbCredentials);
        //$header = $this->displayHeader();
        $data = $this->getData($connectCredentials[1], $sortoption = 'null', $sortorder = 'null');
        $renderData = $this->renderData($data);



}
    public function createXmlElement(){
        $xml = json_decode(json_encode(simpleXML_load_file('/var/www/magento/app/etc/local.xml','SimpleXMLElement', LIBXML_NOCDATA)),true);
        //var_dump($xml);
        return $xml;
        }

    public function dbCredentials($xml){
        $contents = $xml['global']['resources']['default_setup']['connection'];
        $connectAttributes = array_slice($contents,0,4);
        $host = $connectAttributes['host'];
        $username = $connectAttributes['username'];
        $password = $connectAttributes['password'];
        $dbName = $connectAttributes['dbname'];
        $storeCredentials = array();
        $storeCredentials[] = $host;
        $storeCredentials[] = $username;
        $storeCredentials[] = $password;
        $storeCredentials[] = $dbName;
        return $storeCredentials;
    }

    public function dbConnect($storeCredentials){
        $link = mysql_connect($storeCredentials[0], $storeCredentials[1], $storeCredentials[2]);
        if (!$link){
            //$this->create_log_entry("Error connecting to database: " . mysql_error());
            die("Could not connect : " . mysql_error());
        }
        /* Uncomment below if needed for the test! */
        //else {echo "Link was established.<br/>";}
        //else {$this->create_log_entry("Successfully connected to database");}
        $dbSelected = mysql_select_db($storeCredentials[3], $link);
        if(!$dbSelected){
            die("Can\'t use database : " . mysql_error());
        }
        /* Uncomment below if needed for the test! */
        //else {echo "<br/>Database $storeCredentials[3] was selected.";}
        //else {$this->create_log_entry("Successfully selected database $storeCredentials[3]");}
        $connectCredentials = array();
        $connectCredentials[] = $link;
        $connectCredentials[] = $dbSelected;
        return $connectCredentials;
    }

    public function displayHeader(){
        include("displayHeader.phtml");
    }

    public function displayHeaderCustomers(){
        include("displayHeaderCustomers.phtml");
    }

    public function sqlCategory(){
        $sql = "SELECT catalog_category_entity.entity_id, catalog_category_entity_varchar.value
        FROM catalog_category_entity
        JOIN catalog_category_entity_varchar
        ON catalog_category_entity.entity_id = catalog_category_entity_varchar.entity_id
        WHERE catalog_category_entity_varchar.attribute_id = 111";
        return($sql);
    }

    public function sqlCustomers(){
        $sql = "SELECT DISTINCT customer_entity.entity_id , customer_entity.email, customer_entity_varchar.value
        FROM customer_entity
        JOIN customer_entity_varchar
        ON customer_entity.entity_id = customer_entity_varchar.entity_id
        WHERE customer_entity_varchar.attribute_id in (1)";
        return $sql;
    }

    public function getData($dbSelected,$sortoption = 'null', $sortorder = 'null'){

        if(isset($_POST['category'])){
            $header = $this->displayHeader();
            $sql = $this->sqlCategory();
                    //var_dump($sql);
            if(isset($_GET['value'])){
                $sql .= " AND catalog_category_entity_varchar.value = '" . $_GET['value'] . "' ";
            }
            else {
                if(isset($_POST['sortoption'])){
                    $sortoption = $_POST['sortoption'];
                }
                else $sortoption = 'value';
                if(isset($_POST['sortorder'])){
                    $sortorder = $_POST['sortorder'];
                }
                else $sortorder = 'asc';
                $sql .= " ORDER BY $sortoption $sortorder";
                if((isset($_POST['limit'])) AND $_POST['limit'] > 0){
                    $limit = $_POST['limit'];
                    $sql .= " limit 0, $limit";
                }
                else {
                    $sql .= " limit 0, 30";
                }
            }
        $result = mysql_query($sql);
        if(!$result){
            die("Invalid query : " . mysql_error());
        }
        /* Uncomment if needed for the test! */
        //else {echo "<br/>Query was successfully executed.<br/>";}
        else { }

        while ($row = mysql_fetch_assoc($result)){
            echo $string = "<tr><td>" . $row['entity_id'] .
                "</td><td>" . '<a href="index.php?value=' . $row['value'] . '">' . $row['value'] . '</a>' .
                "</td></tr>";
        }return $string;

    }
        if(isset($_POST['customers'])){
            $header = $this->displayHeaderCustomers();
            $sql = $this->sqlCustomers();
            //var_dump($sql);
            if(isset($_GET['value'])){
                $sql .= " AND catalog_category_entity_varchar.value = '" . $_GET['value'] . "' ";
            }
            else {
                if(isset($_POST['sortoption'])){
                    $sortoption = $_POST['sortoption'];
                }
                else $sortoption = 'value';
                if(isset($_POST['sortorder'])){
                    $sortorder = $_POST['sortorder'];
                }
                else $sortorder = 'asc';
                $sql .= " ORDER BY $sortoption $sortorder";
                if((isset($_POST['limit'])) AND $_POST['limit'] > 0){
                    $limit = $_POST['limit'];
                    $sql .= " limit 0, $limit";
                }
                else {
                    $sql .= " limit 0, 30";
                }
            }
            $result = mysql_query($sql);
            if(!$result){
                die("Invalid query : " . mysql_error());
            }
            /* Uncomment if needed for the test! */
            //else {echo "<br/>Query was successfully executed.<br/>";}
            else { }

            while ($row = mysql_fetch_assoc($result)){
                echo $string = "<tr><td>" . $row['entity_id'] .
                    "</td><td>" . '<a href="index.php?value=' . $row['value'] . '">' . $row['value'] . '</a>' .
                    "</td><td>" . $row['email'] .
                    "</td></tr>";
    }
            return $string;
    }
        if(!isset($_POST['customers']) AND (!isset($_POST['category']))){
            echo "Nothing was submitted";
        return null;
    }
    }

    public function renderData($string){
        include("renderData.phtml");
    }




























    }




