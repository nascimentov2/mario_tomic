<?php $registros = new registro; ?>
<div class="wrap">
	
	<?php 	if(!isset($_GET['editFalha'])): ?>

				<?php
	
					$registros = new registro;

					$listaProcessos = isset($_GET["lista"]) ? $_GET["lista"] : 'aguardando';
					$pagina = isset($_GET["p"]) ? $_GET["p"] : $pagina = 1;

					$porpagina = 50;
					$inicio = ($pagina-1)*$porpagina;

					$paginate = array('inicio' => $inicio, 'limite' => $porpagina);

					switch ($listaProcessos):
						case "falha": 
							$where = " status = 'falha' ";
							$label = "Falha";			
						break;
						case "enviados": 
							$where = " status = 'enviado' ";
							$label = "Enviado";
						break;
						default:
							$where = " status = 'pendente' ";
							$label = "Aguardando processamento...";
						break;
					endswitch;

					$consulta = $registros->getImgsQueue( DB_IMG_QUEUE, $where, $paginate, array('campo'=>'id_img_queue', 'tipo'=>'ASC'));
					
					$totalFalhas = $registros->getImgsQueue( DB_IMG_QUEUE, " status = 'falha' ", $paginate, array('campo'=>'id_img_queue', 'tipo'=>'ASC'));
					

					$numeropaginas = ceil($consulta['total']/$porpagina);
					$readOnly = ( $numeropaginas == 1 ) ? "readonly=\"readonly\"" : "";
					
					$until = mysql_num_rows($consulta['result']);
					$total = $consulta['total'];
				?>

				<h2>
					Processamento de Imagens
				</h2>

				<ul class="subsubsub">
					<li class="all">
						<?php if($listaProcessos!='aguardando'): ?>
							<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_processa_imagens">Aguardando processamento</a>
						<?php else: ?>
							<strong>Aguardando processamento</strong>
						<?php endif; ?>
						&#124;
					</li>
					<li class="active">
						<?php if($listaProcessos!='enviados'): ?>
							<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_processa_imagens&lista=enviados">Enviados</a>
						<?php else: ?>
								<strong>Enviados</strong>
						<?php endif; ?>
						&#124;
					</li>
					<li class="active">
						<?php
							// $falhas = lista numero de falhas
						
						if($listaProcessos!='falha'): ?>
							<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_processa_imagens&lista=falha">Falhas (<?=$totalFalhas['total']?>)</a>
						<?php else: ?>
								<strong>Falhas (<?=$totalFalhas['total']?>)</strong>
						<?php endif; ?>
					</li>
				</ul>

				<div style="clear:both"></div>

				<?php if(isset($_SESSION['retorno'])): ?>
		         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
							<p><?=$_SESSION['retorno']['mensagem']?></p>
						</div>
			    <?php unset($_SESSION['retorno']); endif; ?>

				<div class="tablenav top">
					<?php if($consulta['total'] > 0)
							include("paginacao-core.php"); ?>
				</div>

				<div id="baseDados">
					<table class="wp-list-table widefat fixed posts" cellspacing="0">
						<colgroup>
							   <col span="1" style="width: 25%;">
							   <col span="2" style="width: 50%;">
							   <col span="1" style="width: 25%;">
							   <?=($listaProcessos=='enviados')?'<col span="1" style="width: 20%;">':''?>
							   <?=($listaProcessos=='falha')?'<col span="1" style="width: 30%;">':''?>
						</colgroup>
						<thead>
							<tr>
								<th>Post ID</th>
								<th>Nome Produto</th>
								<th>Arquivo Original</th>
								<!--th>Validação</th-->
								<?=($listaProcessos=='enviados')?'<th>Miniatura</th>':''?>								
								<th>Status</th>
								<?=($listaProcessos=='falha')?'<th>&nbsp;</th>':''?>
							</tr>
						</thead>
						<tbody>
						<?php if($consulta['total']>0):
								while ($o = mysql_fetch_object($consulta['result'])):

									$objProduto = $registros->getRegistro(DB_PRODUTOS, 'id_post', $o->id_post); ?>
								
								<tr>
									<td><?="#".$o->id_post?></td>
									<td><?=(!empty($objProduto['registro']->label))?$objProduto['registro']->label:'Não encontrado'?></td>
									<td><?

										$urlimagem = $o->url_imagem;
										$arrurl = explode("/", $urlimagem);
										$filename = $arrurl[count($arrurl)-1];
										echo '<a href="'.$urlimagem.'" target="_blank">'.$filename.'</a>';

									?>
									<?php if($listaProcessos=='falha'): ?>
										<form style="display:inline" action="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_processa_imagens&editFalha=<?=$o->id_img_queue?>" method="post">
											<input name="id_img_queue" value="<?=$o->id_img_queue?>" type="hidden">
											<input type="submit" name="publicar" id="publicar" class="button action" value="Editar">
										</form>
									<?php endif; ?>
									</td>
									<!--td>
										<?php /*
											$info = @getimagesize($urlimagem);
											if (isset($info['mime'])):
												echo "Válida";
											else:
												echo "Erro no formato da imagem";
											endif;*/
										?>
									</td-->
									<?php if($listaProcessos=='enviados'):	?>
											<td><?php echo wp_get_attachment_image( $o->url_imagem_wp ); ?></td>
									<?php endif; ?>
									<td><?=$label?></td>
									<?php if($listaProcessos=='falha'): ?>
											<td>
												<form action="<?=bloginfo('url')?>/action/registro/setImgQueue" method="post">
													<input name="id_img_queue" value="<?=$o->id_img_queue?>" type="hidden">
													<input name="status" value="pendente" type="hidden">
													<input type="submit" id="publicar" class="button action" value="Enviar para processamento">
												</form>
											</td>
									<?php endif; ?>
								</tr>	
							<?php endwhile;
							 else: ?>
								<tr>
									<td colspan="5">
										<h3> Nenhum resultado encontrado. </h3>
									</td>
								</tr>
						<?php endif; ?>
						</tbody>
					</table>
					</form>
				</div>

				<div class="tablenav bottom">
					<?php if($consulta['total'] > 0):?>
						<?php include("paginacao-core.php"); ?>
					<?php endif; ?>
				</div>

	<?php 	else: 

				$where = " id_img_queue = ".$_GET['editFalha'];
				$paginate = array('inicio' => 0, 'limite' => 1);
				$consulta = $registros->getImgsQueue( DB_IMG_QUEUE, $where, $paginate, array('campo'=>'id_img_queue', 'tipo'=>'ASC'));
				$o = mysql_fetch_object($consulta['result']);

				$objProduto = $registros->getRegistro(DB_PRODUTOS, 'id_post', $o->id_post); ?>

				<form id="editar-falha" action="<?=bloginfo('url')?>/action/registro/setImgQueue" method="post">

				<table class="form-table">
					<tbody>
						<tr>
							<th colspan="2"><h3>Processamento de imagem - Editar link</h3></th>
						</tr>
						<tr>
							<th><label for="id_produto">Produto</label></th>
							<td><input type="text" class="regular-text" readonly="readonly" value="<?=(!empty($objProduto['registro']->label))?$objProduto['registro']->label:'Não encontrado'?>" id="id_produto" name="id_produto"></td>
						</tr>
						<tr>
							<th><label for="link">Link</label></th>
							<td><input type="text" class="large-text" value="<?=$o->url_imagem?>" id="url_imagem" name="url_imagem"></td>
						</tr>
						<tr>
							<th></th>
							<td>
								<input type="hidden" name="id_img_queue" value="<?=$o->id_img_queue?>">
								<input type="submit" id="editar" class="button button-primary button-large" value="Salvar">
							</td>
						</tr>
					</tbody>
				</table>

				</form>

	<?php 	endif;?>

</div>