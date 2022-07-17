<?php

class usuario extends Usuarios {
	
		public $id;
		public $nome;
		public $apelido;
		public $nascimento;
		public $cidade;
		public $uf;
		public $biografia;
		public $email;
		public $avatar;
		public $site;
		public $admin;
		
		public function totalUsuarios ($filtro="ORDER BY nome"){
		
			$c = "SELECT * FROM ".DB_USUARIOS." ".$filtro;
			$q = mysql_query ($c);
			$r = mysql_num_rows ($q);
			
			return $r;		
		
		}
		
		public function exibirUsuarios($quantidade=30, $filtro="ORDER BY nome", $ordem="",$inicio=0){
		
			$c = "SELECT * FROM ".DB_USUARIOS." ".$filtro." LIMIT $inicio, $quantidade";
			$q = mysql_query($c);

				return $q;				
			
		}
		
		public function getEstados(){
		
			$c = "SELECT * FROM ".DB_CIDADES." GROUP BY uf";
			$q = mysql_query($c);
								
			return $q;				
			
		}

		public function setStatusUsuario($data){
		
			$sql = "UPDATE ".DB_USUARIOS." SET ativado = ".$data["novo_status"]." WHERE id = ".$data["id_usuario"];
			$query = mysql_query( $sql );
			
				if($query):
					return true;
				else:
					return false;
				endif;
		}
		
		public function getInvestimentosUsuario($id, $idprojeto, $status=1){
		
		
		//$c = "SELECT * FROM investimentos WHERE idusuario = '$id' AND status = '$status'";
		$c	= "SELECT count(id) as total FROM investimentos WHERE idusuario = '$id' AND status = '$status' AND idprojeto <> '$idprojeto' GROUP BY idprojeto";
		$q	= mysql_query ($c);
		$l = mysql_num_rows ($q);
		
			return $l;
		}
		
		//Retorna para a página de pérfil os projetos que o usuário apoia e que criou
		public function getProjetosUsuario($id_usuario, $tipo){
		
			if( $tipo == "criado" ):
				$sql = "SELECT * FROM projetos WHERE autor = $id_usuario AND (status = 4 OR status = 5 OR status = 6) ORDER BY data_aprovacao DESC";
			elseif( $tipo == "apoiado" ):
				$sql  = "SELECT * FROM investimentos, projetos WHERE investimentos.idusuario = $id_usuario AND investimentos.status = 1 AND investimentos.idprojeto = projetos.id GROUP BY investimentos.idprojeto ORDER BY projetos.status ASC, projetos.id DESC";
			endif;
			
				$query = mysql_query( $sql );
					return $query;
		}
				
		public function buscarUsuarios($filtro){
		
			$c = "SELECT * FROM ".DB_USUARIOS." WHERE email like '%".$filtro."%' OR nome like '%".$filtro."%' ORDER BY id DESC";
			$q = mysql_query($c);
								
			return $q;				
			
		}
		
		public function getUsuario($id){
			$c = "SELECT * FROM ".DB_USUARIOS." WHERE id = $id";
			$q = mysql_query($c);
			$o = mysql_fetch_object($q);
			return $o;
		}

		public function preencherSeLogado(){
			if (! usuarios::estaLogado() ) return false;
			
			$id = mysql_escape_string( $_COOKIE['uid'] );
			$chave = mysql_escape_string( $_COOKIE['key'] );
			$q = mysql_query("SELECT u.id, u.nome, u.site, u.apelido, u.avatar, u.email, u.cidade, u.UF, u.admin, u.biografia, u.nascimento FROM usuarios u LEFT JOIN cidades c ON u.idCidade = c.id WHERE u.id LIKE '$id' AND u.chave LIKE '$chave'");
			$user = mysql_fetch_object( $q );
			
			
			//print_r($user);
			
			$this->id = $user->id;
			$this->nome = $user->nome;
			$this->apelido = $user->apelido;
			$this->cidade = $user->cidade;
			$this->uf = $user->UF;
			$this->email = $user->email;
			$this->admin = $user->admin;
			$this->avatar = $user->avatar;
			$this->site = $user->site;
			$this->nascimento = date('d/m/Y', strtotime($user->nascimento));
			$this->biografia = $user->biografia;
			return true;
		}
		public function getAvatar($id) {
		
			
			if ($id != $this->id):
			
				$s = "SELECT avatar FROM ".DB_USUARIOS." WHERE id = $id";
				$q = mysql_query($s);
				$avatar = mysql_result($q, 0, "avatar");
			
			else:
				$avatar = $this->avatar;
			endif;
		
			$pathServer = "/home/novoambiente_vps/novoambiente.com.br/images/usuarios/".$avatar;
			$path = "http://www.novoambiente.com.br/images/usuarios/".$avatar;
			
			if (is_file($pathServer)):
				
				return $path;
			
			else:
				
				return "http://www.novoambiente.com.br/images/usuarios/none.jpg";
			
			endif;
		
		}

