<?php

class Mysql {
    
    private $object_;
    private $database_;
    private $operation_;
    private $fields_;
    private $tables_;
    private $attributes_;
    private $restrictions_;
    private $order_;

    public function select($fields=null)
    {
        $this->operation_ = "SELECT";
        if(is_array($fields)&&sizeof($fields)) $this->fields_ = " ".implode(",",$fields)." ";
        else if(gettype($fields)=="string") $this->fields_ = $fields;
        else $this->fields_ = " * ";
        return $this;
    }

    public function update(String $table="")
    {
        $this->operation_ = "UPDATE";
        if($table) $this->tables_ = ($this->database_ ? $this->database_."." : "") . $table;
        else $this->tables_ = " ERR{NO_TABLE} ";
        return $this;
    }

    public function from($tables)
    {
        if(is_array($tables)&&sizeof($tables)){
            $this->tables_ = [];
            foreach($tables as $t) $this->tables_[] = ($this->database_ ? $this->database_."." : "").$t;
            $this->tables_ = implode(",", $this->tables_);
        }
        else if(gettype($tables)=="string") $this->tables_ = ($this->database_ ? $this->database_."." : "") . $tables;
        else $this->tables_ = " ERR{NO_TABLE} ";
        return $this;
    }

    public function set($attributes=null)
    {
        if(sizeof($attributes)){
            $this->attributes_ = [];
            foreach($attributes as $k=>$v) $this->attributes_[] = $k."='".$v."'";
            $this->attributes_ = implode(",", $this->attributes_);
        }
        else $this->attributes_ = " ERR{NO_ATTRIBUTES} ";
        return $this;
    }

    public function where(String $restrictions=""){
        $this->restrictions_ = $restrictions;
        return $this;
    }

    public function order(String $order=""){
        $this->order_ = $order;
        return $this;
    }

    public function cell($cell,$table,$restrictions,$response_type=__OBJECT__){
        return $this->select($cell)->from($table)->where($restrictions)->query($response_type);
    }

    public function count(String $table, String $restrictions){
        return sizeof($this->select()->from($table)->where($restrictions)->query(__ARRAY__));
    }

    public function exists(String $table, String $restrictions){
        return $this->count($table, $restrictions);
    }

