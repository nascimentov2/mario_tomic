<?php
if (!current_user_can('upload_files'))  {
	 wp_die( __('You do not have sufficient permissions to access this page.') );
	} ?>
	<div class="wrap">
		
<?	$myusuarios = new usuario;

	if(!isset($_GET['action'])):
		
		$status = ( isset($_GET["status"]) ) ? $_GET["status"] : 1 ;
		
		if( isset($_GET["acao"]) && isset($_GET["usuario"]) ):
			include_once("includes/include_usuarios_acao.php");
		else: ?>
	
		<h2>Usuários Novo Ambiente</h2>

			<?php if(isset($_SESSION['retorno'])): ?>
         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
					<p><?=$_SESSION['retorno']['mensagem']?></p>
				</div>
    		<?php unset($_SESSION['retorno']); endif; ?>

		<!--ul style="margin: 18px 0;">
			<li>
				<?php

				if( !isset($_GET["query"]) ):

					if ($status == 1): echo "<strong>Ativos</strong> | "; else:
						echo "<a href=\"?page=".$_GET["page"]."\">Ativos</a> | ";
					endif;

					if ($status == 0): echo "<strong>Inativos</strong>"; else:
						echo "<a href=\"?page=".$_GET["page"]."&status=0\">Inativos</a>";
					endif;

				else:
				
					echo "<a href=\"?page=menu_novoambiente_usuarios\">Ver todos</a>";
					
				endif;
				
				?>
			</li>
		</ul-->
		<hr>
		<?
			if( isset($_SESSION["mensagem"]) ){ echo '<div class="'.$_SESSION["classe"].'">'.$_SESSION["mensagem"].'</div>'; unset($_SESSION["mensagem"]); }
					
				$url_redirect_page = $_GET["page"];
														
				if(isset($_GET["query"])):
					$filtro = htmlentities($_GET["query"]);
					$consulta = $myusuarios->buscarUsuarios($filtro);
					$total = mysql_num_rows($consulta);
				else:
					//Dados de paginação
					$total 	= $myusuarios->totalUsuarios("WHERE ativado = ".$status);
					$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;
						$porpagina = 80;
						$inicio = ($pagina-1)*$porpagina;
						$numeropaginas = ceil($total/$porpagina);
						$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
					$consulta = $myusuarios->exibirUsuarios($porpagina, "WHERE ativado = ".$status." ORDER BY id DESC", "", $inicio);
					$until = mysql_num_rows($consulta);
				endif; ?>
				
