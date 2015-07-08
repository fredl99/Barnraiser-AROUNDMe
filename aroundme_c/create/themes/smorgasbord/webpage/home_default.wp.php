<AM_BLOCK plugin="custom" name="navigation" />

<div id="col_left_420">
    <h1>Activity log</h1>
    <AM_BLOCK plugin="barnraiser_connection" name="log" limit="20" />
    <h1>Blog</h1>
    <AM_BLOCK plugin="barnraiser_blog" name="list" limit="8" trim="160" webpage="blog" />
</div>

<div id="col_right_420">
    <h1>Connections</h1>
    <AM_BLOCK plugin="barnraiser_connection" name="gallery" limit="30" />
    <h1>Connect</h1>
    <AM_BLOCK plugin="barnraiser_connection" name="connect" />
    <h1>Forum</h1>
    <AM_BLOCK plugin="barnraiser_forum" name="subject_list" limit="8" trim="60" webpage="forum" />

</div>

<div style="clear:both;"></div>

<AM_BLOCK plugin="custom" name="footer" />