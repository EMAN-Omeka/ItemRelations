<?php 
/**
 * Item Relations
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Item Relations plugin.
 */
class ItemRelationsPlugin extends Omeka_Plugin_AbstractPlugin
{
    /**
     * @var array Hooks for the plugin.
     */
    protected $_hooks = array(
        'install',
        'uninstall',
        'upgrade',
        'config',
        'config_form',
        'define_acl',
        'define_routes',        
        'initialize',
        'after_save_item',
        'admin_items_show_sidebar',
        'admin_items_search',
        'admin_items_batch_edit_form',
        'items_batch_edit_custom',
        'public_items_show',
        'items_browse_sql',
    );

    /**
     * @var array Filters for the plugin.
     */
    protected $_filters = array(
        'admin_items_form_tabs',
        'admin_navigation_main',
    );

    /**
     * @var array Options and their default values.
     */
    protected $_options = array(
        'item_relations_public_append_to_items_show' => 1,
        'item_relations_relation_format' => 'prefix_local_part'
    );

    /**
     * Install the plugin.
     */
    public function hookInstall()
    {
        // Create tables.
        $db = $this->_db;

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ItemRelationsVocabulary` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(100) NOT NULL,
            `description` text,
            `namespace_prefix` varchar(100) NOT NULL,
            `namespace_uri` varchar(200) DEFAULT NULL,
            `custom` BOOLEAN NOT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ItemRelationsProperty` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `vocabulary_id` int(10) unsigned NOT NULL,
            `local_part` varchar(100) NOT NULL,
            `label` varchar(100) DEFAULT NULL,
						`available` BOOLEAN NOT NULL DEFAULT 1,            
            `description` text,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        // du pull Github 12 : champ relation_comment
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ItemRelationsRelation` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `subject_item_id` int(10) unsigned NOT NULL,
            `property_id` int(10) unsigned NOT NULL,
            `object_item_id` int(10) unsigned NOT NULL,
        		`relation_comment` varchar(200) NOT NULL DEFAULT '',             
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);

        // Install the formal vocabularies and their properties.
        $formalVocabularies = include 'formal_vocabularies.php';
        foreach ($formalVocabularies as $formalVocabulary) {
            $vocabulary = new ItemRelationsVocabulary;
            $vocabulary->name = $formalVocabulary['name'];
            $vocabulary->description = $formalVocabulary['description'];
            $vocabulary->namespace_prefix = $formalVocabulary['namespace_prefix'];
            $vocabulary->namespace_uri = $formalVocabulary['namespace_uri'];
            $vocabulary->custom = 0;
            $vocabulary->save();

            $vocabularyId = $vocabulary->id;

            foreach ($formalVocabulary['properties'] as $formalProperty) {
                $property = new ItemRelationsProperty;
                $property->vocabulary_id = $vocabularyId;
                $property->local_part = $formalProperty['local_part'];
                $property->label = $formalProperty['label'];
                $property->description = $formalProperty['description'];
                $property->available = 1; 
                $property->save();
            }
        }

        // Install a custom vocabulary.
        $customVocabulary = new ItemRelationsVocabulary;
        $customVocabulary->name = 'Custom';
        $customVocabulary->description = 'Custom vocabulary containing relations defined for this Omeka instance.';
        $customVocabulary->namespace_prefix = ''; // cannot be NULL
        $customVocabulary->namespace_uri = null;
        $customVocabulary->custom = 1;
        $customVocabulary->save();

        $this->_installOptions();
    }

    /**
     * Uninstall the plugin.
     */
    public function hookUninstall()
    {
        $db = $this->_db;

        // Drop the vocabularies table.
        $sql = "DROP TABLE IF EXISTS `$db->ItemRelationsVocabulary`";
        $db->query($sql);

        // Drop the properties table.
        $sql = "DROP TABLE IF EXISTS `$db->ItemRelationsProperty`";
        $db->query($sql);

        // Drop the relations table.
        $sql = "DROP TABLE IF EXISTS `$db->ItemRelationsRelation`";
        $db->query($sql);

        $this->_uninstallOptions();
    }

   /**
     * Display the plugin configuration form.
     */
    public static function hookConfigForm()
    {
        $publicAppendToItemsShow = get_option('item_relations_public_append_to_items_show');
        $relationFormat = get_option('item_relations_relation_format');

        require dirname(__FILE__) . '/config_form.php';
    }

    /**
     * Handle the plugin configuration form.
     */
    public static function hookConfig()
    {
        set_option('item_relations_public_append_to_items_show',
            (int)(boolean) $_POST['item_relations_public_append_to_items_show']);
        set_option('item_relations_relation_format',
            $_POST['item_relations_relation_format']);
    }

    /**
     * Upgrade the plugin.
     *
     * @param array $args
     */
    public function hookUpgrade($args)
    {
        $oldVersion = $args['old_version'];
        $db = $this->_db;
        if ($oldVersion <= '1.1') {
            $sql = "
            INSERT INTO `{$db->ItemRelationsProperty}`
            (`vocabulary_id`, `local_part`, `label`, `description`)
            VALUES
            (1, 'abstract', 'Abstract', 'A summary of the resource.'),
            (1, 'accessRights', 'Access Rights', 'Information about who can access the resource or an indication of its security status.'),
            (1, 'accrualMethod', 'Accrual Method', 'The method by which items are added to a collection.'),
            (1, 'accrualPeriodicity', 'Accrual Periodicity', 'The frequency with which items are added to a collection.'),
            (1, 'accrualPolicy', 'Accrual Policy', 'The policy governing the addition of items to a collection.'),
            (1, 'audience', 'Audience', 'A class of entity for whom the resource is intended or useful.'),
            (1, 'contributor', 'Contributor', 'An entity responsible for making contributions to the resource.'),
            (1, 'coverage', 'Coverage', 'The spatial or temporal topic of the resource, the spatial applicability of the resource, or the jurisdiction under which the resource is relevant.'),
            (1, 'creator', 'Creator', 'An entity primarily responsible for making the resource.'),
            (1, 'description', 'Description', 'An account of the resource.'),
            (1, 'educationLevel', 'Audience Education Level', 'A class of entity, defined in terms of progression through an educational or training context, for which the described resource is intended.'),
            (1, 'extent', 'Extent', 'The size or duration of the resource.'),
            (1, 'format', 'Format', 'The file format, physical medium, or dimensions of the resource.'),
            (1, 'instructionalMethod', 'Instructional Method', 'A process, used to engender knowledge, attitudes and skills, that the described resource is designed to support.'),
            (1, 'language', 'Language', 'A language of the resource.'),
            (1, 'license', 'License', 'A legal document giving official permission to do something with the resource.'),
            (1, 'mediator', 'Mediator', 'An entity that mediates access to the resource and for whom the resource is intended or useful.'),
            (1, 'medium', 'Medium', 'The material or physical carrier of the resource.'),
            (1, 'provenance', 'Provenance', 'A statement of any changes in ownership and custody of the resource since its creation that are significant for its authenticity, integrity, and interpretation.'),
            (1, 'publisher', 'Publisher', 'An entity responsible for making the resource available.'),
            (1, 'rights', 'Rights', 'Information about rights held in and over the resource.'),
            (1, 'rightsHolder', 'Rights Holder', 'A person or organization owning or managing rights over the resource.'),
            (1, 'spatial', 'Spatial Coverage', 'Spatial characteristics of the resource.'),
            (1, 'subject', 'Subject', 'The topic of the resource.'),
            (1, 'tableOfContents', 'Table Of Contents', 'A list of subunits of the resource.'),
            (1, 'temporal', 'Temporal Coverage', 'Temporal characteristics of the resource.'),
            (1, 'type', 'Type', 'The nature or genre of the resource.')";
            $db->query($sql);
        }

        if ($oldVersion <= '2.0') {
            // Fix un-upgraded old table name if present.
            $correctTableName = (bool) $db->fetchOne("SHOW TABLES LIKE '{$db->ItemRelationsRelation}'");
            if (!$correctTableName) {
                $sql = "RENAME TABLE `{$db->prefix}item_relations_item_relations` TO `{$db->ItemRelationsRelation}`";
                $db->query($sql);
            }
        }
        if ($oldVersion < '2.0.2.1') {
        	// Insert relation_comment column
        	$sql = "ALTER TABLE `{$db->prefix}item_relations_relations` ADD `relation_comment` VARCHAR(200) NOT NULL DEFAULT '' AFTER `object_item_id`";
        	$db->query($sql);
        }        
        $sql = "
        CREATE TABLE IF NOT EXISTS `$db->ItemRelationsRelation` (
        `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
        `subject_item_id` int(10) unsigned NOT NULL,
        `property_id` int(10) unsigned NOT NULL,
        `object_item_id` int(10) unsigned NOT NULL,
        `relation_comment` varchar(200) NOT NULL DEFAULT '',
        PRIMARY KEY (`id`)
        ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
        $db->query($sql);
        $sql = "ALTER TABLE `$db->ItemRelationsProperty` ADD `available` BOOLEAN NULL DEFAULT 1";
        $db->query($sql);
    }

