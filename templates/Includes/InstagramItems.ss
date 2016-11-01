<% if $ShowFeed %>
    <% loop $ShowInstagramFeed() %>
        <p><a href="$link" target="_blank">$caption.text</a></p>
        <% if $images %>
            <img src="$images.thumbnail.url"/>
            <img src="$images.low_resolution.url"/>
            <img src="$images.standard_resolution.url"/>
        <% end_if %>
    <% end_loop %>
<% end_if %>