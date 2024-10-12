<?php
class DB extends EzObject {
    private static $ins = null;
    private $map = [];
	private $dbCon = [];
	private $sysHash = [];

	private const DB_MYSQL = "mysql";
	private const DB_MONGOPS = "mongops";

    private function getSysName($database)
    {
        if(isset($this->sysHash[$database])){
            return $this->sysHash[$database];
        }
        if(isset($this->sysHash["@all"])){
            return $this->sysHash["@all"];
        }
        DBC::throwEx('[Mysql Exception]null database:'.$database);
    }

    public static function getInstance($database, $env = null) {
        return self::get($database, $env);
    }

    private function getDbConfig($database, $env = null):DbConfiguration
    {
        $this->dbCon = Config::getRecursion('db');
        if(is_null($env)){
            DBC::throwEx("[DB] Null Env");
        }
        $this->sysHash = Config::getRecursion('syshash')[$env] ?? [];
        if(empty($this->dbCon) || empty($this->sysHash)){
            DBC::throwEx("[DB] Null DB Config");
        }
        $sysName = $this->getSysName($database);
        $config = $this->dbCon[$sysName];
        /**
         * @var DbConfiguration $dbConfiguration
         */
        $dbConfiguration = EzObjectUtils::create($config, DbConfiguration::class);
        return $dbConfiguration;
    }

    public static function get($database = '', $env = null):IDbSe
    {
        if(null == self::$ins)
        {
            self::$ins = new self();
        }
        $env = null == $env ? Env::get() : $env;
        if(is_null($env)){
            DBC::throwEx("[DB] Null Env");
        }
        $se = self::$ins->map[$database.$env] ?? null;

        if(!$se instanceof IDbSe || $se->isExpired())
        {
            if($se instanceof IDbSe && $se->isExpired()){
                Logger::console("[DB] database $database.$env is expired, rebuilding...");
            }
            $se = self::$ins->getDB($database, $env);
            self::$ins->map[$database.$env] = $se;
        }
        return $se;
    }

    private function getDB($database = '', $env = null):IDbSe
    {
        $dbConfig = self::getDbConfig($database, $env);
        if (BeanFinder::get()->has(DbConfiguration::class)) {
            $dbConfig = BeanFinder::get()->pull(DbConfiguration::class);
        }
        $dbType = $dbConfig->dbType ?? '';
        /**
         * @var IDbSe $se
         */
        $se = null;
        switch($dbType){
            case self::DB_MONGOPS:
                $se = new MongoSql();
                $database = str_replace("_mongo", "", $database);
                break;
            case self::DB_MYSQL:
                $se = new MySqlSE();
                break;
            default:
                DBC::throwEx("[DB]Unknow Db-Type:$dbType");
        }
        return $se->init($dbConfig->host, $dbConfig->port, $dbConfig->user, $dbConfig->pwd, $database);
    }
}
