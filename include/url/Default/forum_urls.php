<?php

/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

// Make sure no one attempts to run this script "directly"
if (!defined('FORUM'))
	exit;

// These are the regular, "non-SEF" URLs (you probably don't want to edit these)
$forum_url = array(
	'change_email'					=>	'profile.php?action=change_email&amp;id=$1',
	'change_email_key'				=>	'profile.php?action=change_email&amp;id=$1&amp;key=$2',
	'change_password'				=>	'profile.php?action=change_pass&amp;id=$1',
	'change_password_key'			=>	'profile.php?action=change_pass&amp;id=$1&amp;key=$2',
	'delete'						=>	'delete.php?id=$1',
	'delete_avatar'					=>	'profile.php?action=delete_avatar&amp;id=$1',
	'edit'							=>	'edit.php?id=$1',
	'email'							=>	'misc.php?email=$1',
	'forum'							=>	'viewforum.php?id=$1',
	'forum_rss'						=>	'extern.php?action=feed&amp;fid=$1&amp;type=rss',
	'forum_atom'					=>	'extern.php?action=feed&amp;fid=$1&amp;type=atom',
	'help'							=>	'help.php?section=$1',
	'index'							=>	'index.php',
	'index_rss'						=>	'extern.php?action=feed&amp;type=rss',
	'index_atom'					=>	'extern.php?action=feed&amp;type=atom',
	'login'							=>	'login.php',
	'logout'						=>	'login.php?action=out&amp;id=$1&amp;csrf_token=$2',
	'mark_read'						=>	'misc.php?action=markread',
	'mark_forum_read'				=>	'misc.php?action=markforumread&amp;fid=$1',
	'new_topic'						=>	'post.php?fid=$1',
	'new_reply'						=>	'post.php?tid=$1',
	'post'							=>	'viewtopic.php?pid=$1#p$1',
	'profile_essentials'			=>	'profile.php?section=essentials&amp;id=$1',
	'profile_personal'				=>	'profile.php?section=personal&amp;id=$1',
	'profile_messaging'				=>	'profile.php?section=messaging&amp;id=$1',
	'profile_personality'			=>	'profile.php?section=personality&amp;id=$1',
	'profile_display'				=>	'profile.php?section=display&amp;id=$1',
	'profile_privacy'				=>	'profile.php?section=privacy&amp;id=$1',
	'profile_admin'					=>	'profile.php?section=admin&amp;id=$1',
	'quote'							=>	'post.php?tid=$1&amp;qid=$2',
	'register'						=>	'register.php',
	'report'						=>	'misc.php?report=$1',
	'request_password'				=>	'login.php?action=forget',
	'rules'							=>	'misc.php?action=rules',
	'search'						=>	'search.php',
	'search_results'				=>	'search.php?search_id=$1',
	'search_new'					=>	'search.php?action=show_new',
	'search_new_forum'				=>	'search.php?action=show_new&fid=$1',
	'search_recent'					=>	'search.php?action=show_recent',
	'search_replies'				=>	'search.php?action=show_replies',
	'search_user_posts'				=>	'search.php?action=show_user_posts&amp;user_id=$1',
	'search_user_topics'			=>	'search.php?action=show_user_topics&amp;user_id=$1',
	'search_unanswered'				=>	'search.php?action=show_unanswered',
	'search_subscriptions'			=>	'search.php?action=show_subscriptions&amp;user_id=$1',
	'subscribe_topic'				=>	'misc.php?action=subscribe&tid=$1',
	'subscribe_forum'				=>	'misc.php?action=subscribe&fid=$1',
	'topic'							=>	'viewtopic.php?id=$1',
	'topic_rss'						=>	'extern.php?action=feed&amp;tid=$1&amp;type=rss',
	'topic_atom'					=>	'extern.php?action=feed&amp;tid=$1&amp;type=atom',
	'topic_new_posts'				=>	'viewtopic.php?id=$1&amp;action=new',
	'topic_last_post'				=>	'viewtopic.php?id=$1&amp;action=last',
	'unsubscribe_topic'				=>	'misc.php?action=unsubscribe&tid=$1',
	'unsubscribe_forum'				=>	'misc.php?action=unsubscribe&fid=$1',
	'user'							=>	'profile.php?id=$1',
	'users'							=>	'userlist.php',
	'users_browse'					=>	'userlist.php?username=$1&amp;show_group=$2&amp;sort_by=$3&amp;sort_dir=$4',
	'upload_avatar'					=>	'profile.php?action=upload_avatar&id=$1',
	'page'							=>	'&amp;p=$1',
	'moderate_forum'				=>	'moderate.php?fid=$1',
	'get_host'						=>	'moderate.php?get_host=$1',
	'move'							=>	'moderate.php?fid=$1&amp;move_topics=$2',
	'open'							=>	'moderate.php?fid=$1&amp;open=$2',
	'close'							=>	'moderate.php?fid=$1&amp;close=$2',
	'stick'							=>	'moderate.php?fid=$1&amp;stick=$2',
	'unstick'						=>	'moderate.php?fid=$1&amp;unstick=$2',
	'moderate_topic'				=>	'moderate.php?fid=$1&amp;tid=$2',
);