   /**
     * Add the translations.
     */
    public function hookInitialize()
    {
        add_translation_source(dirname(__FILE__) . '/languages');
    }

    /**
     * Define the ACL.
     *
     * @param array $args
     */
    public function hookDefineAcl($args)
    {
        $acl = $args['acl'];

        $indexResource = new Zend_Acl_Resource('ItemRelations_Index');
        $vocabResource = new Zend_Acl_Resource('ItemRelations_Vocabularies');
        $acl->add($indexResource);
        $acl->add($vocabResource);
        
        // Define access rights to Manage Available Relations Page
        $manageRelations = new Zend_Acl_Resource('managerelations_Index');
        $acl->add($manageRelations);
        
        $acl->allow(array('super', 'admin'), array('managerelations_Index'));
                
    }

    /**
     * Display item relations on the public items show page.
     */
    public function hookPublicItemsShow() {
        if (get_option('item_relations_public_append_to_items_show')) {
            $item = get_current_record('item');

            echo common('item-relations-show', array(
                'subjectRelations' => self::prepareSubjectRelations($item),
                'objectRelations' => self::prepareObjectRelations($item)
            ));
        }
    }

    /**
     * Display item relations on the admin items show page.
     *
     * @param Item $item
     */
    public function hookAdminItemsShowSidebar($args)
    {
        $item = $args['item'];

        echo common('item-relations-show', array(
            'subjectRelations' => self::prepareSubjectRelations($item),
            'objectRelations' => self::prepareObjectRelations($item)
        ));
    }

