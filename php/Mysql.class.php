<?php
class Mysql extends Activity {

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
        // $this->fields_ = $this->object_->real_escape_string($this->fields_);
        return $this;
    }

    public function insert(String $table, Array $values=null)
    {
        $this->operation_ = "INSERT INTO";
        if($table) $this->tables_ = ($this->database_ ? "`" . $this->database_."`.`" : "`") . $table . "` (";
        else $this->tables_ = " ERR{NO_TABLE}(";
        
        $tmpTables = [];
        if($values && sizeof($values)){
            $this->attributes_ = [];
            foreach($values as $k=>$v){
                $this->attributes_[] = "'$v'";
                $tmpTables[] = "`$k`";
            }
            $this->attributes_ = implode(",", $this->attributes_);
        } else $this->attributes_ = " ERR{NO_ATTRIBUTES} ";
        $this->tables_ .= (sizeof($tmpTables) ? implode(",", $tmpTables) : "") . ")";
        return $this;
    }

    public function update(String $table="")
    {
        $this->operation_ = "UPDATE";
        if($table) $this->tables_ = ($this->database_ ? "`" . $this->database_."`.`" : "`") . $table . "` ";
        else $this->tables_ = " ERR{NO_TABLE} ";
        // $this->tables_ = $this->object_->real_escape_string($this->tables_);
        return $this;
    }

    public function from($tables)
    {
        if(is_array($tables)&&sizeof($tables)){
            $this->tables_ = [];
            foreach($tables as $t) $this->tables_[] = ($this->database_ ? "`" . $this->database_."`.`" : "`").$t . "`";
            $this->tables_ = implode(",", $this->tables_);
        }
        else if(gettype($tables)=="string") $this->tables_ = ($this->database_ ? $this->database_."." : "") . $tables;
        else $this->tables_ = " ERR{NO_TABLE} ";
        // $this->tables_ = $this->object_->real_escape_string($this->tables_);
        return $this;
    }

    public function values($atributes=null)
    {
        if(sizeof($attributes)){
            $this->attributes_ = [];
            foreach($attributes as $v) $this->attributes_[] = "'".$v."'";
            // $this->attributes_ = $this->object_->real_escape_string(implode(",", $this->attributes_));
            $this->attributes_ = implode(",", $this->attributes_);
        }
        else $this->attributes_ = " ERR{NO_ATTRIBUTES} ";
        return $this;
    }

    public function set($attributes=null)
    {
        if(sizeof($attributes)){
            $this->attributes_ = [];
            foreach($attributes as $k=>$v) $this->attributes_[] = "`" . $k . "`='" . $v . "'";
            // $this->attributes_ = $this->object_->real_escape_string(implode(",", $this->attributes_));
            $this->attributes_ = implode(",", $this->attributes_);
        }
        else $this->attributes_ = " ERR{NO_ATTRIBUTES} ";
        return $this;
    }

    public function where(String $restrictions=""){
        // $this->restrictions_ = $this->object_->real_escape_string($restrictions);
        $this->restrictions_ = $restrictions;
        return $this;
    }

    public function order(String $order=""){
        // $this->order_ = $this->object_->real_escape_string($order);
        $this->order_ = $order;
        return $this;
    }

    public function cell($cell,$table,$restrictions,$response_type=__OBJECT__){
        return $this->select($cell)->from($table)->where($restrictions)->query($response_type);
    }

    public function count(String $table, String $restrictions){
        // print_r($this->select()->from($table)->where($restrictions)->query(__ARRAY__));die;
        return sizeof($this->select()->from($table)->where($restrictions)->query(__ARRAY__));
    }

    public function exists(String $table, String $restrictions)
	{
        	return $this->count($table, $restrictions);
	}

    public function query($response_type=__ARRAY__)
    {
        $tmp = [];
        $data = [];
        $this->query_ = $this->operation_ . " ";
        switch($this->operation_){
            case("SELECT"):
                $this->query_ .= $this->fields_ . " FROM " . $this->tables_ . ($this->restrictions_ ? " WHERE " . $this->restrictions_ : "") . ($this->order_ ? " ORDER BY " . $this->order_ : "");
            break;
            case("UPDATE"):
                $this->query_ .= $this->tables_ . " SET " . $this->attributes_ . ($this->restrictions_ ? " WHERE " . $this->restrictions_ : "");
            break;
            case("INSERT INTO"):
                $this->query_ .= $this->tables_ . " VALUES (" . $this->attributes_ . ")";
            break;
            case("DELETE"):
                $this->query_ .= "{{ CASE DELETE }}" . ($this->restrictions_ ? " WHERE " . $this->restrictions_ : "");
            break;
        }

        $tmp = $this->object_ ? $this->object_->query(implode('{{ NO COMMENTS ALLOWED }}',explode('--',$this->query_))) : null;

        if($this->operation_ == "INSERT INTO" && $this->object_->affected_rows && !$this->object_->errno) return Core::response(1, "insertion accepted");
        else if($this->operation_ == "INSERT INTO") return Core::response(0, "insertion not accepted");

        if(gettype($tmp) == "object" && $tmp->num_rows)
        {
            switch($response_type){
                case(__ASSOC__)     : { $data = (array)$tmp->fetch_assoc(); }                                                      break;
                case(__ARRAY__)     : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; }                              break;
                case(__JSON__)      : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; $data = json_encode($data); }  break;
                case(__OBJECT__)    : { $data = Convert::atoo($tmp->fetch_assoc()); }                                              break;
                case(__MYSQLI_OBJ__): { $data = $tmp; }                                                                            break;
            }
        } else {
            Core::response(mysqli_connect_errno(),$this->object_->error . $this->query_);
            if(sizeof($this->object_->error_list)) $data[mysqli_err] = [ "errno" => $this->object_->errno, "error_list" => $this->object_->error_list ];
        }
        return $data;
    }

    ## returns a mysqli->mysql valid, based on conn() conn, or 0 if the mysql failed
    ## usage: $result = mysql()->query('SQL QUERY HERE')
	public static function connect($datasource=DEFAULT_DB)
	{
        	$tmp = Convert::atoo(App::connections($datasource));
        	if(!$tmp) return new Mysql();
        	// echo "<pre>"; print_r($tmp); die;
        	$c = @(new \mysqli($tmp->host,$tmp->user,$tmp->pass,$tmp->database));
		    // echo "<pre>"; print_r($c); die;
        	if($c->connect_error)
		    {
            	return Core::response(-1,"MYSQL CONNECTION ERROR: " . $c->errno);
        	};
        	@$c->set_charset($tmp->encoding);
        	return new Mysql($c,$datasource);
    	}

    public function __construct(mysqli $obj=null, $datasource=null){
        if($datasource){
            $tmp = Convert::atoo(App::connections($datasource));
            $this->database_ = $tmp->database;
        }
        if($obj) $this->object_ = $obj;
    }

}
