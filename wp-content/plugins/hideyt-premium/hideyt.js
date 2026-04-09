/* Copyright 2025 Maximillian Laumeister */
/* https://www.hideyt.com/ */
(function () {
    "use strict";
    
    let DEBUG = false;
    window.HIDEYT_VERSION = "2.2.21";

    const iframeInnerHTML = `
        <!doctype html>
        <meta charset="utf-8">
        <title>HideYT Embedded Frame</title>
        <script src="https://www.youtube.com/iframe_api"></script>
        <style>

        html, body {
            margin: 0;
            padding: 0;
            width: 100vw;
            height: 100vh;
            font-size: 0;
            overflow: hidden;
        }
        
        body, .hytWPOverlay.ended::after, .hytWPOverlay.fullHiding.paused, .hytWPOverlay.hidestart {
            background-repeat: no-repeat;
            background-position: center;
            background-size: cover;
        }
        
        iframe {
            position: absolute;
            top: 0;
            right: 0;
            width: 100vw;
            height: 100vh;
        }
        
        .hytWPOverlay {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
            pointer-events: none;
        }
        
        .hytWPOverlay.ended::after {
            content:"";
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            right: 0;
        }
        
        .hytWPOverlay.paused::after, .hytWPOverlay.hidestart::after {
            content:"";
            pointer-events: auto;
            position: absolute;
            top: 70px;
            left: 0;
            bottom: 50px;
            right: 0;
            cursor: pointer;
            background-color: #1b1b1b;
            background-repeat: no-repeat;
            background-position: center; 
            background-size: 70px 70px;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' version='1' width='400' height='400' viewBox='0 0 300 300'%3E%3Cg transform='translate(-.234 .173)'%3E%3Ccircle r='147.914' cy='149.827' cx='150.234' fill='%231b1b1b' fill-opacity='.818'/%3E%3Cpath d='M107.06 76.304l127.346 73.523L107.06 223.35z' fill='%23fff'/%3E%3C/g%3E%3C/svg%3E");
        }

        @media (pointer: coarse) {
            .hytWPOverlay.paused::after, .hytWPOverlay.hidestart::after {
                bottom: 60px;
            }
        }
        
        .hytWPOverlay.paused.fullHiding::after, .hytWPOverlay.hidestart::after {
            bottom: 0;
            top: 0;
            background-color: transparent;
        }
        
        .hytWPPlayerWrapExpired {
            position: absolute;
            pointer-events: auto;
            font-size: 14px;
            top: 0;
            left: 0;
            right: 0;
            padding: 10px;
            background: darkred;
            color: white;
            font-family: sans-serif;
            line-height: 1.1;
            z-index: 1;
        }
        
        .hytWPPlayerWrapExpired p {
            margin: 5px 0;
        }
        
        .hytWPPlayerWrapExpired a, .hytWPPlayerWrapExpired a:visited {
            color: #ffa5a5 !important;
            text-decoration: underline !important;
            font-weight: bold;
        }
        
        .hytWPPlayerWrapExpired a:hover {
            color: white !important;
        }
        
        </style>
        <script>
        (function () {
                "use strict";

                let DEBUG = false;
                if (DEBUG) console.log("inner document script parsed");
            
                let playerData;
                
                function init() {
                    if (DEBUG) console.log("begin init");
            
                    // Set background images in CSS
                    function addCss(cssCode) {
                        // https://stackoverflow.com/a/6211716/2234742
                        var styleElement = document.createElement("style");
                        styleElement.type = "text/css";
                        if (styleElement.styleSheet) {
                        styleElement.styleSheet.cssText = cssCode;
                        } else {
                        styleElement.appendChild(document.createTextNode(cssCode));
                        }
                        document.getElementsByTagName("head")[0].appendChild(styleElement);
                    }
                    const styles = \`
                        body, .hytWPOverlay.ended::after, .hytWPOverlay.fullHiding.paused, .hytWPOverlay.hidestart {
                            background-image: url('https://img.youtube.com/vi/\${playerData.videoId}/0.jpg');
                        }
                    \`;
                    addCss(styles); // TODO: Tear this down before new player init, avoid duplicate css
            
                    // Import options
                    let HIDING_OPTION = playerData.options.hideyt_field_appearance || "full";
                    let EXPIRED = playerData.options.expired ? true : false;
                    let PAUSE_OPTION = playerData.options.hideyt_field_pausehiding || "partial";
                    let START_HIDING = (playerData.options.hideyt_field_starthiding === "true");
                    
                    // Configure overlay
                    let overlay = document.querySelector(".hytWPOverlay");
                    if (PAUSE_OPTION === "full") overlay.classList.add("fullHiding");
            
                    // Reset overlay
                    overlay.classList.remove("ended");
                    overlay.classList.remove("paused");
                    if (START_HIDING) overlay.classList.add("hidestart");
            
                    // Clean up from last instantiation
                    const prevFrame = document.querySelector("#player");
                    if (prevFrame) prevFrame.remove();
            
                    let surrogate = document.createElement("div");
                    surrogate.id = "player";
                    document.body.insertBefore(surrogate, overlay);
            
                    const ytplayer = new YT.Player('player', {
                        height: '390',
                        width: '640',
                        videoId: playerData.videoId,
                        playerVars: playerData.urlParameters,
                        events: {
                            'onReady': onPlayerReady
                        }
                    });
                    const ytiframe = ytplayer.getIframe();
            
                    if (DEBUG) console.log(ytiframe);
                    
                    // Add enablejsapi on YouTube Embed
                    /*let url = new URL(ytiframe.src);
                    if (url.searchParams.get('enablejsapi') !== "1") {
                        url.searchParams.set('enablejsapi', "1");
                        url.searchParams.set('rel', "0"); // Since the iframe needs to reload anyway...
                        ytiframe.src = url;
                    }*/
                    
                    // Init player
            
                    function resetClasses() {
                        overlay.classList.remove("ended");
                        overlay.classList.remove("paused");
                    }
                    
                    let pauseTimeout;
                    function onPlayerStateChange(event) {
                        if (DEBUG) console.log("player state change: ", ytplayer);
                        if (EXPIRED) return;
                        if (event.data == YT.PlayerState.ENDED) {
                            if (DEBUG) console.log("player state ended: ", ytplayer);
                            if (HIDING_OPTION !== "none") {
                                window.clearTimeout(pauseTimeout);
                                resetClasses();
                                if (START_HIDING) overlay.classList.add("hidestart");
                                ytplayer.stopVideo();
                            }
                        } else if (event.data == YT.PlayerState.PAUSED) {
                            if (DEBUG) console.log("player state paused: ", ytplayer);
                            if (HIDING_OPTION === "full") {
                                if (PAUSE_OPTION === "partial") {
                                    resetClasses();
                                    overlay.classList.add("paused");
                                } else {
                                    // PAUSE_OPTION === "full"
                                    pauseTimeout = window.setTimeout(function() {
                                        // We need this timeout, otherwise the click event will get stuck in the overlay and not make it to the YouTube player.
                                        resetClasses();
                                        overlay.classList.add("paused");
                                    }, 0);
                                }
                            }
                        } else if (event.data == YT.PlayerState.PLAYING || event.data == YT.PlayerState.BUFFERING) {
                            if (START_HIDING) overlay.classList.remove("hidestart");
                            window.clearTimeout(pauseTimeout);
                            // We need this timeout so that the related videos don't flicker
                            pauseTimeout = window.setTimeout(function() {
                                resetClasses();
                            }, 100);
                        }
                    };
                    
                    function onPlayerApiChange(ch) {
                        console.log("playerapichange:", ch);
                    }
                    function onPlayerError(error) {
                        console.log("playererror:", error);
                    }
                    function onPlayerReady(event) {
                        if (DEBUG) console.log("player ready: ", ytplayer);
                        // Register YouTube Player change
                        ytplayer.addEventListener('onStateChange', onPlayerStateChange);
                        if (DEBUG) ytplayer.addEventListener('onError', onPlayerError);
                        if (DEBUG) ytplayer.addEventListener('onApiChange', onPlayerApiChange);
                        // Register overlay click
                        overlay.addEventListener("click", function() {
                            ytplayer.playVideo();
                        });
                    };
            
                    if (DEBUG) console.log("player created: ", ytplayer);
                    
                    if (EXPIRED && playerData.options.has_plugin_privileges) {
                        let message = document.createElement("div");
                        message.classList.add("hytWPPlayerWrapExpired");
                        message.innerHTML = "<p><b>HideYT Plugin License Expired</b></p><p>To continue hiding YouTube related videos, please <a href='/wp-admin/options-general.php?page=hideyt'>renew your license</a>! (This message is shown only to WordPress admins, not users).";
                        overlay.appendChild(message);
                    }
            
                    window.theplayer = ytplayer;
                }
            
                window.addEventListener("message", function(msg) {
                    if (msg.data.type === "hytInit") {
                        const msgdata = msg.data.data;
                        msgdata.urlParameters.rel = "0"; // Force rel=0

                        // Fullscreen
                        if (msgdata.options.hideyt_field_forcefullscreen === "forceon") {
                            msgdata.urlParameters.fs = "1";
                        } else if (msgdata.options.hideyt_field_forcefullscreen === "forceoff") {
                            msgdata.urlParameters.fs = "0";
                        }
                        
                        playerData = msgdata;
                        // Init HideYT
                        init();
                    }
                }, false);
            })();
        
        </script>
        <div class="hytWPOverlay"></div>
    `;
    
    // Disable HideYT if Thrive Architect editor is open
    if (new URL(window.location.href).searchParams.get("tve") === "true") return;
    
    // Returns an array all elements from array "a" that are also in array "b"
    function arrayDiff(a, b) {
        return a.filter(function(i) {return b.indexOf(i) < 0;});
    };
    
    // Deduplicate an array
    function arrayUnique(array) {
        var a = array.concat();
        for(var i=0; i<a.length; ++i) {
            for(var j=i+1; j<a.length; ++j) {
                if(a[i] === a[j])
                    a.splice(j--, 1);
            }
        }
        return a;
    }

    let iframeList = [];

    function initPlayer(playerFrame) {
        // Replace the youtube iframe with the custom hideyt iframe
        let src;
        if (!playerFrame.src.includes(".com/embed/") && playerFrame.dataset.src.includes(".com/embed/")) {
            src = new URL(playerFrame.dataset.src, new URL(window.location.href));
        } else {
            src = new URL(playerFrame.src, new URL(window.location.href));
        }
        const hideYTFrame = playerFrame.cloneNode(false);
        delete hideYTFrame.dataset.src;
        hideYTFrame.style.visibility = "visible";
        hideYTFrame.style.opacity = "1";
        hideYTFrame.removeAttribute("src");
        const supportsSrcdoc = !!("srcdoc" in document.createElement("iframe"));
        if (supportsSrcdoc) {
            hideYTFrame.setAttribute("srcdoc", iframeInnerHTML);
        } else {
            const blob = new Blob([iframeInnerHTML], 'text/html');
            const blobURL = URL.createObjectURL(blob);
            hideYTFrame.setAttribute("src", blobURL);
        }
        // TODO: Force frameborder, allow, allowfullscreen

        playerFrame.parentNode.insertBefore(hideYTFrame, playerFrame);

        playerFrame.parentNode.removeChild(playerFrame);

        const playerFrameHTML = playerFrame.outerHTML;

        if (DEBUG) console.log("player replaced");

        hideYTFrame.addEventListener("load", function() {
            if (DEBUG) console.log("iframe load");

            const videoId = src.pathname.split("/").slice(-1)[0];

            // Copy url parameters to js object
            const urlParameters = {};
            src.searchParams.forEach((value, key) => {
                urlParameters[key] = value;
            });
            
            // Fix elementor autoplay
            try {
                const elementorWidget = hideYTFrame.closest(".elementor-widget-video");
                if (elementorWidget) {
                    const elementorSettings = JSON.parse(elementorWidget.dataset.settings);
                    if (elementorSettings.show_image_overlay === "yes") {
                        urlParameters.autoplay = 1;
                    }
                }
            } catch (error) {
                // Bail on elementor fix
            }

            hideYTFrame.contentWindow.postMessage({
                type: "hytInit",
                data: {
                    options: hideyt_options,
                    videoId: videoId,
                    urlParameters: urlParameters
                }
            }, "*");
        });
    }
    
    function updatePlayers() {
        let ytiframes = Array.from(document.querySelectorAll(`

            iframe[src^="https://www.youtube.com/embed/"],
            iframe[src^="http://www.youtube.com/embed/"],
            iframe[src^="//www.youtube.com/embed/"],
            
            iframe[src^="https://youtube.com/embed/"],
            iframe[src^="http://youtube.com/embed/"],
            iframe[src^="//youtube.com/embed/"],

            iframe[src^="https://www.youtube-nocookie.com/embed/"],
            iframe[src^="http://www.youtube-nocookie.com/embed/"],
            iframe[src^="//www.youtube-nocookie.com/embed/"],

            iframe[src^="https://youtube-nocookie.com/embed/"],
            iframe[src^="http://youtube-nocookie.com/embed/"],
            iframe[src^="//youtube-nocookie.com/embed/"],

            iframe[data-src^="https://www.youtube.com/embed/"],
            iframe[data-src^="http://www.youtube.com/embed/"],
            iframe[data-src^="//www.youtube.com/embed/"],

            iframe[data-src^="https://youtube.com/embed/"],
            iframe[data-src^="http://youtube.com/embed/"],
            iframe[data-src^="//youtube.com/embed/"],

            iframe[data-src^="https://www.youtube-nocookie.com/embed/"],
            iframe[data-src^="http://www.youtube-nocookie.com/embed/"],
            iframe[data-src^="//www.youtube-nocookie.com/embed/"],

            iframe[data-src^="https://youtube-nocookie.com/embed/"],
            iframe[data-src^="http://youtube-nocookie.com/embed/"],
            iframe[data-src^="//youtube-nocookie.com/embed/"]

        `));
        ytiframes = ytiframes.filter(elem => {
            // ThemePunch / Slider Revolution background video container.
            if (elem.closest(".rs-background-video-layer") !== null) return false;

            // Slider Revolution sometimes uses IDs like sr...video; skip those embeds.
            let id = (elem.id || "").toLowerCase();
            let isSliderRevolutionVideo = id.startsWith("sr") && id.endsWith("video");
            return !isSliderRevolutionVideo;
        });
        let newFrames = arrayDiff(ytiframes, iframeList); // Now ytiframes has only the NEW iframes.
        iframeList = arrayUnique(iframeList.concat(newFrames));
        
        // Init all new YouTube players
        for (let ytiframe of newFrames) {
            initPlayer(ytiframe)
        }
    }

    // The updatePlayers function benchmarks at 30 microseconds for a dry poll,
    // so there will be no jank even at a high polling rate.
    updatePlayers();
    setInterval(updatePlayers, 100);

})();
