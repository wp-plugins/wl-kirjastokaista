<p>WL Kirjastokaista plugin provides a very easy and efficient way to embed videos from Kirjastokaista (Library Channel) service in your posts, pages and widgets. You can create listings that connect in real-time with Kirjastokaista's API or get a single media embed-code to post editor.</p>

<h2>USAGE</h2>

<h4 style="text-transform: uppercase;">Shortcodes</h4>

<p>In WordPress admin, navigate to <i><strong>Tools > WL Kirjastokaista</i></strong>. Choose UI, categories, filters etc. and test shortcode results with pressing button View Shortcode Results. If you're satisfied with the results, press Generate Shortcode -button and copy paste the generated shortcode to WordPress content area of your choice.</p>

<p>If you want to use shortcodes in widget areas, turn on <i><strong>Allow Kirjastokaista and other shortcodes to run in Text Widget</i></strong> setting from <i><strong>Tools > WL Kirjastokaista>Settings</i></strong>. </p>

<h4 style="text-transform: uppercase;">Saving a shortcode & Caching</h4>

<p>You can save the generated shortcoded to database and reference it via [kirjastokaista id=xx] where xx is the id of the saved shortcode. One benefit of saving is that it’s possible to mark saved shortcodes for caching on first load. That means, shortcode gets processed on first load of the page and saved as html in database, so the page loads very fast after the first initial load.</p>

<h4 style="text-transform: uppercase;">Shortcode layouts</h4>

<p>To edit shortcode layout templates, copy templates from plugin's <i><strong>templates</i></strong> -folder and include them in your <i><strong>/wp-content/active theme's folder/kirjastokaista/plugin-layout-templates/</i></strong> . That way, if you update plugin, templates won't get overwritten. For reference what variables you can use in templates, see plugin file <i><strong>kirjastokaista-api-info.php</i></strong></p>

<h4 style="text-transform: uppercase;">Get video embed code to post content</h4>

<p>In post content WYSIWYG editor, press button Kirjastokaista. Paste URL of your Kirjastokaista video and embed code of the video will automatically appear in your content.</p>

<p>If you don't see Kirjastokaista button on WYSIWYG content editor, insure that you have <i><strong>Show Kirjastokaista embed button on content editor</i></strong> setting turned on in <i><strong>Tools > WL Kirjastokaista>Settings</i></strong>. </p>

<h4 style="text-transform: uppercase;">Localization</h4>

<p>To localize this plugin, translate <i><strong>language/kirjastokaista.po</i></strong> file and save it as .mo. Save .mo file as <i><strong>/wp-content/languages/plugins/kirjastokaista-xx.mo</i></strong> where xx is your language code. </p>

<p>&nbsp;</p>

<h2>KNOWN ISSUES</h2>

<ul style="list-style:initial;margin-left:20px;">
	<li>Due to API limitation it's not possible to choose both filters <strong>media languages</strong> + <strong>order by most popular/random</strong> at the same time.</li>
	<li>With some combinations, there might be Vimeo / YouTube screenshots coming with different aspect ratios - this might mess up the slideshows a little bit. To turn off reponsive slideshows and add static height to thumbnails you can remove comments from last lines of css/jcarousel.responsive.css or add those to your styles.</li>
</ul>

<p>&nbsp;</p>

<h2>OTHER</h2>

<p>Read more info on Kirjastokaista (Library Channel) service <a href="http://www.kirjastokaista.fi/en/about-kirjastokaistasta/" target="_blank">here</a>.</p>

<p>This plugin is developed as open source by <a href="http://webloft.no/">Buskerud fylkesbibliotek/Webløft</a> and <a href="http://fi.linkedin.com/in/jonnitammisto" target="_blank">Jonni Tammisto</a> in spring 2015.</p>
