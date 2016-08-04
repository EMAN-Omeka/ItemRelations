<?php if ($subjectRelations || $objectRelations): ?>
<div id="item-relations-display-item-relations">
    <h4>Dossier génétique</h4>
<P>
Ce document est en relation avec :
			<ul>
        <?php foreach ($subjectRelations as $subjectRelation): ?>
           <li><a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a></li>
        <?php endforeach; ?>
			</ul>
			<ul>
        <?php foreach ($objectRelations as $objectRelation): ?>
            <li><a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a></li>
        <?php endforeach; ?>
			</ul>
</div>
    <?php endif; ?>
