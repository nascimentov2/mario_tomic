<?php if( !isset($_SESSION) ){ session_start(); }

    abstract class Sql {

        protected function get($campos, $parametros, $retorno=false){
        
            if( empty($campos) ) { $campos = "*"; }

            $sql = "SELECT SQL_CALC_FOUND_ROWS ".$campos." FROM ".$this->tabela." ".$parametros;
            
            $query = mysql_query( $sql );
                                            
            switch($retorno):
                case("objeto"):
                    return mysql_fetch_object($query);
                    break;
                case("array"):
                    return mysql_fetch_array($query, MYSQL_ASSOC);
                    break;
                case("rows"):
                    return mysql_num_rows($query);
                    break;
                default:
                    return $query;
                    break;
            endswitch;
        }
        

        protected function set($identificador, $set_data){
        
            $parametros = hm_sql_prepare($set_data, "update");
                                
            $sql = "UPDATE ".$this->tabela." SET ".$parametros." WHERE ".$this->index." = ".$identificador;
            //print $sql;
            $query = mysql_query( $sql );

            return $query;
        }
        
        protected function insert($set_data, $get_id=false){
            
            $parametros = hm_sql_prepare($set_data, "insert");
            
            if( isset($get_id) ) { mysql_query('LOCK TABLES '.$this->tabela.' WRITE;'); }
                        
            $sql = "INSERT INTO ".$this->tabela." ".$parametros;
            $query = mysql_query( $sql );
            
                if( $query ):
                    if( isset($get_id) ):
                        $id_criado = mysql_insert_id();
                        mysql_query('UNLOCK TABLES');
                        return $id_criado;
                    else:
                        return true;
                    endif;
                else:
                    if( $get_id ) { mysql_query('UNLOCK TABLES'); }
                    return false;
                endif;
        }
        
        protected function delete($identificador){
        
                
            $sql = "DELETE FROM ".$this->tabela." WHERE ".$this->index." = ".$identificador;
            $query = mysql_query( $sql );
            
                if( $query ):
                    return true;
                else:
                    return false;
                endif;
        
        }
        
        protected function getSql($query){
            
            $action = mysql_query($query);
                return $action;
        }
        
        /**
         * validaCampos
         * @description Valida se os campos obrigatórios foram preenchidos
         * 
         * @param $form_data(array) Campos do formulário
         * 
         * @return $boolean() Em caso de false a variavel de classe invalidFields será populada com os erros
         */
        protected function validaCampos($form_data)
        {
            $erros = false;
            
            foreach($form_data as $campo => $valor):
                if(isset($this->validFields[$campo])){
                    if(empty($valor)){
                        $erros = true;
                        $this->invalidFields .= '<p>'.$this->validFields[$campo].'</p>';
                    }
                }
            endforeach;
            
            if($erros === FALSE):
                return true;
            else:
                return false;
            endif;
        }

        /**
         * registrar
         * @description Registra logs
         * 
         * @param $form_data(array) Campos do formulário
         * 
         * @return $boolean() Em caso de false a variavel de classe invalidFields será populada com os erros
         */
        protected function registrar($data, $metodo){
        
            /*
            if(!empty($data['assunto']) && !empty($data['corpoemail'])):
                $this->sendEmail();
            endif;
            */
        
            foreach($data as $dados => $valor):
                $dadosClean[$dados] = mysql_real_escape_string($valor); 
            endforeach;
            
            $i = "INSERT INTO ".DB_REGISTROS." (id_usuario, quando, mensagem, metodo) VALUES (".$dadosClean['id_usuario'].", NOW(), '".$dadosClean['mensagem']."', '".$metodo."')";             
            $q = mysql_query($i);
        
        }

        protected function enviarEmail($to, $assunto, $titulo, $mensagem){

            //$urlBase = urlBase;
            // multiple recipients (note the commas)
            // $to de producao
            //$to .= ", thalles@homemmaquina.com.br";
            // $to de dev
            //$to = "thalles@homemmaquina.com.br";
            
            // subject
            $subject = "Novo Ambiente | $assunto";
            $disclaimer = 'Esta é uma mensagem automática, por favor não responda ao remetente. Qualquer dúvida acesse nossa <a href="'.BASE_URL.'" style="color:#6d7684">página</a> ou entre em <a target="_blank" style="color:#6d7684" href="'.BASE_URL.'/contato/">contato</a>.';

            if((isset($mensagem['orcamento']))&&($mensagem['orcamento']=='SIM')):

                $argsUsuario = array(
                    "titulo" => $titulo,
                    "mensagem" => $mensagem['usuario'],
                    "email-logo" => EMAIL_LOGO,
                    "disclaimer" => $disclaimer,
                    "bgcolor" => '#f0f0f0',
                    "color" => '#101010',
                    "font" => '13px/18px arial, helvetica, sans-serif',
                    "bgcolor_minor" => '#e8e8e8',
                    "color_minor" => '#101010'
                );
                $mensagemUsuario = $this->emailTemplate($argsUsuario);

                $argsConsultor = array(
                    "titulo" => $titulo,
                    "mensagem" => $mensagem['consultor'],
                    "email-logo" => EMAIL_LOGO,
                    "disclaimer" => $disclaimer,
                    "bgcolor" => '#f0f0f0',
                    "color" => '#101010',
                    "font" => '13px/18px arial, helvetica, sans-serif',
                    "bgcolor_minor" => '#e8e8e8',
                    "color_minor" => '#101010'
                );
                $mensagemConsultor = $this->emailTemplate($argsConsultor);

                // compose message
                

                // To send HTML mail, the Content-type header must be set
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "From: Novo Ambiente <contato@novoambiente.dreamhosters.com>\r\n"; 
                $headers .= "BCC: Richard <richard@homemmaquina.com.br>, Thalles <thalles@homemmaquina.com.br>\r\n";

                // send email
                mail('crosman.bruno@gmail.com, pedro.leite@novoambiente.com.br, camila.wergles@novoambiente.com', 'Novo orçamento | '.$assunto, $mensagemConsultor, $headers);
                mail($to, $subject, $mensagemUsuario, $headers);

            else:

                $args = array(
                    "titulo" => $titulo,
                    "mensagem" => $mensagem,
                    "email-logo" => EMAIL_LOGO,
                    "disclaimer" => $disclaimer,
                    "bgcolor" => '#f0f0f0',
                    "color" => '#101010',
                    "font" => '10px/18px arial, helvetica, sans-serif',
                    "bgcolor_minor" => '#e8e8e8',
                    "color_minor" => '#101010'
                );
                $mensagem = $this->emailTemplate($args);
                // compose message
                

                // To send HTML mail, the Content-type header must be set
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
                $headers .= "From: Novo Ambiente <contato@homemmaquina.com.br>\r\n"; 
                $headers .= "BCC: Richard <richard@homemmaquina.com.br>, Thalles <thalles@homemmaquina.com.br>\r\n";

                // send email   
                mail($to, $subject, $mensagem, $headers);

            endif;

        }

        private function emailTemplate($args){


            $message = file_get_contents(PATH_PLUGIN.'/app/view/templates/email.html');
            
            $message = str_replace('{titulo}', $args['titulo'], $message);
            $message = str_replace('{email-logo}', $args['email-logo'], $message);
            $message = str_replace('{mensagem}', $args['mensagem'], $message);

            $message = str_replace('{color}', $args['color'], $message);
            $message = str_replace('{bgcolor}', $args['bgcolor'], $message);
            
            $message = str_replace('{color_minor}', $args['color_minor'], $message);
            $message = str_replace('{bgcolor_minor}', $args['bgcolor_minor'], $message);
            
            $message = str_replace('{font}', $args['font'], $message);
            $message = str_replace('{disclaimer}', $args['disclaimer'], $message);

            return $message;

        }

    }