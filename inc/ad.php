<?php
error_reporting(E_ALL);
ini_set('display_errors', 'On');
class ad {
	public static $domain = "learnet.se";
	public static $server = "172.16.12.11";
	private static $conn = null;
	private static $username;
	private static $password;
	private static $userinfo;
	public $status = false;
	public $data = false;
	public function __construct($username, $password) {
		self::$username = strip_tags($username) .'@'. $this::$domain;
		self::$password = stripslashes($password);
		self::$conn = ldap_connect("ldap://".$this::$server."/");
		self::$userinfo = self::getUserInfo($username);
		$this->status = "connected";
	}
	public function getUser() {
		return self::$userinfo;
	}
	public function getUserInfo($username) {
		$conn = self::$conn;
		if(!$conn) {
			$err = 'Could not connect to LDAP server';
			$this->status = "disconnected";
		} else {
			if (!defined('LDAP_OPT_DIAGNOSTIC_MESSAGE')) define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
			ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
			$bind = @ldap_bind($conn, self::$username, self::$password);
			ldap_get_option($conn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			if (!empty($extended_error)) {
				$errno = explode(',', $extended_error);
				$errno = $errno[2];
				$errno = explode(' ', $errno);
				$errno = $errno[2];
				$errno = intval($errno);
				if ($errno == 532) {
					$err = 'Unable to login: Password expired';
				}
				$this->status = false;
				echo "<pre>";
				print_r($extended_error);
				echo "</pre>";
			}
			elseif ($bind) {
				$base_dn = "OU=Personal,OU=Kristianstad,OU=ITG,DC=".join(",DC=", explode(".", $this::$domain));
				$result = ldap_search($conn, $base_dn, "cn=".$username."*");
				if (!count($result)) {
					echo "Unable to search: ". ldap_error($conn);
				} else {
					$info = ldap_get_entries($conn, $result);
					$ret = [];
					foreach($info as $v) {
						if(isset($v["cn"][0])) {
							array_push($ret, [
								"user" => $v["cn"][0], 
								"namn" => $v["displayname"][0], 
								"role" => $v["title"][0], 
								"created" => $v["usncreated"][0], 
								"attr" => $v["extensionattribute1"][0], 
								"homedir" => $v["homedirectory"][0],  
								"logontimes" => $v["logoncount"][0], 
								"mail" => $v["mail"][0]]);
						}
					}
					$rows = count($ret);
					if($rows !== 1) {
						$ret = false;
						$this->status = "Login conflict";
					} else {
						$ret = $ret[0];
					}
				}
			}
			$this->status = "logged in";
		}
		if(!isset($err)) $err = "Unable to login: ".ldap_error($conn);
		ldap_close($conn);
		if(isset($ret)) {
			$this->data = $ret;
			return $ret;
		} else {
			return $this->status;
		}
	}
	public function query($query) {
		$conn = self::$conn;
		if(!$conn) {
			$err = 'Could not connect to LDAP server';
			$this->status = false;
		} else {
			if (!defined('LDAP_OPT_DIAGNOSTIC_MESSAGE')) define('LDAP_OPT_DIAGNOSTIC_MESSAGE', 0x0032);
			ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
			ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
			$bind = @ldap_bind($conn, self::$username, self::$password);
			ldap_get_option($conn, LDAP_OPT_DIAGNOSTIC_MESSAGE, $extended_error);
			if (!empty($extended_error)) {
				$errno = explode(',', $extended_error);
				$errno = $errno[2];
				$errno = explode(' ', $errno);
				$errno = $errno[2];
				$errno = intval($errno);
				if ($errno == 532) {
					$err = 'Unable to login: Password expired';
				}
				$this->status = false;
				echo "<pre>";
				print_r($extended_error);
				echo "</pre>";
			}
			elseif ($bind) {
				$base_dn = "OU=Personal,OU=Kristianstad,OU=ITG,DC=".join(",DC=", explode(".", $this::$domain));
				$result = ldap_search($conn, $base_dn, "cn=".$query."*");
				if (!count($result)) {
					echo "Unable to search: ". ldap_error($conn);
				} else {
					$info = ldap_get_entries($conn, $result);
					$ret = [];
					foreach($info as $v) {
						if(isset($v["cn"][0])) {
							array_push($ret, [
								"user" => $v["cn"][0], 
								"namn" => $v["displayname"][0], 
								"role" => $v["title"][0], 
								"created" => $v["usncreated"][0], 
								"attr" => $v["extensionattribute1"][0], 
								"homedir" => $v["homedirectory"][0],  
								"logontimes" => $v["logoncount"][0], 
								"mail" => $v["mail"][0]]);
						}
					}
				}
			}
			$this->status = true;
		}
		if(!isset($err)) $err = "Unable to login: ".ldap_error($conn);
		ldap_close($conn);
		if(isset($ret)) {
			$this->data = $ret;
			return $ret;
		} else {
			return $this->status;
		}
	}
	public function get() {
		return $this->data;
	}
}
?>
