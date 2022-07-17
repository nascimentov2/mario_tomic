<?php
	
	$id_usuario = $_GET["usuario"];
	$profile = $myusuarios->getUsuario($id_usuario);
	$novo_status = ( $_GET["acao"] == "ativar" ) ? 1 : 0 ;
	
	$hoje = time();
	
?>
<h4>Tem certeza que deseja <?php echo $_GET["acao"]; ?> o usuário <span style="color: #F00"><?php echo $profile->nome; ?></span>?</h4>

<p><strong>Localização</strong>: <?php echo ( !empty($profile->cidade) && !empty($profile->UF) ) ? ucfirst($profile->cidade)." / ".$profile->UF : "Não preenchido" ?></p>

<form action="<?php echo bloginfo("url"); ?>/action/usuario/desativar" method="post">
	<fieldset>
		<input type="hidden" name="id_usuario" value="<?php echo $id_usuario; ?>" />
		<input type="hidden" name="nome" value="<?php echo $profile->nome; ?>" />
		<input type="hidden" name="redirect" value="<?php echo bloginfo("url"); ?>/wp-admin/admin.php?page=novo_ambiente_usuarios" />
		<input type="hidden" name="novo_status" value="<?php echo $novo_status; ?>" />
	</fieldset>
	<fieldset class="submit">
		<input type="submit" name="confirmar" value="Confirmar" />
	</fieldset>
</form>
