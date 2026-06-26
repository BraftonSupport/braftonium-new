<?php 
    //Functions which we find useful and reuse often

    //Output to JS console
    function consoleJS($txt='here!'){
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
?>