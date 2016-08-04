<script type="text/javascript" src="<?php echo WEB_ROOT; ?>/plugins/ItemRelations/javascripts/ajax.js"></script>
<p>
<?php
$link = '<a href="' . url('item-relations/vocabularies/') . '">'
      . __('Browse Vocabularies') . '</a>';

echo __('Here you can relate this item to another item and delete existing '
     . 'relations. For descriptions of the relations, see the %s page. Invalid '
     . 'item IDs will be ignored.', $link
);
?>
</p>
<table>
    <thead>
    <tr>
        <th><?php echo __('Subject'); ?></th>
        <th><?php echo __('Relation'); ?></th>
        <th><?php echo __('Object'); ?></th>      
        <th><?php echo __('Delete'); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($subjectRelations as $subjectRelation): ?>
    <tr>
        <td><?php echo __('This Item'); ?></td>
        <td><?php echo $subjectRelation['relation_text']; ?></td>
        <td><a href="<?php echo url('items/show/' . $subjectRelation['object_item_id']); ?>" target="_blank"><?php echo $subjectRelation['object_item_title']; ?></a></td>
        <td><input type="checkbox" name="item_relations_item_relation_delete[]" value="<?php echo $subjectRelation['item_relation_id']; ?>" /></td>
        <tr>        
				<td colspan='4'>
        <?php if ($subjectRelation) { ?>
        						<label>Commenter cette relation <i>(200 caract&egrave;res maximum)</i></label>
                    <input name="item_relations_subject_comment[<?php echo $subjectRelation['item_relation_id']; ?>]"
                    id="item_relations_subject_comment_<?php echo $subjectRelation['item_relation_id']; ?>"
                    size="74" maxlength="200" value="<?php echo $subjectRelation['relation_comment'];  ?>" />
                <?php }
                else {
                     echo $relation['relation_comment'];
                } ?>
        </td>
        </tr>        
    </tr>
    <?php endforeach; ?>
    <?php foreach ($objectRelations as $objectRelation): ?>
    <tr>
        <td><a href="<?php echo url('items/show/' . $objectRelation['subject_item_id']); ?>" target="_blank"><?php echo $objectRelation['subject_item_title']; ?></a></td>
        <td><?php echo $objectRelation['relation_text']; ?></td>
        <td><?php echo __('This Item'); ?></td>
        <td><input type="checkbox" name="item_relations_item_relation_delete[]" value="<?php echo $objectRelation['item_relation_id']; ?>" /></td>
    </tr>
    <?php endforeach; ?>
    <tr class="item-relations-entry">
        <td><?php echo __('This Item'); ?></td>
        <td><?php echo get_view()->formSelect('item_relations_property_id[]', null, array('multiple' => false), $formSelectProperties); ?></td>
        <td><div class='ui-widget'><?php echo __('Item Title'); ?> 
        <?php echo get_view()->formText('item_relations_item_relation_object_item_id[]', null, array('size' => 8, 'class' => 'itemId', 'style' => 'display:none;')); ?>
        <input type="text" class="ir-autocomplete" />
        </div></td>
        <td><span style="color:#ccc;">n/a</span></td>
    </tr>
    </tbody>
</table>
<button type="button" class="item-relations-add-relation"><?php echo __('Add a Relation'); ?></button>
<input type="hidden" id="phpWebRoot" value="<?php echo WEB_ROOT; ?>">
<script type="text/javascript">
jQuery(document).ready(function () {
    jQuery('.item-relations-add-relation').click(function () {
        var oldRow = jQuery('.item-relations-entry').last();
        var newRow = oldRow.clone();
        oldRow.after(newRow);
        var inputs = newRow.find('input, select');
        inputs.val('');
    });
});
</script>
