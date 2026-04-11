<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta content="IE=edge,chrome=1" http-equiv="X-UA-Compatible">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <title>Smash-Map API Documentation</title>

    <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.style.css") }}" media="screen">
    <link rel="stylesheet" href="{{ asset("/vendor/scribe/css/theme-default.print.css") }}" media="print">

    <script src="https://cdn.jsdelivr.net/npm/lodash@4.17.10/lodash.min.js"></script>

    <link rel="stylesheet"
          href="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/styles/obsidian.min.css">
    <script src="https://unpkg.com/@highlightjs/cdn-assets@11.6.0/highlight.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jets/0.14.1/jets.min.js"></script>

    <style id="language-style">
        /* starts out as display none and is replaced with js later  */
                    body .content .bash-example code { display: none; }
                    body .content .javascript-example code { display: none; }
            </style>

    <script>
        var tryItOutBaseUrl = "http://127.0.0.1:8000";
        var useCsrf = Boolean();
        var csrfUrl = "/sanctum/csrf-cookie";
    </script>
    <script src="{{ asset("/vendor/scribe/js/tryitout-5.9.0.js") }}"></script>

    <script src="{{ asset("/vendor/scribe/js/theme-default-5.9.0.js") }}"></script>

</head>

<body data-languages="[&quot;bash&quot;,&quot;javascript&quot;]">

<a href="#" id="nav-button">
    <span>
        MENU
        <img src="{{ asset("/vendor/scribe/images/navbar.png") }}" alt="navbar-image"/>
    </span>
</a>
<div class="tocify-wrapper">
            <img src="images/logo.png" alt="logo" class="logo" style="padding-top: 10px;" width="100%"/>
    
            <div class="lang-selector">
                                            <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                            <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                    </div>
    
    <div class="search">
        <input type="text" class="search" id="input-search" placeholder="Search">
    </div>

    <div id="toc">
                    <ul id="tocify-header-introduction" class="tocify-header">
                <li class="tocify-item level-1" data-unique="introduction">
                    <a href="#introduction">Introduction</a>
                </li>
                            </ul>
                    <ul id="tocify-header-authenticating-requests" class="tocify-header">
                <li class="tocify-item level-1" data-unique="authenticating-requests">
                    <a href="#authenticating-requests">Authenticating requests</a>
                </li>
                            </ul>
                    <ul id="tocify-header-events" class="tocify-header">
                <li class="tocify-item level-1" data-unique="events">
                    <a href="#events">Events</a>
                </li>
                                    <ul id="tocify-subheader-events" class="tocify-subheader">
                                                    <li class="tocify-item level-2" data-unique="events-GETapi-events">
                                <a href="#events-GETapi-events">List events with filtering, sorting, and pagination support.</a>
                            </li>
                                                                                <li class="tocify-item level-2" data-unique="events-GETapi-events-statistics">
                                <a href="#events-GETapi-events-statistics">Get event statistics in a format suitable for charts.</a>
                            </li>
                                                                        </ul>
                            </ul>
            </div>

    <ul class="toc-footer" id="toc-footer">
                    <li style="padding-bottom: 5px;"><a href="{{ route("scribe.postman") }}">View Postman collection</a></li>
                            <li style="padding-bottom: 5px;"><a href="{{ route("scribe.openapi") }}">View OpenAPI spec</a></li>
                <li><a href="http://github.com/knuckleswtf/scribe">Documentation powered by Scribe ✍</a></li>
    </ul>

    <ul class="toc-footer" id="last-updated">
        <li>Last updated: April 11, 2026</li>
    </ul>
</div>

<div class="page-wrapper">
    <div class="dark-box"></div>
    <div class="content">
        <h1 id="introduction">Introduction</h1>
<p>API documentation for the Smash Map website</p>
<aside>
    <strong>Base URL</strong>: <code>http://127.0.0.1:8000</code>
</aside>
<pre><code>This documentation aims to provide all the information you need to work with our API.

