<?php 
    //Functions which we find useful and reuse often

    //Output to JS console
    function consoleJS($txt){
        echo '<script>console.log("'.$txt.'");</script>';
    }

    //Reading time
    function readingTime($postId, $makeMinutes = false, $appendTxt='') {//append txt example= read/reading time etc.
        //Get the post content and strip all tags
            $content = strip_tags(get_post_field( 'post_content', $postId ));
        
        //Total word count -> minutes
            $word_count = str_word_count(  $content );
            $readingtime = ceil($word_count / 200);

        //minutes/mins & singular
            $timer = $makeMinutes ? 'minutes' : 'mins';
            $timer = $readingtime==1 ? str_replace('s','',$timer) : $timer;

        return $readingtime . $timer . $appendTxt;
    }

    //Include a file if user is administrator
    function includeForAdmin($file){
        if(current_user_can('administrator')){
            include $file;
        }
    }

    //Include template assets
    function templateAssets($jsLocation='/library/js',$cssLocation='/library/styles'){
        //All files to be included MUST have the same name as the template
        $templateName=str_replace('.php','',get_page_template_slug());
        if(is_file(get_template_directory().$cssLocation.'/'.$templatName.'.css')){
            wp_enqueue_style('style-'.$templatName, get_template_directory_uri().$cssLocation.'/'.$templatName.'.css');
        }
        if(is_file(get_template_directory().$jsLocation.'/'.$templatName.'.js')){
            wp_enqueue_script('script-'.$templatName, get_template_directory_uri().$jsLocation.'/'.$templatName.'.js');
        }
    }
?>