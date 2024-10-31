<?php
/* 
 * Plugin Name:   Psychic Search
 * Version:       2.0.4
 * Plugin URI:    http://www.maxblogpress.com/plugins/ps/
 * Description:   Secretly discover what your visitors want to read on your blog. Adjust your settings <a href="options-general.php?page=psychic-search/psychic-search.php">here</a>.
 * Author:        MaxBlogPress
 * Author URI:    http://www.maxblogpress.com
 *
 * License:       GNU General Public License
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 * 
 * Psychic Search plugin was inspired from Search Meter Plugin by Bennett. 
 * Psychic Search uses some of the codes from Search Meter and is being 
 * developed for adding more features over the search meter plugin.
 * You can get more info about search meter plugin here:
 * http://www.thunderguy.com/semicolon/wordpress/search-meter-wordpress-plugin/ 
 * 
 * Copyright (C) 2007 www.maxblogpress.com
 * 
 */
 
define('PWP_NAME', 'Psychic Search');  // Name of the Plugin
define('PWP_VERSION', '2.0.4');		   // Current version of the Plugin

/**
 * PsychicMBP - Psychic Search
 * Holds all the necessary functions and variables
 */
class PsychicMBP 
{
	var $pwp_path = "";
	var $pwp_option = "";
	var $pwp_request = array();
	var $pwp_summary_table = 'psychic_maxblogpress';
	var $pwp_current_table = 'psychic_maxblogpress_curr';
	var $pwp_se_table      = 'psychic_maxblogpress_se';
	
	/**
     * Holds the default option values. These values will be set while activating the plugin
     * @var array
     */
	var	$default_options = array(
							'stats_access_level' => 10, 'admin_search' => 1, 'loggedin_user_search' => 1, 
							'recent_searches_title' => 'Recent Searches', 'recent_searches_num' => 5, 
							'popular_searches_title' => 'Popular Searches', 'popular_searches_num' => 5, 'popular_searches_days' => 30, 
							'rpt_type' => 'daily', 'noof_related_posts' => 5, 'related_posts_position' => 'top', 
							'before_post' => '<br><b>Related Posts For "%keyword%":</b><ul>', 'before_title' => '<li>', 
							'after_title' => '</li>', 'after_post' => '</ul>', 'unique_terms' => 0, 
							'no_related_posts_txt' => 'We are planning to write more posts about %keyword% next week. <a href="%rss-url%">Subscribe to the RSS feed</a> for getting informed about it.', 
							'disable_related_posts' => 1
							);
	/**
     * Search Engines and their search string parameter
     * @var array
     */
	var $pwp_search_engines = array(
							'google.com' => 'q', 'go.google.com' => 'q', 'maps.google.com' => 'q',
							'local.google.com' => 'q', 'search.yahoo.com' => 'p', 'search.msn.com' => 'q',
							'msxml.excite.com' => '&', 'a9.com' => '&', 'search.lycos.com' => 'query',
							'alltheweb.com' => 'q', 'search.aol.com' => 'query', 'search.iwon.com' => 'searchfor',
							'ask.com' => 'q', 'ask.co.uk' => 'ask', 'search.cometsystems.com' => 'qry',
							'hotbot.com' => 'query', 'overture.com' => 'Keywords', 'metacrawler.com' => 'qkw',
							'search.netscape.com' => 'query', 'looksmart.com' => 'key', 'dpxml.webcrawler.com' => 'qkw',
							'search.earthlink.net' => 'q', 'search.viewpoint.com' => 'k', 'mamma.com' => 'query'
							);
	
	/**
	 * Constructor. Adds Psychic Search plugin actions/filters and gets the user defined options.
	 * @access public
	 */
	function PsychicMBP() {
		global $table_prefix;
		$this->pwp_path          = preg_replace('/^.*wp-content[\\\\\/]plugins[\\\\\/]/', '', __FILE__);
		$this->pwp_path          = str_replace('\\','/',$this->pwp_path);
		$this->pwp_siteurl       = get_bloginfo('wpurl');
		$this->pwp_siteurl       = (strpos($this->pwp_siteurl,'http://') === false) ? get_bloginfo('siteurl') : $this->pwp_siteurl;
		$this->pwp_fullpath      = $this->pwp_siteurl.'/wp-content/plugins/'.substr($this->pwp_path,0,strrpos($this->pwp_path,'/')).'/';
		$this->pwp_abspath       = str_replace("\\","/",ABSPATH); 
		$this->img_how           = '<img src="'.$this->pwp_fullpath.'include/images/how.gif" border="0" align="absmiddle">';
		$this->img_comment       = '<img src="'.$this->pwp_fullpath.'include/images/comment.gif" border="0" align="absmiddle">';
		$this->pwp_action_count  = 0;
		$this->pwp_pagination    = 1;   // Pagination set to true by default
		$this->pwp_rows_to_show  = 10;  // No. of rows to show per page
		$this->pwp_rows_in_rpt   = 10;  // No. of rows to show while sending report
		$this->pwp_max_rows      = 110; // Max. records to store in DB
		$this->pwp_summary_table = $table_prefix.$this->pwp_summary_table;
		$this->pwp_current_table = $table_prefix.$this->pwp_current_table;
		$this->pwp_se_table      = $table_prefix.$this->pwp_se_table;
		
	    add_action('activate_'.$this->pwp_path, array(&$this, 'pwpActivate'));
		add_action('admin_head', array(&$this, 'pwpInit'));	
		$this->ps_activate = get_option('ps_activate');
		if ( $this->ps_activate == 2 ) {
			add_filter('the_posts', array(&$this, 'pwpSaveSearch'), 30);	
		}
		if( !$this->pwp_option = get_option('ps_options') ) {
			$this->pwp_option  = $this->default_options;
		}
		$this->pwp_stats_access_level = $this->pwp_option['stats_access_level'];
		
		add_action('admin_menu', array(&$this, 'pwpAddMenu'));
		add_action('admin_footer', array(&$this, 'pwpAddFulltextIndex'));
		add_action('pwp_schedule', array(&$this, 'pwpEmailReport'));
	}
	
	/**
	 * Called when plugin is activated. Adds 'ps_options' options to the options table.
	 * Creates summary and recent tables
	 * @access public
	 */
	function pwpActivate() {
		add_option('ps_activate', 0);
		$this->default_options['rpt_email'] = get_bloginfo('admin_email');
		add_option('ps_options', $this->default_options, 'Psychic Search plugin options', 'no');
		$this->pwpCreateSummaryTable();
		$this->pwpCreateCurrentTable();
		$this->pwpCreateSETable();
		add_option('ps_version', PWP_VERSION);
		return true;
	}
	
