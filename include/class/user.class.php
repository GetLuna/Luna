<?php

class User {
    private $id;
    private $group;
    private $username;
    private $password;
    private $salt;
    private $email;
    private $title;
    private $realname;
    private $url;
    private $facebook;
    private $microsoft;
    private $twitter;
    private $google;
    private $location;
    private $signature;
    private $email_setting;
    private $notify_with_comment;
    private $auto_notify;
    private $show_img;
    private $show_sig;
    private $php_timezone;
    private $time_format;
    private $date_format;
    private $language;
    private $num_comments;
    private $last_comment;
    private $last_search;
    private $last_email_sent;
    private $last_report_sent;
    private $registered;
    private $registration_ip;
    private $last_visit;
    private $admin_note;
    private $activate_string;
    private $activate_key;
    private $use_inbox;
    private $num_inbox;
    private $first_run;
    private $color_scheme;
    private $adapt_time;
    private $accent;

    private $g_id ;
    private $g_title;
    private $g_user_title;
    private $g_moderator;
    private $g_mod_edit_users;
    private $g_mod_rename_users;
    private $g_mod_change_passwords;
    private $g_mod_ban_users;
    private $g_read_board;
    private $g_view_users;
    private $g_comment;
    private $g_create_threads;
    private $g_edit_comments;
    private $g_delete_comments;
    private $g_delete_threads;
    private $g_set_title;
    private $g_search;
    private $g_search_users;
    private $g_send_email;
    private $g_comment_flood;
    private $g_search_flood;
    private $g_email_flood;
    private $g_inbox;
    private $g_inbox_limit;
    private $g_report_flood;
    private $g_soft_delete_view;
    private $g_soft_delete_comments;
    private $g_soft_delete_threads;  

    private $o_logged;
    private $o_idle;

    public function __construct() {}

    public static function withId( $id ) {
        $user = new self();
        $user->getById( $id );
        return $user;
    }

    public static function withRow( array $row ) {
        $user = new self();
        $user->fill( $row );
        return $user;
    }

    protected function getById( $id ) {
        global $db;

        $result = $db->query('SELECT u.*, g.*, o.logged, o.idle FROM '.$db->prefix.'users AS u INNER JOIN '.$db->prefix.'groups AS g ON u.group_id=g.g_id LEFT JOIN '.$db->prefix.'online AS o ON o.user_id=u.id WHERE u.id='.intval( $id)) or error('Unable to fetch user information', __FILE__, __LINE__, $db->error());
        $row = $db->fetch_assoc( $result);

        $user->fill( $row );
    }

