<?php
/**
 * データベースアクセスクラス
 *
 * データベースへのアクセスを行うクラス
 *
 * Developed on PHP versions 5.1.6
 *
 * @category	database
 * @package		phpadhoc
 * @author		makies <makies@gmail.com>
 * @license		別紙契約内容を参照
 * @see
 * @since
 *
 */
class DatabaseAccess {
	
	/**
	 *
	 */
	protected $db;
	private static $inst_singleton;			// 一般インスタンス
	
	public function __construct() {
		$this->connectDB();
	}




	static function singleton() {
		if (!isset(self::$inst_singleton)) {
			self::$inst_singleton = new DatabaseAccess(false);
		}
		
		return self::$inst_singleton;
		
	}




	/**
	 * DB接続する
	 */
	protected function connectDB (){
		if(!$this->db){
			try {
				$this->db = new PDO(sprintf('mysql:host=%s;port=%d;dbname=%s', DB_HOST, DB_PORT, DB_NAME), DB_USER, DB_PASS);
				$this->db->setAttribute(PDO::ATTR_PERSISTENT, true);
				$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
				$this->db->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
				$this->db->query('SET CHARACTER SET UTF8');
			} catch (PDOException $exc) {
				$_connection_error = $exc->getMessage();
				return false;
			}
		}
	}




	/**
	 * SELECT文を実行する
	 * @param string $sql_str SQL文
	 * @param array $sql_param パラメータ
	 * @param bool $all true:fetchAll false:fetch
	 * @return mixed
	 */
	protected function fetch($sql_str, $sql_param, $all = true) {
		// SQLを実行
		$stmt = $this->db->prepare($sql_str);
		$stmt->execute($sql_param);
		if ($all) {
			return $stmt->fetchAll(PDO::FETCH_ASSOC);
		} else {
			return $stmt->fetch(PDO::FETCH_ASSOC);
		}
	}




	/**
	 * SQLを実行する
	 * @param	string	$sql_str	実行するSQL(INSERT, UPDATE, DELETE文)
	 * @param	array	$sql_param	バインドするパラメータ
	 * @return	int					作用した行数
	 */
	protected function execute($sql_str, $sql_param = array())
	{
		// SQLを実行
		$stmt = $this->db->prepare($sql_str);
		$stmt->execute($sql_param);
		return $stmt->rowCount();
	}
	
	
	/**
	* 指定したkeyを持つアプリケーションの情報を取得する
	* @param string $ipa application.key の値
	* @return array
	*/
	function find_application($ipa) {
		$sql_str = 'SELECT * FROM `application` WHERE `key` = ?';
		$sql_param = array($ipa);
		$res = $this->fetch($sql_str, $sql_param, false);
		if (count($res)) {
			return $res;
		}
	}




	/**
	* 指定したkeyを持つアプリケーションの情報を取得する
	* @return array
	*/
	function find_application_list() {
		$sql_str = 'SELECT * FROM `application` ORDER BY timestamp DESC';
		$sql_param = array();
		$res = $this->fetch($sql_str, $sql_param);
		return $res;
	}




	function insertApplication($key, $name, $version, $identifier, $size, $minos, $file_name, $pass = null, $memo = null) {
		
		v(func_get_args());
		$data = array(
			'name' => $name,
			'key' => $key,
			'version' => $version,
			'identifier' => $identifier,
			'size' => "$size",
			'minimumOS' => $minos,
			'file_name' => $file_name,
		);
		if (!is_null($pass)) {
			$data['pass'] = sha1($pass);
		}
		if (!is_null($memo) and strlen($memo)) {
			$data['memo'] = $memo;
		}
		$target = array_keys($data);
		$values = array_fill(0, count($data), '?');
		
		$sql_param = array_values($data);
		$sql_str = sprintf('INSERT INTO  `application` (`%s`) VALUES (%s);', implode('`, `', $target), implode(', ', $values));
		try {
			$this->myBegin();
			if (1 == $this->execute($sql_str, $sql_param)) {
				$this->myCommit();
				return true;
			}
		} catch (PDOException $e) {
			$this->myRollback();
			echo $e->getLine();
			echo $e->getFile();
			v($e->getMessage());
			v($e);
			exit;
			return false;
		}
		
	}




	/**
	 * トランザクション開始
	 */
	protected function myBegin() {
		$this->execute('begin;');
	}




	/**
	 * トランザクション ロールバック
	 */
	protected function myRollback() {
		$this->execute('rollback;');
	}




	/**
	 * トランザクション コミット
	 */
	protected function myCommit() {
		$this->execute('commit;');
	}
}