<div class="wrap">
	
	<h2>
		Lista de Produtos
		<a href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_importar" class="add-new-h2">Importar xls</a>
	</h2>

	<ul class="subsubsub">
		<li class="all">
			<?php if($listaProduto!='inativos'): ?>
				<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_core">
			<?php else: ?>
				<strong>
			<?php endif; ?>
					Inativos
			<?php if($listaProduto!='inativos'): ?>
				</a> 
			<?php else: ?>
				</strong>
			<?php endif; ?>
			&#124;
		</li>
		<li class="active">
			<?php if($listaProduto!='publicados'): ?>
				<a href="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_core&lista=publicados">
			<?php else: ?>
					<strong>
			<?php endif; ?>
					Publicados
			<?php if($listaProduto!='publicados'): ?>
					</a>
			<?php else: ?>
					</strong>
			<?php endif; ?>
		</li>
	</ul>

	<div style="clear:both"></div>

	<?php if(isset($_SESSION['retorno'])): ?>
         		<div id="message" class="<?=$_SESSION['retorno']['classe']?>' updated below-h2">
					<p><?=$_SESSION['retorno']['mensagem']?></p>
					<?php if(isset($_SESSION['retorno']['detalhes'])): ?>
					<div class="detalhes">
						<p><?=isset($_SESSION['retorno']['detalhes'])?$_SESSION['retorno']['detalhes']:''?></p>
					</div>
					<?php endif; ?>
				</div>
    <?php unset($_SESSION['retorno']); endif; ?>

	

	<div class="tablenav top">

		<!--<form action="action/registros/delProdutos" name="filtrar" method="post">
			<div class="alignleft actions">
				<input type="checkbox" name="ids_produtos[]" value="2">
				<input type="checkbox" name="ids_produtos[]" value="3">
				<input type="checkbox" name="ids_produtos[]" value="4">
				<input type="checkbox" name="ids_produtos[]" value="5">
				<input type="checkbox" name="ids_produtos[]" value="6">
				<select name="action">
						<option value="-1" selected="selected">Ações em Massa</option>
						<option value="edit" class="hide-if-no-js">Excluir</option>
				</select>
				<input type="submit" name="" id="doaction" class="button action" value="Aplicar">
			</div>
		</form-->

		<form class="formProdutos" action="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_core&lista=<?=$listaProduto?>" name="filtrar" method="post">
			<?php if($listaProduto!='publicados'): ?>
				<div class="alignleft actions">
					<select name="massAction">
						<option selected="selected" value="0">Ações em Massa</option>
						<option class="hide-if-no-js" value="deletar">Excluir</option>
						<option class="hide-if-no-js" value="catalogo">Enviar para o catálogo</option>
					</select>
					<input type="hidden" value="0" class="button action" id="idsProdutos" name="idsProdutos">
					<input type="submit" value="Aplicar" class="button action" id="doaction" name="">
				</div>
			<?php endif; ?>
			<div class="alignleft actions">
				<select name="fabrica" class="postform">
					<?php 	$fabricas = $registros->getRegistros(DB_FABRICAS, array('inicio' => 0, 'limite' => '100000'), array('campo' => 'label', 'tipo' => 'DESC'));
							
							if(!is_numeric($filt_fabrica)):
								echo '<option value="0" selected>'.$filt_fabrica.'</option>';
							else:
								echo '<option value="0" selected>Todas as fábricas</option>';
							endif;
							
							while ($fb = mysql_fetch_object($fabricas['result'])):	?>
								<option value="<?=$fb->id_fabrica?>" <?=($fb->id_fabrica===$filt_fabrica)?'selected':''?> ><?=$fb->label?></option>
					
					<?php 	endwhile; ?>
				
				</select>
				<input type="submit" name="" id="post-query-submit" class="button" value="Filtrar">
			</div>
		

		<?php if($consulta['total'] > 0)
				include("paginacao-core.php"); ?>

	</div>

	<div id="baseDados">
		<table class="wp-list-table widefat fixed posts" cellspacing="0">
			<thead>
				<tr>
					<?php if($listaProduto!='publicados'): ?>
						<th class="manage-column column-cb check-column"><input id="todosCheck" value="todos" onclick="marcardesmarcar();" type="checkbox" name="ids_produtos"></th>
					<?php endif; ?>
					<th>Código</th>
					<th>Fábrica</th>
					<th>Produto</th>
					<th>Variações</th>
					<th>Registro</th>
					<th>Novidade</th>
					<th>Medida Especial</th>
					<th>Low Cost</th>
					<th>Ecommerce</th>
					<th>Status</th>
					<!--th>Atualizado</th-->
					<th>&nbsp;</th>
				</tr>
			</thead>
			<tbody>
			<?php while ($o = mysql_fetch_object($consulta['result'])):
				
				$c_variacao = $registros->getRegistros('na_sys_produtos_variacoes WHERE id_produto = '.$o->id_produto, $paginate, array('campo' => 'id_produto', 'tipo' => 'DESC'));
				
			?>
				<tr id="produto-<?=$o->id_produto?>">
					<?php if($listaProduto!='publicados'): ?>
						<th class="check-column"><input class="markCheckbox" type="checkbox" name="ids_produtos" value="<?=$o->id_produto?>"></th>
					<?php endif; ?>
					<td><?php echo $o->cod_produto?></th>
					<td>
						<?php $fabrica = $registros->getRegistro(DB_FABRICAS, 'id_fabrica', $o->id_fabrica); ?>
						<?php print $fabrica['registro']->label; ?></th>
					<td>
						<!--a class="row-title" href="<?php echo get_bloginfo('url'); ?>/wp-admin/admin.php?page=novo_ambiente_core&view=detalhe&value=<?php echo $o->id_produto; ?>" title="Detalhes do protudo"-->
						<?php if($o->id_post!=0): ?>
						<a class="row-title" href="<?=bloginfo('url')?>/wp-admin/post.php?post=<?=$o->id_post?>&action=edit" title="Detalhes do produto"><?php echo $o->label; ?></a>
						<?php else: ?>
						<?php echo $o->label; ?>
						<?php endif; ?>
						
					</td>
					<td>
						<table>
					<?php
						$ct=0;
						while ($ov = mysql_fetch_object($c_variacao['result'])):
							
							// Armazena a url da imagem da primeira variação para cadastrar ao produto
							if($ct==0)
								$vIMage_1 = $ov->foto1;
								$vIMage_2 = $ov->foto2;
								$vIMage_3 = $ov->foto3;
								$vIMage_4 = $ov->foto4;
								$vIMage_5 = $ov->foto5;
								$ct++;

							//echo $vIMage_2."<br />";

							echo "<tr style='margin:0; border:0;'>
									<td style='margin:0; border:0; width:30px;'>".$ov->comprimento."</td>
									<td style='margin:0; border:0; width:30px;'>".$ov->profundidade."</td>
									<td style='margin:0; border:0; width:30px;'>".$ov->altura."</td>
								</tr>";	
							
						endwhile;
					?>
				</table>
					</td>
					<td><?php echo hm_time_left($o->register_time); ?></td>
					<td>
						<input class="form-change-flag" <?=($o->novidade=="1")?'checked="checked"':''?> rel="novidade" type="checkbox" name="novidade">
					</td>
					<td>
						<input class="form-change-flag" <?=($o->medida_especial=="1")?'checked="checked"':''?> rel="medida_especial" type="checkbox" name="medida_especial">
					</td>
					<td>
						<input class="form-change-flag" <?=($o->low_cost=="1")?'checked="checked"':''?> rel="low_cost" type="checkbox" name="low_cost">
					</td>
					<td><?php echo ($o->in_ecommerce == 1) ? '<a href="'.$o->ecommerce_link.' target="_blank">sim</a>"':  'não'; ?></td>
					<!--td><?php echo hm_time_left($o->update_time); ?></td-->
					<td><?php echo ucfirst(strtolower($o->status)); ?></td>
					<td>
						<?php if($o->id_post==0): ?>
							<!--<form action="<?=bloginfo('url')?>/action/registro/createPostProduto" method="post">-->
							<form action="<?=get_bloginfo('url')?>/wp-admin/admin.php?page=novo_ambiente_core&lista=<?=$listaProduto?>" method="post">
								<input name="id_produto" value="<?=$o->id_produto?>" type="hidden" />
								<input name="fabrica" value="<?=$fabrica['registro']->label?>" type="hidden" />
								<input name="nome" value="<?=$o->label?>" type="hidden" />
								<input name="descricao" value="<?=strip_tags($o->descricao)?>" type="hidden" />
								<input name="imagem_1" value="<? echo (isset($vIMage_1)) ? $vIMage_1: ''; ?>" type="hidden" />
								<input name="imagem_2" value="<? echo (isset($vIMage_2)) ? $vIMage_2: ''; ?>" type="hidden" />
								<input name="imagem_3" value="<? echo (isset($vIMage_3)) ? $vIMage_3: ''; ?>" type="hidden" />
								<input name="imagem_4" value="<? echo (isset($vIMage_4)) ? $vIMage_4: ''; ?>" type="hidden" />
								<input name="imagem_5" value="<? echo (isset($vIMage_5)) ? $vIMage_5: ''; ?>" type="hidden" />
								
								<input type="hidden" name="idsProdutos" value="<?php echo $o->id_produto; ?>," />
								<input type="hidden" name="massAction" value="catalogo" />
								
								<input type="submit" name="publicar" id="publicar" class="button action" value="Enviar para cat&aacute;logo">
							</form>
						<?php else: ?>
							<a rel="tipsy" href="<?=bloginfo('url')?>/wp-admin/post.php?post=<?=$o->id_post?>&action=edit" title="Conectado com o cat&aacute;logo"><img src="<?=bloginfo('url')?>/wp-content/plugins/novoambiente-core/_static/img/link.gif" /></a>
						<?php endif; ?>
					</td>
				</tr>	
			<?php 	$vIMage_1 = ""; $vIMage_2 = ""; $vIMage_3 = ""; $vIMage_4 = ""; $vIMage_5 = ""; 
				endwhile;	?>
			
			</tbody>
		</table>
		</form>
	</div>

	<div class="tablenav bottom">
		<?php if($consulta['total'] > 0):?>
			<?php include("paginacao-core.php"); ?>
		<?php endif; ?>
	</div>

</div>