	/**
	 * Creates a table "psychic_maxblogpress"
	 * @access public 
	 */
	function pwpCreateSummaryTable() {
		global $wpdb;
		if ( $wpdb->get_var("show tables like '$this->pwp_summary_table'") != $this->pwp_summary_table ) {
			if ( file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ) {
				require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );
			} else { // Wordpress <= 2.2
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			}
			dbDelta("CREATE TABLE `{$this->pwp_summary_table}` (
					`terms` VARCHAR(50) NOT NULL,
					`date` DATE NOT NULL,
					`count` INT(11) NOT NULL,
					`last_hits` INT(11) NOT NULL,
					PRIMARY KEY (`terms`,`date`)
					);
				");
		}
	}
	
	/**
	 * Creates a table "psychic_maxblogpress_curr"
	 * @access public 
	 */
	function pwpCreateCurrentTable() {
		global $wpdb;
		if ( $wpdb->get_var("show tables like '$this->pwp_current_table'") != $this->pwp_current_table ) {
			if ( file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ) {
				require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			} else { // Wordpress <= 2.2
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			}
			dbDelta("CREATE TABLE `{$this->pwp_current_table}` (
					`terms` VARCHAR(50) NOT NULL,
					`datetime` DATETIME NOT NULL,
					`hits` INT(11) NOT NULL,
					`details` TEXT NOT NULL,
					KEY `datetimeindex` (`datetime`)
					);
				");
		}
	}
	
	/**
	 * Creates a table "psychic_maxblogpress_se"
	 * @access public 
	 */
	function pwpCreateSETable() {
		global $wpdb;
		if ( $wpdb->get_var("show tables like '$this->pwp_se_table'") != $this->pwp_se_table ) {
			if ( file_exists(ABSPATH . 'wp-admin/includes/upgrade.php') ) {
				require_once(ABSPATH . '/wp-admin/includes/upgrade.php');
			} else { // Wordpress <= 2.2
				require_once(ABSPATH . 'wp-admin/upgrade-functions.php');
			}
			dbDelta("CREATE TABLE `{$this->pwp_se_table}` (
					`host` VARCHAR(200) NOT NULL,
					`terms` VARCHAR(250) NOT NULL,
					`searches` INT(11) DEFAULT 1,
					`datetime` DATETIME NOT NULL,
					`related_posts` INT(4),
					`referrer` TEXT,
					`ip` VARCHAR(150)
					);
				");
		}
	}
	
	/**
	 * Adds fulltext index to the columns post_name, post_content
	 * @access public
	 */
	function pwpAddFulltextIndex() {
		global $wpdb;
		if ( get_option('ps_index_added') != 1 ) {
			$posts_table = $table_prefix.'posts';
			$sql = "ALTER TABLE $wpdb->posts ADD FULLTEXT ps_related_post (post_name, post_content)";
			$wpdb->hide_errors();
			$wpdb->query($sql);
			$wpdb->show_errors();
			add_option('ps_index_added', 1, 'Psychic Search FULLTEXT Index Added');
		}
		return true;
	}
	
	/**
	 * Plugins CSS and Javascripts
	 * @access public
	 */
	function pwpInit() {
		?>
		<script type="text/javascript">
		function pwpShowHide(curr, img) {
			var curr = document.getElementById(curr);
			if ( img != '' ) {
				var img  = document.getElementById(img);
			}
			var showRow = 'block'
			if ( navigator.appName.indexOf('Microsoft') == -1 && curr.tagName == 'TR' ) {
				var showRow = 'table-row';
			}
			if ( curr.style == '' || curr.style.display == 'none' ) {
				curr.style.display = showRow;
				img.src = '<?php echo $this->pwp_fullpath?>include/images/minus.gif';
			} else if ( curr.style != '' || curr.style.display == 'block' || curr.style.display == 'table-row' ) {
				curr.style.display = 'none';
				img.src = '<?php echo $this->pwp_fullpath?>include/images/plus.gif';
			}
		}
		</script>
		<style type="text/css">
		.pwp_img {
		text-decoration: none;
		border-bottom: none;
		}
		</style>
		<?php
	}
	
	/**
	 * Adds "Psychic Search" link to admin's Options and Dashboard menu
	 * @access public 
	 */
	function pwpAddMenu() {
		if ( $this->ps_activate == 2 ) {
			add_submenu_page('index.php', 'Psychic Search Statistics', 'Psychic Search', $this->pwp_stats_access_level, $this->pwp_path, array(&$this, 'pwpStatsPg'));
		}
		add_options_page('Psychic Search', 'Psychic Search', 'manage_options', $this->pwp_path, array(&$this, 'pwpOptionsPg'));
	}
	
	/**
	 * Page Header
	 */
	function pwpHeader() {
		if ( !isset($_GET['dnl']) ) {	
			$pwp_version_chk = $this->pwpRecheckData();
			if ( ($pwp_version_chk == '') || strtotime(date('Y-m-d H:i:s')) > (strtotime($pwp_version_chk['last_checked_on']) + $pwp_version_chk['recheck_interval']*60*60) ) {
				$update_arr = $this->pwpExtractUpdateData();
				if ( count($update_arr) > 0 ) {
					$latest_version   = $update_arr[0];
					$recheck_interval = $update_arr[1];
					$download_url     = $update_arr[2];
					$msg_in_plugin    = $update_arr[3];
					$msg_in_plugin    = $update_arr[4];
					$upgrade_url      = $update_arr[5];
					if( PWP_VERSION < $latest_version ) {
						$pwp_version_check = array('recheck_interval' => $recheck_interval, 'last_checked_on' => date('Y-m-d H:i:s'));
						$this->pwpRecheckData($pwp_version_check);
						$msg_in_plugin = str_replace("%latest-version%", $latest_version, $msg_in_plugin);
						$msg_in_plugin = str_replace("%plugin-name%", PWP_NAME, $msg_in_plugin);
						$msg_in_plugin = str_replace("%upgrade-url%", $upgrade_url, $msg_in_plugin);
						$msg_in_plugin = '<div style="border-bottom:1px solid #CCCCCC;background-color:#FFFEEB;padding:6px;font-size:11px;text-align:center">'.$msg_in_plugin.'</div>';
					} else {
						$msg_in_plugin = '';
					}
				}
			}
		}
		echo '<h2>'. PWP_NAME .' '. PWP_VERSION .'</h2>';	
		if ( trim($msg_in_plugin) != '' && !isset($_GET['dnl']) ) echo $msg_in_plugin;
		echo '<br /><strong>'.$this->img_how.' <a href="http://www.maxblogpress.com/plugins/ps/ps-use/" target="_blank">How to use it?</a></strong>&nbsp;&nbsp;&nbsp;';
		echo '<strong>'.$this->img_comment.' <a href="http://www.maxblogpress.com/plugins/ps/ps-comments/" target="_blank">Comments and Suggestions</a></strong><br><br>';
	}
	
	/**
	 * Page footer
	 * @param string $pg dashboard or options
	 */
	function pwpFooter($pg='') {
		if ( current_user_can('manage_options') && $pg == 'dashboard' ) { ?>
			<p><a href="<?php echo $this->pwp_siteurl;?>/wp-admin/options-general.php?page=<?php echo $this->pwp_path;?>"><strong>Click here to change the settings</strong></a></p>
		<?php } else if ( $pg == 'options' ) {?>
			<p><a href="<?php echo $this->pwp_siteurl;?>/wp-admin/index.php?page=<?php echo $this->pwp_path;?>"><strong>Click here to view search statistics</strong></a></p>
		<?php } ?>
		<p style="text-align:center;margin-top:2em;"><strong><?php echo PWP_NAME.' '.PWP_VERSION; ?> by <a href="http://www.maxblogpress.com/" target="_blank" >MaxBlogPress</a></strong></p>
	<?php
	}
	
	/**
	 * Psychic Search's search statistics menu
	 * @access public 
	 */
	function pwpStatsMenu() {
		?>
		<style type="text/css">
		#pwp_menu { 
			margin: 0;
			padding: 0; 
		}
		#pwp_menu li { 
			display: inline; list-style-type: none; list-style-image: none; list-style-position: outside; text-align: center;
			margin: 1px;
			line-height: 170%;
		}
		#pwp_menu li.current { 
			font-weight: bold;
			background-color: #fff;
			color: #343434;
			padding: 4px;
		}
		#pwp_menu li.next { 
			font-weight: normal;
			background-color: #83B4D8;
			color: #ffffff;
			padding: 4px;
		}
		#pwp_menu a {
			background-color: #83B4D8;
			color: #ffffff; 
			padding: 4px;
			border-bottom: none;
		}
		#pwp_menu a:hover {
			background-color: #83B4D8;
			color: #343434; 
		}
		</style>
		<?php if ( $_GET['pwp_show'] == '100' ) { ?>
			<p>
			<ul id="pwp_menu">
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']?>">Summary</a></li>
			<li class="current">Last 100 Searches</li>
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&amp;pwp_show=100se"?>">Last 100 Searches From Search Engines</a></li>
			</ul>
			</p>
		<?php } else if ( $_GET['pwp_show'] == '100se' ) { ?>
			<p>
			<ul id="pwp_menu">
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']?>">Summary</a></li>
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&amp;pwp_show=100"?>">Last 100 Searches</a></li>
			<li class="current">Last 100 Searches From Search Engines</li>
			</ul>
			</p>
		<?php } else { ?>
			<p>
			<ul id="pwp_menu">
			<li class="current">Summary</li>
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&amp;pwp_show=100" ?>">Last 100 Searches</a></li>
			<li class="next"><a href="<?php echo $_SERVER['PHP_SELF']."?page=".$_REQUEST['page']."&amp;pwp_show=100se"?>">Last 100 Searches From Search Engines</a></li>
			</ul>
			</p>
		<?php }
	}
	
	/**
	 * Displays the search statistics
	 * Carries out all the operations in Dashboard.
	 * @access public 
	 */
	function pwpStatsPg() {
		$pwp_show = $_GET['pwp_show'];
		if ( $pwp_show == '100' ) {
			$this->pwpLatestSearches(100);
		} else if ( $pwp_show == '100se' ) {
			$this->pwpLatestSearchesSE(100);
		} else {
			$this->pwpStatsSummary();
		}
	}
	
	/**
	 * Displays the current search statistics within a blog
	 * @param integer $noof_rows Number of rows to display
	 * @access public 
	 */
	function pwpLatestSearches($noof_rows) {
		global $wpdb;
		$query = "SELECT `datetime`, `terms`, `hits`, `details`
				FROM `{$this->pwp_current_table}`
				ORDER BY `datetime` DESC, `terms` ASC
				LIMIT $noof_rows";
		$results = $wpdb->get_results($query);
		require_once('include/latest_searches.php');
	}
	
	/**
	 * Displays the current search statistics from search engines
	 * @param integer $noof_rows Number of rows to display
	 * @access public 
	 */
	function pwpLatestSearchesSE($noof_rows) {
		global $wpdb;
		$query = "SELECT `host`, `terms`, `datetime`, `searches`, `related_posts`, `referrer`, `ip`  
				FROM `{$this->pwp_se_table}`
				ORDER BY `datetime` DESC, `terms` ASC
				LIMIT $noof_rows";
		$results = $wpdb->get_results($query);
		foreach ( $results as $result ) { 
			$se_terms_arr[] = array($result->host,$result->terms,$result->datetime,$result->related_posts,$result->referrer,$result->ip,$result->searches);
		}
		$preview_url = $this->pwpGetRandomPreviewURL();
		require_once('include/latest_searches_se.php');
	}
		
	/**
	 * Emails the search statistics report
	 * @access public
	 */
	function pwpEmailReport() {
		if ( $this->pwp_option['email_report'] == 1 && trim($this->pwp_option['rpt_email']) != '' ) {
			if ( $this->pwp_option['rpt_type'] == 'daily' ) {
				$rpt_time_span = 24*60*60;
			} else if ( $this->pwp_option['rpt_type'] == 'weekly' ) {
				$rpt_time_span = 24*7*60*60;
			} else { // Monthly
				$rpt_time_span = 24*30*60*60;
			}
			$rpt_sent_datetime = $this->pwp_option['rpt_sent_datetime'];
			$rpt_next_datetime = $rpt_sent_datetime + $rpt_time_span;
			$this->pwp_option['rpt_sent_datetime'] = $rpt_next_datetime;
			update_option('ps_options', $this->pwp_option);
			wp_clear_scheduled_hook('pwp_schedule');
			wp_schedule_single_event($rpt_next_datetime, 'pwp_schedule');
			
			$this->pwp_pagination   = 0;
			$this->pwp_rows_to_show = $this->pwp_rows_in_rpt;
			$to         = $this->pwp_option['rpt_email'];
			$from_email = $this->pwp_option['rpt_email'];
			$from_name  = get_bloginfo('blogname');
			$charset    = "iso-8859-1";
			$headers    = "From: \"{$from_name}\" <{$from_email}>\n";
			$headers   .= "MIME-Version: 1.0\n";
			$headers   .= "Content-Type: text/html; charset=\"{$charset}\"\n";
			$subject    = PWP_NAME.' '.ucfirst($rpt_type).' Report';
			
			ob_start();
			require_once('include/email_report.php');
			$message = ob_get_contents();
			ob_end_clean();
			
			if ( strpos($message,'mysql_error()') === false ) {
				@mail($to, $subject, $message, $headers);
			}
		}
		return true;
	}
	
	/**
	 * Returns the search string parameter depending upon the referrer
	 * @param string $ref
	 * @access public
	 */
	function pwpGetSearchStringParam($referrer) {
		if ( isset($search_param) ) return $search_param;
		$search_param = false;
		
		// Check if the referrer is in the above array
		if ( isset($this->pwp_search_engines[$referrer]) ) {
			$search_param = $this->pwp_search_engines[$referrer];
		} else {
			// Lets check referrals for international TLDs and sites with strange formats
			if( substr($referrer, 0, 7) == 'google.' )
				$search_param = "q";
			elseif(  substr($referrer, 0, 13) == 'search.atomz.' )
				$search_param = "sp-q";
			elseif( substr($referrer, 0, 11) == 'search.msn.' )
				$search_param = "q";
			elseif(  substr($referrer, 0, 13) == 'search.yahoo.' )
				$search_param = "p";
			elseif( preg_match('/home\.bellsouth\.net\/s\/s\.dll/i', $referrer) )
				$search_param = "bellsouth";
		}
		return $search_param;
	}
	
	/**
	 * Extracts search string from the referrer and returns search terms
	 * @access public 
	 */
	function pwpGetTerms($search_param) {
		if ( isset($search_terms) ) return $search_terms;
		$query_array = array();
		$query_terms = null;
	
		// A few search engines include the query as a URL path, not a variable (Excite/A9, etc)
		if ( $search_param == '&' ) {
			$query = urldecode(substr(strrchr($_SERVER['HTTP_REFERER'], '/'), 1));
		} else {
			// Extract search string
			$query = explode($search_param.'=', $_SERVER['HTTP_REFERER']);
			$query = explode('&', $query[1]);
			$query = urldecode($query[0]);
		}
	
		// Remove quotes, split into words, and format for HTML display
		$query = str_replace("'", '', $query);
		$query = str_replace('"', '', $query);
		$query_array = preg_split('/[\s,\+\.]+/',$query);
		$query_terms = implode(' ', $query_array);
		$search_terms = htmlspecialchars(urldecode($query_terms));
	
		return $search_terms;
	}

	/**
	 * Extracts and returns Host Name from the referrer string
	 * @access public 
	 */
	function pwpGetReferrer() {
		if (isset($referrer)) return $referrer;
	
		if ( !isset($_SERVER['HTTP_REFERER']) || ($_SERVER['HTTP_REFERER'] == '') ) return false;
	
		$referrer_info = parse_url($_SERVER['HTTP_REFERER']);
		$referrer      = $referrer_info['host'];
	
		// Remove 'www.' is it exists
		if ( substr($referrer, 0, 4) == 'www.' ) {
			$referrer = substr($referrer, 4);
		}	
		return $referrer;
	}
	
	/**
	 * Extracts and displays related posts if search was made from search engines
	 * @access public 
	 */
	function pwpRelatedPosts($limit, $before_title, $after_title, $before_post, $after_post) {
		global $wpdb, $id;
		
		$pwp_preview = $_GET['pwp_preview'];
		$referrer = $this->pwpGetReferrer();
		if ( !$referrer && !$pwp_preview ) return false; // If no referrer and is not preview
		$search_param = $this->pwpGetSearchStringParam($referrer);
		
		if ( $search_param || $pwp_preview ) { 
			if ( $pwp_preview ) {
				$search_terms = $pwp_preview; 
			} else {
				$search_terms = $this->pwpGetTerms($search_param); 
			}
			
			// Show related posts if not disable by administrator
			if ( $this->pwp_option['disable_related_posts'] != 1 ) {
				if ( $limit == '' )
					$limit = $this->pwp_option['noof_related_posts'];
				if ( $before_title == '' )
					$before_title = $this->pwp_option['before_title'];
				if ( $after_title == '' )
					$after_title  = $this->pwp_option['after_title'];
				if ( $before_post == '' )
					$before_post  = $this->pwp_option['before_post'];
				if ( $after_post == '' )
					$after_post	  = $this->pwp_option['after_post'];
				$before_title = stripslashes(str_replace('%keyword%',$search_terms,$before_title));
				$after_title  = stripslashes(str_replace('%keyword%',$search_terms,$after_title));
				$before_post  = stripslashes(str_replace('%keyword%',$search_terms,$before_post));
				$after_post   = stripslashes(str_replace('%keyword%',$search_terms,$after_post));
	
				$time_difference = get_settings('gmt_offset');
				$now = gmdate("Y-m-d H:i:s",(time()+($time_difference*3600)));
	
				// Primary SQL query
				$sql = "SELECT ID, post_title, post_content,"
					 . "MATCH (post_name,post_content) "
					 . "AGAINST ('$search_terms') AS score "
					 . "FROM $wpdb->posts WHERE "
					 . "MATCH (post_name,post_content) "
					 . "AGAINST ('$search_terms') "
					 . "AND post_date <= '$now' "
					 . "AND (post_status IN ('publish', 'static')) ";
				if ( $id > 0 ) $sql .= "AND ID != '$id' ";
				$sql .= "ORDER BY score DESC LIMIT $limit";

				$results = $wpdb->get_results($sql);
				$output = '';
				if ( $results ) { // If related post found
					$noof_related_posts = count($results);
					foreach ( $results as $result ) {
						$title = stripslashes(apply_filters('the_title', $result->post_title));
						$permalink = get_permalink($result->ID);
						$post_content = strip_tags($result->post_content);
						$post_content = stripslashes($post_content);
						$output .= $before_title .'<a href="'.$permalink.'" rel="bookmark" title="Permanent Link: '.$title.'">'.$title.'</a>' . $after_title;
					}
					$output = $before_post . $output . $after_post;
				} else {
					$rss_url = get_bloginfo('rss2_url');
					$no_related_post_txt = str_replace('%keyword%', '"'.$search_terms.'"', $this->pwp_option['no_related_posts_txt']);
					$no_related_post_txt = str_replace('%rss-url%', $rss_url, $no_related_post_txt);
					echo '<p><table cellpadding="3" width="98%" align="center" bgcolor="#f8f8f8" style="border:1px solid #bdbdbd"><tr><td>'.$no_related_post_txt.'</td></tr></table></p>';
				}
			} // EOF show related posts
			if ( !$pwp_preview ) {
				$this->pwpSaveSearchFromSE($search_terms,$noof_related_posts,$referrer); // Save search details
			}
			echo $output;
		}
	}
	
	/**
	 * Saves the search details for searches made from search engine
	 * @access public 
	 */
	function pwpSaveSearchFromSE($search_terms,$noof_related_posts,$host) {
		global $wpdb;
		
		$search_terms = trim($search_terms);
		$host     	  = strtolower(trim($host));
		$referrer 	  = $_SERVER['HTTP_REFERER'];
		$ip       	  = $_SERVER['REMOTE_ADDR'];
		$noof_related_posts = intval($noof_related_posts);
		
		$query  = "SELECT terms FROM $this->pwp_se_table WHERE terms='$search_terms' AND referrer='$referrer' AND ip='$ip' 
		           AND datetime > DATE_SUB(NOW(), INTERVAL 1 DAY)";
		$rs     = mysql_query($query);
		$exists = mysql_num_rows($rs);
		if ( $exists <= 0 ) {
			$query = "INSERT INTO $this->pwp_se_table (host, terms, searches, datetime, related_posts, referrer, ip) 
					  VALUES ('$host', '$search_terms', 1, NOW(), $noof_related_posts, '$referrer', '$ip')";
			$success = mysql_query($query);
			// Delete old rows from table after certain limit attained
			$query = "SELECT count(`datetime`) as rowcount FROM `{$this->pwp_se_table}`";
			$rowcount = mysql_query($query);
			if ( $rowcount > ($this->pwp_max_rows) ) {
				$query = "DELETE FROM `{$this->pwp_se_table}` WHERE `datetime` < DATE_SUB(NOW(), INTERVAL 35 DAY)";
				mysql_query($query);
			}
		}
	}	
	
	/**
	 * Saves the search details for searches within the blog
	 * @access public 
	 */
	function pwpSaveSearch(&$posts) {
		global $wpdb, $wp_query;
		
		// if not to count searches from administration console
		if ( $this->pwp_option['admin_search'] != 1 && is_admin()) {
			return $posts;
		}
		// if not to count searches if user is logged in
		if ( $this->pwp_option['loggedin_user_search'] != 1 && is_user_logged_in() ) {
			return $posts;
		}
	
		++$this->pwp_action_count;
		// save search only if it is from search page, not the second or subsequent page of a previously-counted search,
		// not a duplicate save, not from sidebar's recent/popular search list
		if ( is_search() && !is_paged()	&& (1 == $this->pwp_action_count) && !isset($_GET['pwp']) ) {
			$search_string = $wp_query->query_vars['s'];
			if ( get_magic_quotes_gpc() ) {
				$search_string = stripslashes($search_string);
			}
			$search_terms  = $search_string;
			$search_terms  = preg_replace('/[," ]+/', ' ', $search_terms);
			$search_terms  = trim($search_terms);
			$hit_count     = count($posts); // Total no. of search results per page
			$details       = $_SERVER['REMOTE_ADDR'];
			$search_string = addslashes($search_string);
			$search_terms  = addslashes($search_terms);
			$details       = addslashes($details);
	
			$query = "INSERT INTO `{$this->pwp_current_table}` (`terms`,`datetime`,`hits`,`details`)
					  VALUES ('$search_string',NOW(),$hit_count,'$details')";
			$success = mysql_query($query);
			if ( $success ) {
				// Delete old rows from table after certain limit attained
				$query = "SELECT count(`datetime`) as rowcount FROM `{$this->pwp_current_table}`";
				$rowcount = mysql_query($query);
				if ( $rowcount > $this->pwp_max_rows ) {
					$query = "DELETE FROM `{$this->pwp_current_table}` WHERE `datetime` < DATE_SUB(NOW(), INTERVAL 35 DAY)";
					mysql_query($query);
				}
			}
			// Add search data in Summary Table.
			$query = "INSERT INTO `{$this->pwp_summary_table}` (`terms`,`date`,`count`,`last_hits`)
					  VALUES ('$search_terms',CURDATE(),1,$hit_count)";
			$success = mysql_query($query);
			if ( !$success ) { // If duplicate term or date
				$query = "UPDATE `{$this->pwp_summary_table}` SET 
						`count` = `count` + 1, 
						`last_hits` = $hit_count 
						WHERE `terms` = '$search_terms' AND `date` = CURDATE()";
				mysql_query($query);
			}
		}
		return $posts;
	}
	
	/**
	 * Select random post for preview and return the preview url
	 * @access public 
	 */
	function pwpGetRandomPreviewURL() {
		global $wpdb;
		$preview_url = '';
		$sql = "SELECT ID FROM $wpdb->posts WHERE post_status='publish' ORDER BY RAND()";
		$post_id = $wpdb->get_var($sql);
		$preview_url = get_permalink($post_id);
		if ( strpos($preview_url,'?') === false ) $preview_url = $preview_url.'?';
		else if ( $preview_url != '' ) $preview_url = $preview_url.'&';
		return $preview_url;
	}
	/**
	 * Lists search summary. All searches, searches without any result, searches from search engine
	 * @access public 
	 */
	function pwpStatsSummary() {
		global $wpdb;
		
		$ps_msg = '';
		$preview_text = '';
		$preview_tooltip = "Wondering how your page will look like when a visitor comes from search engine?<br><br>Enter the keyword you want to test and then click on &quot;Preview&quot; button. A new window will open with the preview of a post as the way it will be seen by the visitors coming from search engines.";
		$this->pwp_request = $_REQUEST['pwp'];
		
		if ( isset($this->pwp_request['rpt_submit']) ) {
			$now_time = current_time('mysql'); // Current time
			$this->pwp_option['email_report'] = $this->pwp_request['email_report'];
			$this->pwp_option['rpt_email']    = $this->pwp_request['rpt_email'];
			$this->pwp_option['rpt_type']     = $this->pwp_request['rpt_type'];
			if ( $this->pwp_request['rpt_type'] == 'daily' ) {
				$rpt_time_span = 24*60*60;
			} else if ( $this->pwp_request['rpt_type'] == 'weekly' ) {
				$rpt_time_span = 24*7*60*60;
			} else { // Monthly
				$rpt_time_span = 24*30*60*60;
			}
			$send_rpt_time = strtotime($now_time)+$rpt_time_span;
			$this->pwp_option['rpt_sent_datetime'] = $send_rpt_time;
			update_option('ps_options', $this->pwp_option);
			// Schedule the report emailing event
			if ( $this->pwp_request['email_report'] == 1 ) {
				wp_schedule_single_event($send_rpt_time, 'pwp_schedule');
			} else {
				wp_clear_scheduled_hook('pwp_schedule');
			}
			$ps_msg = 'Saved Successfully.';
		}
		if ( $ps_msg != '' ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$ps_msg.'</strong></p></div>';
		}
		// Delete old records
		$wpdb->query("DELETE FROM `{$this->pwp_summary_table}` WHERE `date` < DATE_SUB(CURDATE(), INTERVAL 30 DAY)");
		
		if ( intval($this->pwp_option['email_report']) == 1 ) $email_report_chk = 'checked';
		$preview_url = $this->pwpGetRandomPreviewURL();
		
		require_once('include/stats_summary.php');		
	}
	
	/**
	 * Extracts and displays search statistics in tabular format
	 * @param integer $days
	 * @param boolean $all_searches
	 * @param string $search_group
	 * @access public 
	 */
	function pwpSummaryTable($days, $all_searches=false, $search_group) {
		global $wpdb;
		
		$having_no_hits = $all_searches ? '' : 'HAVING hits=0';
		$results = $wpdb->get_results(
			"SELECT `terms`, 
			SUM(`count`) AS totcount, 
			SUBSTRING(MAX(CONCAT(`date`, ' ', `last_hits`)), 12) AS hits 
			FROM `{$this->pwp_summary_table}` 
			WHERE DATE_SUB(CURDATE( ), INTERVAL $days DAY) <= `date` 
			GROUP BY `terms` 
			$having_no_hits 
			ORDER BY totcount DESC, `terms` ASC 
			");
		$total_rows = count($results);
		if ( $total_rows ) {
			require('include/stats_summary_tbl.php');
		} else { 
			echo '<p>No searches saved for this period.</p>';
		}
	}
	
	/**
	 * Displays search engine icon
	 * @param string $hosts
	 * @param string $referrers
	 * @access public 
	 */
	function pwpDisplayIcon($hosts,$referrers) {
		$hosts_arr     = explode(',', $hosts);
		$referrers_arr = explode(',', $referrers);
		$se_icons      = '';
		foreach ( (array) $hosts_arr as $key => $host ) {
			if ( strpos($host,'google') !== false )		    $se_icon = 'google.gif';
			else if ( strpos($host,'yahoo') !== false )	    $se_icon = 'yahoo.gif';
			else if ( strpos($host,'msn') !== false )	    $se_icon = 'msn.gif';
			else if ( strpos($host,'ask') !== false )		$se_icon = 'ask.gif';
			else if ( strpos($host,'aol') !== false )	    $se_icon = 'aol.gif';
			else if ( strpos($host,'netscape') !== false )  $se_icon = 'netscape.gif';
			else if ( strpos($host,'iwon') !== false )		$se_icon = 'iwon.gif';
			else if ( strpos($host,'hotbot') !== false )	$se_icon = 'hotbot.gif';
			else if ( strpos($host,'mamma') !== false )	    $se_icon = 'mamma.gif';
			else if ( strpos($host,'earthlink') !== false ) $se_icon = 'earthlink.gif';
			else if ( strpos($host,'viewpoint') !== false ) $se_icon = 'viewpoint.gif';
			else if ( strpos($host,'alltheweb') !== false ) $se_icon = 'alltheweb.gif';
			else $se_icon = 'none.gif';
			if ( strpos($se_icons,$se_icon) === false ) {
				$se_icon   = $this->pwp_fullpath .'include/images/'.$se_icon;
				$se_icons .= '<a href="'.$referrers_arr[$key].'" target="_blank" class="pwp_img"><img src="'.$se_icon.'" border="0" align="absmiddle" hspace="3" /></a>';
			}
		}
		return $se_icons;
	}
	
	/**
	 * Extracts and displays search statistics in tabular format for searches from search engines
	 * @param integer $days
	 * @param boolean $all_searches
	 * @param string $search_group
	 * @access public 
	 */
	function pwpSummaryTableSE($days, $search_group) {  
		global $wpdb;
		
		$se_terms_arr = array();
		$sql = "SELECT host,terms,searches,datetime,related_posts,referrer FROM $this->pwp_se_table 
				WHERE DATE_SUB(NOW( ),INTERVAL $days DAY) <= datetime ORDER BY searches DESC, terms ASC";
		$results = $wpdb->get_results($sql);
		$total_rows = count($results);
		if ( $total_rows ) {
			foreach ( $results as $result ) { 
				$se_terms_arr[$result->terms][] = array($result->host,$result->searches,$result->datetime,$result->related_posts,$result->referrer);
			}
			$pwp_anchor = 'se';
			$preview_url = $this->pwpGetRandomPreviewURL();
			require('include/stats_summary_tbl_se.php');
		} else {
			echo '<p>No searches saved for this period.</p>';
		}
	}
	
	/**
	 * Deletes all the search staticts from database
	 * @access public 
	 */
	function pwpResetStats() {
		global $wpdb;
		if ( $wpdb->get_var("show tables like '$this->pwp_current_table'") == $this->pwp_current_table ) {
			$wpdb->query("DELETE FROM `{$this->pwp_current_table}`");
		}
		if ( $wpdb->get_var("show tables like '$this->pwp_summary_table'") == $this->pwp_summary_table ) {
			$wpdb->query("DELETE FROM `{$this->pwp_summary_table}`");
		}
		if ( $wpdb->get_var("show tables like '$this->pwp_se_table'") == $this->pwp_se_table ) {
			$wpdb->query("DELETE FROM `{$this->pwp_se_table}`");
		}
	}
	
	/**
	 * Displays the page content for "Psychic Search" Options submenu
	 * Carries out all the operations in Options page.
	 * @access public 
	 */
	function pwpOptionsPg() {
		global $wp_version;
		$ps_msg = '';
		$this->pwp_request = $_REQUEST['pwp'];
	  		
		$form_1 = 'ps_reg_form_1';
		$form_2 = 'ps_reg_form_2';
		// Activate the plugin if email already on list
		if ( trim($_GET['mbp_onlist']) == 1 ) { 
			$this->ps_activate = 2;
			update_option('ps_activate', $this->ps_activate);
			$ps_msg = 'Thank you for registering the plugin. It has been activated'; 
		} 
		// If registration form is successfully submitted
		if ( ((trim($_GET['submit']) != '' && trim($_GET['from']) != '') || trim($_GET['submit_again']) != '') && $this->ps_activate != 2 ) { 
			update_option('ps_name', $_GET['name']);
			update_option('ps_email', $_GET['from']);
			$this->ps_activate = 1;
			update_option('ps_activate', $this->ps_activate);
		}
		if ( intval($this->ps_activate) == 0 ) { // First step of plugin registration
			$this->psRegister_1($form_1);
		} else if ( intval($this->ps_activate) == 1 ) { // Second step of plugin registration
			$name  = get_option('ps_name');
			$email = get_option('ps_email');
			$this->psRegister_2($form_2,$name,$email);
		} else if ( intval($this->ps_activate) == 2 ) { // Options page
			if ( isset($this->pwp_request['save_related_posts_options']) ) {
				check_admin_referer('psychic-search-options');
				$this->pwp_option['disable_related_posts']  = $this->pwp_request['disable_related_posts'];
				$this->pwp_option['noof_related_posts']     = $this->pwp_request['noof_related_posts'];
				$this->pwp_option['related_posts_position'] = $this->pwp_request['related_posts_position'];
				$this->pwp_option['before_post']            = stripslashes($this->pwp_request['before_post']);
				$this->pwp_option['before_title']           = stripslashes($this->pwp_request['before_title']);
				$this->pwp_option['after_title']            = stripslashes($this->pwp_request['after_title']);
				$this->pwp_option['after_post']             = stripslashes($this->pwp_request['after_post']);
				$this->pwp_option['no_related_posts_txt']   = stripslashes($this->pwp_request['no_related_posts_txt']);
				update_option('ps_options', $this->pwp_option);
				$ps_msg = 'Related Posts Options Saved Successfully.';
			} else if ( isset($this->pwp_request['save_more_options']) ) {
				check_admin_referer('psychic-search-options');
				$this->pwp_option['stats_access_level']    = $this->pwp_request['stats_access_level'];
				$this->pwp_option['admin_search']          = $this->pwp_request['admin_search'];
				$this->pwp_option['loggedin_user_search']  = $this->pwp_request['loggedin_user_search'];
				update_option('ps_options', $this->pwp_option);
				$ps_msg = 'More Options Saved Successfully.';
			} else if ( isset($this->pwp_request['pwp_reset']) ) {
				check_admin_referer('psychic-search-options');
				$this->pwpResetStats();
				$ps_msg = 'Search statistics have been reset.';
			} else if ( $_GET['action'] == 'upgrade' ) {
				$this->pwpUpgradePlugin();
				exit;
			}
			if ( $ps_msg != '' ) {
				echo '<div id="message" class="updated fade"><p><strong>'.$ps_msg.'</strong></p></div>';
			}
			if ( $wp_version >= 2.5 ) $pwp_block_style = 'style="background-color:#f1f1f1; padding:0 5px 5px 5px"';
			else $pwp_block_style = '';
			$admin_search_chk = $this->pwp_option['admin_search'] == 1 ? 'checked' : '';
			$loggedin_user_search_chk = $this->pwp_option['loggedin_user_search'] == 1 ? 'checked' : '';
			$disable_related_posts_chk = $this->pwp_option['disable_related_posts'] == 1 ? 'checked' : '';
			$related_posts_position_top_chk = $this->pwp_option['related_posts_position'] =='top' ? 'checked' : '';
			$related_posts_position_bottom_chk = $this->pwp_option['related_posts_position'] == 'bottom' ? 'checked' : '';
			$related_posts_position_manual_chk = $this->pwp_option['related_posts_position'] == 'manual' ? 'checked' : '';
			$disable_related_posts_show = $disable_related_posts_chk == 'checked' ? 'none' : 'block';
	
			require_once('include/options_page.php');
		}
	}
	
	/**
	 * Gets recheck data fro displaying auto upgrade information
	 */
	function pwpRecheckData($data='') {
		if ( $data != '' ) {
			update_option('ps_version_check',$data);
		} else {
			$version_chk = get_option('ps_version_check');
			return $version_chk;
		}
	}
	
	/**
	 * Extracts plugin update data
	 */
	function pwpExtractUpdateData() {
		$arr = array();
		$version_chk_file = "http://www.maxblogpress.com/plugin-updates/psychic-search.php?v=".PWP_VERSION;
		$content = wp_remote_fopen($version_chk_file);
		if ( $content ) {
			$content          = nl2br($content);
			$content_arr      = explode('<br />', $content);
			$latest_version   = trim(trim(strstr($content_arr[0],'~'),'~'));
			$recheck_interval = trim(trim(strstr($content_arr[1],'~'),'~'));
			$download_url     = trim(trim(strstr($content_arr[2],'~'),'~'));
			$msg_plugin_mgmt  = trim(trim(strstr($content_arr[3],'~'),'~'));
			$msg_in_plugin    = trim(trim(strstr($content_arr[4],'~'),'~'));
			$upgrade_url      = $this->pwp_siteurl.'/wp-admin/options-general.php?page='.$this->pwp_path.'&action=upgrade&dnl='.$download_url;
			$arr = array($latest_version, $recheck_interval, $download_url, $msg_plugin_mgmt, $msg_in_plugin, $upgrade_url);
		}
		return $arr;
	}
	
	/**
	 * Interface for upgrading plugin
	 */
	function pwpUpgradePlugin() {
		global $wp_version;
		$plugin = $this->pwp_path;
		echo '<div class="wrap">';
		$this->pwpHeader();
		echo '<h3>Upgrade Plugin &raquo;</h3>';
		if ( $wp_version >= 2.5 ) {
			$res = $this->pwpDoPluginUpgrade($plugin);
		} else {
			echo '&raquo; Wordpress 2.5 or higher required for automatic upgrade.<br><br>';
		}
		if ( $res == false ) echo '&raquo; Plugin couldn\'t be upgraded.<br><br>';
		echo '<br><strong><a href="'.$this->pwp_siteurl.'/wp-admin/plugins.php">Go back to plugins page</a> | <a href="'.$this->pwp_siteurl.'/wp-admin/options-general.php?page='.$this->pwp_path.'">'.PWP_NAME.' home page</a></strong>';
		$this->pwpFooter();
		echo '</div>';
		include('admin-footer.php');
	}
	
	/**
	 * Carries out plugin upgrade
	 */
	function pwpDoPluginUpgrade($plugin) {
		set_time_limit(300);
		global $wp_filesystem;
		$debug = 0;
		$was_activated = is_plugin_active($plugin); // Check current status of the plugin to retain the same after the upgrade

		// Is a filesystem accessor setup?
		if ( ! $wp_filesystem || !is_object($wp_filesystem) ) {
			WP_Filesystem();
		}
		if ( ! is_object($wp_filesystem) ) {
			echo '&raquo; Could not access filesystem.<br /><br />';
			return false;
		}
		if ( $wp_filesystem->errors->get_error_code() ) {
			echo '&raquo; Filesystem error '.$wp_filesystem->errors.'<br /><br />';
			return false;
		}
		
		if ( $debug ) echo '> File System Okay.<br /><br />';
		
		// Get the URL to the zip file
		$package = $_GET['dnl'];
		if ( empty($package) ) {
			echo '&raquo; Upgrade package not available.<br /><br />';
			return false;
		}
		// Download the package
		$file = download_url($package);
		if ( is_wp_error($file) || $file == '' ) {
			echo '&raquo; Download failed. '.$file->get_error_message().'<br /><br />';
			return false;
		}
		$working_dir = $this->pwp_abspath . 'wp-content/upgrade/' . basename($plugin, '.php');
		
		if ( $debug ) echo '> Working Directory = '.$working_dir.'<br /><br />';
		
		// Unzip package to working directory
		$result = $this->pwpUnzipFile($file, $working_dir);
		if ( is_wp_error($result) ) {
			unlink($file);
			$wp_filesystem->delete($working_dir, true);
			echo '&raquo; Couldn\'t unzip package to working directory. Make sure that "/wp-content/upgrade/" folder has write permission (CHMOD 755).<br /><br />';
			return $result;
		}
		
		if ( $debug ) echo '> Unzip package to working directory successful<br /><br />';
		
		// Once extracted, delete the package
		unlink($file);
		if ( is_plugin_active($plugin) ) {
			deactivate_plugins($plugin, true); //Deactivate the plugin silently, Prevent deactivation hooks from running.
		}
		
		// Remove the old version of the plugin
		$plugin_dir = dirname($this->pwp_abspath . PLUGINDIR . "/$plugin");
		$plugin_dir = trailingslashit($plugin_dir);
		// If plugin is in its own directory, recursively delete the directory.
		if ( strpos($plugin, '/') && $plugin_dir != $base . PLUGINDIR . '/' ) {
			$deleted = $wp_filesystem->delete($plugin_dir, true);
		} else {

			$deleted = $wp_filesystem->delete($base . PLUGINDIR . "/$plugin");
		}
		if ( !$deleted ) {
			$wp_filesystem->delete($working_dir, true);
			echo '&raquo; Could not remove the old plugin. Make sure that "/wp-content/plugins/" folder has write permission (CHMOD 755).<br /><br />';
			return false;
		}
		
		if ( $debug ) echo '> Old version of the plugin removed successfully.<br /><br />';

		// Copy new version of plugin into place
		if ( !$this->pwpCopyDir($working_dir, $this->pwp_abspath . PLUGINDIR) ) {
			echo '&raquo; Installation failed. Make sure that "/wp-content/plugins/" folder has write permission (CHMOD 755)<br /><br />';
			return false;
		}
		//Get a list of the directories in the working directory before we delete it, we need to know the new folder for the plugin
		$filelist = array_keys( $wp_filesystem->dirlist($working_dir) );
		// Remove working directory
		$wp_filesystem->delete($working_dir, true);
		// if there is no files in the working dir
		if( empty($filelist) ) {
			echo '&raquo; Installation failed.<br /><br />';
			return false; 
		}
		$folder = $filelist[0];
		$plugin = get_plugins('/' . $folder);      // Pass it with a leading slash, search out the plugins in the folder, 
		$pluginfiles = array_keys($plugin);        // Assume the requested plugin is the first in the list
		$result = $folder . '/' . $pluginfiles[0]; // without a leading slash as WP requires
		
		if ( $debug ) echo '> Copy new version of plugin into place successfully.<br /><br />';
		
		if ( is_wp_error($result) ) {
			echo '&raquo; '.$result.'<br><br>';
			return false;
		} else {
			//Result is the new plugin file relative to PLUGINDIR
			echo '&raquo; Plugin upgraded successfully<br><br>';	
			if( $result && $was_activated ){
				echo '&raquo; Attempting reactivation of the plugin...<br><br>';	
				echo '<iframe style="display:none" src="' . wp_nonce_url('update.php?action=activate-plugin&plugin=' . $result, 'activate-plugin_' . $result) .'"></iframe>';
				sleep(15);
				echo '&raquo; Plugin reactivated successfully.<br><br>';	
			}
			return true;
		}
	}
	
	/**
	 * Copies directory from given source to destinaktion
	 */
	function pwpCopyDir($from, $to) {
		global $wp_filesystem;
		$dirlist = $wp_filesystem->dirlist($from);
		$from = trailingslashit($from);
		$to = trailingslashit($to);
		foreach ( (array) $dirlist as $filename => $fileinfo ) {
			if ( 'f' == $fileinfo['type'] ) {
				if ( ! $wp_filesystem->copy($from . $filename, $to . $filename, true) ) return false;
				$wp_filesystem->chmod($to . $filename, 0644);
			} elseif ( 'd' == $fileinfo['type'] ) {
				if ( !$wp_filesystem->mkdir($to . $filename, 0755) ) return false;
				if ( !$this->pwpCopyDir($from . $filename, $to . $filename) ) return false;
			}
		}
		return true;
	}
	
	/**
	 * Unzips the file to given directory
	 */
	function pwpUnzipFile($file, $to) {
		global $wp_filesystem;
		if ( ! $wp_filesystem || !is_object($wp_filesystem) )
			return new WP_Error('fs_unavailable', __('Could not access filesystem.'));
		$fs =& $wp_filesystem;
		require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
		$archive = new PclZip($file);
		// Is the archive valid?
		if ( false == ($archive_files = $archive->extract(PCLZIP_OPT_EXTRACT_AS_STRING)) )
			return new WP_Error('incompatible_archive', __('Incompatible archive'), $archive->errorInfo(true));
		if ( 0 == count($archive_files) )
			return new WP_Error('empty_archive', __('Empty archive'));
		$to = trailingslashit($to);
		$path = explode('/', $to);
		$tmppath = '';
		for ( $j = 0; $j < count($path) - 1; $j++ ) {
			$tmppath .= $path[$j] . '/';
			if ( ! $fs->is_dir($tmppath) )
				$fs->mkdir($tmppath, 0755);
		}
		foreach ($archive_files as $file) {
			$path = explode('/', $file['filename']);
			$tmppath = '';
			// Loop through each of the items and check that the folder exists.
			for ( $j = 0; $j < count($path) - 1; $j++ ) {
				$tmppath .= $path[$j] . '/';
				if ( ! $fs->is_dir($to . $tmppath) )
					if ( !$fs->mkdir($to . $tmppath, 0755) )
						return new WP_Error('mkdir_failed', __('Could not create directory'));
			}
			// We've made sure the folders are there, so let's extract the file now:
			if ( ! $file['folder'] )
				if ( !$fs->put_contents( $to . $file['filename'], $file['content']) )
					return new WP_Error('copy_failed', __('Could not copy file'));
				$fs->chmod($to . $file['filename'], 0755);
		}
		return true;
	}
	
	/**
	 * Plugin registration form
	 * @access public 
	 */
	function psRegistrationForm($form_name, $submit_btn_txt='Register', $name, $email, $hide=0, $submit_again='') {
		$wp_url = get_bloginfo('wpurl');
		$wp_url = (strpos($wp_url,'http://') === false) ? get_bloginfo('siteurl') : $wp_url;
		$thankyou_url = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'];
		$onlist_url   = $wp_url.'/wp-admin/options-general.php?page='.$_GET['page'].'&amp;mbp_onlist=1';
		if ( $hide == 1 ) $align_tbl = 'left';
		else $align_tbl = 'center';
		?>
		
		<?php if ( $submit_again != 1 ) { ?>
		<script><!--
		function trim(str){
			var n = str;
			while ( n.length>0 && n.charAt(0)==' ' ) 
				n = n.substring(1,n.length);
			while( n.length>0 && n.charAt(n.length-1)==' ' )	
				n = n.substring(0,n.length-1);
			return n;
		}
		function psValidateForm_0() {
			var name = document.<?php echo $form_name;?>.name;
			var email = document.<?php echo $form_name;?>.from;
			var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
			var err = ''
			if ( trim(name.value) == '' )
				err += '- Name Required\n';
			if ( reg.test(email.value) == false )
				err += '- Valid Email Required\n';
			if ( err != '' ) {
				alert(err);
				return false;
			}
			return true;
		}
		//-->
		</script>
		<?php } ?>
		<table align="<?php echo $align_tbl;?>">
		<form name="<?php echo $form_name;?>" method="post" action="http://www.aweber.com/scripts/addlead.pl" <?php if($submit_again!=1){;?>onsubmit="return psValidateForm_0()"<?php }?>>
		 <input type="hidden" name="unit" value="maxbp-activate">
		 <input type="hidden" name="redirect" value="<?php echo $thankyou_url;?>">
		 <input type="hidden" name="meta_redirect_onlist" value="<?php echo $onlist_url;?>">
		 <input type="hidden" name="meta_adtracking" value="ps-w-activate">
		 <input type="hidden" name="meta_message" value="1">
		 <input type="hidden" name="meta_required" value="from,name">
	 	 <input type="hidden" name="meta_forward_vars" value="1">	
		 <?php if ( $submit_again == 1 ) { ?> 	
		 <input type="hidden" name="submit_again" value="1">
		 <?php } ?>		 
		 <?php if ( $hide == 1 ) { ?> 
		 <input type="hidden" name="name" value="<?php echo $name;?>">
		 <input type="hidden" name="from" value="<?php echo $email;?>">
		 <?php } else { ?>
		 <tr><td>Name: </td><td><input type="text" name="name" value="<?php echo $name;?>" size="25" maxlength="150" /></td></tr>
		 <tr><td>Email: </td><td><input type="text" name="from" value="<?php echo $email;?>" size="25" maxlength="150" /></td></tr>
		 <?php } ?>
		 <tr><td>&nbsp;</td><td><input type="submit" name="submit" value="<?php echo $submit_btn_txt;?>" class="button" /></td></tr>
		 </form>
		</table>
		<?php
	}
	
	/**
	 * Register Plugin - Step 2
	 * @access public 
	 */
	function psRegister_2($form_name='frm2',$name,$email) {
		$msg = 'You have not clicked on the confirmation link yet. A confirmation email has been sent to you again. Please check your email and click on the confirmation link to activate the plugin.';
		if ( trim($_GET['submit_again']) != '' && $msg != '' ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$msg.'</strong></p></div>';
		}
		?>
		<div class="wrap"><h2> <?php echo PWP_NAME.' '.PWP_VERSION; ?></h2>
		 <center>
		 <table width="640" cellpadding="5" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
		  <tr><td align="center"><h3>Almost Done....</h3></td></tr>
		  <tr><td><h3>Step 1:</h3></td></tr>
		  <tr><td>A confirmation email has been sent to your email "<?php echo $email;?>". You must click on the link inside the email to activate the plugin.</td></tr>
		  <tr><td><strong>The confirmation email will look like:</strong><br /><img src="http://www.maxblogpress.com/images/activate-plugin-email.jpg" vspace="4" border="0" /></td></tr>
		  <tr><td>&nbsp;</td></tr>
		  <tr><td><h3>Step 2:</h3></td></tr>
		  <tr><td>Click on the button below to Verify and Activate the plugin.</td></tr>
		  <tr><td><?php $this->psRegistrationForm($form_name.'_0','Verify and Activate',$name,$email,$hide=1,$submit_again=1);?></td></tr>
		 </table>
		 <p>&nbsp;</p>
		 <table width="640" cellpadding="5" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
           <tr><td><h3>Troubleshooting</h3></td></tr>
           <tr><td><strong>The confirmation email is not there in my inbox!</strong></td></tr>
           <tr><td>Dont panic! CHECK THE JUNK, spam or bulk folder of your email.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>It's not there in the junk folder either.</strong></td></tr>
           <tr><td>Sometimes the confirmation email takes time to arrive. Please be patient. WAIT FOR 6 HOURS AT MOST. The confirmation email should be there by then.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>6 hours and yet no sign of a confirmation email!</strong></td></tr>
           <tr><td>Please register again from below:</td></tr>
           <tr><td><?php $this->psRegistrationForm($form_name,'Register Again',$name,$email,$hide=0,$submit_again=2);?></td></tr>
           <tr><td><strong>Help! Still no confirmation email and I have already registered twice</strong></td></tr>
           <tr><td>Okay, please register again from the form above using a DIFFERENT EMAIL ADDRESS this time.</td></tr>
           <tr><td>&nbsp;</td></tr>
           <tr>
             <td><strong>Why am I receiving an error similar to the one shown below?</strong><br />
                 <img src="http://www.maxblogpress.com/images/no-verification-error.jpg" border="0" vspace="8" /><br />
               You get that kind of error when you click on &quot;Verify and Activate&quot; button or try to register again.<br />
               <br />
               This error means that you have already subscribed but have not yet clicked on the link inside confirmation email. In order to  avoid any spam complain we don't send repeated confirmation emails. If you have not recieved the confirmation email then you need to wait for 12 hours at least before requesting another confirmation email. </td>
           </tr>
           <tr><td>&nbsp;</td></tr>
           <tr><td><strong>But I've still got problems.</strong></td></tr>
           <tr><td>Stay calm. <strong><a href="http://www.maxblogpress.com/contact-us/" target="_blank">Contact us</a></strong> about it and we will get to you ASAP.</td></tr>
         </table>
		 </center>		
		<?php $this->pwpFooter();?>
	    </div>
		<?php
	}
	
	/**
	 * Register Plugin - Step 1
	 * @access public 
	 */
	function psRegister_1($form_name='frm1') {
		global $userdata;
		$name  = trim($userdata->first_name.' '.$userdata->last_name);
		$email = trim($userdata->user_email);
		?>
		<div class="wrap"><h2> <?php echo PWP_NAME.' '.PWP_VERSION; ?></h2>
		 <center>
		 <table width="620" cellpadding="3" cellspacing="1" bgcolor="#ffffff" style="border:1px solid #e9e9e9">
		  <tr><td align="center"><h3>Please register the plugin to activate it. (Registration is free)</h3></td></tr>
		  <tr><td align="left">In addition you'll receive complimentary subscription to MaxBlogPress Newsletter which will give you many tips and tricks to attract lots of visitors to your blog.</td></tr>
		  <tr><td align="center"><strong>Fill the form below to register the plugin:</strong></td></tr>
		  <tr><td><?php $this->psRegistrationForm($form_name,'Register',$name,$email);?></td></tr>
		  <tr><td align="center"><font size="1">[ Your contact information will be handled with the strictest confidence <br />and will never be sold or shared with third parties ]</font></td></td></tr>
		 </table>
		 </center>
		<?php $this->pwpFooter();?>
	    </div>
		<?php
	}
	
} // Eof Class

