<?php

/**
 * @Configuration("application.datasource.mysql")
 */
class DbConfiguration extends EzObject implements EzBean {
    public $host;
    public $user;
    public $pwd = "";
    /**
     * @Alias("db_type")
     */
    public $dbType;
    public $port = 3306;
}
