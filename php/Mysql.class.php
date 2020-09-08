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

    public function update(String $table="")
    {
        $this->operation_ = "UPDATE";
        if($table) $this->tables_ = ($this->database_ ? $this->database_."." : "") . $table;
        else $this->tables_ = " ERR{NO_TABLE} ";
        // $this->tables_ = $this->object_->real_escape_string($this->tables_);
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
        // $this->tables_ = $this->object_->real_escape_string($this->tables_);
        return $this;
    }

    public function set($attributes=null)
    {
        if(sizeof($attributes)){
            $this->attributes_ = [];
            foreach($attributes as $k=>$v) $this->attributes_[] = $k."='".$v."'";
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
        	$this->query_ =
            		$this->operation_
            		. " " . ($this->operation_ == "SELECT" ? $this->fields_ : ($this->operation_ == "UPDATE" ? $this->tables_ : "CASE DELETE"))
            		. ($this->operation_ == "SELECT" ? " FROM " : ($this->operation_ == "UPDATE" ? " SET " : "CASE DELETE"))
            		. " " . ($this->operation_ == "SELECT" ?  $this->tables_ : ($this->operation_ == "UPDATE" ? $this->attributes_ : "CASE DELETE"))
            		. " " . ($this->restrictions_ ? "WHERE " . $this->restrictions_ : "")
            		. " " . ($this->operation_ == "SELECT" && $this->order_ ? "ORDER BY " . $this->order_ : "");

        	$tmp = $this->object_ ? $this->object_->query(implode('{{ NO COMMENTS ALLOWED }}',explode('--',$this->query_))) : null;

            // echo $this->query_;die;

        	if(gettype($tmp) == "object" && $tmp->num_rows)
            {
        		switch($response_type){
            		case(__ASSOC__)     : { $data = (array)$tmp->fetch_assoc(); }                                                      break;
            		case(__ARRAY__)     : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; }                              break;
            		case(__JSON__)      : { $data = []; while($nt = $tmp->fetch_assoc()) $data[] = $nt; $data = json_encode($data); }  break;
            		case(__OBJECT__)    : { $data = Convert::atoo($tmp->fetch_assoc()); }                                              break;
            		case(__MYSQLI_OBJ__): { $data = $tmp; }                                                                            break;
        		}
        	} else Core::response(mysqli_connect_errno(),$this->object_->error . $this->query_);
        	return $data;
    	}

    	## returns a mysqli->mysql valid, based on conn() conn, or 0 if the mysql failed
    	## usage: $result = mysql()->query('SQL QUERY HERE')
	public static function connect($datasource=DEFAULT_DB)
	{
        	$tmp = Convert::atoo(App::connections($datasource));
        	if(!$tmp) return new Mysql();
        	// echo "<pre>"; print_r($tmp); die;
        	$c = @(new \mysqli($tmp->host,$tmp->username,$tmp->passwd,$tmp->database));
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
