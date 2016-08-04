<div id="item-relations-display-item-relations">
    <!--  <h4>Liste des relations de ce document</h4> -->
    <?php if (!$subjectRelations && !$objectRelations): ?>
    <p><?php echo "Ce document n'a pas de relations"; ?></p>
    <?php else: ?>
    <table>
    		<?php $previous_relation = $previous_collection = ""; ?>
    		<?php foreach ($subjectRelations as $subjectRelation): ?>
	        <?php if ($previous_collection <> $subjectRelation['item_collection']) { ?>
		        <tr>
		        		<td colspan='2'>
		        			<h4><span title="<?php echo html_escape($subjectRelation['item_collection']); ?>" style="font-weight:bold;"><?php echo strip_tags($subjectRelation['item_collection']); ?></span></h4>
	            	</td>
		     		</tr>
		     	<?php } ?>
	        <?php if ($previous_relation <> $subjectRelation['relation_text'] || $previous_collection <> $subjectRelation['item_collection']) { ?>
		        <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>  		        
		        		<td><?php echo __('Ce document');?>
	            	<span title="<?php echo html_escape($subjectRelation['relation_description']); ?>" style="font-style:italic;"><?php echo strip_tags($subjectRelation['relation_text']); ?> : </span>
	            	</td>
		     		</tr>
          <?php } ?>
		     	
		     	<tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
	            <td>
	            	<?php echo $subjectRelation['item_thumbnail']; ?>
	            	<a style='line-height:40px;margin-left:5px;' href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a>
	            	<br /><div class='relation-comment' style='clear:both;background:#ddd;font-style:italic;'><?php echo $subjectRelation['relation_comment']; ?></div>	            	
	            </td>
	            </tr>
		     	<?php
  		     	$previous_collection = $subjectRelation['item_collection'];
					  $previous_relation = $subjectRelation['relation_text'];
          
          endforeach; ?>
        
    		<?php $previous_relation = $previous_collection = ""; ?>
    		<?php foreach ($objectRelations as $objectRelation): ?>
	        <?php if ($previous_collection <> $objectRelation['subject_collection']) { ?>
		        <tr>
		        		<td colspan='2'>
		        			<h4><span title="<?php echo html_escape($objectRelation['subject_collection']); ?>"  style="font-weight:bold;"><?php echo strip_tags($objectRelation['subject_collection']); ?></span></h4>
	            	</td>
		     		</tr>    		
		     	<?php } ?>		     		
	        <?php if ($previous_relation <> $objectRelation['relation_text']) { ?>
		     	<?php $previous_relation = $objectRelation['relation_text'];} ?>        
	        <tr>
              <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>  
	            <td><?php echo $objectRelation['item_thumbnail']; ?><a style='line-height:40px;margin-left:5px;' href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a>		            
	            </td>
	            		            	
	            </tr>
		        <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>  		        
		        		<td><span title="<?php echo html_escape($objectRelation['relation_description']); ?>" style="font-style:italic;"><?php echo $objectRelation['relation_text']; echo __(' ce document'); ?> </span>
<div class='relation-comment' style='clear:both;background:#ddd;font-style:italic;'><?php echo $objectRelation['relation_comment']; ?></div>		        		
	            	</td>
		     		</tr>	            
		     	<?php $previous_collection = $objectRelation['subject_collection'];
		     				$previous_relation = "";
							?>	        
        <?php endforeach; ?>
    </table>
    <?php endif; ?>
</div>