    protected function fill( array $row ) {
        $this->setId( $row['id'] );
        $this->setGroup( $row['group'] );
        $this->setUsername( $row['username'] );
        $this->setPassword( $row['password'] );
        $this->setSalt( $row['salt'] );
        $this->setEmail( $row['email'] );
        $this->setTitle( $row['title'] );
        $this->setRealname( $row['realname'] );
        $this->setUrl( $row['url'] );
        $this->setFacebook( $row['facebook'] );    
        $this->setMicrosoft( $row['microsoft'] );    
        $this->setTwitter( $row['twitter'] );    
        $this->setGoogle( $row['google'] );    
        $this->setLocation( $row['location'] );    
        $this->setSignature( $row['signature'] );    
        $this->setEmailSetting( $row['email_setting'] );    
        $this->setNotifyWithComment( $row['notify_with_comment'] );    
        $this->setAutoNotify( $row['auto_notify'] );    
        $this->setShowImg( $row['show_img'] );    
        $this->setShowSig( $row['show_sig'] );    
        $this->setPhpTimezone( $row['php_timezone'] );    
        $this->setTimeFormat( $row['time_format'] );    
        $this->setDateFormat( $row['date_format'] );    
        $this->setLanguage( $row['language'] );    
        $this->setNumComments( $row['num_comments'] );    
        $this->setLastComment( $row['last_comment'] );    
        $this->setLastSearch( $row['last_search'] );
        $this->setLastEmailSent( $row['last_email_sent'] );    
        $this->setLastReportSent( $row['last_report_sent'] );    
        $this->setRegistered( $row['registered'] );    
        $this->setRegistration_ip( $row['registration_ip'] );    
        $this->setLastVisit( $row['last_visit'] );    
        $this->setAdminNote( $row['admin_note'] );    
        $this->setActivateString( $row['activate_string'] );    
        $this->setActivateKey( $row['activate_key'] );    
        $this->setUseInbox( $row['use_inbox'] );    
        $this->setNumInbox( $row['num_inbox'] );    
        $this->setFirstRun( $row['first_run'] );    
        $this->setColorScheme( $row['color_scheme'] );    
        $this->setAdaptTime( $row['adapt_time'] );    
        $this->setAccent( $row['accent'] );    
        $this->setGId( $row['g_id'] );    
        $this->setGTitle( $row['g_title'] );    
        $this->setGUserTitle( $row['g_user_title'] );    
        $this->setGModerator( $row['g_moderator'] );    
        $this->setGModEditUsers( $row['g_mod_edit_users'] );    
        $this->setGModRenameUsers( $row['g_mod_rename_users'] );    
        $this->setGModChangePasswords( $row['g_mod_change_passwords'] );    
        $this->setGModBanUsers( $row['g_mod_ban_users'] );    
        $this->setGReadBoard( $row['g_read_board'] );    
        $this->setGViewUsers( $row['g_view_users'] );    
        $this->setGComment( $row['g_comment'] );    
        $this->setGCreateThreads( $row['g_create_threads'] );    
        $this->setGEditComments( $row['g_edit_comments'] );    
        $this->setGDeleteComments( $row['g_delete_comments'] );    
        $this->setGDeleteThreads( $row['g_delete_threads'] );    
        $this->setGSetTitle( $row['g_set_title'] );    
        $this->setGSearch( $row['g_search'] );    
        $this->setGSearchUsers( $row['g_search_users'] );    
        $this->setGSendEmail( $row['g_send_email'] );    
        $this->setGCommentFlood( $row['g_comment_flood'] );    
        $this->setGSearchFlood( $row['g_search_flood'] );    
        $this->setGEmailFlood( $row['g_email_flood'] );    
        $this->setGInbox( $row['g_inbox'] );    
        $this->setGInboxLimit( $row['g_inbox_limit'] );    
        $this->setGReportFlood( $row['g_report_flood'] );    
        $this->setGSoftDeleteView( $row['g_soft_delete_view'] );    
        $this->setGSoftDeleteComments( $row['g_soft_delete_comments'] );    
        $this->setGSoftDeleteThreads( $row['g_soft_delete_threads'] );    
        $this->setOLogged( $row['o_logged'] );    
        $this->setOIdle( $row['o_idle'] );
    }

    // Setters
    public function getId() {
        return $this->id;
    }

    public function getGroup() {
        return $this->group;
    }

    public function getUsername() {
        return $this->username;
    }

    public function getPassword() {
        return $this->password;
    }

    public function getSalt() {
        return $this->salt;
    }

    public function getEmail() {
        return $this->email;
    }

    public function getTitle() {
        return $this->title;
    }

    public function getRealname() {
        return $this->realname;
    }

    public function getUrl() {
        return $this->url;
    }

    public function getFacebook() {
        return $this->facebook;
    }

    public function getMicrosoft() {
        return $this->microsoft;
    }

    public function getTwitter() {
        return $this->twitter;
    }

    public function getGoogle() {
        return $this->google;
    }

    public function getLocation() {
        return $this->location;
    }

    public function getSignature() {
        return $this->signature;
    }

    public function getEmailSetting() {
        return $this->email_setting;
    }

    public function getNotifyWithComment() {
        return $this->notify_with_comment;
    }

    public function getAutoNotify() {
        return $this->auto_notify;
    }

    public function getShowImg() {
        return $this->show_img;
    }

