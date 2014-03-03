<?php
/*******************************************************************************
 * Redis PHP Bindings - http://code.google.com/p/redis/
 *
 * Copyright 2009 Ludovico Magnocavallo
 * Copyright 2009 Salvatore Sanfilippo (ported it to PHP5, fixed some bug)
 * Released under the same license as Redis.
 *
 * Version: 0.1
 *
 * $Revision: 139 $
 * $Date: 2009-03-15 22:59:40 +0100 (Dom, 15 Mar 2009) $
 *
 ******************************************************************************/


class Redis {
    public $server;
    public $port;
    private $_sock;

    public function __construct($host='localhost', $port=6379) {
        $this->host = $host;
        $this->port = $port;
    }
    
    public function connect() {
        if ($this->_sock) return;
        if ($sock = fsockopen($this->host, $this->port, $errno, $errstr)) {
            $this->_sock = $sock;
            return;
        }
        $msg = "Cannot open socket to {$this->host}:{$this->port}";
        if ($errno || $errmsg)
            $msg .= "," . ($errno ? " error $errno" : "") .
                            ($errmsg ? " $errmsg" : "");
        trigger_error("$msg.", E_USER_ERROR);
    }
    
    public function disconnect() {
        if ($this->_sock) @fclose($this->_sock);
        $this->_sock = null;
    }
    
    public function ping() {
        $this->connect();
        $this->write_cmd("PING");
        return $this->get_response();
    }
    
    public function do_echo($s) {
        $this->connect();
        $this->write_cmd("ECHO",$s);
        return $this->get_response();
    }
    
    public function set($name, $value, $preserve=false) {
        $this->connect();
        $this->write_cmd(($preserve ? 'SETNX' : 'SET'),$name,$value);
        return $this->get_response();
    }
    
    public function get($name) {
        $this->connect();
        $this->write_cmd("GET",$name);
        return $this->get_response();
    }

    public function mget($keys) {
        $this->connect();
        $this->write_cmd("MGET",$keys);
        return $this->get_response();
    }
    
    public function incr($name, $amount=1) {
        $this->connect();
        if ($amount == 1)
            $this->write_cmd("INCR",$name);
        else
            $this->write_cmd("INCRBY",$name,$amount);
        return $this->get_response();
    }
    
    public function decr($name, $amount=1) {
        $this->connect();
        if ($amount == 1)
            $this->write_cmd("DECR",$name);
        else
            $this->write_cmd("DECRBY",$name,$amount);
        return $this->get_response();
    }
    
    public function exists($name) {
        $this->connect();
        $this->write_cmd("EXISTS",$name);
        return $this->get_response();
    }
    
    public function delete($name) {
        $this->connect();
        $this->write_cmd("DEL",$name);
        return $this->get_response();
    }
    
    public function keys($pattern) {
        $this->connect();
        $this->write_cmd("KEYS",$pattern);
        return explode(' ', $this->get_response());
    }
    
    public function randomkey() {
        $this->connect();
        $this->write_cmd("RANDOMKEY");
        return $this->get_response();
    }
    
    public function rename($src, $dst) {
        $this->connect();
        $this->write_cmd("RENAME",$src,$dst);
        return $this->get_response();
    }

    public function renamenx($src, $dst) {
        $this->connect();
        $this->write_cmd("RENAMENX",$src,$dst);
        return $this->get_response();
    }
    
    public function expire($name, $time) {
        $this->connect();
        $this->write_cmd("EXPIRE",$name,$time);
        return $this->get_response();
    }
    
    public function push($name, $value, $tail=true) {
        $this->connect();
        $this->write_cmd(($tail ? 'RPUSH' : 'LPUSH'),$name,$value);
        return $this->get_response();
    }

    public function lpush($name, $value) {
        return $this->push($name, $value, false);
    }

    public function rpush($name, $value) {
        return $this->push($name, $value, true);
    }

    public function ltrim($name, $start, $end) {
        $this->connect();
        $this->write_cmd("LTRIM",$name,$start,$end);
        return $this->get_response();
    }
    
    public function lindex($name, $index) {
        $this->connect();
        $this->write_cmd("LINDEX",$name,$index);
        return $this->get_response();
    }
    
    public function pop($name, $tail=true) {
        $this->connect();
        $this->write_cmd(($tail ? 'RPOP' : 'LPOP'),$name);
        return $this->get_response();
    }

    public function lpop($name, $value) {
        return $this->pop($name, $value, false);
    }

    public function rpop($name, $value) {
        return $this->pop($name, $value, true);
    }
    
    public function llen($name) {
        $this->connect();
        $this->write_cmd("LLEN",$name);
        return $this->get_response();
    }
    
    public function lrange($name, $start, $end) {
        $this->connect();
        $this->write_cmd("LRANGE",$name,$start,$end);
        return $this->get_response();
    }

    public function lset($name, $value, $index) {
        $this->connect();
        $this->write_cmd("LSET",$name,$index,$value);
        return $this->get_response();
    }
    
    public function sadd($name, $value) {
        $this->connect();
        $this->write_cmd("SADD",$name,$value);
        return $this->get_response();
    }
    
