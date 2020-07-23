<?php
  /*
          File: sftp_put_config.php
       Created: 07/23/2020
       Updated: 07/23/2020
    Programmer: Cuates
    Updated By: Cuates
       Purpose: Sensitive Database information
  */

  // Can ONLY be inherited by another class
  abstract class sftp_put_config
  {
    // Declare protected variables
    protected $driver = NULL;
    protected $servername = NULL;
    protected $port = NULL;
    protected $database = NULL;
    protected $username = NULL;
    protected $password = NULL;
    protected $url = NULL;
    protected $urlapi = NULL;
    protected $remotePath = NULL;
    protected $subscriptionKey = NULL;

    // PHP 5+ Style constructor
    public function __construct()
    {
      // This function needs to be here so the class can be executed when called
    }

    // PHP 4 Style constructor
    public function sftp_put_config()
    {
      // Call the constructor
      self::__construct();
    }

    // Set host variables
    protected function setConfigVars($type)
    {
      // Retrieve server information
      $ServerInfo = php_uname('n');

      // Define array of dev words
      $ServerType = array('dev');

      // Check if server info does not consist of server type
      if(!preg_match("/\b[a-zA-Z0-9(\W)(\_)(\s)]{0,}" . implode('|', $ServerType) . "[a-zA-Z0-9(\W)(\_)(\s)]{0,}\b/i", $ServerInfo))
      {
        // Set production database information
        // Check if type is ms sql
        if($type === "<Database_Name>")
        {
          // Set variables
          $this->driver = "<Drive_Name_On_Linux_Machine>"; // Driver Name on Linux machine (e.g. FreeTDS)
          $this->servername = "<Database_Server_Name>";
          $this->port = "<Database_Port_Number>";
          $this->database = "<Production_Database_Name>";
          $this->username = "<Username>";
          $this->password = "<Password>";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "";
          $this->subscriptionKey = "";
        }
        else if ($type === "SFTP")
        {
          // Set variables
          $this->driver = "";
          $this->servername = "<Production_SFTP_Server_Name>";
          $this->port = "<Production_SFTP_Port_Number>";
          $this->database = "";
          $this->username = "<Username>";
          $this->password = "<Password>";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "<Directory_Path_In_SFTP_Server>";
          $this->subscriptionKey = "";
        }
        else
        {
          // Set variables
          $this->driver = "";
          $this->servername = "";
          $this->port = "";
          $this->database = "";
          $this->username = "";
          $this->password = "";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "";
          $this->subscriptionKey = "";
        }
      }
      else
      {
        // Else set development database information
        // Check if type is ms sql
        if($type === "<Database_Name>")
        {
          // Set variables
          $this->driver = "<Drive_Name_On_Linux_Machine>"; // Driver Name on Linux machine (e.g. FreeTDS)
          $this->servername = "<Database_Server_Name>";
          $this->port = "<Database_Port_Number>";
          $this->database = "<Development_Database_Name>";
          $this->username = "<Username>";
          $this->password = "<Password>";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "";
          $this->subscriptionKey = "";
        }
        else if ($type === "SFTP")
        {
          // Set variables
          $this->driver = "";
          $this->servername = "<Development_SFTP_Server_Name>";
          $this->port = "<Development_SFTP_Port_Number>";
          $this->database = "";
          $this->username = "<Username>";
          $this->password = "<Password>";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "<Directory_Path_In_SFTP_Server>";
          $this->subscriptionKey = "";
        }
        else
        {
          // Set variables
          $this->driver = "";
          $this->servername = "";
          $this->port = "";
          $this->database = "";
          $this->username = "";
          $this->password = "";
          $this->url = "";
          $this->urlapi = "";
          $this->remotePath = "";
          $this->subscriptionKey = "";
        }
      }
    }

    // Get variables
    protected function getConfigVars()
    {
      // Return array of variables
      return array("Driver" => $this->driver, "Servername" => $this->servername, "Port" => $this->port, "Database" => $this->database, "Username" => $this->username, "Password" => $this->password, "URL" => $this->url, "URLAPI" => $this->urlapi, "RemotePath" => $this->remotePath, "SubscriptionKey" => $this->subscriptionKey);
    }
  }
?>