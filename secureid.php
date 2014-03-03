<?
# SecureID implementation
# Copyright (C) 2005 Salvatore Sanfilippo <antirez@gmail.com>
# All Rights Reserved.

# This file implements secure IDs, that can be safely exposed on the web.
# The algoritm is simple. Given the ID, the createSecureId() function
# returns (ID in hex)|(MD5 (<SECRET>|ID in hex|<SECRET>))
# The function readSecureId() check the signature and returns
# the ID if the signature matches, otherwise -1 is returned.
#
# The ID value is bit-shuffled before to be conveted into an HEX number
# in order to obtain some degree of obfuscation of the actual ID number.
#
# Note that the IDs are not encrpyted, but just signed and obfuscated.
# The attacker can't create a new ID, nor a random one, but
# can be able to read the ID cracking just the shuffle step.
# This is usually not a problem for the applications in segnalo.com
# where it is important only that IDs can't be forged, but they can be
# safely read.

$idsecret = "@/@#?2/32kjhasfdkjhsdfe982394895723489uSDIUPSDYsdfupydsfuisyf3";
$shuffleseq = Array("5","3","20","16","9","27","29","23","4","0","21","24","7","6","12","14","11","30","28","22","18","2","15","17","10","25","26","8","13","1","19","31");

function shuffleId($id) {
    global $shuffleseq;
    $sid = 0;
    for ($i = 0; $i < 32; $i++) {
        $sid |= (($id&(1<<$i))>>$i)<<$shuffleseq[$i];
    }
    return $sid;
}

function unshuffleId($sid) {
    global $shuffleseq;
    $id = 0;
    for ($i = 0; $i < 32; $i++) {
        $id |= ((($sid&(1<<$shuffleseq[$i]))>>$shuffleseq[$i])) << $i;
    }
    return $id;
}

function createSecureId($id,$subkey) {
    global $idsecret;
    $hex = sprintf("%x",shuffleId($id));
    $mac = md5($idsecret.$hex.$subkey.$idsecret);
    $mac = substr($mac,0,8);
    return $hex.$mac;
}

function readSecureId($str,$subkey) {
    global $idsecret;
    $l = strlen($str);
    if ($l <= 8) return -1;
    $hex = substr($str, 0, $l-8);
    $mac = substr($str, -8, 8);
    $vmac = md5($idsecret.$hex.$subkey.$idsecret);
    $vmac = substr($vmac,0,8);
    if ($mac !== $vmac) return -1;
    sscanf($hex, "%x", $id);
    return unshuffleId($id);
}

?>