		public function alterarSenha( $id, $senha, $novasenha, $novasenhaconfirma ) {
		
			$senha = md5( $senha );
			
			$s = "SELECT * FROM ".DB_USUARIOS." WHERE id = $id AND senha like '$senha'";
			
			echo $s;
			
			$q = mysql_query($s);
			$l = mysql_num_rows($q);
			
			if ($l == 1):
			
				if ($this->verificarSenha($novasenha, $novasenhaconfirma)):
					$md5 = md5 ($novasenha); 
					$s = "UPDATE ".DB_USUARIOS." SET senha = '$md5' WHERE id = $id";
					$q = mysql_query($s);
					return $q;
				else:
					return false;
				endif;
		
		
			else:
				return false;
			endif;
		
		}

		public function desativar($data) {
		
			//$senha = md5( $senha );
			
			$s = "UPDATE ".DB_USUARIOS." SET ativado = 0 WHERE id = ".$data['id_usuario']." LIMIT 1";
			$q = mysql_query($s);
			
			echo $s;
			//echo mysql_error();
			if($q):
				$_SESSION["globalRedirectUrl"] = "wp-admin/admin.php?page=novo_ambiente_usuarios";
				$_SESSION['retorno']['classe'] = 'success';
	            $_SESSION['retorno']['mensagem'] = 'Usuário '.$data['nome'].' desativado com sucesso.';
			else:
				$_SESSION["globalRedirectUrl"] = "wp-admin/admin.php?page=novo_ambiente_usuarios";
				$_SESSION['retorno']['classe'] = 'error';
	            $_SESSION['retorno']['mensagem'] = 'Erro interno. Por favor, contate o administrador.';
			endif;

		
		
		}

		public function Editar( $id, $nome, $apelido, $nascimento, $cidade, $estado, $biografia, $site  ) {

			//$senha = md5( $senha );
			
			$s = "UPDATE ".DB_USUARIOS." SET nome = '$nome', apelido = '$apelido',  nascimento = '$nascimento',  cidade = '$cidade', UF = '$estado', biografia = '$biografia', site = '$site' WHERE id = $id LIMIT 1";
			$q = mysql_query($s);
			
			echo $s;
			//echo mysql_error();
			if($q):
				return true;
			else:
				return false;
			endif;
		
		
		}

		public function cadastrarUsuario() {


			session_start();

			$urlBase = urlBase;

			unset($_SESSION["login_error"]);
			unset($_SESSION["login_email"]);

			unset($_SESSION["cadastro_error"]);

			unset($_SESSION["cadastro_error"]);
			unset($_SESSION["cadastro_nome"]);
			unset($_SESSION["cadastro_email"]);

			$nome = mysql_escape_string( $_POST['nome'] );
			$email = strtolower(mysql_escape_string( $_POST['email'] ));
			$senha = mysql_escape_string( $_POST['senha'] );
			$confirmarSenha = mysql_escape_string( $_POST['confirmarSenha'] );
			$estado = mysql_escape_string( $_POST['estado'] );
			$cidade = mysql_escape_string( $_POST['cidade'] );
			$novidades = $_POST['novidades'];

			$nomeArray = explode(" ", $nome);
			$apelido = ucfirst(strtolower($nomeArray[0]));

			$_SESSION["cadastro_nome"] = $nome;
			$_SESSION["cadastro_email"] = $email;

			if( !isset( $novidades ) ) $novidades = '0'; 

			/* So vamos verificar se as coisas obrigatórias vieram */
			if( ($nome == "") ||  ($senha == "") || ($estado == "") ){
				//finish( array( 'ok' => false, 'msg' => 'Algo esta vazio' ) );
				$_SESSION["cadastro_error"] = "Por favor, Preencha os campos corretamente.";
				header("location: ".BASE_URL."/sys/login/");
				die();
			}

			/* verificamos tb se a senha bate com a confirmacao da senha */
			//print "verificar senha: ".$this->verificarSenha( $senha, $confirmarSenha )."<br>";
			if( ! $this->verificarSenha( $senha, $confirmarSenha ) ):
				//finish( array( 'ok' => false, 'msg' => 'Senhas nao batem' ) );
				
				$_SESSION["cadastro_error"] = "Confirmação de senha incorreta.";
				header("location: ".BASE_URL."/sys/login/");
				die();
				
			endif;

			//print "verificarEmail : ".$this->verificarEmail( $email )."<br>";
			/*	Também nao podemos cadastrar um cara que já se encontra cadastrado
				Vamos verificar isso pelo email dele! */
			if( ! $this->verificarEmail( $email ) ):
				//finish( array( 'ok' => false, 'msg' => 'Email ja cadastrado' ) );
				$_SESSION["cadastro_error"] = "Este e-mail já está cadastrado.";
				header("location: ".BASE_URL."/sys/login/");
				die();
				
			endif;

			//print "filter_var FILTER_VALIDATE_EMAIL : ".filter_var($email, FILTER_VALIDATE_EMAIL)."<br>";
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

				$_SESSION["cadastro_error"] = "E-mail inválido.";
				header("location: ".BASE_URL."/sys/login/");
				die();

			}