$PsychicMBP = new PsychicMBP();
add_action('plugins_loaded', 'pwpWidgetInit');
if ( !is_admin() && $PsychicMBP->pwp_option['related_posts_position'] == 'top' ) {
	add_action('loop_start', 'ps_related_posts');
} else if ( !is_admin() && $PsychicMBP->pwp_option['related_posts_position'] == 'bottom' ) {
	add_action('loop_end', 'ps_related_posts');
}

function ps_related_posts($limit='', $before_title='', $after_title='', $before_post='', $after_post='') {
	global $PsychicMBP;
	$PsychicMBP->pwpRelatedPosts($limit, $before_title, $after_title, $before_post, $after_post);
}

/**
 * Template Tag - List the most recent successful searches.
 * @access public
 */
function pmp_recent_searches($count=5, $before='<li>', $after='</li>', $unique_terms=0) {
	global $wpdb, $PsychicMBP, $pwp_title;
	$count   = intval($count);
	
	if ( $unique_terms == 1 ) {	
		$distinct = "DISTINCT";
	}
	$sql = "SELECT $distinct `terms` FROM `{$PsychicMBP->pwp_current_table}` 
			WHERE 0 < `hits` ORDER BY `datetime` DESC LIMIT $count";
	$results = $wpdb->get_results($sql);
	if ( trim($pwp_title) != '' ) {
		echo "<h2 class='widgettitle'>$pwp_title</h2><ul>";
	} else {
		echo "<ul>";
	}
	if (count($results)) {
		foreach ($results as $result) {
			echo $before."<a href='".$PsychicMBP->pwp_siteurl."/?s=".urlencode($result->terms)."&pwp=1'>".htmlspecialchars($result->terms)."</a>".$after;
		}
	} else {
		echo "No recent searches found";
	}
	echo "</ul><br>";
}

