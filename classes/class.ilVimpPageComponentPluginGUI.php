<?php

use srag\Plugins\ViMP\UIComponents\Player\VideoPlayer;
use srag\Plugins\VimpPageComponent\Config\Config;

require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Class ilVimpPageComponentPluginGUI
 *
 * @author            Theodor Truffer <tt@studer-raimann.ch>
 * @ilCtrl_isCalledBy ilVimpPageComponentPluginGUI: ilPCPluggedGUI
 */
class ilVimpPageComponentPluginGUI extends ilPageComponentPluginGUI {

    const CMD_CREATE = 'create';
	const CMD_INSERT = 'insert';
	const CMD_STANDARD = self::CMD_INSERT;
	const CMD_SHOW = 'show';
	const CMD_SHOW_FILTERED = 'showFiltered';
	const CMD_SHOW_FILTERED_OWN_VIDEOS = 'showFilteredOwnVideos';
	const CMD_OWN_VIDEOS = 'indexOwnVideos';
	const CMD_SHOW_OWN_VIDEOS = 'showOwnVideos';
	const CMD_EDIT_VIDEO = 'editVideo';
	const CMD_DELETE_VIDEO = 'deleteVideo';
	const CMD_UPDATE_VIDEO = 'updateVideo';

	const SUBTAB_SEARCH = 'subtab_search';
	const SUBTAB_OWN_VIDEOS = 'subtab_own_videos';
    const CMD_EDIT = 'edit';
    /**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilVimpPageComponentPlugin
	 */
	protected $pl;


	/**
	 * ilVimpPageComponentPluginGUI constructor.
	 */
	public function __construct() {
		global $ilCtrl, $tpl, $ilTabs, $lng;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->tabs = $ilTabs;
		$this->lng = $lng;
		$this->pl = new ilVimpPageComponentPlugin();
	}