<? 				if($total > 0):?>
					<div class="tablenav">
						<?php 	if(!isset($_GET["query"])):
									include("includes/paginacao-core.php");?>
						<?php 	else:
									echo "<p>Exibindo resultados para a busca <strong>".$filtro."</strong></p>"; ?>
						<?php	endif; ?>
						<!--form style="float:left; margin-bottom:2px;" id="searchForm" action="admin.php">
							<fieldset>
								<label>
									<label>Busca: <input class="search" type="text" name="query" value="<?=$_GET["query"]?>" /></label>
									<input type="hidden" name="page" value="menu_novoambiente_usuarios" />
								</label>
							</fieldset>
						</form-->
					</div><!-- End tablenav -->
									
					<table class="widefat">
						<thead>
							<tr>
								<th>ID</th>
								<th>Nome de Usu&aacute;rio</th>
								<th>E-mail</th>
								<th>Cidade/Estado</th>
								<th>&uacute;ltimo acesso</th>
								<th>A&ccedil;&atilde;o</th>
							</tr>
						</thead>
						<tbody>
							<? while ($objUsuarios = mysql_fetch_object($consulta)): ?>
							<tr>
								<td>#<?=$objUsuarios->id?></td>
								<td>
									<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_usuarios&idu=<?=$objUsuarios->id?>&action=view" class="add-new-h2"><?php echo ( isset($filtro) ) ? boldBusca($objUsuarios->apelido, $filtro) : $objUsuarios->apelido; ?>
								</td>
								<td><?php echo ( isset($filtro) ) ? boldBusca($objUsuarios->email, $filtro) : $objUsuarios->email; ?></td>
								<? if($objUsuarios->cidade == "" || $objUsuarios->UF == ""): ?>
								<td></td>
								<? else: ?>
								<td><?=ucfirst($objUsuarios->cidade)?>/<?=$objUsuarios->UF?></td>
								<? endif; ?>
								<? if ($objUsuarios->ultimo_acesso == ""): ?>
								<td></td>
								<? else: $hoje=time(); ?>
								<td><?=hm_time_left($hoje-strtotime($objUsuarios->ultimo_acesso));?> atrás</td>
								<? endif; ?>
								<?php $action_usuario = ( $objUsuarios->ativado == 1 ) ? "desativar" : "ativar" ; ?>
								<td>
									<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_usuarios&acao=<?php echo $action_usuario; ?>&usuario=<?php echo $objUsuarios->id; ?>">
										<?php echo ucfirst($action_usuario); ?>
									</a>
								</td>
							</tr>
							<?				
							endwhile;
							$l = mysql_num_rows($consulta);
							?>
									
						</tbody>	
					</table>
					<div class="tablenav bottom">
						<?php if(!isset($_GET["query"])) { include("includes/paginacao-core.php"); } ?>
					</div>

<? 				else: ?>
			
					<p class="not-found">Nenhum resultado encontrado para a busca <strong><?=$filtro?></strong></p>
					<form style="float:left; margin-bottom:2px;" id="searchForm" action="admin.php">
						<fieldset>
							<label>
								<label>Busca: <input class="search" type="text" name="query" value="<?=$_GET["query"]?>" /></label>
								<input type="hidden" name="page" value="menu_novoambiente_usuarios" />
							</label>
						</fieldset>
					</form>
			
 <? 			endif; 

			endif; 
		
 	else:

		if($_GET['action']=='view'):

			$objUsuario = $myusuarios->getUsuario($_GET['idu']);
			//print "<pre>"; print_r($objUsuario); print "</pre>";

		?>

			<h3>Visualizar dados | <?=$objUsuario->nome?></h3>

				<ul class="subsubsub">
					<li class="all"><a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_usuarios">Usuários </a> &gt;</li>
					<li class="active"> <?=$objUsuario->nome?> </li>
				</ul>

				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row">Nome</th>
							<td>
								<label for="rich_editing">
									<input type="text" class="regular-text" readonly="readonly" value="<?=$objUsuario->nome?>" name="nome">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">E-mail</th>
							<td>
								<label for="rich_editing">
									<input type="text" class="regular-text" readonly="readonly" value="<?=$objUsuario->email?>" name="email">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">Endereço</th>
							<td>
								<label for="rich_editing">
									<input size="60" type="text" class="full-text" readonly="readonly" value="<?=$objUsuario->endereco?>" name="endereco">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">Cidade</th>
							<td>
								<label for="rich_editing">
									<input type="text" class="regular-text" readonly="readonly" value="<?=$objUsuario->cidade?>" name="cidade">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">UF</th>
							<td>
								<label for="rich_editing">
									<input type="text" class="regular-text" readonly="readonly" value="<?=$objUsuario->UF?>" name="uf">
								</label>
							</td>
						</tr>
						<tr>
							<th scope="row">Status</th>
							<td>
								<label for="rich_editing">
									<input type="text" class="regular-text" readonly="readonly" value="<?=($objUsuario->ativado)?'Ativo':'Inativo'?>" name="status">
								</label>
							</td>
						</tr>
					</tbody>
				</table>
			<div style="height:20px; width:100%;"></div>
			<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_usuarios" class="button action"> Voltar </a>

<?php 	endif;

	endif; ?>

</div>
