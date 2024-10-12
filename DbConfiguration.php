<?php

/**
 * @Configuration("application.datasource.mysql")
 */
class DbConfiguration extends EzObject implements EzBean {
    public $host;
    public $user;
    public $pwd = "";
    public $dbType;
    public $port = 3306;
}