	/**
	 *
	 */
	public function executeCommand() {
		try {
			$next_class = $this->ctrl->getNextClass();
			$cmd = $this->ctrl->getCmd();

			switch ($next_class) {
				default:
					if ($cmd == self::CMD_INSERT && $_GET['vpco_cmd']) {
						$cmd = $_GET['vpco_cmd'];
						$this->performCommand($cmd);
						break;
					} else {
						$cmd = $this->ctrl->getCmd();
						$this->performCommand($cmd);
						break;
					}
			}
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
	}

	protected function performCommand($cmd) {
		switch ($cmd) {
			case self::CMD_STANDARD:
			case self::CMD_SHOW:
			case self::CMD_SHOW_FILTERED:
				$this->ctrl->setParameter($this, 'vpco_cmd', 'resetFilter');
				$url = $this->ctrl->getLinkTarget($this, self::CMD_STANDARD);
				$this->ctrl->clearParameters($this);
				$name = $this->lng->txt('reset_filter');
				$this->tpl->addJavaScript($this->pl->getDirectory() . '/js/vpco.js');
				$this->tpl->addOnLoadCode('VimpPageComponent.overwriteResetButton("' . $name .'", "' . $url . '");');
				$this->$cmd();
				break;
			case self::CMD_SHOW_FILTERED_OWN_VIDEOS:
			case self::CMD_OWN_VIDEOS:
			case self::CMD_SHOW_OWN_VIDEOS:
				$this->ctrl->setParameter($this, 'vpco_cmd', 'resetFilterOwnVideos');
				$url = $this->ctrl->getLinkTarget($this, self::CMD_STANDARD);
				$this->ctrl->clearParameters($this);
				$name = $this->lng->txt('reset_filter');
				$this->tpl->addJavaScript($this->pl->getDirectory() . '/js/vpco.js');
				$this->tpl->addOnLoadCode('VimpPageComponent.overwriteResetButton("' . $name .'", "' . $url . '");');
				$this->$cmd();
			    break;
			case self::CMD_EDIT_VIDEO:
			case self::CMD_DELETE_VIDEO:
			case self::CMD_UPDATE_VIDEO:
			case self::CMD_CREATE:
			case self::CMD_EDIT:
			case self::CMD_INSERT:
			default:
				$this->$cmd();
		}
	}

	/**
	 * @param $cmd
	 */
	public function redirect($cmd) {
		$this->ctrl->setParameter($this, 'vpco_cmd', $cmd);
		$this->ctrl->redirect($this, self::CMD_INSERT);
	}


	/**
	 * @param $cmd
	 *
	 * @return string
	 */
	public function getLinkTarget($cmd) {
		$this->ctrl->setParameter($this, 'vpco_cmd', $cmd);
		return $this->ctrl->getLinkTarget($this, self::CMD_INSERT);
	}


	/**
	 *
	 */
	public function insert() {
		$this->setSubTabs(self::SUBTAB_SEARCH);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'));
		try {
			$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
        $table_gui->setFilterCommand(self::CMD_INSERT);
        $this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function show() {
		$this->setSubTabs(self::SUBTAB_SEARCH);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'));
		try {
			$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		} catch (xvmpException $e) {
			ilUtil::sendFailure($e->getMessage(), true);
			$this->ctrl->returnToParent($this);
		}
        $table_gui->setFilterCommand(self::CMD_INSERT);
        $table_gui->parseData();
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	protected function showFiltered() {
		$this->setSubTabs(self::SUBTAB_SEARCH);
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$table_gui->determineOffsetAndOrder();
		$this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function applyFilter() {
		$this->ctrl->clearParameters($this);
		$table_gui = new vpcoSearchVideosTableGUI($this, self::CMD_INSERT);
        $table_gui->setFilterCommand(self::CMD_INSERT);
        $table_gui->resetOffset();
		$table_gui->writeFilterToSession();
		$this->redirect(self::CMD_SHOW_FILTERED);
	}


	/**
	 *
	 */
	public function resetFilter() {
		$table_gui = new xvmpSearchVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->resetFilter();
		$this->ctrl->redirect($this, self::CMD_INSERT);
	}


	/**
	 *
	 */
	public function indexOwnVideos() {
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'), true);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$this->tpl->setContent($table_gui->getHTML());
	}


	/**
	 *
	 */
	public function showOwnVideos() {
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		ilUtil::sendInfo($this->getPlugin()->txt('choose_video'), true);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$table_gui->determineOffsetAndOrder();
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	public function applyFilterOwnVideos() {
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->writeFilterToSession();
		$this->redirect(self::CMD_SHOW_FILTERED_OWN_VIDEOS);
	}

	/**
	 *
	 */
	public function resetFilterOwnVideos() {
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT);
		$table_gui->resetOffset();
		$table_gui->resetFilter();
		$this->redirect( self::CMD_OWN_VIDEOS);
	}

	/**
	 *
	 */
	protected function showFilteredOwnVideos() {
		$this->setSubTabs(self::SUBTAB_OWN_VIDEOS);
		$table_gui = new vpcoOwnVideosTableGUI($this, self::CMD_INSERT, self::CMD_SHOW_FILTERED_OWN_VIDEOS);
		$table_gui->setFilterCommand(self::CMD_INSERT);
		$table_gui->parseData();
		$this->tpl->setContent($table_gui->getHTML());
	}

	/**
	 *
	 */
	public function editVideo() {
		$mid = $_GET['mid'];
		$xvmpEditVideoFormGUI = new xvmpEditVideoFormGUI($this, $mid);
		$xvmpEditVideoFormGUI->fillForm();
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function updateVideo() {
		$xvmpEditVideoFormGUI = new xvmpEditVideoFormGUI($this, $_POST['mid']);
		$xvmpEditVideoFormGUI->setValuesByPost();
		if ($xvmpEditVideoFormGUI->saveForm()) {
			ilUtil::sendSuccess($this->pl->txt('form_saved'), true);
			$this->redirect(xvmpOwnVideosGUI::CMD_EDIT_VIDEO);
		}
		ilUtil::sendFailure($this->pl->txt('msg_incomplete'));
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}

	/**
	 *
	 */
	public function uploadVideoForm() {
		$xvmpEditVideoFormGUI = new xvmpUploadVideoFormGUI($this);
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function createVideo() {
		$xvmpEditVideoFormGUI = new xvmpUploadVideoFormGUI($this);
		$xvmpEditVideoFormGUI->setValuesByPost();
		if ($xvmpEditVideoFormGUI->uploadVideo()) {
			ilUtil::sendSuccess($this->pl->txt('video_uploaded'), true);
			$this->ctrl->redirect($this, self::CMD_STANDARD);
		}

		ilUtil::sendFailure($this->pl->txt('form_incomplete'));
		$xvmpEditVideoFormGUI->setValuesByPost();
		$this->tpl->setContent($xvmpEditVideoFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function deleteVideo() {
		$mid = $_GET['mid'];
		$video = xvmpMedium::find($mid);
		$confirmation_gui = new ilConfirmationGUI();
		$confirmation_gui->setFormAction($this->ctrl->getFormAction($this));
		$confirmation_gui->setHeaderText($this->pl->txt('confirm_delete_text'));
		$confirmation_gui->addItem('mid', $mid, $video->getTitle());
		$confirmation_gui->setConfirm($this->lng->txt('delete'),xvmpOwnVideosGUI::CMD_CONFIRMED_DELETE_VIDEO);
		$confirmation_gui->setCancel($this->lng->txt('cancel'), xvmpOwnVideosGUI::CMD_STANDARD);
		$this->tpl->setContent($confirmation_gui->getHTML());
	}


	/**
	 *
	 */
	public function confirmedDeleteVideo() {
		$mid = $_POST['mid'];

		// fetch the video for logging purposes
		$video = xvmpMedium::getObjectAsArray($mid);

		xvmpMedium::deleteObject($mid);

//		xvmpEventLog::logEvent(xvmpEventLog::ACTION_DELETE, $this->getObjId(), $video);

		ilUtil::sendSuccess($this->pl->txt('video_deleted'), true);
		$this->redirect(self::CMD_STANDARD);
	}

	/**
	 *
	 */
	protected function uploadChunks() {
		$xoctPlupload = new xoctPlupload();
		$tmp_id = $_GET['tmp_id'];

		$dir = ILIAS_ABSOLUTE_PATH  . ltrim(ilUtil::getWebspaceDir(), '.') . '/vimp/' . $tmp_id;
		if (!is_dir($dir)) {
			ilUtil::makeDir($dir);
		}

		$xoctPlupload->setTargetDir($dir);
		$xoctPlupload->handleUpload();
	}

	/**
	 *
	 */
	public function create() {
		$mid = filter_input(INPUT_GET, 'mid', FILTER_SANITIZE_NUMBER_INT);
        $video = xvmpMedium::find($mid);

        $video_properties = array(
			"mid" => $mid,
			"width" => Config::getField(Config::KEY_DEFAULT_WIDTH) ?: (isset($video->getProperties()['width']) ? $video->getProperties()['width'] : 268),
			"height" => Config::getField(Config::KEY_DEFAULT_HEIGHT) ?: (isset($video->getProperties()['height']) ? $video->getProperties()['height'] : 150)
		);

		if ($this->createElement($video_properties)) {
            $pc_id = $this->getPCGUI()->getContentObject()->readPCId();
            $this->ctrl->setParameter($this, 'pc_id', $pc_id);
            $this->ctrl->setParameter($this, 'hier_id', 1); // this seems to be ignored, but is still necessary
            $this->ctrl->redirect($this, self::CMD_EDIT);
		}
	}


	/**
	 *
	 */
	public function edit() {
		global $tpl;

		$form = $this->initForm();
		$tpl->setContent($form->getHTML());
	}


	/**
	 *
	 */
	public function update() {
		global $tpl, $lng;

		$form = $this->initForm();
		if ($form->checkInput()) {
			$properties = $this->getProperties();
			$size = $form->getInput('size');
			$properties['width'] = $size['width'];
			$properties['height'] = $size['height'];
			if ($this->updateElement($properties)) {
				ilUtil::sendSuccess($lng->txt("msg_obj_modified"), true);
				$this->returnToParent();
			}
		}

		$form->setValuesByPost();
		$tpl->setContent($form->getHtml());
	}


    /**
     * @return ilPropertyFormGUI
     * @throws ilTemplateException
     * @throws xvmpException
     */
	public function initForm() {
		global $lng, $ilCtrl, $tpl;

		$tpl->addJavaScript($this->getPlugin()->getDirectory() . '/node_modules/ion-rangeslider/js/ion.rangeSlider.min.js');
		$tpl->addCss($this->getPlugin()->getDirectory() . '/node_modules/ion-rangeslider/css/ion.rangeSlider.min.css');
        $tpl->addCss($this->getPlugin()->getDirectory() . '/templates/form.css');
        $tpl->addJavaScript($this->getPlugin()->getDirectory() . '/js/vpco.js');
		$tpl->addOnLoadCode('VimpPageComponent.initForm();');

		include_once("Services/Form/classes/class.ilPropertyFormGUI.php");
		$form = new ilPropertyFormGUI();
        $prop = $this->getProperties();
        $prop['width'] = round($prop['width']);
        $prop['height'] = round($prop['height']);
        $video = xvmpMedium::find($prop['mid']);

        // slider
        $slider = new ilNonEditableValueGUI('', '', true);
        $slider_tpl = $this->getPlugin()->getTemplate('tpl.slider_input.html', false, false);
        $slider_tpl->setVariable('CONFIG', json_encode($this->getRangeSliderConfig()));
        $slider->setValue($slider_tpl->get());
        $form->addItem($slider);

        // thumbnail
        $thumbnail = new ilNonEditableValueGUI($lng->txt('preview'), '', true);
        $thumbnail->setValue('<img width="' . $prop['width'] . 'px" height="' . $prop['height'] . 'px" id="vpco_thumbnail" src="' . $video->getThumbnail() . '">');
        $form->addItem($thumbnail);

        // width height
        $width_height = new ilWidthHeightInputGUI($lng->txt("cont_width") .
            " / " . $lng->txt("cont_height"), "size");
        $width_height->setConstrainProportions(true);
        $width_height->setRequired(true);
        $width_height->setValueByArray(['size' => array_merge($prop, ['constr_prop' => true])]);
        $form->addItem($width_height);

        $form->addCommandButton("update", $lng->txt("save"));
		$form->addCommandButton("cancel", $lng->txt("cancel"));
		$form->setTitle($this->getPlugin()->txt("edit_ex_el"));

		$form->setFormAction($ilCtrl->getFormAction($this));

		return $form;
	}


	/**
	 *
	 */
	public function cancel() {
		$this->returnToParent();
	}


	/**
	 * Get HTML for element
	 *
	 * @param       $a_mode
	 * @param array $a_properties
	 * @param       $a_plugin_version
	 *
	 * @return mixed
	 */
	public function getElementHTML($a_mode, array $a_properties, $a_plugin_version) {
		try {
			$video = xvmpMedium::find($a_properties['mid']);

		VideoPlayer::loadVideoJSAndCSS(false);
		$video_player = new VideoPlayer($video, xvmpConf::getConfig(xvmpConf::F_EMBED_PLAYER) || xvmpMedium::isVimeoOrYoutube($video));
		$video_player->setOption('height', $a_properties['height'] . 'px');
		$video_player->setOption('width', $a_properties['width'] . 'px');
		return $video_player->getHTML();
        } catch (xvmpException $e) {
//			ilUtil::sendInfo($e->getMessage());
			return '<img 
				src="' . ilViMPPlugin::getInstance()->getImagePath('not_available.png') . '" 
				height="' . $a_properties['height'] . '" 
				width="' . $a_properties['width'] . '"
			>';
        }
	}


	/**
	 * @param $active
	 */
	protected function setSubTabs($active) {
		$this->tabs->addSubTab(self::SUBTAB_SEARCH, $this->pl->txt(self::SUBTAB_SEARCH), $this->getLinkTarget(self::CMD_STANDARD));
		$this->tabs->addSubTab(self::SUBTAB_OWN_VIDEOS, $this->pl->txt(self::SUBTAB_OWN_VIDEOS), $this->getLinkTarget(self::CMD_OWN_VIDEOS));
		$this->tabs->setSubTabActive($active);
	}


    /**
     * @return ilVimpPageComponentPlugin
     */
    public function getPlugin()
    {
        return parent::getPlugin();
    }


    /**
     * @return array
     */
    protected function getRangeSliderConfig()
    {
        return [
            'skin' => 'round',
            'min' => 0,
            'max' => 100,
            'from' => 50,
            'from_min' => 10,
            'step' => 1,
            'grid' => true,
            'postfix' => '%',
        ];
    }
}