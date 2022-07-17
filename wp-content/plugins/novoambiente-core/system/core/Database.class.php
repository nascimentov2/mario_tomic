<?php

class database {

    private $sqlServer  = DB_HOST;
    private $sqlUser    = DB_USER;
    private $sqlPass    = DB_PASS;
    private $sqlTable   = DB_NAME;
    
    /**
     * __construct
     * @description Construtor, chama o método que inicializa a conexão com banco de dados
     * 
     * @return void
     */
    public function __construct()
    {
        $this->startConnection();
    }
    
    /**
     * startConnection
     * @description Inicializa a conexão com banco de dados
     * 
     * @return void
     */
    private function startConnection(){
        $con = mysql_connect($this->sqlServer, $this->sqlUser, $this->sqlPass);
        $sel = mysql_select_db($this->sqlTable);
    }
    
    /**
     * closeConnection
     * @description Finaliza a conexão com banco de dados
     * 
     * @return void
     */
    public function closeConnection(){
        mysql_close();
    }

}