&lt;aside&gt;As you scroll, you'll see code examples for working with the API in different programming languages in the dark area to the right (or as part of the content on mobile).
You can switch the language used with the tabs at the top right (or from the nav menu at the top left on mobile).&lt;/aside&gt;</code></pre>

        <h1 id="authenticating-requests">Authenticating requests</h1>
<p>This API is not authenticated.</p>

        <h1 id="events">Events</h1>

    

                                <h2 id="events-GETapi-events">List events with filtering, sorting, and pagination support.</h2>

<p>
</p>



<span id="example-requests-GETapi-events">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/events?startDate=2024-01-01&amp;endDate=2024-12-31&amp;games=1%2C2%2C1386&amp;name=Genesis&amp;type=offline&amp;continents=NA%2CEU&amp;countries=US%2CCA%2CJP&amp;orderBy=dateDESC&amp;lat=37.7749&amp;lng=-122.4194&amp;radius=100&amp;paginate=false" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/events"
);

const params = {
    "startDate": "2024-01-01",
    "endDate": "2024-12-31",
    "games": "1,2,1386",
    "name": "Genesis",
    "type": "offline",
    "continents": "NA,EU",
    "countries": "US,CA,JP",
    "orderBy": "dateDESC",
    "lat": "37.7749",
    "lng": "-122.4194",
    "radius": "100",
    "paginate": "false",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-events">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: [
        {
            &quot;id&quot;: 1,
            &quot;game&quot;: {
                &quot;name&quot;: &quot;Super Smash Bros. Melee&quot;,
                &quot;color&quot;: &quot;#A30010&quot;
            },
            &quot;address&quot;: {
                &quot;country&quot;: {
                    &quot;name&quot;: &quot;United States&quot;,
                    &quot;timezone&quot;: &quot;America/Los_Angeles&quot;
                },
                &quot;name&quot;: &quot;Anaheim Convention Center&quot;,
                &quot;latitude&quot;: 33.8003,
                &quot;longitude&quot;: -117.9211
            },
            &quot;image&quot;: {
                &quot;url&quot;: &quot;https://smashmap.example.com/storage/images/events/1.png&quot;
            },
            &quot;is_online&quot;: false,
            &quot;name&quot;: &quot;Genesis 10&quot;,
            &quot;timezone_start_date_time&quot;: &quot;2024-01-05T10:00:00-08:00&quot;,
            &quot;timezone_end_date_time&quot;: &quot;2024-01-07T20:00:00-08:00&quot;,
            &quot;timezone&quot;: &quot;America/Los_Angeles (PST)&quot;,
            &quot;attendees&quot;: 5000,
            &quot;link&quot;: &quot;https://start.gg/tournament/genesis-10&quot;,
            &quot;user_subscribed&quot;: false
        }
    ],
    &quot;links&quot;: {
        &quot;first&quot;: &quot;https://smashmap.example.com/api/events?page=1&quot;,
        &quot;last&quot;: &quot;https://smashmap.example.com/api/events?page=10&quot;,
        &quot;prev&quot;: null,
        &quot;next&quot;: &quot;https://smashmap.example.com/api/events?page=2&quot;
    },
    &quot;meta&quot;: {
        &quot;current_page&quot;: 1,
        &quot;from&quot;: 1,
        &quot;last_page&quot;: 10,
        &quot;per_page&quot;: 12,
        &quot;to&quot;: 12,
        &quot;total&quot;: 120
    }
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;An error occurred while retrieving the events E 006&quot;,
    &quot;error&quot;: &quot;Error message details&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-events" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-events"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-events"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-events" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-events">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-events" data-method="GET"
      data-path="api/events"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-events', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-events"
                    onclick="tryItOut('GETapi-events');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-events"
                    onclick="cancelTryOut('GETapi-events');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-events"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/events</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-events"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-events"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>startDate</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="startDate"                data-endpoint="GETapi-events"
               value="2024-01-01"
               data-component="query">
    <br>
