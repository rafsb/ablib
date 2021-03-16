<?php
class System extends User
{
    public static function refresh_token(String $hash = null)
    {
        if(!self::allow(ADMIN, self::get_hash($hash))) return Core::response(0, "Use::refresh_system_token -> hash not allowed");
        $who = User::info($hash)->uuid;
        $when = time();
        $hash = Hash::word($who . "@" . $when);
        if(self::update($hash, [ "uuid" => "_system", "hash" => $hash, "who" => $who, "when" => $when ]))
        { 
            IO::write("ROOT", $hash);
            return $hash; 
        }
        return Core::response(0, "System::refresh_token -> failed to update the hash");
    }
}