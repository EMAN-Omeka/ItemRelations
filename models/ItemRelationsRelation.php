<?php
/**
 * Item Relations
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Item Relations Relation model.
 */
class ItemRelationsRelation extends Omeka_Record_AbstractRecord
{
    /**
     * @var int
     */
    public $id;

    /**
     * @var int
     */
    public $subject_item_id;

    /**
     * @var int
     */
    public $property_id;

    /**
     * @var int
     */
    public $object_item_id;

    /**
     * @var varchar
     */
    public $relation_comment;
    
    /**
     * Get a textual representation of the property for this relation.
     *
     * @uses ItemRelationsProperty::getText()
     * @return string
     */
    public function getPropertyText()
    {
    	$vocs = array (
    		'dcterms' => 'DUBLIN CORE',
    		'bibo' => 'BIBO',
    		'foaf' => 'FOAF',
    		'frbr' => 'FRBR',
    	);
        $property = new ItemRelationsProperty;
        $property->local_part = $this->property_local_part;
        if (isset($vocs[$this->vocabulary_namespace_prefix])) {
          $property->label = $this->getTable()->translate($this->property_label, $vocs[$this->vocabulary_namespace_prefix]);          
        } else {
          $property->label = $this->property_label;
        }
        $property->vocabulary_namespace_prefix = $this->vocabulary_namespace_prefix;
        return $property->getText();
    }
}