<p>Filter events starting from this date (Y-m-d format). Example: <code>2024-01-01</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>endDate</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="endDate"                data-endpoint="GETapi-events"
               value="2024-12-31"
               data-component="query">
    <br>
<p>Filter events ending before this date (Y-m-d format). Example: <code>2024-12-31</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>games</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="games"                data-endpoint="GETapi-events"
               value="1,2,1386"
               data-component="query">
    <br>
<p>Comma-separated game IDs to filter by. Use "default" for no filter. Example: <code>1,2,1386</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>name</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="name"                data-endpoint="GETapi-events"
               value="Genesis"
               data-component="query">
    <br>
<p>Search events by name (case-insensitive partial match). Example: <code>Genesis</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-events"
               value="offline"
               data-component="query">
    <br>
<p>Event type filter. Options: "default" (all), "online" (online-only), "offline" (in-person), "followed" (subscribed by authenticated user). Example: <code>offline</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>continents</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="continents"                data-endpoint="GETapi-events"
               value="NA,EU"
               data-component="query">
    <br>
<p>Comma-separated continent codes to filter by. Use "default" for no filter. Example: <code>NA,EU</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>countries</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="countries"                data-endpoint="GETapi-events"
               value="US,CA,JP"
               data-component="query">
    <br>
<p>Comma-separated country codes to filter by. Use "default" for no filter. Example: <code>US,CA,JP</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>orderBy</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="orderBy"                data-endpoint="GETapi-events"
               value="dateDESC"
               data-component="query">
    <br>
<p>Sort order for events. Options: "default", "attendeesASC", "attendeesDESC", "dateASC", "dateDESC". Example: <code>dateDESC</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>lat</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="lat"                data-endpoint="GETapi-events"
               value="37.7749"
               data-component="query">
    <br>
<p>Latitude for location-based filtering. Requires lng and radius. Example: <code>37.7749</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>lng</code></b>&nbsp;&nbsp;
<small>number</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="lng"                data-endpoint="GETapi-events"
               value="-122.4194"
               data-component="query">
    <br>
<p>Longitude for location-based filtering. Requires lat and radius. Example: <code>-122.4194</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>radius</code></b>&nbsp;&nbsp;
<small>integer</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="number" style="display: none"
               step="any"               name="radius"                data-endpoint="GETapi-events"
               value="100"
               data-component="query">
    <br>
<p>Search radius in kilometers from the provided coordinates. Requires lat and lng. Example: <code>100</code></p>
            </div>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>paginate</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="paginate"                data-endpoint="GETapi-events"
               value="false"
               data-component="query">
    <br>
<p>Set to "false" to return all events without pagination. Defaults to true (12 per page). Example: <code>false</code></p>
            </div>
                </form>

                    <h2 id="events-GETapi-events-statistics">Get event statistics in a format suitable for charts.</h2>

<p>
</p>



<span id="example-requests-GETapi-events-statistics">
<blockquote>Example request:</blockquote>


<div class="bash-example">
    <pre><code class="language-bash">curl --request GET \
    --get "http://127.0.0.1:8000/api/events/statistics?type=games" \
    --header "Content-Type: application/json" \
    --header "Accept: application/json"</code></pre></div>


<div class="javascript-example">
    <pre><code class="language-javascript">const url = new URL(
    "http://127.0.0.1:8000/api/events/statistics"
);

const params = {
    "type": "games",
};
Object.keys(params)
    .forEach(key =&gt; url.searchParams.append(key, params[key]));

const headers = {
    "Content-Type": "application/json",
    "Accept": "application/json",
};


fetch(url, {
    method: "GET",
    headers,
}).then(response =&gt; response.json());</code></pre></div>

</span>