    public function getShowSig() {
        return $this->show_sig;
    }

    public function getPhpTimezone() {
        return $this->php_timezone;
    }

    public function getTimeFormat() {
        return $this->time_format;
    }

    public function getDateFormat() {
        return $this->date_format;
    }

    public function getLanguage() {
        return $this->language;
    }

    public function getNumComments() {
        return $this->num_comments;
    }

    public function getLastComment() {
        return $this->last_comment;
    }

    public function getLastSearch() {
        return $this->last_search;
    }

    public function getLastEmailSent() {
        return $this->last_email_sent;
    }

    public function getLastReportSent() {
        return $this->last_report_sent;
    }

    public function getRegistered() {
        return $this->registered;
    }

    public function getRegistration_ip() {
        return $this->registration_ip;
    }

    public function getLastVisit() {
        return $this->last_visit;
    }

    public function getAdminNote() {
        return $this->admin_note;
    }

    public function getActivateString() {
        return $this->activate_string;
    }

    public function getActivateKey() {
        return $this->activate_key;
    }

    public function getUseInbox() {
        return $this->use_inbox;
    }

    public function getNumInbox() {
        return $this->num_inbox;
    }

    public function getFirstRun() {
        return $this->first_run;
    }

    public function getColorScheme() {
        return $this->color_scheme;
    }

    public function getAdaptTime() {
        return $this->adapt_time;
    }

    public function getAccent() {
        return $this->accent;
    }

    public function getGId() {
        return $this->g_id;
    }

    public function getGTitle() {
        return $this->g_title;
    }

    public function getGUserTitle() {
        return $this->g_user_title;
    }

    public function getGModerator() {
        return $this->g_moderator;
    }

    public function getGModEditUsers() {
        return $this->g_mod_edit_users;
    }

    public function getGModRenameUsers() {
        return $this->g_mod_rename_users;
    }

    public function getGModChangePasswords() {
        return $this->g_mod_change_passwords;
    }

    public function getGModBanUsers() {
        return $this->g_mod_ban_users;
    }

    public function getGReadBoard() {
        return $this->g_read_board;
    }

    public function getGViewUsers() {
        return $this->g_view_users;
    }

    public function getGComment() {
        return $this->g_comment;
    }
    
    public function getGCreateThreads() {
        return $this->g_create_threads;
    }

    public function getGEditComments() {
        return $this->g_edit_comments;
    }

    public function getGDeleteComments() {
        return $this->g_delete_comments;
    }

    public function getGDeleteThreads() {
        return $this->g_delete_threads;
    }

    public function getGSetTitle() {
        return $this->g_set_title;
    }

    public function getGSearch() {
        return $this->g_search;
    }

    public function getGSearchUsers() {
        return $this->g_search_users;
    }

    public function getGSendEmail() {
        return $this->g_send_email;
    }

    public function getGCommentFlood() {
        return $this->g_comment_flood;
    }

    public function getGSearchFlood() {
        return $this->g_search_flood;
    }

    public function getGEmailFlood() {
        return $this->g_email_flood;
    }

    public function getGInbox() {
        return $this->g_inbox;
    }

    public function getGInboxLimit() {
        return $this->g_inbox_limit;
    }

    public function getGReportFlood() {
        return $this->g_report_flood;
    }

    public function getGSoftDeleteView() {
        return $this->g_soft_delete_view;
    }

    public function getGSoftDeleteComments() {
        return $this->g_soft_delete_comments;
    }

    public function getGSoftDeleteThreads() {
        return $this->g_soft_delete_threads;
    }

    public function getOLogged() {
        return $this->o_logged;
    }

    public function getOIdle() {
        return $this->o_idle;
    }

    // Getters
    public function setId( $id ) {
        $this->id = $id;
        return $this;
    }

    public function setGroup( $group ) {
        $this->group = $group;
        return $this;
    }

    public function setUsername( $username ) {
        $this->username = $username;
        return $this;
    }