    public function query($response_type=__ARRAY__,String $query="") {
        $tmp = [];
        $data = [];
        if($query)
        {
            $this->query_ = $query;
            $tmp = $this->object_->query($this->query_);
        }
        else
        {
            $this->query_ = 
                $this->operation_ 
                . " " . ($this->operation_ == "SELECT" ? $this->fields_ : ($this->operation_ == "UPDATE" ? $this->tables_ : "CASE DELETE"))
                . ($this->operation_ == "SELECT" ? " FROM " : ($this->operation_ == "UPDATE" ? " SET " : "CASE DELETE"))
                . " " . ($this->operation_ == "SELECT" ?  $this->tables_ : ($this->operation_ == "UPDATE" ? $this->attributes_ : "CASE DELETE"))
                . " " . ($this->restrictions_ ? "WHERE " . $this->restrictions_ : "")
                . " " . ($this->operation_ == "SELECT" && $this->order_ ? "ORDER BY " . $this->order_ : "");
            $tmp = $this->object_ ? $this->object_->query($this->query_) : null;
        }
        if(gettype($tmp) == "object" && $tmp->num_rows){
            switch($response_type){
                case(__ASSOC__)     : { $data = (array)$tmp->fetch_assoc(); }                                                      break;
                case(__ARRAY__)     : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; }                              break;
                case(__JSON__)      : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; $data = json_encode($data); }  break;
                case(__OBJECT__)    : { $data = Convert::atoo($tmp->fetch_assoc()); }                                              break;
                case(__MYSQLI_OBJ__): { $data = $tmp; }                                                                            break;
            }
        }
        Core::response(100,$this->query_);
        return $data;
    }

    ## returns a mysqli->mysql valid, based on conn() conn, or 0 if the mysql failed
    ## usage: $result = mysql()->query('SQL QUERY HERE')
    public static function connect($datasource=DEFAULT_DB){
        $tmp = Convert::atoo(App::connections($datasource));
        if(!$tmp) return new Mysql();
        // echo "<pre>"; print_r($tmp); die;
        $c = new \mysqli($tmp->host,$tmp->username,$tmp->passwd,$tmp->database);
        $c->set_charset($tmp->encoding);
        if($c->connect_error){ return Core::response(-1,"MYSQL CONNECTION ERROR"); }
        return new Mysql($c,$datasource);
    }

    public function __construct(mysqli $obj=null, $datasource=null){
        if($datasource){
            $tmp = Convert::atoo(App::connections($datasource));
            $this->database_ = $tmp->database;
        }
        if($obj) $this->object_ = $obj;
    }

    // ## perform a query into local mysql database based on mysql()
    // ## it can perform any query string, althogh is designed to work better with
    // ## insertions Queries (i.e: qin('INSERT INTO table VALUES('...','...'))
    // ## return 1 for success query and 0 as it fails
    // function in($table,$obj,$datasource=DEFAULT_DB){
    //     if(!$table||!$obj){ if(DEBUG) echo PHP_EOL . "TABLE, OBJECT OR DATABASE MISSING: T[$table] - O[".var_dump($obj)."] - D[$datasource]"; return null; }
    //     $table = "INSERT INTO $table ('";
    //     $tmp = ") VALUES ('";
    //     foreach($obj as $k=>$v){ $table.="$k',"; $tmp.="$v',"; }
    //     $table = substr($table,0,strlen($table)-1).substr($tmp,0,strlen($tmp)-1).")";
    //     $conn = Mysql::conn($datasource);
    //     if($conn -> query( $table )){ 
    //         $_SESSION["MYSQL_LAST_INSERTED_ID"] = $c->insert_id;
    //         return $c->insert_id;
    //     }
    //     else{ if(DEBUG) echo PHP_EOL . "MYSQL QUERY DIDN'T WORK: $table"; return 0; }
    // }

    // ## perform a query into local mysql database based on mysql()
    // ## it can perform any query string, althogh is designed to work better with
    // ## selections Queries (i.e: qio('SELECT * FROM table')
    // ## return the entire object Mysql::result_object if some register matches the search query and 0 as it fails
    // ## usage: $result = qout('SELECT * FROM table')
    // function out($query,$obj=__MYSQLI_OBJ,$datasource=DEFAULT_DB){
    //     if(!$query){ if(DEBUG) echo PHP_EOL . "NO QUERY GIVEN"; return null; }
    //     $result = Convert::atoo(["status"=>0, "data"=>null]);
    //     if($query && (strpos(strtolower($query),"select") >= 0)){
    //         $record = (Mysql::conn($datasource)) -> query( $query );
    //         if(gettype($record) == "object" && $record -> num_rows){
    //             $data = null;
    //             switch($obj){
    //                 case(__ASSOC)     : { $data = (array)$record->fetch_assoc(); }                                                      break;
    //                 case(__ARRAY)     : { $data = []; while($nt = $record->fetch_assoc()) $data[] = $nt; }                              break;
    //                 case(__JSON)      : { $data = []; while($nt = $record->fetch_assoc()) $data[] = $nt; $data = json_encode($data); }  break;
    //                 case(__OBJECT)    : { $data = Convert::atoo($record->fetch_assoc()); }                                              break;
    //                 case(__MYSQLI_OBJ) : { $data = $record; }                                                                            break;
    //             }
    //             $result -> status = true;
    //             $result -> data = ($data ? $data : Convert::atoo(["error"=>"empty data"]));
    //         }
    //     }
    //     return $result;
    // }


    // ## perform a query into local mysql database based on mysql()
    // ## it can perform any query string, althogh is designed to work better with
    // ## selections Queries (i.e: qio('SELECT * FROM table')
    // ## return 1 if some register matches the search query and 0 as it fails
    // function count($query,$datasource=DEFAULT_DB){
    //     if(!$query){ if(DEBUG) echo PHP_EOL . "NO QUERY: Q[$queryt]"; return null; }
    //     $request = Mysql::out($query,__MYSQLI_OBJ,$datasource);
    //     if($request -> status && $request -> data && $request -> data -> num_rows) return (int)$request -> data -> num_rows;
    //     else return 0;
    // }
    // ## return a value from a single cell into the mysql result query, if it matches, else return 0
    // ## $t: indicates the table wich it will search for a register
    // ## $f: inform what cell exatanly it will read
    // ## $r: stands for restrictions, as 'code=1', if it argument is blank, the result may be unlike your which
    // ## because the first matched row's cell will be returned
    // function cell($table,$cell,$restriction=null,$datasource=DEFAULT_DB){
    //     if(!$restriction) $restriction="id='".User::logged()."'";
    //     $tmp = Mysql::out("select $cell from $table where $restriction",__ASSOC,$datasource);
    //     $tmp = ($tmp -> status ? (isset($tmp -> data[ $cell ]) ? $tmp -> data[ $cell ] : "-2") : "-1" );
    //     return $tmp;
    // }

}