<span id="example-responses-GETapi-events-statistics">
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;data&quot;: {
        &quot;labels&quot;: [
            &quot;64&quot;,
            &quot;Melee&quot;,
            &quot;Brawl&quot;,
            &quot;Project M&quot;,
            &quot;Project +&quot;,
            &quot;3DS / WiiU&quot;,
            &quot;Ultimate&quot;,
            &quot;HDR&quot;,
            &quot;Rivals 2&quot;
        ],
        &quot;datasets&quot;: [
            {
                &quot;data&quot;: [
                    12,
                    450,
                    80,
                    95,
                    150,
                    60,
                    800,
                    30,
                    120
                ],
                &quot;backgroundColor&quot;: [
                    &quot;#FAC41A&quot;,
                    &quot;#A30010&quot;,
                    &quot;#660d02&quot;,
                    &quot;#3B448B&quot;,
                    &quot;#6FD19C&quot;,
                    &quot;#AFC1EE&quot;,
                    &quot;#F18A41&quot;,
                    &quot;#015500&quot;,
                    &quot;#B19EEF&quot;
                ],
                &quot;hoverBackgroundColor&quot;: [
                    &quot;#D8A504&quot;,
                    &quot;#82000C&quot;,
                    &quot;#510A01&quot;,
                    &quot;#2F366F&quot;,
                    &quot;#3EC17A&quot;,
                    &quot;#6A8CDF&quot;,
                    &quot;#E46810&quot;,
                    &quot;#013D00&quot;,
                    &quot;#9A85D1&quot;
                ]
            }
        ],
        &quot;eventCount&quot;: 1797
    },
    &quot;message&quot;: &quot;Statistics retrieved with success&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (200):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">(type=months) {
  &quot;data&quot;: {
    &quot;labels&quot;: [&quot;January&quot;, &quot;February&quot;, &quot;March&quot;, &quot;April&quot;, &quot;May&quot;, &quot;June&quot;],
    &quot;datasets&quot;: [
      {
        &quot;label&quot;: &quot;64&quot;,
        &quot;backgroundColor&quot;: [&quot;#FAC41A&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#D8A504&quot;],
        &quot;data&quot;: [2, 3, 4, 2, 1, 0]
      },
      {
        &quot;label&quot;: &quot;Melee&quot;,
        &quot;backgroundColor&quot;: [&quot;#A30010&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#82000C&quot;],
        &quot;data&quot;: [80, 95, 120, 85, 70, 0]
      },
      {
        &quot;label&quot;: &quot;Brawl&quot;,
        &quot;backgroundColor&quot;: [&quot;#660d02&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#510A01&quot;],
        &quot;data&quot;: [10, 15, 20, 18, 17, 0]
      },
      {
        &quot;label&quot;: &quot;Project M&quot;,
        &quot;backgroundColor&quot;: [&quot;#3B448B&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#2F366F&quot;],
        &quot;data&quot;: [20, 25, 30, 20, 0, 0]
      },
      {
        &quot;label&quot;: &quot;Project +&quot;,
        &quot;backgroundColor&quot;: [&quot;#6FD19C&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#3EC17A&quot;],
        &quot;data&quot;: [30, 40, 50, 30, 0, 0]
      },
      {
        &quot;label&quot;: &quot;3DS / WiiU&quot;,
        &quot;backgroundColor&quot;: [&quot;#AFC1EE&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#6A8CDF&quot;],
        &quot;data&quot;: [5, 8, 10, 7, 30, 0]
      },
      {
        &quot;label&quot;: &quot;Ultimate&quot;,
        &quot;backgroundColor&quot;: [&quot;#F18A41&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#E46810&quot;],
        &quot;data&quot;: [150, 200, 250, 150, 50, 0]
      },
      {
        &quot;label&quot;: &quot;HDR&quot;,
        &quot;backgroundColor&quot;: [&quot;#015500&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#013D00&quot;],
        &quot;data&quot;: [5, 8, 9, 8, 0, 0]
      },
      {
        &quot;label&quot;: &quot;Rivals 2&quot;,
        &quot;backgroundColor&quot;: [&quot;#B19EEF&quot;],
        &quot;hoverBackgroundColor&quot;: [&quot;#9A85D1&quot;],
        &quot;data&quot;: [25, 35, 40, 20, 0, 0]
      }
    ],
    &quot;eventCount&quot;: 0
  },
  &quot;message&quot;: &quot;Statistics retrieved with success&quot;
}</code>
 </pre>
            <blockquote>
            <p>Example response (500):</p>
        </blockquote>
                <pre>

<code class="language-json" style="max-height: 300px;">{
    &quot;message&quot;: &quot;An error occurred while retrieving the events statistics E 010&quot;,
    &quot;error&quot;: &quot;Error message details&quot;
}</code>
 </pre>
    </span>
<span id="execution-results-GETapi-events-statistics" hidden>
    <blockquote>Received response<span
                id="execution-response-status-GETapi-events-statistics"></span>:
    </blockquote>
    <pre class="json"><code id="execution-response-content-GETapi-events-statistics"
      data-empty-response-text="<Empty response>" style="max-height: 400px;"></code></pre>
</span>
<span id="execution-error-GETapi-events-statistics" hidden>
    <blockquote>Request failed with error:</blockquote>
    <pre><code id="execution-error-message-GETapi-events-statistics">

Tip: Check that you&#039;re properly connected to the network.
If you&#039;re a maintainer of ths API, verify that your API is running and you&#039;ve enabled CORS.
You can check the Dev Tools console for debugging information.</code></pre>
</span>
<form id="form-GETapi-events-statistics" data-method="GET"
      data-path="api/events/statistics"
      data-authed="0"
      data-hasfiles="0"
      data-isarraybody="0"
      autocomplete="off"
      onsubmit="event.preventDefault(); executeTryOut('GETapi-events-statistics', this);">
    <h3>
        Request&nbsp;&nbsp;&nbsp;
                    <button type="button"
                    style="background-color: #8fbcd4; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-tryout-GETapi-events-statistics"
                    onclick="tryItOut('GETapi-events-statistics');">Try it out ⚡
            </button>
            <button type="button"
                    style="background-color: #c97a7e; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-canceltryout-GETapi-events-statistics"
                    onclick="cancelTryOut('GETapi-events-statistics');" hidden>Cancel 🛑
            </button>&nbsp;&nbsp;
            <button type="submit"
                    style="background-color: #6ac174; padding: 5px 10px; border-radius: 5px; border-width: thin;"
                    id="btn-executetryout-GETapi-events-statistics"
                    data-initial-text="Send Request 💥"
                    data-loading-text="⏱ Sending..."
                    hidden>Send Request 💥
            </button>
            </h3>
            <p>
            <small class="badge badge-green">GET</small>
            <b><code>api/events/statistics</code></b>
        </p>
                <h4 class="fancy-heading-panel"><b>Headers</b></h4>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Content-Type</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Content-Type"                data-endpoint="GETapi-events-statistics"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                                <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>Accept</code></b>&nbsp;&nbsp;
&nbsp;
 &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="Accept"                data-endpoint="GETapi-events-statistics"
               value="application/json"
               data-component="header">
    <br>
<p>Example: <code>application/json</code></p>
            </div>
                            <h4 class="fancy-heading-panel"><b>Query Parameters</b></h4>
                                    <div style="padding-left: 28px; clear: unset;">
                <b style="line-height: 2;"><code>type</code></b>&nbsp;&nbsp;
<small>string</small>&nbsp;
<i>optional</i> &nbsp;
 &nbsp;
                <input type="text" style="display: none"
                              name="type"                data-endpoint="GETapi-events-statistics"
               value="games"
               data-component="query">
    <br>
<p>The type of statistics to retrieve. Options: "games" (event count by game), "months" (event count by month for the next 6 months, broken down by game). Example: <code>games</code></p>
            </div>
                </form>

            

        
    </div>
    <div class="dark-box">
                    <div class="lang-selector">
                                                        <button type="button" class="lang-button" data-language-name="bash">bash</button>
                                                        <button type="button" class="lang-button" data-language-name="javascript">javascript</button>
                            </div>
            </div>
</div>
</body>
</html>
