<?php
class Mysql {
    ## returns a mysqli->mysql valid, based on conn() conn, or 0 if the mysql failed
    ## usage: $result = mysql()->query('SQL QUERY HERE')
    function conn($datasource=DEFAULT_DB){
        $tmp = (new App()) -> mysql_config($datasource);
        $c = new \mysqli($tmp -> host,$tmp -> user,$tmp -> pass,$tmp -> base);
        $c->set_charset("utf8");
        if($c->connect_error){ if(DEBUG) echo PHP_EOL . "MYSQL CONNECTION ERROR"; return -1; }
        return $c;
    }

    ## perform a query into local mysql database based on mysql()
    ## it can perform any query string, althogh is designed to work better with
    ## insertions Queries (i.e: qin('INSERT INTO table VALUES('...','...'))
    ## return 1 for success query and 0 as it fails
    function in($table,$obj,$datasource=DEFAULT_DB){
        if(!$table||!$obj){ if(DEBUG) echo PHP_EOL . "TABLE, OBJECT OR DATABASE MISSING: T[$table] - O[".var_dump($obj)."] - D[$datasource]"; return null; }
        $table = "INSERT INTO $table ('";
        $tmp = ") VALUES ('";
        foreach($obj as $k=>$v){ $table.="$k',"; $tmp.="$v',"; }
        $table = substr($table,0,strlen($table)-1).substr($tmp,0,strlen($tmp)-1).")";
        $conn = Mysql::conn($datasource);
        if($conn -> query( $table )){ 
            $_SESSION["MYSQL_LAST_INSERTED_ID"] = $c->insert_id;
            return $c->insert_id;
        }
        else{ if(DEBUG) echo PHP_EOL . "MYSQL QUERY DIDN'T WORK: $table"; return 0; }
    }

    ## perform a query into local mysql database based on mysql()
    ## it can perform any query string, althogh is designed to work better with
    ## selections Queries (i.e: qio('SELECT * FROM table')
    ## return the entire object Mysql::result_object if some register matches the search query and 0 as it fails
    ## usage: $result = qout('SELECT * FROM table')
    function out($query,$obj=__MYSQLI_OBJ,$datasource=DEFAULT_DB){
        if(!$query){ if(DEBUG) echo PHP_EOL . "NO QUERY GIVEN"; return null; }
        $result = Convert::atoo(["status"=>0, "data"=>null]);
        if($query && (strpos(strtolower($query),"select") >= 0)){
            $record = (Mysql::conn($datasource)) -> query( $query );
            if(gettype($record) == "object" && $record -> num_rows){
                $data = null;
                switch($obj){
                    case(__ASSOC)     : { $data = (array)$record->fetch_assoc(); }                                                      break;
                    case(__ARRAY)     : { $data = []; while($nt = $record->fetch_assoc()) $data[] = $nt; }                              break;
                    case(__JSON)      : { $data = []; while($nt = $record->fetch_assoc()) $data[] = $nt; $data = json_encode($data); }  break;
                    case(__OBJECT)    : { $data = Convert::atoo($record->fetch_assoc()); }                                              break;
                    case(__MYSQLI_OBJ) : { $data = $record; }                                                                            break;
                }
                $result -> status = true;
                $result -> data = ($data ? $data : Convert::atoo(["error"=>"empty data"]));
            }
        }
        return $result;
    }


    ## perform a query into local mysql database based on mysql()
    ## it can perform any query string, althogh is designed to work better with
    ## selections Queries (i.e: qio('SELECT * FROM table')
    ## return 1 if some register matches the search query and 0 as it fails
    function count($query,$datasource=DEFAULT_DB){
        if(!$query){ if(DEBUG) echo PHP_EOL . "NO QUERY: Q[$queryt]"; return null; }
        $request = Mysql::out($query,__MYSQLI_OBJ,$datasource);
        if($request -> status && $request -> data && $request -> data -> num_rows) return (int)$request -> data -> num_rows;
        else return 0;
    }
    ## return a value from a single cell into the mysql result query, if it matches, else return 0
    ## $t: indicates the table wich it will search for a register
    ## $f: inform what cell exatanly it will read
    ## $r: stands for restrictions, as 'code=1', if it argument is blank, the result may be unlike your which
    ## because the first matched row's cell will be returned
    function cell($table,$cell,$restriction=null,$datasource=DEFAULT_DB){
        if(!$restriction) $restriction="id='".User::logged()."'";
        $tmp = Mysql::out("select $cell from $table where $restriction",__ASSOC,$datasource);
        $tmp = ($tmp -> status ? (isset($tmp -> data[ $cell ]) ? $tmp -> data[ $cell ] : "-2") : "-1" );
        return $tmp;
    }
}