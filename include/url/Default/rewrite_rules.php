<?php
/*
 * Copyright (C) 2013-2015 Luna
 * Based on code by FluxBB copyright (C) 2008-2012 FluxBB
 * Based on code by Rickard Andersson copyright (C) 2002-2008 PunBB
 * Licensed under GPLv3 (http://getluna.org/license.php)
 */

$forum_rewrite_rules = array(
	'/^topic[\/_-]?([0-9]+).*(new|last)[\/_-]?(posts?)(\.html?|\/)?$/i'														=>	'viewtopic.php?id=$1&action=$2',
	'/^post[\/_-]?([0-9]+)(\.html?|\/)?$/i'																					=>	'viewtopic.php?pid=$1',
	'/^(forum|topic)[\/_-]?([0-9]+).*[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'											=>	'view$1.php?id=$2&p=$4',
	'/^feed[\/_-]?(rss|atom)[\/_-]?(f|t)(orum|opic)[\/_-]?([0-9]+)[\/_-]?(\.xml?|\/)?$/i'									=>	'extern.php?action=feed&$2id=$4&type=$1',
	'/^(forum|topic)[\/_-]?([0-9]+).*(\.html?|\/)?$/i'																		=>	'view$1.php?id=$2',
	'/^new[\/_-]?reply[\/_-]?([0-9]+)(\.html?|\/)?$/i'																		=>	'post.php?tid=$1',
	'/^new[\/_-]?reply[\/_-]?([0-9]+)[\/_-]?quote[\/_-]?([0-9]+)(\.html?|\/)?$/i'											=>	'post.php?tid=$1&qid=$2',
	'/^new[\/_-]?topic[\/_-]?([0-9]+)(\.html?|\/)?$/i'																		=>	'post.php?fid=$1',
	'/^(delete|edit)[\/_-]?([0-9]+)(\.html?|\/)?$/i'																		=>	'$1.php?id=$2',
	'/^(login|search|register|help)(\.html?|\/)?$/i'																		=>	'$1.php',
	'/^logout[\/_-]?([0-9]+)[\/_-]([a-z0-9]+)(\.html?|\/)?$/i'																=>	'login.php?action=out&id=$1&csrf_token=$2',
	'/^request[\/_-]?password(\.html?|\/)?$/i'																				=>	'login.php?action=forget',
	'/^user[\/_-]?([0-9]+)(\.html?|\/)?$/i'																					=>	'profile.php?id=$1',
	'/^user[\/_-]?([0-9]+)[\/_-]?([a-z]+)(\.html?|\/)?$/i'																	=>	'profile.php?section=$2&id=$1',
	'/^(delete|upload)[\/_-]?(avatar)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'														=>	'profile.php?action=$1_$2&id=$3',
	'/^change[\/_-]?(email|pass)(word)?[\/_-]?([0-9]+)[\/_-]([a-zA-Z0-9]+)(\.html?|\/)?$/i'									=>	'profile.php?action=change_$1&id=$3&key=$4',
	'/^change[\/_-]?(email|pass)(word)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'														=>	'profile.php?action=change_$1&id=$3',
	'/^search[\/_-]?(new)[\/_-]([0-9-]+)(\.html?|\/)?$/i'																	=>	'search.php?action=show_new&fid=$2',
	'/^search[\/_-]?(new)[\/_-]([0-9-]+)[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'										=>	'search.php?action=show_new&fid=$2&p=$4',
	'/^search[\/_-]?(recent)[\/_-]([0-9]+)(\.html?|\/)?$/i'																	=>	'search.php?action=show_recent&value=$2',
	'/^search[\/_-]?(recent)[\/_-]([0-9]+)[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'										=>	'search.php?action=show_recent&value=$2&p=$4',
	'/^search[\/_-]?(new|recent|replies|unanswered)(\.html?|\/)?$/i'														=>	'search.php?action=show_$1',
	'/^search[\/_-]?(new|recent|replies|unanswered)[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'							=>	'search.php?action=show_$1&p=$3',
	'/^search[\/_-]?subscriptions[\/_-]?([0-9]+)(\.html?|\/)?$/i'															=>	'search.php?action=show_subscriptions&user_id=$1',
	'/^search[\/_-]?subscriptions[\/_-]?([0-9]+)[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'								=>	'search.php?action=show_subscriptions&user_id=$1&p=$3',
	'/^search[\/_-]?([0-9]+)(\.html?|\/)?$/i'																				=>	'search.php?search_id=$1',
	'/^search[\/_-]?([0-9]+)[\/_-]?p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'													=>	'search.php?search_id=$1&p=$3',
	'/^search[\/_-]?user[\/_-]?posts[\/_-]?([0-9]+)(\.html?|\/)?$/i'														=>	'search.php?action=show_user_posts&user_id=$1',
	'/^search[\/_-]?user[\/_-]?posts[\/_-]?([0-9]+)[\/_-]?p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'							=>	'search.php?action=show_user_posts&user_id=$1&p=$3',
	'/^search[\/_-]?user[\/_-]?topics[\/_-]?([0-9]+)(\.html?|\/)?$/i'														=>	'search.php?action=show_user_topics&user_id=$1',
	'/^search[\/_-]?user[\/_-]?topics[\/_-]?([0-9]+)[\/_-]?p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'							=>	'search.php?action=show_user_topics&user_id=$1&p=$3',	'/^users(\.html?|\/)?$/i'																								=>	'userlist.php',
	'/^users\/(.*)\/([0-9-]+)\/?([a-z_]+)[\/_-]([a-zA-Z]+)[\/_-]p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'						=>	'userlist.php?username=$1&show_group=$2&sort_by=$3&sort_dir=$4&p=$6',
	'/^users\/(.*)\/([0-9-]+)\/?([a-z_]+)[\/_-]([a-zA-Z]+)(\.html?|\/)?$/i'													=>	'userlist.php?username=$1&show_group=$2&sort_by=$3&sort_dir=$4',
	'/^(email|report)[\/_-]?([0-9]+)(\.html?|\/)?$/i'																		=>	'misc.php?$1=$2',
	'/^(subscribe|unsubscribe)[\/_-]?topic[\/_-]?([0-9]+)(\.html?|\/)?$/i'													=>	'misc.php?action=$1&tid=$2',
	'/^(subscribe|unsubscribe)[\/_-]?forum[\/_-]?([0-9]+)(\.html?|\/)?$/i'													=>	'misc.php?action=$1&fid=$2',
	'/^(mark|rules)[\/_-]?(read)?(\.html?|\/)?$/i'																			=>	'misc.php?action=$1$2',
	'/^mark[\/_-](forum)[\/_-]?([0-9]+)[\/_-](read)(\.html?|\/)?$/i'														=>	'misc.php?action=markforumread&fid=$2',
	'/^moderate[\/_-]?([0-9]+)(\.html?|\/)?$/i'																				=>	'moderate.php?fid=$1',
	'/^move_topics[\/_-]?([0-9]+)[\/_-]([0-9]+)(\.html?|\/)?$/i'															=>	'moderate.php?fid=$1&move_topics=$2',
	'/^(open|close|stick|unstick)[\/_-]?([0-9]+)[\/_-]([0-9]+)(\.html?|\/)?$/i'												=>	'moderate.php?fid=$2&$1=$3',
	'/^moderate[\/_-]?([0-9]+)[\/_-]?p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'												=>	'moderate.php?fid=$1&p=$3',
	'/^moderate[\/_-]?([0-9]+)[\/_-]([0-9]+)(\.html?|\/)?$/i'																=>	'moderate.php?fid=$1&tid=$2',
	'/^moderate[\/_-]?([0-9]+)[\/_-]([0-9]+)[\/_-]?p(age)?[\/_-]?([0-9]+)(\.html?|\/)?$/i'									=>	'moderate.php?fid=$1&tid=$2&p=$4',
	'/^get_host[\/_-]?([0-9]+|[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})(\.html?|\/)?$/i'								=>	'moderate.php?get_host=$1',
	'/^feed[\/_-]?(rss|atom)(\.xml?|\/)?$/i'																				=>	'extern.php?action=feed&type=$1'
);