			$usuarioFoiCadastrado = $this->Cadastrar( $nome, $email, $senha, $estado, $cidade, $novidades );

			//print "usuarioFoiCadastrado : ".$usuarioFoiCadastrado."<br>";

			if ($usuarioFoiCadastrado):

				$urlBase = urlBase;
				
				$senhamd5 = md5($senha);
				
				$s = "SELECT id, chave, email FROM ".DB_USUARIOS." WHERE email = '$email' and senha = '$senhamd5'";
				$q = mysql_query($s);
				
				setcookie('uid', '' );
				setcookie('key', '' );

				if ( mysql_num_rows($q) == 1 ):

					$q = mysql_fetch_object( $q );

					$log = array(
						'id_usuario' => $q->id,
						'mensagem' => 'Usuário '.$q->email.' se cadastrou no site.'
					);
					$this->registrar($log, __METHOD__);
					$this->enviarEmail($q->email, "Cadastro no Catálogo Novo Ambiente", "Bem vindo ao Novo Ambiente", "Seu cadastro foi realizado com sucesso no cat&aacute;logo online do Novo Ambiente");

					$ok = true;
					setcookie('uid', $q->id, time() + (60*60*24*3), "/" );
					setcookie('key', $q->chave, time() +  (60*60*24*3),  "/" );
				else:
					$ok = false;
				endif;

				if ($ok):
					header("location: ".BASE_URL.$_POST["globalRedirect"]);
				else:
					$_SESSION["login_error"] = "Login/senha incorretos";
					header("location: ".BASE_URL.$_POST["globalRedirect"]);
				endif;
				die();

			else:

