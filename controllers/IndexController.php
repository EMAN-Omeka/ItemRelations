<?php
/**
 * Item Relations
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

/**
 * Dummy index controller.
 *
 * Simply redirects to the VocabularyController.
 */
class ItemRelations_IndexController extends Omeka_Controller_AbstractActionController
{
    /**
     * Redirect to vocabularies/browse.
     */
    public function indexAction()
    {
        $this->_helper->redirector('browse', 'vocabularies');
    }   

    public function managerelationsAction()
    {
    		    	
    	$form = new Zend_Form();
    	$form->setName('ItemRelationsAvailableRelations');
    	$formDecoration = array(
    			'FormElements',
    			array(array('data'=>'HtmlTag'), array('tag'=>'table', 'class'=>'forms')),
    			'Form'
    	);
    	 
			$form->setDecorators($formDecoration);
			
	    // create decoration for form's elements
    	$elementDecoration = array(
    			'ViewHelper',
    			'Description',
    			'Errors',    			
    			array(array('data'=>'HtmlTag'), array('tag' => 'td', 'valign' => 'TOP')),
    			array('Label', array('tag' => 'td')),
    			array('Errors'),
    			array(array('row'=>'HtmlTag'),array('tag'=>'tr'))
    	);    	
    	$checkall = new Zend_Form_Element_Checkbox('checkall');
    	$checkall->setLabel('Tout cocher');
    	$checkall->setAttrib('title', 'Tout cocher');
    	$checkall->setValue(0);
    	$checkall->setDecorators($elementDecoration);
    	$form->addElement($checkall);
    	    	
    	$db = get_db();
    	$query = "SELECT v.name voc, p.label lab , p.description des, p.id id, p.available available FROM omeka_item_relations_properties p LEFT JOIN omeka_item_relations_vocabularies v ON p.vocabulary_id = v.id WHERE 1 ORDER BY v.name, p.label";
    	$relationsProperties = $db->query($query)->fetchAll();
    	foreach ($relationsProperties as $num => $relationProperty) {
    		$boxName = 'rel_' . $relationProperty['id'];
    		$available = new Zend_Form_Element_Checkbox($boxName);
    		$relationProperty['des'] = get_db()->getTable('ItemRelationsRelation')->translate($relationProperty['lab'], $relationProperty['voc'], 'description'); 
    		$relationProperty['lab'] = get_db()->getTable('ItemRelationsRelation')->translate($relationProperty['lab'], $relationProperty['voc']); 
    		$available->setLabel($relationProperty['voc'] . ' : ' . $relationProperty['lab'] . ' (' . $relationProperty['des'] . ')');
    		$available->setAttrib('title', $relationProperty['voc']);
    		$available->setAttrib('class', 'check-available');
    		$available->setValue($relationProperty['available']);
    		$available->setDecorators($elementDecoration);
    		$form->addElement($available);    		    		
    	} 
    	
    	$submit = new Zend_Form_Element_Submit('submit');
    	$submit->setLabel('Save Available Relations');
    	$submit->setDecorators(array(
        'ViewHelper',
        array(array('data'  => 'HtmlTag'), array('tag' => 'td', 'colspan' => 2)),
        array(array('row'   => 'HtmlTag'), array('tag' => 'tr')),
    	));

    	$form->addElement($submit);

    	// Sauvegarde
    	if ($this->_request->isPost()) {
    		$formData = $this->_request->getPost();
    		if ($form->isValid($formData)) {
    			// Sauvegarde form dans DB
    			$db = get_db();
    			$ids = array();
    			foreach (array_keys($formData) as $key => $id) {
    				if (substr($id, 0, 4) == 'rel_') {
    					$ids[] = substr($id, 4);
    				}
    			}
    			$ids = implode(',', $ids);
    			$sql = "UPDATE `$db->ItemRelationsProperty` SET available = CASE id ";
    			foreach ($formData as $id => $value) {
    				if (is_numeric($value)) {
    					$sql .= " WHEN " . substr($id, 4) . " THEN " . $value;
    				}
    			}
    			$sql .= " END WHERE id IN ($ids)";
    			$db->query($sql);
    			$this->_helper->flashMessenger('Relations disponibles sauvegardées.');
    		}
    	}
    	
    	$content = $form->render();
    	
    	$this->view->content = $content;
			
    	$this->render();
    }   
}

    	$this->view->content = $content;
			
    	$this->render();
    }   
}