/**
 * Template Tag - List the latest most popular searches.
 * @access public
 */
function pmp_popular_searches($count=5, $days=30, $before='<li>', $after='</li>') {
	global $wpdb, $PsychicMBP, $pwp_title;
	$count   = intval($count);
	$days    = intval($days);
	$results = $wpdb->get_results(
		"SELECT `terms`, SUM(`count`) AS countsum
		FROM `{$PsychicMBP->pwp_summary_table}`
		WHERE DATE_SUB(CURDATE(), INTERVAL $days DAY) <= `date`
		AND 0 < `last_hits`
		GROUP BY `terms`
		ORDER BY countsum DESC, `terms` ASC
		LIMIT $count
		");
	if ( trim($pwp_title) != '' ) {
		echo "<h2 class='widgettitle'>$pwp_title</h2><ul>";
	} else {
		echo "<ul>";
	}
	if (count($results)) {
		foreach ($results as $result) {
			echo $before."<a href='".$PsychicMBP->pwp_siteurl."/?s=".urlencode($result->terms)."&pwp=1'>".htmlspecialchars($result->terms)."</a>".$after;
		}
	} else {
		echo "No popular searches found";
	}
	echo "</ul><br>";
}

/**
 * Widget - Psychic Search Widget for recent searches, popular searches and related posts
 * @access public
 */
function pwpWidgetInit() {
	// Check if required Widget API functions are defined
	if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') ) {
		return; 
	}
	
	function pwpRelatedPostsWidget($args) {
		global $PsychicMBP;
		$title     = htmlspecialchars($PsychicMBP->pwp_option['before_post']);
		$noof_post = $PsychicMBP->pwp_option['noof_related_posts'];
		ps_related_posts();
	}
	
	function pwpRelatedPostsControl() {
		global $PsychicMBP;
		if ( $_POST["pwp_related_posts_submit"] ) {
			$PsychicMBP->pwp_option['before_post']        = stripslashes($_POST["pwp_related_posts_title"]);
			$PsychicMBP->pwp_option['noof_related_posts'] = (int) $_POST["pwp_noof_related_posts"];
			update_option('ps_options', $PsychicMBP->pwp_option);
		}
		$title     = htmlspecialchars($PsychicMBP->pwp_option['before_post']);
		$noof_post = $PsychicMBP->pwp_option['noof_related_posts'];
		?>
		<label for="pwp_rp_head" style="line-height:2px;display:block;">Show the recent successful search keywords:</label>
		<label for="pwp_rp_none1" style="line-height:18px;display:block;">&nbsp;</label>
		<label for="pwp_rp__title" style="line-height:28px;display:block;"><?php _e('Title:'); ?> <input id="pwp_related_posts_title" name="pwp_related_posts_title" type="text" value="<?php echo $title;?>" size="35" /></label>
		<label for="pwp_rp__num" style="line-height:28px;display:block;"><?php _e('Number of Related Posts to Show :'); ?> <input id="pwp_noof_related_posts" name="pwp_noof_related_posts" type="text" value="<?php echo $noof_post;?>" size="4" maxlength="3" /></label>
		<label for="pwp_rp_none2" style="line-height:24px;display:block;">&nbsp;</label>
		<label for="pwp_rp_by" style="line-height:24px;display:block;"><div align="center">Powered by <a href="http://www.maxblogpress.com/" target="_blank">MaxBlogPress</a></div></label>
		<input type="hidden" name="pwp_related_posts_submit" value="1" />
		<?php
	}
	
	function pwpRecentSearchesWidget($args) {
		global $pwp_title;
		$options   = get_option('ps_options'); 
		$pwp_title = $options['recent_searches_title'];
		$number    = pwpWidgetSearchCount((int)$options['recent_searches_num']);
		$unique_terms = $options['unique_terms'];
		pmp_recent_searches($number, '<li>', '</li>', $unique_terms);
	}
	
	function pwpRecentSearchesControl() {
		$options = get_option('ps_options');
		if ( $_POST['recent_searches_submit'] ) {
			$options['recent_searches_title'] = strip_tags(stripslashes($_POST['recent_searches_title']));
			$options['recent_searches_num']   = (int) $_POST['recent_searches_num'];
			$options['unique_terms']          = (int) $_POST['unique_terms'];
			update_option('ps_options', $options);
		}
		$title        = $options['recent_searches_title'];
		$unique_terms = $options['unique_terms'];
		$number       = pwpWidgetSearchCount((int)$options['recent_searches_num']);
		if ( trim($title) == '' ) $title = 'Recent Searches';
		if ( $unique_terms == 1 ) $unique_terms_chk = 'checked';
		?>
		<label for="recent_searches_head" style="line-height:2px;display:block;">Show the recent successful search keywords:</label>
		<label for="none1" style="line-height:18px;display:block;">&nbsp;</label>
		<label for="recent_searches_title" style="line-height:24px;display:block;"><?php _e('Title:'); ?> <input id="recent_searches_title" name="recent_searches_title" type="text" value="<?php echo $title;?>" size="26" /></label>
		<label for="recent_searches_num" style="line-height:24px;display:block;"><?php _e('Number of searches to show:'); ?> <input id="recent_searches_num" name="recent_searches_num" type="text" value="<?php echo $number;?>" size="4" maxlength="3" /></label>
		<label for="pwp_unique_terms" style="line-height:24px;display:block;"><input id="unique_terms" name="unique_terms" type="checkbox" value="1" <?php echo $unique_terms_chk;?> /> <?php _e('Show Unique Keywords Only :'); ?></label>
		<label for="pwp_rp_none3" style="line-height:18px;display:block;">&nbsp;</label>
		<label for="recent_searches_by" style="line-height:24px;display:block;"><div align="center">Powered by <a href="http://www.maxblogpress.com/" target="_blank">MaxBlogPress</a></div></label>
		<input type="hidden" id="recent_searches_submit" name="recent_searches_submit" value="1" />
		<?php
	}
	
	function pwpPopularSearchesWidget($args) {
		global $pwp_title;
		$options   = get_option('ps_options');
		$pwp_title = $options['popular_searches_title'];
		$number    = pwpWidgetSearchCount((int)$options['popular_searches_num']);
		$days      = $options['popular_searches_days'];
		pmp_popular_searches($number, $days, '<li>', '</li>');
	}
	
	function pwpPopularSearchesControl() {
		$options = get_option('ps_options');
		if ($_POST['popular_searches_submit']) {
			$options['popular_searches_title'] = $_POST['popular_searches_title'];
			$options['popular_searches_num']   = (int) $_POST['popular_searches_num'];
			$options['popular_searches_days']  = (int) $_POST['popular_searches_days'];
			update_option('ps_options', $options);
		}
		$title  = $options['popular_searches_title'];
		$number = pwpWidgetSearchCount((int)$options['popular_searches_num']);
		$days   = $options['popular_searches_days'];
		if ( trim($title) == '') $title = 'Popular Searches';
		if ( intval($days) <= 0) $days = 30;
		?>
		<label for="popular_searches_head" style="line-height:2px;display:block;">Show the popular successful search keywords:</label>
		<label for="none3" style="line-height:18px;display:block;">&nbsp;</label>
		<label for="popular_searches_title" style="line-height:24px;display:block;"><?php _e('Title:'); ?> <input id="popular_searches_title" name="popular_searches_title" type="text" value="<?php echo $title;?>" size="28" /></label>
		<label for="popular_searches_num" style="line-height:24px;display:block;"><?php _e('Number of searches to show:'); ?> <input id="popular_searches_num" name="popular_searches_num" type="text" value="<?php echo $number; ?>" size="6" maxlength="3" /></label>
		<label for="popular_searches_days" style="line-height:24px;display:block;"><?php _e('Show popular searches from last:'); ?> <input id="popular_searches_days" name="popular_searches_days" type="text" value="<?php echo $days;?>" size="3" maxlength="3" /> days.</label>
		<label for="none4" style="line-height:18px;display:block;">&nbsp;</label>
		<label for="popular_searches_by" style="line-height:24px;display:block;"><div align="center">Powered by <a href="http://www.maxblogpress.com/" target="_blank">MaxBlogPress</a></div></label>
		<input type="hidden" id="popular_searches_submit" name="popular_searches_submit" value="1" />
		<?php
	}
	
	function pwpWidgetSearchCount($number) {
		if ( !$number ) {
			$number = 5;
		} else if ( $number < 1 ) {
			$number = 1;
		} else if ( $number > 100 ) {
			$number = 100;
		}
		return $number;
	}
	
	register_sidebar_widget('Recent Searches', 'pwpRecentSearchesWidget', 'recent_searches');
	register_sidebar_widget('Popular Searches', 'pwpPopularSearchesWidget', 'popular_searches');
	register_sidebar_widget('Related Posts', 'pwpRelatedPostsWidget', 'related_posts');
	register_widget_control('Recent Searches', 'pwpRecentSearchesControl', '', '145px');
	register_widget_control('Popular Searches', 'pwpPopularSearchesControl', '', '150px');
	register_widget_control('Related Posts', 'pwpRelatedPostsControl', '', '150px');
}
?>