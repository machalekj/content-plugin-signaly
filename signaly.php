<?php
/*------------------------------------------------------------------------
04.# plg_signaly
05.# ------------------------------------------------------------------------
06.# Jiri Machalek
07.# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
08.# jQuery required for popup mode
09.-------------------------------------------------------------------------*/
defined( '_JEXEC' ) or die();

jimport( 'joomla.event.plugin' );

class plgContentsignaly extends JPlugin 
{

	function plgContentsignaly( &$subject, $params ) 
	{
		parent::__construct( $subject, $params );
 	}

	function onContentPrepare( $context, &$row, &$params, $limitstart=0 )
	{
		global $mainframe;

		$regex = '/{(signaly)\s*(.*?)}/i';

		$plugin	=& JPluginHelper::getPlugin('content', 'signaly');
		$pluginParams = $this->params;

		// $size=$pluginParams->get('size','');
		// if ($size!='') {$size=' size="'.$size.'"';}
		$popup_mode=$pluginParams->get('popup_mode','');

		$matches = array();
		preg_match_all( $regex, $row->text, $matches, PREG_SET_ORDER );

		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root().'plugins/content/signaly/assets/plg_signaly.css');
		JPlugin::loadLanguage('plg_content_signaly');

		foreach ($matches as $args) 
		{
			$args=str_replace(" ","&", $args);
			parse_str( $args[2], $pars );

			$str="";

			$lang_tag="";
			if (isset($pars['lang'])) {$lang_tag="{lang: '".$pars['lang']."'}";}

			$uri =& JURI::getInstance();
			$curl = $uri->toString();

			$id="";if (isset($pars['id'])) {$id=$pars['id'];}
			if ($id!="")
			{
				$article = JTable::getInstance('content');
				$article->load($id);
				$slug = $article->get('id').':'.$article->get('alias');
				$catid = $article->get('catid');
				$catslug = $catid ? $catid .':'.$article->get('category_alias') : $catid;
				$sectionid = $article->get('sectionid');
			
				$curl = 'http://';
				if (isset($_SERVER["HTTPS"]) && $_SERVER["HTTPS"]=='on') {$curl='https://';};
				$curl .= $_SERVER["SERVER_NAME"];
				$curl .= JRoute::_(ContentHelperRoute::getArticleRoute($slug, $catslug, $sectionid));
			}

			$url="<plugin name=signaly version=0.1/>";
			//$url.='<script type="text/javascript" src="https://apis.google.com/js/signaly.js">'.$lang_tag.'</script><g:signaly'.$show_count.$size.' href="'.$curl.'"></g:signaly>';
			$url.='<a href="http://www.signaly.cz/odjinud/sdilej?url='.$curl.'" class="signaly-share">'.JText::_('PLG_CONTENT_SIGNALY_BUTTON_TITLE').'</a>';
			if ($popup_mode=='1') {
				$url.='<script>(function($) {$(function() {$(".signaly-share").click(function(e) {window.open($(this).attr("href") + "&popupMode=1", "", "width=650,height=420"); e.preventDefault();});});})(jQuery);</script>';
			}

			$row->text = preg_replace($regex, $url, $row->text, 1);
		}
	}
}
?>
