<?php
/**
 * Item Relations
 * @copyright Copyright 2010-2014 Roy Rosenzweig Center for History and New Media
 * @license http://www.gnu.org/licenses/gpl-3.0.txt GNU GPLv3
 */

class ItemRelations_AjaxController extends Omeka_Controller_AbstractActionController
{
    public function itemajaxAction()
  	{
  		$title = strtoupper($this->getParam('q'));
  		$db = get_db();
  		$items = $db->query("SELECT record_id, CONCAT(text, ' [', record_id, ']') text FROM omeka_element_texts WHERE element_id = 50 AND UPPER(text) LIKE '%$title%' ORDER BY text ASC")->fetchAll();
  		$this->_helper->json($items);
  	}
}
>json($items);
  	}
}
