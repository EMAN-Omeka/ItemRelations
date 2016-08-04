<?php if ($subjectRelations || $objectRelations): ?>
<div id="item-relations-display-item-relations">
    <h4>Relations</h4>
<P>


<?php $collection = metadata('item', 'Collection ID'); ?>
				
			
<P>

Cette 
   		<?php if ($collection =='1'): ?> image est en relation avec la/les fiche(s) suivant(e)s :<BR />
           <?php endif; ?>
			<?php if ($collection =='3'): ?> occurence est en relation avec la/les fiche(s) suivant(e)s :<BR />
           <?php endif; ?>
			<?php if ($collection =='2'): ?> fiche est en relation avec les images et occurrences suivantes :<BR />
           <?php endif; ?>

            <?php foreach ($subjectRelations as $subjectRelation): ?>
		<?php if (metadata(get_record_by_id('item', $subjectRelation['object_item_id']),'has files')) {
          echo link_to(get_record_by_id('item',$subjectRelation['object_item_id']),"show",item_image('square_thumbnail', array("style" => "width:70px;vertical-align:middle;"), null, get_record_by_id('item',$subjectRelation['object_item_id']))."     ".$subjectRelation['object_item_title'])." <BR> ";
            } else { ?>

           <li>

<a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a></li>



    <?php } ?>
           <?php endforeach; ?>

		<ul>     
        <?php foreach ($objectRelations as $objectRelation): ?>
			
           <li>       
<?php if (metadata(get_record_by_id('item', $objectRelation['subject_item_id']),'has files')) {
   echo link_to(get_record_by_id('item',$objectRelation['subject_item_id']),"show",item_image('square_thumbnail', array("style" => "width:70px;vertical-align:middle;"), null, get_record_by_id('item',$objectRelation['subject_item_id']))." ".$objectRelation['subject_item_title']);
 } else {?>

         <a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a></li>


   <?php } ?>
   
           <?php endforeach; ?>
			</ul>


</div>
    <?php endif; ?>