    public function setPassword( $password ) {
        $this->password = $password;
        return $this;
    }

    public function setSalt( $salt ) {
        $this->salt = $salt;
        return $this;
    }

    public function setEmail( $email ) {
        $this->email = $email;
        return $this;
    }

    public function setTitle( $title ) {
        $this->title = $title;
        return $this;
    }

    public function setRealname( $realname ) {
        $this->realname = $realname;
        return $this;
    }

    public function setUrl( $url ) {
        $this->url = $url;
        return $this;
    }

    public function setFacebook( $facebook ) {
        $this->facebook = $facebook;
        return $this;
    }

    public function setMicrosoft( $microsoft ) {
        $this->microsoft = $microsoft;
        return $this;
    }

    public function setTwitter( $twitter ) {
        $this->twitter = $twitter;
        return $this;
    }

    public function setGoogle( $google ) {
        $this->google = $google;
        return $this;
    }

    public function setLocation( $location ) {
        $this->location = $location;
        return $this;
    }

    public function setSignature( $signature ) {
        $this->signature = $signature;
        return $this;
    }

    public function setEmailSetting( $email_setting ) {
        $this->email_setting = $email_setting;
        return $this;
    }

    public function setNotifyWithComment( $notify_with_comment ) {
        $this->notify_with_comment = $notify_with_comment;
        return $this;
    }

    public function setAutoNotify( $auto_notify ) {
        $this->auto_notify = $auto_notify;
        return $this;
    }

    public function setShowImg( $show_img ) {
        $this->show_img = $show_img;
        return $this;
    }

    public function setShowSig( $show_sig ) {
        $this->show_sig = $show_sig;
        return $this;
    }

    public function setPhpTimezone( $php_timezone ) {
        $this->php_timezone = $php_timezone;
        return $this;
    }

    public function setTimeFormat( $time_format ) {
        $this->time_format = $time_format;
        return $this;
    }

    public function setDateFormat( $date_format ) {
        $this->date_format = $date_format;
        return $this;
    }

    public function setLanguage( $language ) {
        $this->language = $language;
        return $this;
    }

    public function setNumComments( $num_comments ) {
        $this->num_comments = $num_comments;
        return $this;
    }

    public function setLastComment( $last_comment ) {
        $this->last_comment = $last_comment;
        return $this;
    }

    public function setLastSearch( $last_search ) {
        $this->last_search = $last_search;
        return $this;
    }
    public function setLastEmailSent( $last_email_sent ) {
        $this->last_email_sent = $last_email_sent;
        return $this;
    }

    public function setLastReportSent( $last_report_sent ) {
        $this->last_report_sent = $last_report_sent;
        return $this;
    }

    public function setRegistered( $registered ) {
        $this->registered = $registered;
        return $this;
    }

    public function setRegistration_ip( $registration_ip ) {
        $this->registration_ip = $registration_ip;
        return $this;
    }

    public function setLastVisit( $last_visit ) {
        $this->last_visit = $last_visit;
        return $this;
    }

    public function setAdminNote( $admin_note ) {
        $this->admin_note = $admin_note;
        return $this;
    }

    public function setActivateString( $activate_string ) {
        $this->activate_string = $activate_string;
        return $this;
    }

    public function setActivateKey( $activate_key ) {
        $this->activate_key = $activate_key;
        return $this;
    }

    public function setUseInbox( $use_inbox ) {
        $this->use_inbox = $use_inbox;
        return $this;
    }

    public function setNumInbox( $num_inbox ) {
        $this->num_inbox = $num_inbox;
        return $this;
    }

    public function setFirstRun( $first_run ) {
        $this->first_run = $first_run;
        return $this;
    }

    public function setColorScheme( $color_scheme ) {
        $this->color_scheme = $color_scheme;
        return $this;
    }

    public function setAdaptTime( $adapt_time ) {
        $this->adapt_time = $adapt_time;
        return $this;
    }

    public function setAccent( $accent ) {
        $this->accent = $accent;
        return $this;
    }

