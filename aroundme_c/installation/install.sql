
-- 
-- Table structure for table 'am_applicant'
-- 

CREATE TABLE IF NOT EXISTS am_applicant (
  applicant_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  applicant_openid varchar(200) NOT NULL,
  applicant_nickname varchar(255) default NULL,
  applicant_email varchar(255) default NULL,
  applicant_note text,
  PRIMARY KEY  (applicant_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_block'
--

CREATE TABLE IF NOT EXISTS am_block (
  block_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  block_plugin varchar(100) NOT NULL,
  block_name varchar(100) NOT NULL,
  block_body text,
  PRIMARY KEY  (block_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_connection'
--

CREATE TABLE IF NOT EXISTS am_connection (
  connection_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  connection_openid varchar(200) NOT NULL,
  connection_nickname varchar(255) default NULL,
  connection_email varchar(255) default NULL,
  connection_fullname varchar(255) default NULL,
  connection_country varchar(3) default NULL,
  connection_language varchar(3) default NULL,
  connection_avatar varchar(255) default NULL,
  connection_create_datetime datetime NOT NULL,
  connection_last_datetime datetime default NULL,
  connection_total int(11) NOT NULL,
  status_id int(1) NOT NULL,
  connection_permission int(11) default NULL,
  PRIMARY KEY  (connection_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_file'
--

CREATE TABLE IF NOT EXISTS am_file (
  file_id int(11) NOT NULL auto_increment,
  file_type varchar(20) default NULL,
  file_size int(11) default NULL,
  file_name varchar(255) default NULL,
  webspace_id int(11) default NULL,
  connection_id int(11) default NULL,
  file_create_datetime datetime default NULL,
  file_title varchar(255) NOT NULL,
  PRIMARY KEY  (file_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_log'
--

CREATE TABLE IF NOT EXISTS am_log (
  log_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  log_title varchar(50) NOT NULL,
  log_body text,
  log_link varchar(100) default NULL,
  log_create_datetime datetime default NULL,
  PRIMARY KEY  (log_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_permission'
--

CREATE TABLE IF NOT EXISTS am_permission (
  webspace_id int(11) NOT NULL,
  plugin_name varchar(50) NOT NULL,
  resource_name varchar(50) NOT NULL,
  bitwise_operator int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_blog_comment'
--

CREATE TABLE IF NOT EXISTS am_plugin_blog_comment (
  comment_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  blog_id int(11) NOT NULL,
  connection_id int(11) default NULL,
  comment_body text,
  comment_create_datetime datetime default NULL,
  comment_hidden int(11) default NULL,
  PRIMARY KEY  (comment_id),
  FULLTEXT KEY comment_body_fulltext (comment_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_blog_entry'
--

CREATE TABLE IF NOT EXISTS am_plugin_blog_entry (
  blog_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  blog_title varchar(255) default NULL,
  connection_id int(11) default NULL,
  blog_body text,
  blog_create_datetime datetime default NULL,
  blog_edit_datetime datetime default NULL,
  blog_archived int(1) default NULL,
  blog_allow_comment int(1) default NULL,
  PRIMARY KEY  (blog_id),
  FULLTEXT KEY blog_body_fulltext (blog_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_blog_preference'
--

CREATE TABLE IF NOT EXISTS am_plugin_blog_preference (
  preference_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  default_webpage_id int(11) default NULL,
  rss_title varchar(255) default NULL,
  rss_title_comment varchar(255) default NULL,
  rss_description varchar(255) default NULL,
  PRIMARY KEY  (preference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `am_plugin_contact_recipient`
-- 

CREATE TABLE `am_plugin_contact_recipient` (
  `webspace_id` int(11) NOT NULL,
  `recipient_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `am_plugin_forum_digest`
-- 

CREATE TABLE `am_plugin_forum_digest` (
  `connection_id` int(11) NOT NULL,
  `webspace_id` int(11) NOT NULL,
  `digest_frequency` int(1) NOT NULL,
  `send_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_forum_preference'
--

CREATE TABLE IF NOT EXISTS am_plugin_forum_preference (
  preference_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  default_webpage_id int(11) default NULL,
  PRIMARY KEY  (preference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_forum_reply'
--

CREATE TABLE IF NOT EXISTS am_plugin_forum_reply (
  reply_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  subject_id int(11) NOT NULL,
  connection_id int(11) default NULL,
  reply_body text,
  reply_create_datetime datetime default NULL,
  reply_archived int(1) default NULL,
  PRIMARY KEY  (reply_id),
  FULLTEXT KEY reply_body_fulltext (reply_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_forum_reply_recommendation'
--

CREATE TABLE IF NOT EXISTS am_plugin_forum_reply_recommendation (
  connection_id int(11) NOT NULL,
  reply_id int(11) NOT NULL,
  recommendation_datetime datetime default NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_forum_subject'
--

CREATE TABLE IF NOT EXISTS am_plugin_forum_subject (
  subject_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  subject_title varchar(255) default NULL,
  connection_id int(11) default NULL,
  subject_body text,
  subject_create_datetime datetime default NULL,
  subject_edit_datetime datetime default NULL,
  subject_locked int(1) default NULL,
  subject_sticky int(1) default NULL,
  subject_archived int(1) default NULL,
  PRIMARY KEY  (subject_id),
  FULLTEXT KEY subject_body_fulltext (subject_title,subject_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `am_plugin_forum_subject_notify`
-- 

CREATE TABLE `am_plugin_forum_subject_notify` (
  `subject_id` int(11) NOT NULL,
  `reply_id` int(11) NOT NULL,
  `webspace_id` int(11) NOT NULL,
  `last_connection_id` int(11) NOT NULL,
  `notification_create_datetime` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

-- 
-- Table structure for table `am_plugin_forum_subject_track`
-- 

CREATE TABLE `am_plugin_forum_subject_track` (
  `subject_id` int(11) NOT NULL,
  `connection_id` int(11) NOT NULL,
  `notification` int(1) default NULL,
  `webspace_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_forum_tag'
--

CREATE TABLE IF NOT EXISTS am_plugin_forum_tag (
  webspace_id int(11) NOT NULL default '0',
  connection_id int(11) NOT NULL default '0',
  subject_id int(11) NOT NULL default '0',
  tag_name varchar(255) default NULL,
  sticky int(11) NOT NULL default '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_guestbook'
--

CREATE TABLE IF NOT EXISTS am_plugin_guestbook (
  guestbook_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  connection_id int(11) default NULL,
  guestbook_body varchar(255) default NULL,
  guestbook_create_datetime datetime NOT NULL,
  PRIMARY KEY  (guestbook_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_wiki_note'
--

CREATE TABLE IF NOT EXISTS am_plugin_wiki_note (
  note_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL default '0',
  wikipage_id int(11) NOT NULL default '0',
  connection_id int(11) default NULL,
  note_body text,
  note_create_datetime datetime default NULL,
  PRIMARY KEY  (note_id),
  FULLTEXT KEY comment_body (note_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_wiki_page'
--

CREATE TABLE IF NOT EXISTS am_plugin_wiki_page (
  wikipage_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  wikipage_name varchar(50) default NULL,
  current_revision_id int(11) default NULL,
  wikipage_allow_note int(1) default NULL,
  PRIMARY KEY  (wikipage_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_wiki_preference'
--

CREATE TABLE IF NOT EXISTS am_plugin_wiki_preference (
  preference_id int(11) NOT NULL auto_increment,
  webspace_id int(11) default NULL,
  default_webpage_id int(11) default NULL,
  PRIMARY KEY  (preference_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_plugin_wiki_revision'
--

CREATE TABLE IF NOT EXISTS am_plugin_wiki_revision (
  revision_id int(11) NOT NULL auto_increment,
  wikipage_id int(11) default NULL,
  revision_body text,
  connection_id int(11) default NULL,
  revision_create_datetime datetime default NULL,
  PRIMARY KEY  (revision_id),
  FULLTEXT KEY search (revision_body)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_stylesheet'
--

CREATE TABLE IF NOT EXISTS am_stylesheet (
  stylesheet_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  stylesheet_name varchar(50) NOT NULL,
  stylesheet_body text,
  PRIMARY KEY  (stylesheet_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_webpage'
--

CREATE TABLE IF NOT EXISTS am_webpage (
  webpage_id int(11) NOT NULL auto_increment,
  webspace_id int(11) NOT NULL,
  webpage_body text,
  webpage_name varchar(50) NOT NULL,
  webpage_create_datetime datetime NOT NULL,
  PRIMARY KEY  (webpage_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table 'am_webspace'
--

CREATE TABLE IF NOT EXISTS am_webspace (
  webspace_id int(11) NOT NULL auto_increment,
  owner_connection_id int(11) NOT NULL,
  webspace_unix_name varchar(50) NOT NULL,
  language_code varchar(3) default NULL,
  webspace_title varchar(200) default NULL,
  default_webpage_id int(11) NOT NULL,
  default_permission int(11) NOT NULL default '0',
  stylesheet_id int(11) default NULL,
  webspace_create_datetime datetime NOT NULL,
  webspace_allocation int(11) default NULL,
  webspace_locked int(1) default NULL,
  status_id int(1) default NULL,
  PRIMARY KEY  (webspace_id),
  FULLTEXT KEY webspace (webspace_title)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
