<?php echo $this->message ?> <a href="<?php echo $this->link ?>">&gt;&gt;</a>
<?php if (false !== $this->timeout): ?>
<script type="text/javascript">
    setTimeout('location.href = "<?php echo $this->link ?>"', <?php echo $this->timeout * 1000 ?>);
</script>
<?php endif; ?>