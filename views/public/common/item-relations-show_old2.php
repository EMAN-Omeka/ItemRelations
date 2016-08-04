<?php if ($subjectRelations || $objectRelations): ?>
<div id="item-relations-display-item-relations">
    <h4>Relations</h4>
<P>


<?php $collection = metadata('item', 'Collection ID'); ?>
				
			
<P>

Cette 
   		<?php if ($collection =='2'): ?> image
           <?php endif; ?>
			<?php if ($collection =='3'): ?> occurence
           <?php endif; ?>
			<?php if ($collection =='1'): ?> fiche
           <?php endif; ?>
            
 est en relation avec :
<p>


	    
			<ul>
            <?php foreach ($subjectRelations as $subjectRelation): ?>
           <li>
        <?php if ($collection =='2'): ?> <!-- la collection est "fiche", 1er donc c'est une image qui est reliée -->
l'image
           <?php endif; ?>
			<?php if ($collection =='3'): ?> <!-- la collection est "occurence", 1er donc c'est une fiche qui est reliée -->
       la fiche
       
            <?php endif; ?>   <a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a>
           </li>
        <?php endforeach; ?>

			</ul>


		<ul>     
        <?php foreach ($objectRelations as $objectRelation): ?>
			
           <li>       
			<?php if ($collection =='2'): ?> <!-- la collection est "fiche", 2e donc c'est une occurence qui est reliée -->
l'occurrence
            <?php endif; ?>
			
			<?php if ($collection =='1'): ?> <!-- la collection est "image", 1er donc c'est une fiche qui est reliée -->
la fiche
            <?php endif; ?>
         <a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a></li>
        <?php endforeach; ?>
			</ul>


</div>
    <?php endif; ?>