    /**
     * Display the item relations form on the admin advanced search page.
     */
    public function hookAdminItemsSearch()
    {
        echo common('item-relations-advanced-search', array(
            'formSelectProperties' => get_table_options('ItemRelationsProperty'))
        );
    }
    
    /**
     * Save the item relations after saving an item add/edit form.
     *
     * @param array $args
     */
    public function hookAfterSaveItem($args)
    {
    	    	    	    	
        if (!$args['post']) {
            return;
        }

        $record = $args['record'];
        $post = $args['post'];

        $db = $this->_db;
        					
        // Save item relations.
        foreach ($post['item_relations_property_id'] as $key => $propertyId) {
            self::insertItemRelation(
                $record,
                $propertyId,
                $post['item_relations_item_relation_object_item_id'][$key]
            );
        }
       
        // Delete item relations.
        if (isset($post['item_relations_item_relation_delete'])) {
            foreach ($post['item_relations_item_relation_delete'] as $itemRelationId) {
                $itemRelation = $db->getTable('ItemRelationsRelation')->find($itemRelationId);
                // When an item is related to itself, deleting both relations
                // simultaneously will result in an error. Prevent this by
                // checking if the item relation exists prior to deletion.
                if ($itemRelation) {
                    $itemRelation->delete();
                }
            }
        }
            // update the comment when the comment is edited in subject
        if (isset($post['item_relations_subject_comment'])) {
        	if (isset($post['item_relations_subject_comment'])) {
        		$comments = array();
        		foreach($post['item_relations_subject_comment'] as $key => $value) {
        			if ($value) {
        				$comments[$key] = $value;
        			}
        		}
        		if ($comments) {
	        		$commentIds = implode(',', array_keys($comments));
	        		// Optimized the update query to avoid multiple execution.
	        		$sql = "UPDATE `$db->ItemRelationsRelation` SET relation_comment = CASE id ";
	        		foreach ($comments as $commentId => $comment) {
	        			$sql .= sprintf(' WHEN %d THEN %s', $commentId, $db->quote($comment));
	        		}
	        		$sql .= " END WHERE id IN ($commentIds)";
	        		$db->query($sql);
        		}
        	}
        	else {
        		$this->_helper->flashMessenger(__('There was an error in the item relation comments.'), 'error');
        	}
        }

    }

    /**
     * Filter for an item relation after search page submission.
     *
     * @param array $args
     */
    public function hookItemsBrowseSql($args)
    {
        $select = $args['select'];
        $params = $args['params'];

        if (isset($params['item_relations_property_id'])
            && is_numeric($params['item_relations_property_id'])
        ) {
            $db = $this->_db;
            // Set the field on which to join.
            if (isset($params['item_relations_clause_part'])
                && $params['item_relations_clause_part'] == 'object'
            ) {
                $onField = 'object_item_id';
            } else {
                $onField = 'subject_item_id';
            }
            $select
                ->join(
                    array('item_relations_relations' => $db->ItemRelationsRelation),
                    "item_relations_relations.$onField = items.id",
                    array()
                )
                ->where('item_relations_relations.property_id = ?',
                    $params['item_relations_property_id']
                );
        }
    }

