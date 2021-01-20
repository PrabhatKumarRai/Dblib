<?php

class Dblib {
  
  protected $conn;
  protected $servername = ""; //Add your Server Host Name
  protected $username = ""; //Add your Server User Name
  protected $password = ""; //Add the Server User Password
  protected $dbname = "";  //Add the Database Name

  public function __construct(){
    try{      
      $this->conn = new PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
      // set the PDO error mode to exception
      $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // default fetch mode to fetch_assoc
      $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
      $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
    }
    catch (PDOException $e){
      echo "Connection Failed: ".$e->getMessage();
    }
  }


  //Generates named parameters for insert prepared statements.
  public function preparedInsertStmtPlaceholderGenerator($data){
    $placeholder = '';
    foreach($data as $value){
      $placeholder .= ":".$value.",";
    }
    $placeholder = rtrim($placeholder, ',');    //removing last ','
    return $placeholder;
  }

  //Generates named parameters for update prepared statements.
  public function preparedUpdateStmtPlaceholderGenerator($data){
    $keyValPair = '';
    foreach($data as $key){
      $keyValPair .= "$key=:$key,";
    }
    $keyValPair = rtrim($keyValPair, ',');    //removing last ','
    return $keyValPair;
  }

  //Binds Prepared Statement
  public function preparedStmtBindParam($stmt, $key, $val){
    $i=0;
    foreach($key as $keys){
      $stmt->bindParam(":".$keys, $val[$i++]);
    }
  }

  //Returns Query execution status
  public function returnStatus($stmt){
    if($stmt->execute()){
      return 1;       //Success
    }
    else{
        return 0;       //SQL ERROR
    }
  }

  //Returns Query Output data
  public function returnData($result){
    if($result->rowCount() < 1){
      return '';
    }
    else{
      return $result;
    }
  }



  //------------------- INSERT SECTION -------------------//
  //Insert Single Row Data
  public function setSingle($tableName, $columnName, $val){
    try{
      $i = 0;
      $ValuePlaceholder = $this->preparedInsertStmtPlaceholderGenerator($columnName);
      $query = "INSERT INTO $tableName (" . implode(',' ,$columnName) . ") VALUES (" . $ValuePlaceholder . ");";
      
      $stmt = $this->conn->prepare($query);
      $this->preparedStmtBindParam($stmt, $columnName, $val);
      return $this->returnStatus($stmt);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }


  //------------------- SELECT SECTION -------------------//

  //Get All Data from a Table
  public function getAll($tableName){
    try{
      $query = "SELECT * FROM $tableName;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get All Data from a Table Descending
  public function getAllReverse($tableName, $orderBy='id'){
    try{
      $query = "SELECT * FROM $tableName ORDER BY $orderBy DESC;";
      $result = $this->conn->query($query);      
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get All data with condition
  public function getAllWhere($tableName, $conditions){
    try{
      $query = "SELECT * FROM $tableName WHERE $conditions;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get All data with condition Descending
  public function getAllReverseWhere($tableName, $conditions, $orderBy='id'){
    try{
      $query = "SELECT * FROM $tableName WHERE $conditions ORDER BY $orderBy DESC;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get Limited data from entire table
  public function getLimited($tableName, $columnName){
    try{

      $query = "SELECT ".implode(",", $columnName)." FROM $tableName;";
      $result = $this->conn->query($query);      
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get Limited data from entire table Decending
  public function getLimitedReverse($tableName, $columnName, $orderBy='id'){
    try{
      $query = "SELECT ".implode(",", $columnName)." FROM $tableName ORDER BY $orderBy DESC;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get Limited data with condition
  public function getLimitedWhere($tableName, $columnName, $conditions){
    try{
      $query = "SELECT ".implode(",", $columnName)." FROM $tableName WHERE $conditions;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }

  //Get Limited data with condition Decending
  public function getLimitedReverseWhere($tableName, $columnName, $conditions, $orderBy='id'){
    try{  
      $query = "SELECT ".implode(",", $columnName)." FROM $tableName WHERE $conditions ORDER BY $orderBy DESC;";
      $result = $this->conn->query($query);
      return $this->returnData($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }
  }



  //------------------- UPDATE SECTION -------------------//

  //Update data
  public function updateAll($tableName, $columnName, $val){
    try{
      $keyValPair = $this->preparedUpdateStmtPlaceholderGenerator($columnName);
      $query = "UPDATE $tableName SET $keyValPair;";
      $stmt = $this->conn->prepare($query);
      $this->preparedStmtBindParam($stmt, $columnName, $val);
      return $this->returnStatus($stmt);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }

  }

  //Update data conditional
  public function updateWhere($tableName, $columnName, $val, $conditions){

    try{
      $keyValPair = $this->preparedUpdateStmtPlaceholderGenerator($columnName);
      $query = "UPDATE $tableName SET $keyValPair WHERE $conditions;";
      $stmt = $this->conn->prepare($query);
      $this->preparedStmtBindParam($stmt, $columnName, $val);
      return $this->returnStatus($stmt);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }

  }



  //------------------- DELETE SECTION -------------------//

  //Delete data conditional
  public function deleteWhere($tableName, $conditions){
    try{
      $query = "DELETE FROM $tableName WHERE $conditions;";
      $result = $this->conn->query($query);
      return $this->returnStatus($result);
    }
    catch(PDOException $e) {
      echo "<br>Error: " . $e->getMessage();
    }

  }

}
