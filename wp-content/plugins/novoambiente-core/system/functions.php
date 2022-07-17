<?php if( !isset($_SESSION) ){ session_start(); }
        
    /**
     * hm_sql_prepare function: Monta a string pra consulta no banco de dados 
     * 
     * @param Data (array) Campos e valores do banco no formato array("campo"=>"valor")
     * @param Type (string) Tipo de atualização "insert" ou "update"
     * @return Retorna a string da consulta sql solicitada
     * @uses hm_sql_mount()
     * */
    function hm_sql_prepare($data, $type){

        $data = hm_sql_mount($data);
        $sql_query = "";
        $count = 1;
        $size_data = count($data);
        
        switch( $type ){
            case "insert":
            $sql_query_campos = "(";
            $sql_query_valores = "(";
            foreach( $data as $key => $value ):
                $value = ( (is_numeric($value)) || ($value == 'NOW()') ) ? $value : "'".$value."'" ;
                $end = ( $count < $size_data ) ? " , " : " )" ;
                $sql_query_campos .= $key.$end;
                $sql_query_valores .= $value.$end;
                $count++;
            endforeach;
            $sql_query = $sql_query_campos." VALUES ".$sql_query_valores;
            break;

            case "update":
            foreach( $data as $key => $value ):
                $value = ( (is_numeric($value)) || ($value == 'NOW()') ) ? $value : "'".$value."'" ;
                $end = ( $count < $size_data ) ? " , " : "" ;
                $sql_query .= $key." = ".$value.$end;
                $count++;
            endforeach;
            break;
        }

            return $sql_query;
    }

    //Escape string dos dados SQL
    function hm_sql_mount($data){

        foreach( $data as $campo => $valor ):
            $sql[$campo] =  mysql_escape_string($valor);    
        endforeach;

            return $sql;
    }

    /**
     * hm_cript_pass method: Criptografia de senha
     * 
     * @param Pass (string) Senha sem criptografia
     * @param Level (int) Level de segurança
     * @return Senha 10 niveis de criptografia sha1 sobre 10 niveis de criptografia md5
     * @uses $autosql->set
     * */
    function hm_cript_pass($pass, $level=10){

        for($i=0; $i<$level; $i++){
            $pass = md5($pass);
            $pass = sha1($pass);
        }
        $final_pass = hash('sha512', $pass);
            return $final_pass;
    }   
    
    // Monta os dados em formato de array no return dos controllers
    function hm_mount_data($status, $result=NULL, $error=NULL){
        
        return array("status" => $status, "result" => $result, "error" => $error);
    }
    
    //Gera uma chave de acesso @niveis --> 1 (alfanumérico), 2 (alfanumérico(Aa)), 3 (alfanumérico(Aa)+simbolos)
    function hm_gera_chave($tamanho=20, $nivel=1){
        
        //Conjuntos de caracteres
        $lower      = 'abcdefghijklmnopqrstuvwxyz';
        $upper      = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $cNumbers       = '1234567890';
        $simbols        = '!@#$%*-';
        $chave          = '';
        $caracteres     = '';

        $caracteres .= $lower;
        $caracteres .= $cNumbers;
            if ($nivel > 1) { $caracteres .= $upper; }
            if ($nivel > 2) { $caracteres .= $simbols; }

        $thisLengh = strlen($caracteres);
            for ($n = 1; $n <= $tamanho; $n++) {
                $rand = mt_rand(1, $thisLengh);
                $chave .= $caracteres[$rand-1];
            }

            return $chave;
    }