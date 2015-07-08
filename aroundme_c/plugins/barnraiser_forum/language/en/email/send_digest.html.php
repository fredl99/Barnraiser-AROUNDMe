<html>
	<head>
		<title>AM_KEYWORD_EMAIL_TITLE</title>
		
		<style type="text/css">
			a:link, a:visited, a:active { color: #03C; }
			body, h1, h2, h3 { font-family: Arial, Helvetica, sans-serif; color: #000; }
			h1 { font-size: 24px; font-weight: normal; }
			h2 { font-size: 18px; font-weight: normal; }
			h3 { font-size: 14px; font-weight: bold; }
			hr { margin-top: 10px; margin-bottom: 10px; height: 1px; color: #999; background-color: #999; border: 0; }
			p, li { font-size: 13px; line-height: 16px; color: #000; }
		</style>
	</head>
	
	<body>
		<p>
		Hi AM_SYS_KEYWORD_RECIPIENT_NICKNAME,
		</p>

		<p>
			This is your digest from <a href="AM_KEYWORD_WEBSPACE_URL">AM_KEYWORD_WEBSPACE_TITLE</a>.
		</p>

		<tracked_subjects>
		<h1>Tracked discussions</h1>
		<ul>
			<tracked_subjects_loop>
			<li><a href="AM_KEYWORD_TRACKED_SUBJECT_URL">AM_KEYWORD_TRACKED_SUBJECT_TITLE</a><br />
			Author: AM_KEYWORD_TRACKED_SUBJECT_AUTHOR, published: AM_KEYWORD_TRACKED_SUBJECT_CREATE_DATETIME<br />
			<b>Received AM_KEYWORD_TRACKED_SUBJECT_REPLIES replies</b> since you last connected (AM_KEYWORD_TRACKED_SUBJECT_REPLIES_TOTAL in total)</li>
			</tracked_subjects_loop>
		</ul>

		<hr />
		
		</tracked_subjects>
		
		<new_subjects>
		<h1>New discussions</h1>
		<ul>
			<new_subjects_loop>
			<li><a href="AM_KEYWORD_NEW_SUBJECT_URL">AM_KEYWORD_NEW_SUBJECT_TITLE</a><br />
			Author: AM_KEYWORD_NEW_SUBJECT_AUTHOR, published: AM_KEYWORD_NEW_SUBJECT_CREATE_DATETIME<br />
			Received AM_KEYWORD_NEW_SUBJECT_REPLIES_TOTAL replies in total.</li>
			</new_subjects_loop>
		</ul>
		
		<hr />
		
		</new_subjects>
		
		<new_posts>
		<h1>New replies</h1>
		<ul>
			<new_posts_loop>
			<li><a href="AM_KEYWORD_NEW_POST_URL">AM_KEYWORD_NEW_POST_TITLE</a><br />
			Author: AM_KEYWORD_NEW_POST_AUTHOR, published: AM_KEYWORD_NEW_POST_CREATE_DATETIME<br />
			Reply: AM_KEYWORD_NEW_POST_BODY</li>
			</new_posts_loop>
		</ul>
		
		<hr />
		
		</new_posts>

		<h1>Connection activity</h1>
		<p>AM_SYS_KEYWORD_CONNECTION_TOTAL people have connected to this webspace. AM_SYS_KEYWORD_CONNECTION_PERIOD_TOTAL connections are new since your last digest. You last connected on AM_KEYWORD_LAST_CONNECTION_DATETIME.</p>

		<hr />
		
		<ul>
			<li><a href="AM_KEYWORD_WEBSPACE_URL">Go to webspace</a></li>
			<li><a href="AM_KEYWORD_REMOVE_DIGEST_URL">Cancel this digest email</a></li>
		</ul>

		<hr />
		
		<p>
			This digest email was sent from <a href="AM_KEYWORD_WEBSPACE_URL">AM_KEYWORD_WEBSPACE_TITLE</a> using Barnraisers <a href="http://www.barnraiser.org">AROUNDMe collaboration server</a>; the perfect solution for anyone wishing to create a collaborative social space on the Web.
		</p>
	</body>
</html>