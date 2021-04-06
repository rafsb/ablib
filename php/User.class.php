<?php
define("USER_ONLY_HASH_MODE", true);
define("SHADOW_FILE", "var/users/shadow");
define("TEAM_SIZE_LIMIT", 20);
if(!defined("API_NEEDS_DEVICE_HASH")) define("API_NEEDS_DEVICE_HASH", false);

class _User_Default_Traits
{
    public static function root(String $uuid)
    {
        return [
            "uuid"           => $uuid
            , "username"     => "Almighty"
            , "user"         => "root"
            , "password"     => Hash::word("108698584") // rootz
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::ROOT
            , "hash"         => Hash::word("root")
            , "last_login"   => time()
        ];
    }

    public static function system(String $uuid)
    {
        if(!trim(IO::read("ROOT"))) IO::write("ROOT", Hash::word(time()));
        return [
            "uuid"           => $uuid
            , "username"     => "Sistema"
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::SYSTEM
            , "hash"         => trim(IO::read("ROOT"))
        ];
    }
    
    public static function base()
    {
        return [
            "uuid"           => time()
            , "username"     => ""
            , "user"         => ""
            , "password"     => ""
            , "picture"      => "img/user.svg"
            , "access_level" => EUser::LOGGED
            , "hash"         => Hash::word(time())
            , "last_login"   => ""
            , "projects"     => []
        ];
    }
}

class _User_Primitive_Traits
{
        
    private static function save($list)
    {
        // return Convert::encrypt(SHADOW_FILE, $list);
        return IO::jin(SHADOW_FILE, $list);
    }

    private static function load()
    {
        if(App::driver()==EPersistance::DATABASE)
        {
            $users = Mysql::select()->from("Users")->query(__ARRAY__);
            if(!sizeof($users))
            {
                Mysql::insert("Users", array_merge([ "uuid" => "root_user" ], $default_register))->query();
                $users = Mysql::select()->from("Users")->query(__ARRAY__);
            }
            return $users;
        }
        if(!is_file(IO::root(SHADOW_FILE)))
        {
            $obj = [ 
                "root_user" => _User_Default_Traits::root("root_user")
                , "_system" => _User_Default_Traits::system("_system") 
            ];
            self::save($obj);
        }
        // return _As::json(Convert::decrypt(SHADOW_FILE));
        return IO::jout(SHADOW_FILE);
    }


    public static function list()
    {
        $return = [];
        $list =  self::load();
        if(!$list) return Core::response(-1, "_User_Primitive_Traits::list -> shadow file is empty");
        return $list;
    }

    public static function find(String $field, String $value)
    {
        $user = null;
        $list = self::load();
        if(!$list) return Core::response(0, "_User_Primitive_Traits::find -> shadow file is empty");

        $users = [];

        if(strtoupper($field) == "UUID"){ if(isset($list->{$value})){ $list->{$value}->uuid = $value; $users[] = $list->{$value}; }}
        else foreach($list as $uuid=>$us) if($us->{$field} == $value){ $users[] = $us; };

        return $users;
    }

    public static function hashseek(String $hash)
    {
        $tmp = self::find("hash", $hash);
        if(sizeof($tmp)) return $tmp[0];
        else return Core::response(0, "_User_Primitive_Traits::hashseek -> user not found");
    }

    public static function update(String $uuid, Array $values)
    {
        $user = self::find("uuid", $uuid);
        $user = $user ? (object)$user[0] : (object)[];
        $user->uuid = $uuid;

        foreach($values as $k => $v) $user->{$k} = $v;
        
        $list = self::load();
        $list->{$uuid} = $user;
        return self::save($list);
    }

    public static function delete(String $uuid)
    {
        $user = self::find("uuid", $uuid);
        if(!$user) return Core::response(1, "_User_Primitive_Traits::delete -> user not found");
        
        $list = self::load();
        unset($list->{$uuid});
        return self::save($list);
    }

}

class User extends Activity
{

    /*
     * PRIVATE
     */
    
     private static function pswd_check(String $user, String $password)
    {
        if(!$user||!$password) return Core::response(0,"User::pswd_check -> user or password missing");
        $tmp = _User_Primitive_Traits::find("user", $user);
        if($tmp&&sizeof($tmp)) $tmp = $tmp[0];
        else return Core::Response(0, "User::pswd_check -> no user found");
        return isset($tmp->password)&&$tmp->password==Hash::word($password) ? true : false;
    }

    private static function sign(String $username, String $device=null)
    {
        $user = _User_Primitive_Traits::find("user",$username)[0];
        $time = date("Ymd");
        $hash = Hash::word("{$user->uuid}@$time"); 
        if(_User_Primitive_Traits::update($user->uuid, [ "hash" => $hash, "last_login" => $time, "device" => $device ]))
        {
            IO::log("User::sign -> at $time, $username - $device", "user/$username");
            return $hash;
        }
        return Core::response(0, "User::sign -> error saving new hash/time");
    }
    /*
     * PROTECTED
     */

