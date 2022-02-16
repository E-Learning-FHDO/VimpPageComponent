<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

use srag\Plugins\ViMP\UIComponents\Player\VideoPlayer;

/**
 * Class vpcoSearchVideosTableGUI
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class vpcoSearchVideosTableGUI extends xvmpSearchVideosTableGUI {

	const ROW_TEMPLATE = 'tpl.search_videos_row.html';
    /**
     * @var array
     */
	protected $available_columns = array(
		'thumbnail' => array(
			'no_header' => true
		),
		'title' => array(
			'sort_field' => 'title',
		),
		'description' => array(
			'sort_field' => 'description',
		),
		'username' => array(
			'sort_field' => 'user',
		),
		'created_at' => array(
			'sort_field' => 'unix_time',
		),
	);


	/**
	 * vpcoSearchVideosTableGUI constructor.
	 *
	 * @param       $parent_gui
	 * @param string $parent_cmd
	 */
	public function __construct($parent_gui, $parent_cmd) {
		parent::__construct($parent_gui, $parent_cmd);
        VideoPlayer::loadVideoJSAndCSS(false);
		$base_link = $this->ctrl->getLinkTargetByClass(array(ilObjPluginDispatchGUI::class, ilObjViMPGUI::class, xvmpOwnVideosGUI::class),'', '', true);
		$this->tpl_global->addOnLoadCode('VimpContent.ajax_base_url = "'.$base_link.'";');

		$this->pl = new ilVimpPageComponentPlugin();
		$this->setRowTemplate($this->pl->getDirectory() . '/templates/' . static::ROW_TEMPLATE);

		$this->addHiddenInput('pco_data', json_encode($_POST));
		$this->addHiddenInput('commandpg', $_POST['commandpg']);
		$this->addHiddenInput('target', json_encode($_POST['target']));

		$this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', 'applyFilter');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_obj));

		$this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', 'show');

	}

    public function getHTML()
    {
        return parent::getHTML() . xvmpGUI::getModalPlayer()->getHTML();
    }

    /**
	 *
	 */
	protected function initColumns() {
		$this->addColumn($this->pl->txt('added'), '', 210, false);

		xvmpTableGUI::initColumns();

		$this->addColumn('', '', 100, true);
	}


	/**
	 * @param xvmpObject $a_set
	 */
	protected function fillRow($a_set) {
		$this->tpl->setVariable('VAL_MID', $a_set['mid']);

		foreach ($this->available_columns as $title => $props)
		{
			if ($title == 'thumbnail') {
				$this->tpl->setVariable('VAL_' . strtoupper($title), $a_set[$title] . '&size=' . self::THUMBSIZE);
				continue;
			}

			$this->tpl->setVariable('VAL_' . strtoupper($title), $a_set[$title] ? $a_set[$title] : '&nbsp;');
		}

        foreach ($this->getSelectableColumns() as $title => $props) {
            if ($this->isColumnSelected($title)) {
                $this->tpl->setCurrentBlock('generic');
                $this->tpl->setVariable('VAL_GENERIC', $this->parseColumnValue($title, $a_set[$title]));
                $this->tpl->parseCurrentBlock();
            }
        }

		$this->tpl->setVariable('VAL_ADD', $this->getAddButton($a_set));
	}


	/**
	 * @param $a_set
	 *
	 * @return string
	 */
	protected function getAddButton($a_set) {
		$button = ilLinkButton::getInstance();
		$button->setCaption('add');
		$this->ctrl->setParameter($this->parent_obj, 'mid', $a_set['mid']);
		$button->setUrl($this->ctrl->getLinkTarget($this->parent_obj, ilVimpPageComponentPluginGUI::CMD_CREATE));
		return $button->getToolbarHTML();
	}

	/**
	 *
	 */
	protected function redirectToParent() {
		$this->ctrl->clearParameters($this->parent_obj);
		$this->ctrl->redirect($this->parent_obj, ilVimpPageComponentPluginGUI::CMD_STANDARD);
	}
}