    /**
     * Add custom fields to the item batch edit form.
     */
    public function hookAdminItemsBatchEditForm()
    {
        $formSelectProperties = get_table_options('ItemRelationsProperty');
?>
<fieldset id="item-relation-fields">
<h2><?php echo __('Item Relations'); ?></h2>
<table>
    <thead>
    <tr>
        <th><?php echo __('Subjects'); ?></th>
        <th><?php echo __('Relation'); ?></th>
        <th><?php echo __('Object');  ?></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td><?php echo __('These Items'); ?></td>
        <td><?php echo get_view()->formSelect('custom[item_relations_property_id]', null, array(), $formSelectProperties); ?></td>
        <td>
            <?php echo __('Item ID'); ?>
            <?php echo get_view()->formText('custom[item_relations_item_relation_object_item_id]', null, array('size' => 6)); ?>
        </td>
    </tr>
    </tbody>
</table>
</fieldset>
<?php
    }

    /**
     * Process the item batch edit form.
     *
     * @param array $args
     */
    public function hookItemsBatchEditCustom($args)
    {
        $item = $args['item'];
        $custom = $args['custom'];

        self::insertItemRelation(
            $item,
            $custom['item_relations_property_id'],
            $custom['item_relations_item_relation_object_item_id'],
        		$custom['item_relations_item_relation_relation_comment'][$key]        		
        );
    }

    /**
     * Add the Item Relations link to the admin main navigation.
     *
     * @param array Navigation array.
     * @return array Filtered navigation array.
     */
    public function filterAdminNavigationMain($nav)
    {
        $nav[] = array(
            'label' => __('Item Relations'),
            'uri' => url('item-relations'),
            'resource' => 'ItemRelations_Index',
            'privilege' => 'index'
        );
        return $nav;
    }

    /**
     * Add the "Item Relations" tab to the admin items add/edit page.
     *
     * @return array
     */
    public function filterAdminItemsFormTabs($tabs, $args)
    {
        $item = $args['item'];

        $formSelectProperties = get_table_options('ItemRelationsProperty');
        $subjectRelations = self::prepareSubjectRelations($item);
        $objectRelations = self::prepareObjectRelations($item);

        ob_start();
        include 'item_relations_form.php';
        $content = ob_get_contents();
        ob_end_clean();
				
        $tabs['Item Relations'] = $content;
        return $tabs;
    }
  function hookDefineRoutes($args)
    {
      		$router = $args['router'];
       		$router->addRoute(
      				'ir_ajax_items_autocomplete',
      				new Zend_Controller_Router_Route(
      						'itemrelationsajax/:title', 
      						array(
      								'module' => 'item-relations',
      								'controller'   => 'ajax',
      								'action'       => 'itemajax',
      								'title'					=> ''
      						)
      				)
      		);
       		$router->addRoute(
      				'ir_admin_available_relations',
      				new Zend_Controller_Router_Route(
      						'item-relations/manage-relations/', 
      						array(
      								'module' => 'item-relations',
      								'controller'   => 'index',
      								'action'       => 'managerelations',
      						)
      				)
      		);
    }
    /**
     * Prepare subject item relations for display.
     *
     * @param Item $item
     * @return array
     */
    public static function prepareSubjectRelations(Item $item)
    {
        $subjects = get_db()->getTable('ItemRelationsRelation')->findBySubjectItemId($item->id);
        $subjectRelations = array();

        foreach ($subjects as $subject) {
            if (!($item = get_record_by_id('item', $subject->object_item_id))) {
                continue;
            }           
            $collection = get_collection_for_item($item);
            if ($collection) {
            	$collectionTitle = metadata($collection, array('Dublin Core', 'Title'));
            } else {
            	$collectionTitle = "** Hors collections **";
            }
            $subjectRelations[] = array(
                'item_relation_id' => $subject->id,
                'object_item_id' => $subject->object_item_id,
                'object_item_title' => self::getItemTitle($item),
            		'relation_comment' => $subject->relation_comment,            		
                'relation_text' => $subject->getPropertyText(),
                'relation_description' => $subject->property_description,
            		'item_thumbnail' => self::getItemThumbnail($subject->object_item_id),            		
            		'item_collection' => $collectionTitle
            );
        }
        if ($subjectRelations) {
					$subjectRelations = self::sortByRelationTitle($subjectRelations,  'item_collection', 'relation_text', 'object_item_title');
        }       
        return $subjectRelations;
    }

