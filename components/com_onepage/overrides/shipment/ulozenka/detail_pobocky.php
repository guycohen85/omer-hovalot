<?php


?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="cs-cz" lang="cs-cz" >
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title>Detail pobočky Uloženka.cz</title>
	<style>
	body { 
		font-family: Hanuman, Bentham, sans-serif;
	}
	h1 {
		font-size: 130%;
	}
	.label {
		vertical-align: top;
	}
	.content {
		font-weight: bold;
		vertical-align: top;
	}
	</style>
</head>
<body>
<?php
		$path = dirname(__FILE__).'/../../../cache/ulozenka/pobocky.xml'; 
		$xml = simplexml_load_file($path, "SimpleXMLElement", true);
		$pobocka_id = (integer)$_GET['id'];

		if (count($xml->pobocky)) {
			foreach ($xml->pobocky as $p) {
				if ($p->id == $pobocka_id) {
					?>
<?php echo str_replace('<a href', '<a target="_blank" href', (string)$p->mapa_l); ?>
<!--
<table border="0" cellspacing="8">
	<tr>
		<td class="label">Pobočka:</td>
		<td class="content"><?php echo $p->nazev; ?></td>
		<td rowspan="10"><?php echo str_replace('<a href', '<a target="_blank" href', (string)$p->mapa_l); ?></td>
	</tr>
	<tr>
		<td class="label">Adresa:</td>
		<td class="content">
			<?php echo (string)$p->ulice; ?><br />
			<?php echo (string)$p->obec; ?><br />
			<?php echo (string)$p->psc; ?><br />
		</td>
	</tr>
	<?php if ((string)$p->telefon) : ?>
	<tr>
		<td class="label">Telefon:</td>
		<td class="content"><?php echo (string)$p->telefon; ?></td>
	</tr>
	<?php endif; ?>
	<?php if ((string)$p->gsm) : ?>
	<tr>
		<td class="label">Mobil:</td>
		<td class="content"><?php echo (string)$p->gsm; ?></td>
	</tr>
	<?php endif; ?>
	<tr>
		<td class="label">E-mail:</td>
		<td class="content"><?php echo (string)$p->email; ?></td>
	</tr>
	<tr>
		<td class="label">Otvírací doba:</td>
		<td class="content"><?php echo (string)$p->provoz; ?></td>
	</tr>
</table>
-->
					<?php
				}
			}
		}
?>
</body>
</html>