    /*
     * PUBLIC
     */

    public static function allow(int $level, String $hash)
    {
        $uuid=null;
        $user = _User_Primitive_Traits::find("hash",$hash);
        if($user && sizeof($user)) $uuid = $user[0]->uuid;
        else return Core::response(0, "User::allow -> no valid hash");

        if(App::driver()==EPersistance::DATABASE) return (int)Mysql::cell("Users","access_level","uuid='$uuid'")*1>=$level*1 ? 1 : 0;
        else return $user[0]->access_level*1 >= $level*1 ? 1 : 0;
    }

    public static function pass(String $hash=null, String $device = null)
    {
        $result = self::allow(EUser::LOGGED, self::get_hash($hash)) ? 1 : Core::response(0, "User::pass -> No valid hash");
        if($result){
            $user = self::info($hash);
            IO::log("User::pass -> ". date("Y/m/d h:m:i") .", {$user->username} $device", "user/{$user->username}");
            return $result;
        }
    }
    
    public static function exchange_keys(String $hash=null)
    {
        return self::get_hash($hash) ? self::hashlogin($hash) : Core::response(0, "User:hashlogin -> no valid hash");
    }

    public static function list(String $hash=null)
    {
        if(!self::get_hash($hash)) return Core::response([], "User::list -> no HASH found");
        
        $user = _User_Primitive_Traits::hashseek($hash);
        if(!$user) return Core::response([], "User::list -> not valid hash/no user logged");

        $user_list = _User_Primitive_Traits::list();
        $tmp_list = $user_list;

        if(!User::allow(EUser::ADMIN, $hash))
        {
            $projects_list = [];
            foreach(Projects::list($hash) as $tmp_project) $projects_list[] = $tmp_project->puid;
            foreach($tmp_list as $other_uuid => $other_user)
            {
                if($other_user->uuid != $user->uuid)
                { 
                    if(is_array($other_user->projects) && sizeof($other_user->projects))
                    {
                        $cut = true;
                        foreach($other_user->projects as $puid) if(in_array($puid, $projects_list)) $cut = false;
                        if($cut) unset($user_list->{$other_uuid});
                    } else unset($user_list->{$other_uuid});
                }
            }
        }
        $tmp = [];
        foreach($user_list as $u) if($user->uuid == $u->uuid || $user->access_level == EUser::ROOT || $u->access_level < $user->access_level) $tmp[] = $u;
        return $tmp;
    }

    public static function info(String $hash=null)
    {
        if(!self::allow(EUser::LOGGED, self::get_hash($hash))) return Core::response(0, "User::bio -> no user logged");
        $user = _User_Primitive_Traits::hashseek($hash);
        if($user) unset($user->password);
        return $user ? $user : Core::response(0, "User::info -> not found");
    }


    public function login(String $user=null, String $pswd=null, String $device=null)
    {
        $args = Request::in();
        $user = $user ? $user : $args["user"];
        if(!$user) return Core::response(0,"User::login -> no user found");

        $pswd = $pswd ? $pswd : $args["pswd"];
        if(!$pswd) return Core::response(0,"User::login -> no password hash found");

        $device = $device ? $device : $args["device"];
        if(API_NEEDS_DEVICE_HASH&&!$device) return Core::response(0,"User::login -> no device hash found");

        if(self::pswd_check($user, $pswd)) return self::sign($user, $device);
        
        return Core::response(0, "User::login -> incorrect credentials");;
    }

    public static function hashlogin(String $hash = null, String $device = null)
    {   
        if(!self::get_hash($hash)) return Core::response(0, "User::hashlogin -> no HASH found");

        $device = $device ? $device : $args["device"];
        if(API_NEEDS_DEVICE_HASH&&!$device) return Core::response(0,"User::login -> no device hash found");

        $user = self::info($hash);
        if($user) return self::sign($user->user, $device);
        return Core::response(0, "User::hashlogin -> invalid hash");
    }

    public static function logoff(String $hash)
    {
        $user = _User_Primitive_Traits::hashseek($hash);
        if($user) return _User_Primitive_Traits::update($user->uuid, [ "hash" => "" ]);
        return Core::response(0, "User::logoff -> no user found");
    }

    public static function uuid(String $hash = null, String $uuid = null)
    {
        if(!self::get_hash($hash)) return Core::response([], "User::uuid -> no valid hash");
        if(!$uuid) $uuid = Request::in("uuid");
        if(!$uuid) return Core::response([], "User::uuid -> no UUID found");
        $user = _User_Primitive_Traits::find("uuid", $uuid)[0];
        // print_r($user);die;
        if(!$user || !self::allow($user->access_level, $hash)) return Core::response(0, "User::uuid -> No user found or HASH without privileges");
        return $user;
    }

