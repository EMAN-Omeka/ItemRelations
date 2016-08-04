<?php if ($subjectRelations || $objectRelations): ?>
<div id="item-relations-display-item-relations">
    <h4>Dossier génétique</h4>
<P>

			<ul>
        <?php foreach ($subjectRelations as $subjectRelation): ?>

            <li>
            <?php echo metadata('item', array('Dublin Core', 'Title'), array('delimiter'=>', '));?>
            
            
            <span title="<?php echo html_escape($subjectRelation['relation_description']); ?>"> <?php echo $subjectRelation['relation_text']; ?></span> <a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a></li>
           <?php endforeach; ?>     


        <?php foreach ($objectRelations as $objectRelation): ?>

<li>
            <?php echo metadata('item', array('Dublin Core', 'Title'), array('delimiter'=>', '));?> <span title="<?php echo html_escape($objectRelation['relation_description']); ?>">
			
			
			
			<?php echo $objectRelation['relation_text']; ?></span> <a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a>
</li>


        <?php endforeach; ?>
			</ul>
</div>
    <?php endif; ?>


			<!-- <?php echo __('Ce document'); ?>  -->