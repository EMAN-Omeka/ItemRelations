<?php echo head(array('title' => __('Browse Vocabularies'))); ?>
<?php echo flash(); ?>

<ul id="section-nav" class="navigation tabs">
<li><a href='<?php echo url('item-relations/manage-relations'); ?>'>Manage Available Relations</a></li>
</ul> 

<table>
    <thead>
    <tr>
        <th><?php echo __('Name'); ?></th>
        <th><?php echo __('Description'); ?></th>
        <th><?php echo __('Namespace Prefix'); ?></th>
        <th><?php echo __('Namespace URI'); ?></th>
    </tr>
    </thead>
    <tbody>
<?php foreach ($this->item_relations_vocabularies as $vocabulary): ?>
    <tr>
        <td><a href="<?php echo html_escape($this->url("item-relations/vocabularies/show/id/{$vocabulary->id}")); ?>"><?php echo $vocabulary->name; ?></a></td>
        <td><?php echo $vocabulary->description; ?></td>
        <td><?php echo $vocabulary->custom ? '<span style="color:#ccc;">n/a</span>' : $vocabulary->namespace_prefix; ?></td>
        <td><?php echo $vocabulary->custom ? '<span style="color:#ccc;">n/a</span>' : $vocabulary->namespace_uri; ?></td>
    </tr>
<?php endforeach; ?>
    </tbody>
</table>
<?php echo foot(); ?>