    public function srem($name, $value) {
        $this->connect();
        $this->write_cmd("SREM",$name,$value);
        return $this->get_response();
    }
    
    public function sismember($name, $value) {
        $this->connect();
        $this->write_cmd("SISMEMBER",$name,$value);
        return $this->get_response();
    }
    
    public function sinter($sets) {
        $this->connect();
        $this->write_cmd("SINTER",$sets);
        return $this->get_response();
    }
    
    public function smembers($name) {
        $this->connect();
        $this->write_cmd("SMEMBERS",$name);
        return $this->get_response();
    }

    public function scard($name) {
        $this->connect();
        $this->write_cmd("SCARD",$name);
        return $this->get_response();
    }

    public function zadd($name, $score, $value) {
        $this->connect();
        $this->write_cmd("ZADD",$name,$score,$value);
        return $this->get_response();
    }

    public function zincrby($name, $score, $value) {
        $this->connect();
        $this->write_cmd("ZINCRBY",$name,$score,$value);
        return $this->get_response();
    }

    public function zrem($name, $value) {
        $this->connect();
        $this->write_cmd("ZREM",$name,$value);
        return $this->get_response();
    }

    public function zscore($name, $value) {
        $this->connect();
        $this->write_cmd("ZSCORE",$name,$value);
        return $this->get_response();
    }

    public function zrange($name, $first, $last, $opt=false) {
        $this->connect();
        $this->write_cmd("ZRANGE",$name,$first,$last,$opt);
        return $this->get_response();
    }

    public function zrevrange($name, $first, $last, $opt=false) {
        $this->connect();
        $this->write_cmd("ZREVRANGE",$name,$first,$last,$opt);
        return $this->get_response();
    }

    public function zremrangebyscore($name, $min, $max) {
        $this->connect();
        $this->write_cmd("ZREMRANGEBYSCORE",$name,$min,$max);
        return $this->get_response();
    }

    public function select_db($name) {
        $this->connect();
        $this->write_cmd("SELECT",$name);
        return $this->get_response();
    }
    
    public function move($name, $db) {
        $this->connect();
        $this->write_cmd("MOVE",$name,$db);
        return $this->get_response();
    }
    
    public function info($raw=false) {
        $this->connect();
        $this->write_cmd("INFO");
        $info = array();
        $data =& $this->get_response();
	if ($raw) return $data;
        foreach (explode("\r\n", $data) as $l) {
            if (!$l)
                continue;
            list($k, $v) = explode(':', $l, 2);
            $_v = strpos($v, '.') !== false ? (float)$v : (int)$v;
            $info[$k] = (string)$_v == $v ? $_v : $v;
        }
        return $info;
    }
    
    private function write($s) {
        while ($s) {
            $i = fwrite($this->_sock, $s);
            if ($i == 0) // || $i == strlen($s))
                break;
            $s = substr($s, $i);
        }
    }

    private function write_cmd() {
        $args = func_get_args();
	$argv = Array();
	foreach($args as $a) {
            if ($a === false) continue;
            if (is_array($a)) {
                foreach($a as $e) {
                    if ($e === false) continue;
                    $argv[] = $e;
                }
            } else {
	        $argv[] = $a;
            }
        }
        $query = "*".count($argv)."\r\n";
        foreach($argv as $a) {
            $query .= "$".strlen($a)."\r\n".$a."\r\n";
        }
        $this->write($query);
    }
    
    private function read($len=1024) {
        if ($s = fgets($this->_sock))
            return $s;
        $this->disconnect();
        trigger_error("Cannot read from socket.", E_USER_ERROR);
    }
    
    private function get_response() {
        $data = trim($this->read());
        $c = $data[0];
        $data = substr($data, 1);
        switch ($c) {
            case '-':
                trigger_error($data, E_USER_ERROR);
                break;
            case '+':
                return $data;
            case ':':
                $i = strpos($data, '.') !== false ? (int)$data : (float)$data;
                if ((string)$i != $data)
                    trigger_error("Cannot convert data '$c$data' to integer", E_USER_ERROR);
                return $i;
            case '$':
                return $this->get_bulk_reply($c . $data);
            case '*':
                $num = (int)$data;
                if ((string)$num != $data)
                    trigger_error("Cannot convert multi-response header '$data' to integer", E_USER_ERROR);
                $result = array();
                for ($i=0; $i<$num; $i++)
                    $result[] =& $this->get_response();
                return $result;
            default:
                trigger_error("Invalid reply type byte: '$c'");
        }
    }
    
    private function get_bulk_reply($data=null) {
        if ($data === null)
            $data = trim($this->read());
        if ($data == '$-1')
            return null;
        $c = $data[0];
        $data = substr($data, 1);
        $bulklen = (int)$data;
        if ((string)$bulklen != $data)
            trigger_error("Cannot convert bulk read header '$c$data' to integer", E_USER_ERROR);
        if ($c != '$')
            trigger_error("Unkown response prefix for '$c$data'", E_USER_ERROR);
        $buffer = '';
        while ($bulklen) {
            $data = fread($this->_sock,$bulklen);
            $bulklen -= strlen($data);
            $buffer .= $data;
        }
        $crlf = fread($this->_sock,2);
        return $buffer;
    }
}

?>
