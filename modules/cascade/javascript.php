<?php
    define('JAVASCRIPT', true);
    require_once "../../includes/common.php";
    error_reporting(0);
    header("Content-Type: application/x-javascript");
?>
<!-- --><script>
        var ChyrpAjaxScroll = {
            busy: false,
            fail: false,
            state: null,
            auto: <?php echo ( Config::current()->ajax_scroll_auto ? "true" : "false" ) ?>,
            init: function() {
                if ( ChyrpAjaxScroll.auto ) {
                    $(window).on('scroll', window, ChyrpAjaxScroll.watch);
                } else {
                    $("#next_page_page").click(ChyrpAjaxScroll.fetch);
                }
            },
            watch: function() {
                var docViewTop = $(window).scrollTop();
                var windowHeight = window.innerHeight ? window.innerHeight : $(window).height();
                var docViewBottom = docViewTop + windowHeight;
                var docViewed = docViewBottom - $(document).height();
                if ( docViewed == 0 ) ChyrpAjaxScroll.fetch();
            },
            fetch: function() {
                if ( !ChyrpAjaxScroll.busy && !ChyrpAjaxScroll.fail ) {
                    ChyrpAjaxScroll.busy = true;
                    var last_post = $(".post").last();
                    var next_page_url = $("#next_page_page").attr('href');
                    if ( next_page_url && last_post.length ) {
                        $.get(next_page_url, function(data){
                            if ( !!history.replaceState ) history.replaceState(ChyrpAjaxScroll.state, '', next_page_url );
                            // Insert new posts
                            $(".post").last().after($(data).find(".post"));
                            // Find and execute scripts
                            $(data).filter('script').each(function(){
                                if ( this.src )
                                    $.ajax({ url: this.src, async: false, dataType: "script" });
                                else
                                    $.globalEval( this.text || this.textContent || this.innerHTML || "" );
                            });
                            // Search for next_page_page
                            var ajax_page_url = $(data).find("#next_page_page").last().attr('href');
                            if ( ajax_page_url ) {
                                // We found another page to load
                                $("#next_page_page").attr('href', ajax_page_url);
                                ChyrpAjaxScroll.busy = false;
                            } else {
                                // That's all Folks!
                                $("#next_page_page").fadeOut('fast');
                            }
                        }).fail( function() { ChyrpAjaxScroll.fail = true });
                        return false; // Suppress hyperlink if we can fetch
                    }
                }
            },
        }
        $(document).ready(ChyrpAjaxScroll.init);
<!-- --></script>
