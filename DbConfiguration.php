<?php

/**
 * @Configuration("application.datasource.mysql")
 */
class DbConfiguration extends EzObject implements EzBean,EzIgnoreUnknow {
    public $host;
    public $user;
    public $pwd = "";
    /**
     * @Alias("db_type")
     */
    public $dbType;
    public $port = 3306;
    public $database;
}