    public static function update(String $hash, array $new_user = null)
    {
        
        $user = self::info($hash);
        $new_user = $new_user ? $new_user : Request::in();
        if($user->access_level >= EUser::MANAGER && $new_user)
        {
            $admin = $user->access_level < EUser::ADMIN ? false : true;
            if(!$new_user["uuid"]) $new_user["uuid"] = time();
            if($new_user["access_level"]==null) $new_user["access_level"] = $user->access_level - 1;
            else if($user->access_level < $new_user["access_level"]) return Core::response(0, "User::update -> permission`s issues");
            if(!$new_user["username"]) unset($new_user["username"]);
            if(!$new_user["user"]) unset($new_user["user"]);
            if(!$new_user["hash"]) $new_user["hash"] = Hash::word(time());
            if(!$admin)
            {
                if(!$new_user["team_limit"]) $new_user["team_limit"] = TEAM_SIZE_LIMIT;
                if(!$new_user["groups_blacklist"]) $new_user["groups_blacklist"] = [];
                if(!$new_user["charts_blacklist"]) $new_user["charts_blacklist"] = [];
            }
            if($new_user["password"]) $new_user["password"] = Hash::word($new_user["password"]); else unset($new_user["password"]);
            return _User_Primitive_Traits::update($new_user["uuid"], $new_user) ? 1 : 0;
        } 
        return Core::response(0, "User::update -> something went wrong man...");
    }
    
    public static function delete(String $hash=null)
    {
        if(self::get_hash($hash))
        {
            $uuid = Request::in("uuid");
            if(in_array($uuid, [ "root_user", "_system" ])) return Core::response(0, "User::delete -> these users cannot be removed");
            if(self::allow(self::uuid($hash, $uuid)->access_level, $hash)) return _User_Primitive_Traits::delete($uuid)&&1; 
        }
        return Core::response(0, "User::delete -> no hash or bad permissions");
    }

    public static function add(String $hash)
    {
        if(self::get_hash($hash)&&self::allow(EUser::MANAGER,$hash))
        {
            if(!self::allow(EUser::ADMIN, $hash))
            {
                $user = self::info($hash);
                if(!isset($user->team_limit))
                {
                    $user->team_limit = TEAM_SIZE_LIMIT;
                    self::update($user->uuid, (array)$user);
                } 
                if(sizeof(self::list($hash)) >= $user->team_limit) return Core::response(0, "User::add -> user team limit exedded");
            }
            $user = _User_Default_Traits::base();
            if(!User::allow(EUser::ADMIN,$hash)) $user["projects"] = User::info($hash)->projects;
            return _User_Primitive_Traits::update($user["uuid"], $user) ? 1 : 0;
        }
        return Core::response(0, "User::add -> no hash or bad permissions");
    }

    public static function projectadd(String $hash=null, String $uuid = null, String $puid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::ADMIN,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$puid) $puid = isset($args->puid) ? $args->puid : false;
            if(!($hash&&$uuid&&$puid)) return Core::response(0, "User::projectadd -> missing parameters");

            $user = self::uuid($hash,$uuid);
            if(!$user) return Core::response(0, "User::projectadd -> no user found");
            
            if(!isset($user->projects)||!$user->projects) $user->projects = [];
            if($user->access_level >= EUser::ADMIN || in_array($puid, $user->projects)) return Core::response(0, "User::projectadd -> aready there");

