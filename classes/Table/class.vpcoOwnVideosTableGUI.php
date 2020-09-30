<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

/**
 * Class vpcoOwnVideosTableGUI
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class vpcoOwnVideosTableGUI extends xvmpOwnVideosTableGUI {

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
		'published' => array(
			'sort_field' => 'published'
		),
		'status' => array(
			'sort_field' => 'status'
		),
		'created_at' => array(
			'sort_field' => 'unix_time'
		)
	);
    /**
     * @var ilViMPPlugin
     */
    protected $vimp_pl;


    /**
     * vpcoSearchVideosTableGUI constructor.
     *
     * @param       $parent_gui
     * @param string $parent_cmd
     * @param        $vpco_cmd
     */
	public function __construct($parent_gui, $parent_cmd, $vpco_cmd = '') {
		parent::__construct($parent_gui, $vpco_cmd == ilVimpPageComponentPluginGUI::CMD_SHOW_FILTERED_OWN_VIDEOS ? xvmpOwnVideosGUI::CMD_SHOW_FILTERED : $parent_cmd);
        xvmpVideoPlayer::loadVideoJSAndCSS(false);

		$base_link = $this->ctrl->getLinkTargetByClass(array(ilObjPluginDispatchGUI::class, ilObjViMPGUI::class, xvmpOwnVideosGUI::class),'', '', true);
		$this->tpl_global->addOnLoadCode('VimpContent.ajax_base_url = "'.$base_link.'";');

		$this->pl = new ilVimpPageComponentPlugin();
		$this->vimp_pl = ilViMPPlugin::getInstance();
		$this->setRowTemplate($this->pl->getDirectory() . '/templates/' . static::ROW_TEMPLATE);

		$this->addHiddenInput('pco_data', json_encode($_POST));
		$this->addHiddenInput('commandpg', $_POST['commandpg']);
		$this->addHiddenInput('target', json_encode($_POST['target']));

		$this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', 'applyFilterOwnVideos');
		$this->setFormAction($this->ctrl->getFormAction($this->parent_obj));
		$this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', 'showOwnVideos');

        if ($vpco_cmd !== ilVimpPageComponentPluginGUI::CMD_SHOW_FILTERED_OWN_VIDEOS
            && $parent_cmd !== ilVimpPageComponentPluginGUI::CMD_SHOW_FILTERED_OWN_VIDEOS) {
            $this->tpl = new ilTemplate("tpl.own_videos_table.html", true, true, $this->vimp_pl->getDirectory());
            $this->tpl->setVariable('TABLE_CONTENT_HIDDEN', 'hidden');
            $this->tpl->setCurrentBlock('xvmp_show_videos_button');
            $this->ctrl->setParameter($this->parent_obj, 'vpco_cmd', ilVimpPageComponentPluginGUI::CMD_SHOW_FILTERED_OWN_VIDEOS);
            $this->tpl->setVariable(
                'SHOW_VIDEOS_LINK',
                $this->ctrl->getLinkTarget(
                    $this->parent_obj,
                    $parent_gui instanceof ilVimpPageComponentPluginGUI ? ilVimpPageComponentPluginGUI::CMD_INSERT : ilVimpPageComponentPluginGUI::CMD_SHOW_FILTERED_OWN_VIDEOS)
            );
            $this->tpl->setVariable('SHOW_VIDEOS_LABEL', $this->vimp_pl->txt('btn_show_own_videos'));
            $this->tpl->parseCurrentBlock();
        }
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

		$this->addColumn('', '', 75, true);
	}


	/**
	 * @param xvmpObject $a_set
	 */
	protected function fillRow($a_set) {
		if ($a_set['status'] == 'error') {
			$this->tpl->setVariable('VAL_DISABLED', 'disabled');
		}

		$this->tpl->setVariable('VAL_MID', $a_set['mid']);

		$this->tpl->setVariable('VAL_STATUS_TEXT', $this->vimp_pl->txt('status_' . $a_set['status']));


		foreach ($this->available_columns as $title => $props)
		{
			$this->tpl->setVariable('VAL_' . strtoupper($title), $a_set[$title]);
		}

        foreach ($this->getSelectableColumns() as $title => $props) {
            if ($this->isColumnSelected($title)) {
                $this->tpl->setCurrentBlock('generic');
                $this->tpl->setVariable('VAL_GENERIC', $this->parseColumnValue($title, $a_set[$title]));
                $this->tpl->parseCurrentBlock();
            }
        }

		$this->tpl->setVariable('VAL_ADD', $this->getAddButton($a_set));
		$this->tpl->parseCurrentBlock();
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
}