				$_SESSION["login_error"] = "Login/senha incorretos";
				header("location: ".BASE_URL.$_POST["globalRedirect"]);
				die();
			endif;

		}


		public function cadastrar( $nome, $email, $senha, $estado, $cidade, $novidades ){

			$arrNome = split(' ', $nome );
			$apelido = $arrNome[0];
			if( count($arrNome) > 1  ) $apelido .= ' ' . $arrNome[ count($arrNome)-1  ];

			$senha = md5( $senha );
			$chave = $this->criarNovaChave();

			$sql = 'INSERT INTO '.DB_USUARIOS.'( email, senha, nome, apelido, sexo, biografia, idFacebook, ativado, chave, UF, cidade )';
			$sql .= "VALUES ( '$email', '$senha', '$nome', '$apelido', null, null, null, true, '$chave', '$estado', '$cidade')";
			
			mysql_query( $sql );
			
			return mysql_error() == '' ? true : false;
		}
		
		public function criarNovaChave($total=13){
			for($j=0;$j<$total;$j++):
				$senhatemp .= chr(rand(97,122));
			endfor;
			return $senhatemp;
		}
		
		public function verificarCaracteres($string, $tipo){
			$tamanho = strlen($string);

			for($i=0;$i<$tamanho;$i++):
			
				$letra_atual=substr($string, $i, 1);
				$ascii_atual=ord($letra_atual);
				
				if ($tipo == 'login'):
					if (!preg_match("/^[_a-zA-Z0-9-]{3,20}$/", $string)):
						return false;
					else:
						return true;
					endif;
				elseif ($tipo == 'email'):
				
					if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*$/i", $string)):
						return false;
					else:
						return true;
					endif;
				endif;
			endfor;
		}
	
		public function verificarSenha($senha, $senha_confirma){
		
			$tamanho=strlen($senha_confirma);
			
			if (($senha != $senha_confirma) || ($tamanho<5)):
				//echo "nao entrou";
				return false;
			else:
				return true;
			endif;
		}

		public function verificarLogin($login){
		
			$tamanho=strlen($login);
			$s = "SELECT login FROM ".DB_USUARIOS." WHERE login like '$login'";
			$q = mysql_query($s);
			$l = mysql_num_rows($q);
			//echo $s;
			if (($l > 0) || (!$this->verificarCaracteres($login, "login")) || (($tamanho > 15) && ($tamanho<=3))):
				return false;
			else:
				return true;
			endif;
		}
		
		public function logaAcesso($id){
		
			$i = "UPDATE ".DB_USUARIOS." SET ultimo_acesso = NOW() WHERE id = $id";
			$q = mysql_query($i);
		}
		
		public function verificarEmail($email){
		
			$s = "SELECT email FROM ".DB_USUARIOS." WHERE email like '$email'";
			$q = mysql_query($s);
			$l = mysql_num_rows($q);
			
			if (($l > 0) || (!$this->verificarCaracteres($email, "email")) || (($tamanho > 100) && ($tamanho<=3))):
				return false;
			else:
				return true;
			endif;
		}
		
		
		public static function estaLogado(){

			if(!empty($_COOKIE['uid'])||!empty($_COOKIE['key'])):
				return true;
			else:
				return false;
			endif;
				

		}
		
		
		public static function isAdmin(){
			
			$id = empty( $_COOKIE['uid'] ) ? false : $_COOKIE["uid"];
			$key = empty( $_COOKIE['key'] ) ? false : $_COOKIE["key"];
			
			if ($id):
			
				$s = "SELECT * FROM ".DB_USUARIOS." WHERE id = $id AND chave like '$key' AND admin like 'T' LIMIT 1";
				$q = mysql_query($s);
				$l = mysql_num_rows($q);

				if ($l == 1):
					return true;
				else:
					return false;
				endif;

			else:
				return false;
			endif;
			
		}
		
		public static function estaLogadoPeloFacebook(){
			global $me;
			return $me ? true : false;
		}
		public function solicitarSenha($email){
		
			
			$email = mysql_escape_string(trim($email));
			
			$s = "SELECT * FROM ".DB_USUARIOS." WHERE email like '$email' LIMIT 1";
			$q = mysql_query($s);
			$l = mysql_num_rows($q);
			
			if ($l == 1):
			
				$o = mysql_fetch_object($q);
				$chave_senha = $this->criarNovaChave(25);
				$u = "UPDATE ".DB_USUARIOS." SET chave_senha = '$chave_senha' WHERE id = $o->id";
				$q = mysql_query($u);
			
				$s = "SELECT * FROM ".DB_USUARIOS." WHERE email like '$email' LIMIT 1";
				$q = mysql_query($s);
				$l = mysql_num_rows($q);
				
				$o = mysql_fetch_object($q);
				
				return $o;
				
			endif;
		
		} 
		public function setarNovaSenha($email, $chave_senha, $senha, $nova_senha){
		
			
			$s = "SELECT * FROM ".DB_USUARIOS." WHERE email like '$email' AND chave like '$chave_senha'";
			$q = mysql_query($s);
			$l = mysql_num_rows($q);
			//echo $s;
			if ($l == 1):
			
				//echo "entrou 01";
				
				if ($this->verificarSenha($senha, $nova_senha)):

						//echo "entrou 02";
				
						$senha = md5($senha);
						
						$o = mysql_fetch_object($q);
				
						$u = "UPDATE ".DB_USUARIOS." SET senha = '$senha' WHERE id = $o->id LIMIT 1";
						$q = mysql_query($u);
						
						return true;
				
				else:
				
					return false;
					
				endif;
				
			else:
			
				return false;
			
			endif;
		
		}

		public function logUser(){


			unset($_SESSION["login_error"]);
			unset($_SESSION["login_email"]);

			unset($_SESSION["cadastro_error"]);

			unset($_SESSION["cadastro_error"]);
			unset($_SESSION["cadastro_nome"]);
			unset($_SESSION["cadastro_email"]);

			$email = mysql_escape_string( $_POST['email'] );
			$senha = md5( mysql_escape_string($_POST['senha']) );

			$_SESSION["login_email"] = $email;

			$sql = "SELECT id, chave, ativado, email  FROM ".DB_USUARIOS." WHERE email = '$email' and senha = '$senha'";
			$q = $this->getSql($sql);

			setcookie('uid', '' );
			setcookie('key', '' );

			if ( mysql_num_rows($q) == 1 ):
				$obj = mysql_fetch_object( $q );
				if($obj->ativado == 1):
					$ok = true;
					setcookie('uid', $obj->id, time() + (60*60*24*3), "/" );
					setcookie('key', $obj->chave, time() +  (60*60*24*3),  "/" );
					//$this->logaAcesso($obj->id);
				else:
					$ok = false;
					$mensagem = "Esta conta foi desativada";
				endif;
			else:
				$ok = false;
				$mensagem = "Login/senha incorretos";
			endif;

			$back = $_SERVER['HTTP_REFERER'];
			
			if ($ok):
		
				$log = array(
					'id_usuario' => $obj->id,
					'mensagem' => 'Usuário '.$obj->email.' entrou no sistema'
				);
				$this->registrar($log, __METHOD__);

				header("location: ".BASE_URL.$_POST["globalRedirect"]);
			else:
				$_SESSION["login_error"] = $mensagem;
				header("location: ".$back);
			endif;
			die;

		}

		public function logOut(){

			setcookie('uid', '', -3600, '/' );
			setcookie('key', '', -3600, '/' );
			setcookie('arrml', '', -3600, '/' );

			$urlBase = BASE_URL;
			header("Location: $urlBase");
			die();

		}

		public function minhaLista($data){

			if($data['op']=='add'):

				if(isset($_COOKIE["arrml"])&&!empty($_COOKIE["arrml"])):

					//print "<pre>"; print_r($_COOKIE); print "</pre>";
					$objValores = json_decode(stripslashes($_COOKIE["arrml"]));

					if(isset($objValores)&&!empty($objValores)):

						// Transformar objeto em array
	                    foreach ($objValores as $key => $value){
	                        $arrValores[$key] = $value;
	                    }

	                    if(isset($arrValores)&&!empty($arrValores)):

							if(!in_array($data['idp'], $arrValores)&&!empty($data['idp'])):

								array_push($arrValores, $data['idp']);
								array_unique($arrValores);

								setcookie('arrml', json_encode($arrValores), time() +  (60*60*24*3),  "/" );

								$arr['lista'] = $arrValores;

								return hm_mount_data(true, $arr);
							else:
								return hm_mount_data(true, 'add value - o valor '.$data['idp'].' ja existe no array');
							endif;
						else:
							return hm_mount_data(true, 'add value - o valor '.$data['idp'].' | o COOKIE não está vazio mas não é um array');
						endif;

					else:

						$arrValores = array();

						array_push($arrValores, $data['idp']);
						array_unique($arrValores);

						setcookie('arrml', json_encode($arrValores), time() +  (60*60*24*3),  "/" );

						$arr['lista'] = $arrValores;

						return hm_mount_data(true, $arr);

						return hm_mount_data(true, 'add value - o valor '.$data['idp'].' | o COOKIE está vazio');
					endif;

				else:
					$arrValores = array ( 0 => $data['idp'] );
					setcookie('arrml', json_encode($arrValores), time() +  (60*60*24*3),  "/" );

					$arr['lista'] = $arrValores;

					return hm_mount_data(true, $arr);

				endif;

			else:

				if(isset($_COOKIE["arrml"])):

					//print "<pre>"; print_r($_COOKIE); print "</pre>";
					$objValores = json_decode(stripslashes($_COOKIE["arrml"]));

					// Transformar objeto em array
                    foreach ($objValores as $key => $value){
                        $arrValores[$key] = $value;
                    }

					if(in_array($_POST['idp'], $arrValores)):

							if(count($arrValores)==1):

								setcookie('arrml', '', -3600, '/' );

								$arrValores = 0;

								$arr['lista'] = $arrValores;
								$arr['local'] = 'remove';

								return hm_mount_data(true, $arr);

							else:

								$chave = array_search($data['idp'], $arrValores); //localizo o valor no array
								unset($arrValores[$chave]); //apago

								setcookie('arrml', json_encode($arrValores), time() +  (60*60*24*3),  "/" );

								$arr['lista'] = $arrValores;
								$arr['local'] = 'remove';

								return hm_mount_data(true, $arr);

							endif;

					else:
						//print "ESSE ID: ".$_POST['idp']." NÃO EXISTE NESSE ARRAY: ";
						print_r($arrValores);
						return hm_mount_data(true, 'remover '.$_POST['idp']);
					endif;

				else:

					return hm_mount_data(true, 0);

				endif;

			endif;

		}


		public function solicitaOrcamento($data)
		{
			//print "<pre>"; print_r($data);

			if(empty($data['telefone'])||empty($data['consultor'])||empty($data['email'])||empty($data['nome'])):

				foreach($data as $keyData => $valueData):
					$_SESSION["dados"][$keyData] = $valueData;
				endforeach;

				$_SESSION["globalRedirectUrl"] = "sys/solicitar-orcamento/";
				$_SESSION['retorno']['classe'] = 'erro';
	            	$_SESSION['retorno']['mensagem'] = 'Por favor, preencha os campos corretamente.';

	          elseif(!filter_var($data['email'], FILTER_VALIDATE_EMAIL)):

	          	foreach($data as $keyData => $valueData):
					$_SESSION["dados"][$keyData] = $valueData;
				endforeach;

				$_SESSION["globalRedirectUrl"] = "sys/solicitar-orcamento/";
				$_SESSION['retorno']['classe'] = 'erro';
	            	$_SESSION['retorno']['mensagem'] = 'O e-mail digitado é inválido.';

			else:

				//print "<pre>"; print_r($data); print "</pre>"; die;
				//INSERT INTO `na_sys_pedidos`(`id_pedido`, `id_usuario`, `data`, `detalhes`, `status`) VALUES ([value-1],[value-2],[value-3],[value-4],[value-5])
				/*$sqlUsuario = "SELECT * FROM ".DB_USUARIOS." WHERE id = ".$_COOKIE['uid'];
				$queryUsuario = mysql_query($sqlUsuario);
				$objUsuario = mysql_fetch_object($queryUsuario);*/

				unset($data['solicitar']);

				// Tabela de produtos que irá no e-mail
				$html = array();

				$html['usuario'] = "Você enviou um pedido de orçamento no site Novo Ambiente. Entraremos em contato em breve. <br>Informações do orçamento: <br><br>";

				$novidades = ($data['novidades'])?'Sim':'Não';

      			$estado = (isset($data['estado'])&&!empty($data['estado']))?$data['estado']:'';
		        	$sqlUsuarios = "INSERT INTO ".DB_USUARIOS." (nome, email, telefone, uf) VALUES ('".$data['nome']."', '".$data['email']."', '".$data['telefone']."', '".$estado."')";             
	           	$queryUsuarios = mysql_query($sqlUsuarios);
	           	$idUsuario = mysql_insert_id();

				$detalhes = array('idp' => $data['idp'], 'variacoes' => $data['variacoes'], 'acabamentos' => $data['acabamentos'], 'observacoes' => $data['observacoes']);
				$detalhes = json_encode($detalhes);

				$i = "INSERT INTO ".DB_PEDIDOS." (id_usuario, telefone, consultor, data, detalhes, status) VALUES ( ".$idUsuario.", '".$data['telefone']."', '".$data['consultor']."', NOW(), '".$detalhes."', 1)";             
	           	$q = mysql_query($i);
	           	$idOrcamento = mysql_insert_id();

				$html['usuario'] .= "Nome completo: ".$data['nome']."<br>";
				$html['usuario'] .= "E-mail: ".$data['email']."<bR>";
				$html['usuario'] .= "Telefone: ".$data['telefone']." | Estado: ".$data['estado']."<br>";
				$html['usuario'] .= "Consultor: ".$data['consultor']." | Arquiteto: ".$data['arquiteto']."<br>";
				$html['usuario'] .= "Receber novidades: ".$novidades."<br>";

				$html['usuario'] .= '<table class="" cellpadding="2">
		                        <thead class="">
		                            <tr>
		                            	<th style="font-size: 12px; color: #101010">ID</th>
		                            	<th style="font-size: 12px; color: #101010">Imagem</th>
		                                <th style="font-size: 12px; color: #101010">Produto</th>
		                                <th style="font-size: 12px; color: #101010">Variação</th>
		                                <th style="font-size: 12px; color: #101010">Acabamento</th>
		                            </tr>
		                        </thead>
		                        <tbody>';

		                        foreach($data['idp'] as $key => $value):

		                         	$registro = new registro;
		                            $obj = $registro->getRegistro(DB_PRODUTOS, 'id_produto', $value);
		                            
		                            $acabamento = ($data['acabamentos'][$key]==0)?'Não informado':$data['acabamentos'][$key];

	                                $html['usuario'] .= '<tr>
	                                			<td style="font-size: 12px; color; #101010">
	                                                #'.$obj['registro']->id.'
	                                            </td>
	                                			<td style="font-size: 12px; color; #101010">
	                                                '.get_the_post_thumbnail($obj['registro']->id_post, array(80,auto)).'
	                                            </td>
	                                            <td style="font-size: 12px; color; #101010">
	                                                '.$obj['registro']->label.'
	                                            </td>
	                                            <td style="font-size: 12px; color; #101010">
	                                            	'.$data['variacoes'][$key].'	                                               
	                                            </td>
	                                            <td style="font-size: 12px; color; #101010">
	                                                '.$acabamento.'
	                                            </td>
	                                            </tr>';

		                        endforeach;

		        $html['usuario'] .= '</tbody>
		                 	</table>';

		        	$html['consultor'] = "Mensagem de resposta para o pedido de orçamento #".$idOrcamento." enviado para o Novo Ambiente pelo usuário ".$data['nome']." em ".date('d/m/Y \à\s H:i')."<br><br>";

				$html['consultor'] .= "Nome completo: ".$data['nome']."<br>";
				$html['consultor'] .= "E-mail: ".$data['email']."<bR>";
				$html['consultor'] .= "Telefone: ".$data['telefone']." | Estado: ".$data['estado']."<br>";
				$html['consultor'] .= "Consultor: ".$data['consultor']." | Arquiteto: ".$data['arquiteto']."<br>";
				$html['consultor'] .= "Receber novidades: ".$novidades."<br>";

				$html['consultor'] .= '<table class="" style="font-size: 9pt">
			                        <thead class="">
			                            <tr>
			                            	<th style="font-size: 12px; color: #101010">ID</th>
			                            	<th style="font-size: 12px; color: #101010">Imagem</th>
			                                <th style="font-size: 12px; color: #101010">Produto</th>
			                                <th style="font-size: 12px; color: #101010">Variação</th>
			                                <th style="font-size: 12px; color: #101010">Acabamento</th>
			                                <th style="font-size: 12px; color: #101010">Observação</th>
			                                <th style="font-size: 12px; color: #101010">Valor</th>
			                            </tr>
			                        </thead>
		                        <tbody>';

		                        foreach($data['idp'] as $key => $value):

		                         	$registro = new registro;
		                            $obj = $registro->getRegistro(DB_PRODUTOS, 'id_produto', $value);
		                            
		                            $acabamento = ($data['acabamentos'][$key]==0)?'Não informado':$data['acabamentos'][$key];

	                                $html['consultor'] .= '<tr>
	                                			<td style="font-size: 12px; color: #101010">
	                                                #'.$obj['registro']->id_produto.'
	                                            </td>
	                                			<td style="font-size: 12px; color: #101010">
	                                                '.get_the_post_thumbnail($obj['registro']->id_post, array(80,auto)).'
	                                            </td>
	                                            <td style="font-size: 12px; color: #101010">
	                                                '.$obj['registro']->label.'
	                                            </td>
	                                            <td style="font-size: 12px; color: #101010">
	                                            	'.$data['variacoes'][$key].'	                                               
	                                            </td>
	                                            <td style="font-size: 12px; color: #101010">
	                                                '.$acabamento.'
	                                            </td>
	                                            <td style="font-size: 12px; color: #101010">
	                                               	'.$data['observacoes'][$key].'
	                                            </td>
	                                            <td style="font-size: 12px; color: #101010">
	                                               	R$ 0,00
	                                            </td>
	                                        </tr>';

		                        endforeach;

		        $html['consultor'] .= '
		        			</tbody>
		                 </table>';

		        $html['orcamento'] = 'SIM';

		  
				
	            	$log = array(
					'id_usuario' => $idUsuario,
					'mensagem' => "Usuário ".$data['email']." solicitou orçamento, id ".$idOrcamento."."
				);
				$this->registrar($log, __METHOD__);

	            $this->enviarEmail($data['email'], "Solicitação de orçamento #".$idOrcamento, "Solicitação de orçamento #".$idOrcamento, $html);

	            	$_SESSION["globalRedirectUrl"] = "sys/solicitar-orcamento/";
				$_SESSION['retorno']['classe'] = 'sucesso';
				$_SESSION['resultado'] = 'sucesso';
	            	$_SESSION['retorno']['mensagem'] = 'Solicitação de orçamento enviada com sucesso. <a href="'.BASE_URL.'">Clique aqui para retornar</a>.';

	       	endif;
			
		}

		public function editarUsuario($data){

			//print "<pre>"; print_r($data); print_r($_COOKIE);

			$erro = false;
			$erroMsg = '';
			$campos = false;

			if(!empty($data['nome'])&&!empty($data['email'])):

				if(!isset($_COOKIE['key'])||!isset($_COOKIE['uid'])):
					$erro = true;
					$erroMsg .= "Erro, chave $ COOKIE [ key ] não existe; <br>";
				else:

					$objUsuario = $this->getUsuario($_COOKIE['uid']);

					if(isset($objUsuario->email)):

						if(!empty($data['senha'])&&!empty($data['confirmarSenha'])):
							if(!isset($_COOKIE['key'])):
								$erro = true;
								$erroMsg .= "Erro, chave $ COOKIE [ key ] não existe; <br>";
							else:
								if(!$this->setarNovaSenha($objUsuario->email, $_COOKIE['key'], $data['senha'], $data['confirmarSenha'])):
									$erro = true;
									$erroMsg .= "Erro ao setar nova senha; <br>";
								endif;
							endif;
						endif;

						$identificador = $_COOKIE['uid'];

						unset($data['senha']);
						unset($data['confirmarSenha']);
						unset($data['id_usuario']);

						if (!isset($data['receber_news'])):
								$data['receber_news'] = 0;
						else:
								$data['receber_news'] = 1;
						endif;

						$this->tabela = DB_USUARIOS;
						$this->index = 'id';

						$result = $this->set($identificador, $data);

						if ($result):
				            $_SESSION["cadastro_error"]["msg"] = 'Dados alterados com sucesso.';
				        else:
				        	$_SESSION["cadastro_error"]["msg"] = 'aOcorreu um erro editar o cadastro.';
				        endif;

				        $_SESSION["globalRedirectUrl"] = 'sys/cadastro';

					else:
						$erro = true;
						$erroMsg .= "Erro ao buscar usuario, objUsuario -> email; <br>";
					endif;
				endif;

			else:
				$erro = true;
				$campos = true;
			endif;

			if($erro):
				$_SESSION["cadastro_error"]["msg"] = ($campos)?'Por favor, preencha os campos corretamente.':'Ocorreu um erro editar o cadastro.';
				$_SESSION["globalRedirectUrl"] = 'sys/cadastro';
			endif;

			//print "MSG: ".$_SESSION["cadastro_error"]["msg"]." <hr> ".$erroMsg; die;
			
		}

		public function contatoHotsite($data){

			//print_r($data);
			/*
			[nome] => thalles
   			[sobrenome] => bastos
    			[tel] => 2188081898
    			[email] => thbastos.web@gmail.com
    			[msg] => babababa
    			*/
    			if(empty($data['nome'])||empty($data['tel'])||empty($data['email'])||empty($data['msg'])):
    				print '2';
    			else:
    				$emailNovoAmbiente = "thalles@homemmaquina.com.br";

    				$html = 	"Novo contato do site Novo Ambiente: <br><br>
    						 Nome: ".$data['nome']."<br>
    						 Sobrenome: ".$data['sobrenome']."<br>
    						 Telefone: ".$data['tel']."<br>
    						 Email: ".$data['email']."<br>
    						 Mensagem: ".$data['msg']."<br>";

				$result = @$this->enviarEmail($emailNovoAmbiente, "Contato - ".$data['nome'], "Contato - ".$data['nome'], $html);

				if($result):
					print '1';
				else:
					print '0';
				endif;
    			endif;
			
		}


}

?>
