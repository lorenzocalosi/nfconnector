<?php
/**
 * @package NFConnector
 * @version 1.0
 */
/*
Plugin Name: NFConnector
Plugin URI: https://github.com/lorenzocalosi/nfconnector
Description: A simple WordPress Plugin to connect with Telegram Bot API.
Author: Lorenzo Calosi
Version: 1.0
Author URI: http://nfcblog.herokuapp.com/
*/

function telegram_post_published_notification( $ID, $post ) 
{
	if(get_option('NFConTG'))
	{
		$TelegramSettings = get_option('NFConTG'); 
		if($TelegramSettings['NFConTGON'])
		{
			$TelegramApiKey = $TelegramSettings['NFConTGAPIK'];
			$TelegramMethod = 'sendMessage';
			$TelegramChannelName = $TelegramSettings['NFConTGCN'];
			$postTitle = $post->post_title;
			$postURL = get_permalink($ID);
			$TelegramMessage = 'A new post has been published on my blog: '.$postTitle.'. Check it out here: '.$postURL;
			$TelegramParams = Array('chat_id' => '@'.$TelegramChannelName, 'text' => $TelegramMessage, 'parse_mode' => 'HTML');
			$TelegramURL = 'https://api.telegram.org/bot'.$TelegramApiKey.'/'.$TelegramMethod;

			$result = wp_remote_post( $TelegramURL, array( 'body' => $TelegramParams ) );
		}
	}
}

function nfconnecto_plugin_menu() {
	add_options_page( 'NFConnector Settings', 'NFConnector', 'edit_plugins', 'nfconnector-settings', 'nfconnector_settings');
}

function nfconnector_settings() {
	if ( !current_user_can( 'edit_plugins' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}
	if(isset($_REQUEST['NFConTGON'])||isset($_REQUEST['NFConTGAPIK'])||isset($_REQUEST['NFConTGCN']))
	{
		$TelegramSettings = Array('NFConTGON' => $_REQUEST['NFConTGON'], 'NFConTGAPIK' => $_REQUEST['NFConTGAPIK'], 'NFConTGCN' => $_REQUEST['NFConTGCN']);
		if(get_option('NFConTG'))
		{
			update_option('NFConTG',$TelegramSettings);
			echo '<p>Informations updated successfully</p>';
		}
		else
		{
			add_option('NFConTG',$TelegramSettings);
			echo '<p>Settings Created</p>';
		}
	}
	if(get_option('NFConTG'))
	{
		$TelegramSettings = get_option('NFConTG'); 
	}
	echo '<form action="http://nfcblog.herokuapp.com/wp-admin/options-general.php?page=nfconnector-settings" method="POST">';
	echo '<div>';
	echo '<label>Telegram Connection Active</label>';
	echo '<input type="checkbox" name="NFConTGON"';
	if(isset($TelegramSettings['NFConTGON']))
	{
		echo ' checked';
	}
	echo '/>';
	echo '</div>';
	echo '<div>';
	echo '<label>Telegram API Key</label>';
	echo '<input type="text" name="NFConTGAPIK" value="'.$TelegramSettings['NFConTGAPIK'].'"/>';
	echo '</div>';
	echo '<div>';
	echo '<label>Telegram Channel Name</label>';
	echo '<input type="text" name="NFConTGCN" value="'.$TelegramSettings['NFConTGCN'].'"/>';
	echo '</div>';
	echo '<input type="submit"/>';
	echo '</form>';
}

add_action( 'publish_post', 'telegram_post_published_notification', 10, 2);

add_action( 'admin_menu', 'nfconnector_plugin_menu' );

?>