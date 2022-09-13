<?php
    $verbs = ['add', 'adjust', 'bring', 'remove', 'whisk'];
    $recipe = get_the_content();
    $lines = preg_split("/\r\n|\n|\r/", $recipe);
    $line_count = sizeof($lines);
    if (intval($lines[0])) {
        $page = array_shift($lines);
    } else if (intval($lines[$line_count - 1])) {
        $page = $lines[$line_count - 1];
    }
    $title = array_shift($lines);
    
    // Get the servings and total time
    $split = explode('total time:', array_shift($lines));
    $servings = $split[0];
    $time = $split[1];
    $in_ingredients = $in_instructions = false;
    foreach ($lines as $i => $line) {
        $split = explode(' ', $line);

        // Searches to see if the second word in the line is among the list of verbs.
        // This will indicate if the line is an ingredient or an instruction.
        // Obviously this is going to be prone to bugs, but it's the best option I have right now lol
        if (intval($split[0]) && !array_search($split[1], $verbs)) {
            if (!$in_ingredients) {
                $in_ingredients = true;
                $description_end = $i - 1;
            } else {
                
            }
        }
    } 
    print_r($lines);
    echo <<<EOS
    <p>$page</p>
    <p>$title</p>
    <p>$servings</p>
    <p>total time: $time</p>
    EOS;
?>