    /**
     * Prepare object item relations for display.
     *
     * @param Item $item
     * @return array
     */
    public static function prepareObjectRelations(Item $item)
    {
        $objects = get_db()->getTable('ItemRelationsRelation')->findByObjectItemId($item->id);
        $objectRelations = array();
        foreach ($objects as $object) {
            if (!($item = get_record_by_id('item', $object->subject_item_id))) {
                continue;
            }
            $collection = get_collection_for_item($item);
            if ($collection) {
            	$collectionTitle = metadata($collection, array('Dublin Core', 'Title'));
            } else {
            	$collectionTitle = "** Hors collections **";            	
            }
            $objectRelations[] = array(
                'item_relation_id' => $object->id,
                'subject_item_id' => $object->subject_item_id,
                'subject_item_title' => self::getItemTitle($item),
            		'relation_comment' => $object->relation_comment,            		
                'relation_text' => $object->getPropertyText(),
                'relation_description' => $object->property_description,
            		'item_thumbnail' => self::getItemThumbnail($object->subject_item_id),
            		'subject_collection' => $collectionTitle
            );
        }       
        if ($objectRelations) {
        	$objectRelations = self::sortByRelationTitle($objectRelations, 'subject_collection', 'relation_text', 'subject_item_title');
        }
        return $objectRelations;
    }

    /**
     * Return a item's title.
     *
     * @param Item $item The item.
     * @return string
     */
    public static function getItemTitle($item)
    {
        $title = metadata($item, array('Dublin Core', 'Title'), array('no_filter' => true));
        if (!trim($title)) {
            $title = '#' . $item->id;
        }
        return $title;
    }
    /**
     * Return an item's first file thumbnail .
     *
     * @param Item Id $itemId The item's id.
     * @return string
     */
    public static function getItemThumbnail($itemId)
    {
    	$litem = get_record_by_id('Item', $itemId);
      $files = $litem->getFiles();
    	$thefile = array_pop($files);
    	if ($thefile) {    	
    		$thumbnail = file_image('square_thumbnail', array('class' => 'thumbnail', 'style' => 'width:40px;height:40px;overflow:hidden;float:left;padding-right:3px;vertical-align:middle;margin-bottom:0;'), $thefile);
    	} else {
    		$thumbnail = "<div class='thumbnail' style='width:40px;height:40px;float:left;margin-bottom:0;'><img src='" . WEB_ROOT . "/themes/eman/images/eman-logo.png' /></div>";
    	}
    	return $thumbnail;
    }
    /**
     * Return an associative array sorted by 1 column then another. 
     *
     * @param associative array to sort  
     * @param first column's name 
     * @param second column's name
     * @return array
     */
    public static function sortByRelationTitle($array, $firstSort, $secondSort, $thirdSort) {
    	foreach ($array as $key => $row) {
    		$sort1[$key]  = $row[$firstSort];
    		$sort2[$key] = $row[$secondSort];
    		$sort3[$key] = $row[$thirdSort];
    	}
    	array_multisort($sort1, SORT_STRING, $sort2, SORT_STRING, $sort3, SORT_STRING, $array);
    	return $array;
    }
    /**
     * Insert an item relation.
     *
     * @param Item|int $subjectItem
     * @param int $propertyId
     * @param Item|int $objectItem
     * @return bool True: success; false: unsuccessful
     */
    public static function insertItemRelation($subjectItem, $propertyId, $objectItem)
    {
        // Only numeric property IDs are valid.
        if (!is_numeric($propertyId)) {
            return false;
        }

        // Set the subject item.
        if (!($subjectItem instanceOf Item)) {
            $subjectItem = get_db()->getTable('Item')->find($subjectItem);
        }

        // Set the object item.
        if (!($objectItem instanceOf Item)) {
            $objectItem = get_db()->getTable('Item')->find($objectItem);
        }

        // Don't save the relation if the subject or object items don't exist.
        if (!$subjectItem || !$objectItem) {
            return false;
        }

        $itemRelation = new ItemRelationsRelation;
        $itemRelation->subject_item_id = $subjectItem->id;
        $itemRelation->property_id = $propertyId;
        $itemRelation->object_item_id = $objectItem->id;
        $itemRelation->relation_comment = strlen($relationComment)? $relationComment : '';        
        $itemRelation->save();

        return true;
    }
}
 true;
    }
}
