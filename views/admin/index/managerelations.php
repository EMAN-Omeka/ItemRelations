<?php
echo head(array('title' => __('Manage Available Relations')));
?>
<script type="text/javascript" src="<?php echo WEB_ROOT; ?>/plugins/ItemRelations/javascripts/checkall.js"></script>
<?php 
echo flash();

echo $content;

?>

<?php echo foot(); ?>