    public function setGId( $g_id ) {
        $this->g_id = $g_id;
        return $this;
    }

    public function setGTitle( $g_title ) {
        $this->g_title = $g_title;
        return $this;
    }

    public function setGUserTitle( $g_user_title ) {
        $this->g_user_title = $g_user_title;
        return $this;
    }

    public function setGModerator( $g_moderator ) {
        $this->g_moderator = $g_moderator;
        return $this;
    }

    public function setGModEditUsers( $g_mod_edit_users ) {
        $this->g_mod_edit_users = $g_mod_edit_users;
        return $this;
    }

    public function setGModRenameUsers( $g_mod_rename_users ) {
        $this->g_mod_rename_users = $g_mod_rename_users;
        return $this;
    }

    public function setGModChangePasswords( $g_mod_change_passwords ) {
        $this->g_mod_change_passwords = $g_mod_change_passwords;
        return $this;
    }

    public function setGModBanUsers( $g_mod_ban_users ) {
        $this->g_mod_ban_users = $g_mod_ban_users;
        return $this;
    }

    public function setGReadBoard( $g_read_board ) {
        $this->g_read_board = $g_read_board;
        return $this;
    }

    public function setGViewUsers( $g_view_users ) {
        $this->g_view_users = $g_view_users;
        return $this;
    }

    public function setGComment( $g_comment ) {
        $this->g_comment = $g_comment;
        return $this;
    }

    public function setGCreateThreads( $g_create_threads ) {
        $this->g_create_threads = $g_create_threads;
        return $this;
    }

    public function setGEditComments( $g_edit_comments ) {
        $this->g_edit_comments = $g_edit_comments;
        return $this;
    }

    public function setGDeleteComments( $g_delete_comments ) {
        $this->g_delete_comments = $g_delete_comments;
        return $this;
    }

    public function setGDeleteThreads( $g_delete_threads ) {
        $this->g_delete_threads = $g_delete_threads;
        return $this;
    }

    public function setGSetTitle( $g_set_title ) {
        $this->g_set_title = $g_set_title;
        return $this;
    }

    public function setGSearch( $g_search ) {
        $this->g_search = $g_search;
        return $this;
    }

    public function setGSearchUsers( $g_search_users ) {
        $this->g_search_users = $g_search_users;
        return $this;
    }

    public function setGSendEmail( $g_send_email ) {
        $this->g_send_email = $g_send_email;
        return $this;
    }

    public function setGCommentFlood( $g_comment_flood ) {
        $this->g_comment_flood = $g_comment_flood;
        return $this;
    }

    public function setGSearchFlood( $g_search_flood ) {
        $this->g_search_flood = $g_search_flood;
        return $this;
    }

    public function setGEmailFlood( $g_email_flood ) {
        $this->g_email_flood = $g_email_flood;
        return $this;
    }

    public function setGInbox( $g_inbox ) {
        $this->g_inbox = $g_inbox;
        return $this;
    }

    public function setGInboxLimit( $g_inbox_limit ) {
        $this->g_inbox_limit = $g_inbox_limit;
        return $this;
    }

    public function setGReportFlood( $g_report_flood ) {
        $this->g_report_flood = $g_report_flood;
        return $this;
    }

    public function setGSoftDeleteView( $g_soft_delete_view ) {
        $this->g_soft_delete_view = $g_soft_delete_view;
        return $this;
    }

    public function setGSoftDeleteComments( $g_soft_delete_comments ) {
        $this->g_soft_delete_comments = $g_soft_delete_comments;
        return $this;
    }

    public function setGSoftDeleteThreads( $g_soft_delete_threads ) {
        $this->g_soft_delete_threads = $g_soft_delete_threads;
        return $this;
    }

    public function setOLogged( $o_logged ) {
        $this->o_logged = $o_logged;
        return $this;
    }

    public function setOIdle( $o_idle ) {
        $this->o_idle = $o_idle;
        return $this;
    }
}