<?php if (isset($errors)): ?>
	<?php if (count($errors)>0): ?>
		<div>
			<?php foreach($errors as $error): ?>
				<p> <?php echo $error ?></p>
			<?php endforeach ?>
			
		</div>
		<script type="text/javascript">
			alert('error');
		</script>
		<hr>

	<?php endif ?>
<?php endif ?>