            if(is_dir(IO::root("var/users/projects/$puid")))
            {
                $user->projects[] = $puid;
                return self::update($hash, (array)$user);
            }
            return Core::response(0, "User::projectadd -> the project did not exist");
        }
        return Core::response(0, "User::projectadd -> no hash or bad permissions");
    }

    public static function projectdel(String $hash=null, String $uuid = null, String $puid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::ADMIN,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$puid) $puid = isset($args->puid) ? $args->puid : false;
            if(!($hash&&$uuid&&$puid)) return Core::response(0, "User::projectdel -> missing parameters");

            $user = self::uuid($hash,$uuid);

            if(!$user) return Core::response(0, "User::projectdel -> no user found oor admin");
            if($user->access_level >= EUser::ADMIN) return Core::response(0, "User::projectdel -> cannot remove project from an ADMIN+");

            if(!isset($user->projects)||!is_array($user->projects))
            {
                $user->projects = [];
                self::update($user->uuid, (array)$user);
                return Core::response(1, "User::projectdel -> project array created now");
            }
            if(!in_array($puid, $user->projects)) return Core::response(1, "User::projectdel -> not there");
            else
            {
                $tmp = [];
                foreach($user->projects as $p) if($puid != $p) $tmp[] = $p;
                $user->projects = $tmp;
                return self::update($hash, (array)$user) ? 1 : 0;
            }
            return Core::response(0, "User::projectdel -> the project could not be removed");
        }
        return Core::response(0, "User::projectdel -> no hash or bad permissions");
    }

    public static function groupadd(String $hash=null, String $uuid = null, String $guid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::EDITOR,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$guid) $guid = isset($args->guid) ? $args->guid : false;
            if(!($hash&&$uuid&&$guid)) return Core::response(0, "User::groupadd -> missing parameters");

            $user = self::uuid($hash,$uuid);
            if(!$user) return Core::response(0, "User::groupadd -> no user found");
            
            if(!isset($user->groups_blacklist)||!is_array($user->groups_blacklist)) $user->groups_blacklist = [];

            $tmp = [];
            foreach($user->groups_blacklist as $g) if($g != $guid) $tmp[] = $g;
            $user->groups_blacklist = $tmp;

            return self::update($hash, (array)$user) ? 1 : 0;
        }
        return Core::response(0, "User::groupadd -> no hash or bad permissions");
    }

    public static function groupdel(String $hash=null, String $uuid = null, String $guid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::EDITOR,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$guid) $guid = isset($args->guid) ? $args->guid : false;
            if(!($hash&&$uuid&&$guid)) return Core::response(0, "User::groupdel -> missing parameters");

            $user = self::uuid($hash,$uuid);

            if(!$user) return Core::response(0, "User::groupdel -> no user found oor admin");
            if($user->access_level >= EUser::ADMIN) return Core::response(0, "User::groupdel -> cannot remove project from an ADMIN+");

            if(!isset($user->groups_blacklist)||!is_array($user->groups_blacklist))
            {
                $user->groups_blacklist = [ $guid ];
                // IO::log("User::groupdel -> blacklist array created for user {$user->uuid}", strtolower(get_called_class()));
                return self::update($user->uuid, (array)$user);
            }
            
            if(in_array($guid, $user->groups_blacklist)) return Core::response(1, "User::groupdel -> already there");
            
            $user->groups_blacklist[] = $guid;

            return self::update($hash, (array)$user) ? 1 : 0;
        }
        return Core::response(0, "User::groupdel -> no hash or bad permissions");
    }

    public static function chartadd(String $hash=null, String $uuid = null, String $cuid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::USER,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$cuid) $cuid = isset($args->cuid) ? $args->cuid : false;
            if(!($hash&&$uuid&&$cuid)) return Core::response(0, "User::chartadd -> missing parameters");

            $user = self::uuid($hash,$uuid);
            if(!$user) return Core::response(0, "User::chartadd -> no user found");
            
            if(!isset($user->charts_blacklist)||!is_array($user->charts_blacklist)) 
            {
                $user->charts_blacklist = [];
                // IO::log("User::chartadd -> charts_blacklist created for user {$user->uuid}", strtolower(get_called_class()));
                return self::update($hash, (array)$user) ? 1 : 0;
            }

            $tmp = [];
            foreach($user->charts_blacklist as $g) if($g != $cuid) $tmp[] = $g;
            $user->charts_blacklist = $tmp;

            return self::update($hash, (array)$user) ? 1 : 0;
        }
        return Core::response(0, "User::chartadd -> no hash or bad permissions");
    }

    public static function chartdel(String $hash=null, String $uuid = null, String $cuid = null)
    {
        if(self::get_hash($hash)&&User::allow(EUser::USER,$hash))
        {
            $args = (object)Request::in();
            if(!$uuid) $uuid = isset($args->uuid) ? $args->uuid : false;
            if(!$cuid) $cuid = isset($args->cuid) ? $args->cuid : false;
            if(!($hash&&$uuid&&$cuid)) return Core::response(0, "User::chartdel -> missing parameters");

            $user = self::uuid($hash,$uuid);

            if(!$user) return Core::response(0, "User::chartdel -> no user found oor admin");
            if($user->access_level >= EUser::ADMIN) return Core::response(0, "User::chartdel -> cannot remove project from an ADMIN+");

            if(!isset($user->charts_blacklist)||!is_array($user->charts_blacklist))
            {
                $user->charts_blacklist = [ $cuid ];
                self::update($user->uuid, (array)$user);
                return Core::response(1, "User::chartdel -> blacklist array created now");
            }
            
            if(in_array($cuid, $user->charts_blacklist)) return Core::response(1, "User::chartdel -> already there");
            
            $user->charts_blacklist[] = $cuid;

            return self::update($hash, (array)$user) ? 1 : 0;
        }
        return Core::response(0, "User::chartdel -> no hash or bad permissions");
    }
}
