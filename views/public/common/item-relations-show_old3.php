<?php if ($subjectRelations || $objectRelations): ?>
<div id="item-relations-display-item-relations">
    <h4>Dossier génétique</h4>
<P>

En cours de construction
</div>

<!--
<table width="100%" border="1" cellspacing="2" cellpadding="2">
    <th scope="col">&nbsp;</th>
    <th scope="col">Type de relation</th>
    <th scope="col">&nbsp;</th>
    
        <?php foreach ($subjectRelations as $subjectRelation): ?>
    <tr>
		    <td><?php echo metadata('item', array('Dublin Core', 'Title'), array('delimiter'=>', '));?> </td>
           <td><span title="<?php echo html_escape($subjectRelation['relation_description']); ?>"> <?php echo $subjectRelation['relation_text']; ?></span></td>
			<td>	<a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>"><?php echo $subjectRelation['object_item_title']; ?></a></li></td>
  </tr>
           <?php endforeach; ?>     

        <?php foreach ($objectRelations as $objectRelation): ?>
    <tr>
 
           <td>
           <a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>"><?php echo $objectRelation['subject_item_title']; ?></a></td>
<td><span title="<?php echo html_escape($objectRelation['relation_description']); ?>">
			<?php echo $objectRelation['relation_text']; ?></span> </td>
<td>        <?php echo metadata('item', array('Dublin Core', 'Title'), array('delimiter'=>', '));?> </td>
			
			


  </tr>

        <?php endforeach; ?>
  </table>  
-->

    <?php endif; ?>
