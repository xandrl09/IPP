<?php
/**
 * Author: Ondřej Andrla
 * Mail: xandrl09@fit.vutbr.cz
 * Date: 26.02.2019
 * Time: 16:50
 */


#-----------------------------------------------------------------------------------------------------------------
#-------------------------------------------CLASSES-------------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------

/// class contains global variables
class global_variables_main
{
    #main
    public static $standard_input;
    public static $output;
    public static $output_program;
    public static $xml_count;

    #stats
    public static $process_loc = false;
    public static $process_comments = false;
    public static $process_labels = false;
    public static $process_jumps = false;
    public static $loc_counter = 0;
    public static $comments_counter = 0;
    public static $labels_counter = 0;#todo
    public static $jumps_counter = 0;#todo

    #lexical
    public static $word;
    public static $character;
    public static $keywords = array("MOVE", "CREATEFRAME", "PUSHFRAME", "POPFRAME", "DEFVAR", "CALL", "RETURN", "PUSHS",
        "SETCHAR", "LABEL", "JUMP", "JUMPIFNEQ", "JUMPIFEQ", "DPRINT", "WRITE", "CONCAT", "STRLEN", "GETCHAR", "TYPE",
        "POPS", "ADD", "SUB", "MUL", "IDIV", "GT", "LT", "EQ", "AND", "OR", "NOT", "INT2CHAR", "STRI2INT", "READ",
        "BREAK", "EXIT");
    public static $was_header = false;
    public static $bug;
    public static $control = true;
}

/// class represents token which is used in
/// lexical and syntactical analysis
class lexical_syntax_token
{
    public $keyword;
    public $string;
    public $arbiter;
    function __Construct($new_keyword, $new_string) {
        $this->keyword = $new_keyword;
        $this->string = $new_string;
    }
}

#-----------------------------------------------------------------------------------------------------------------
#-------------------------------------MAIN FUNCTIONS-----------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------

///print help
/// no parameters
function print_help()
{
    echo "Help for script parse.php\n";
    echo "This script parse input code in language IPPcode19\n";
    echo"and transform it into xml output\n";
    echo"Please type input and output file\n";
    echo" Parameter -- is optional and will show help\n";
    echo"\n";
    echo"\n";

}

//------------------------------------------------------------------------------
///check arguments
/// parameters: argc- int represents number of arguments
/// argv- array of arguments
function check_arguments($argc, array $argv)
{
    if($argc === 2)
    {
        switch ($argv[1])
        {
            case "--help":
                print_help();
                die(0);
                break;

            default:
                return 10;
        }
    }
    elseif ($argc === 3 )
    {
        switch($argv[2])
        {
            case "--loc":
                global_variables_main::$process_loc = true;
                break;

            case "--comments":
                global_variables_main::$process_comments = true;
                break;

            case "--labels":
                global_variables_main::$process_labels = true;
                break;

            case "--jumps":
                global_variables_main::$process_jumps = true;
                break;

            default:
                end_with_error("Wrong argument" , 10);
        }
        return 0;
    }
    elseif ($argc === 4 )
    {
        switch($argv[3])
        {
            case "--loc":
                global_variables_main::$process_loc = true;
                break;

            case "--comments":
                global_variables_main::$process_comments = true;
                break;

            case "--labels":
                global_variables_main::$process_labels = true;
                break;

            case "--jumps":
                global_variables_main::$process_jumps = true;
                break;

            default:
                end_with_error("Wrong argument" , 10);
        }
        return 0;
    }
    elseif ($argc === 5 )
    {
        switch($argv[4])
        {
            case "--loc":
                global_variables_main::$process_loc = true;
                break;

            case "--comments":
                global_variables_main::$process_comments = true;
                break;

            case "--labels":
                global_variables_main::$process_labels = true;
                break;

            case "--jumps":
                global_variables_main::$process_jumps = true;
                break;

            default:
                end_with_error("Wrong argument" , 10);
        }
        return 0;
    }
    elseif ($argc === 6 )
    {
        switch($argv[5])
        {
            case "--loc":
                global_variables_main::$process_loc = true;
                break;

            case "--comments":
                global_variables_main::$process_comments = true;
                break;

            case "--labels":
                global_variables_main::$process_labels = true;
                break;

            case "--jumps":
                global_variables_main::$process_jumps = true;
                break;

            default:
                end_with_error("Wrong argument" , 10);
        }
        return 0;
    }
    elseif($argc === 1)
    {
        return 0;
    }
    else
    {
        end_with_error("Wrong argument" , 10 );
        return 10;
    }
}


/// initialization of output XML file
/// have no parameters
function init_XML()
{
    global_variables_main::$output = new DomDocument("1.0", "UTF-8");
    global_variables_main::$output_program = global_variables_main::$output->createElement('program');
    global_variables_main::$xml_count --;
    global_variables_main::$output_program->setAttribute('language', 'IPPcode19');
    global_variables_main::$output->appendChild(global_variables_main::$output_program);
}


/// catching errors
/// arguments: message- text which is print to stderr
/// exit code- returned exit code
function end_with_error($message, $exit_code)
{
    $message = $message . "\n";
    fwrite(STDERR, $message);
    fclose(global_variables_main::$standard_input);
    die($exit_code);
}


/// close of output XML file
/// have no parameters
function close_XML()
{
    fclose(global_variables_main::$standard_input);
    global_variables_main::$output->formatOutput = true;
    echo global_variables_main::$output->saveXML();
}


//-----------------------------------------------------------------------------
///create output file for statistics
/// process statistics data and write it
/// parameter: argv- array of arguments
function process_stats( $argv)
{

    $file_name = $argv[1];
    $file_name = str_replace("--stats=", "", $file_name);

    $stats_file_txt = fopen($file_name, "w");

    if($stats_file_txt == false)
    {
        end_with_error("Cannot open stats file", 12);
    }

    if(global_variables_main::$loc_counter)
    {
        fwrite($stats_file_txt, "lines of code: ");
        fwrite($stats_file_txt, global_variables_main::$loc_counter);
        fwrite($stats_file_txt, "\n");
    }
    if(global_variables_main::$comments_counter)
    {
        fwrite($stats_file_txt, "Amount of comments: ");
        fwrite($stats_file_txt, global_variables_main::$comments_counter);
        fwrite($stats_file_txt, "\n");
    }
    if(global_variables_main::$labels_counter)
    {
        fwrite($stats_file_txt, "Number of labels: ");
        fwrite($stats_file_txt, global_variables_main::$labels_counter);
        fwrite($stats_file_txt, "\n");
    }
    if(global_variables_main::$jumps_counter)
    {
        fwrite($stats_file_txt, "Number of jumps: ");
        fwrite($stats_file_txt, global_variables_main::$jumps_counter);
        fwrite($stats_file_txt, "\n");
    }

    fclose($stats_file_txt);
    return 0;
}

//------------------------------------------------------------------------------



#-----------------------------------------------------------------------------------------------------------------
#-----------------------------------------LEXICAL FUNCTIONS----------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------

/// function checked if word is keyword
function check_if_is_keyword()
{
    foreach (global_variables_main::$keywords as $keywords_member)
    {
        if(strcasecmp(global_variables_main::$word, $keywords_member) != 0)
        {
            ;
        }
        else
        {
            $token = new lexical_syntax_token($keywords_member, $keywords_member);
            global_variables_main::$loc_counter++;
            return $token;
        }
    }
    return false;
}


/// function removes comments
function remove_comments()
{
    global_variables_main::$character = fgetc(global_variables_main::$standard_input);

    if(global_variables_main::$character === '#' && global_variables_main::$control === true)
    {
        while(ord(global_variables_main::$character) != 10)
        {
            global_variables_main::$character = fgetc(global_variables_main::$standard_input);
        }
        global_variables_main::$comments_counter++;
    }
}

/// function removes whitespaces
/// to
function remove_whitespaces()
{
    $repeat_test = true;
     while($repeat_test === true)
     {
         if(global_variables_main::$control === true)
         {
             if(feof(global_variables_main::$standard_input))
             {
                 $tested_token = new lexical_syntax_token("EOF", "EOF");
                 return $tested_token;
             }
             elseif(ord(global_variables_main::$character) == 10 && global_variables_main::$control === true)
             {
                 remove_comments();
                 $tested_token = new lexical_syntax_token("EOL", "EOL");
                 return $tested_token;
             }
             elseif(ctype_space(global_variables_main::$character))
             {
                 remove_comments();
             }
             else
             {
                 $repeat_test = false;
             }
         }
    }
    return $tested_token = false;
}

/// function control if header is OK
/// it return token
function header_my()
{
    $token_to_return = new lexical_syntax_token(".IPPcode19", ".IPPcode19");
    global_variables_main::$was_header = true;
    return $token_to_return;
}


/// function make lexical analysis
/// it use regexes
/// function returns token
function lexical_analyze_and_get_token()
{
    global_variables_main::$word = '';
    $returned_token = remove_whitespaces();

    while($returned_token == false)
    {
        if(ctype_space(global_variables_main::$character ) && global_variables_main::$control === true)
        {
            if (global_variables_main::$word === ".IPPcode19")
            {
                $returned_token = header_my();
            }
            elseif(preg_match('/^(int@[+-]?[0-9]+)|(bool@(true|false))|(string@([a-z]|[A-Z]|[0-9]|\\[0-9]{3}|[\\\_\-\$\&\%\*\<\>\/\@])*)$/',
                    global_variables_main::$word) == 1 && global_variables_main::$control === true)
            {
                $returned_token = new lexical_syntax_token("symbol", global_variables_main::$word);
                if(preg_match ('/^int@[+-]?[0-9]+$/', global_variables_main::$word) == 1)
                {
                    $returned_token->arbiter = "int";
                }
                elseif(preg_match ('/^bool@(true|false)$/', global_variables_main::$word) == 1)
                {
                    $returned_token->arbiter = "bool";
                }
                elseif(preg_match ('/^string@([a-z]|[A-Z]|[0-9]|\\[0-9]{3}|[\\\_\-\$\&\%\*\<\>\/\@])*$/', global_variables_main::$word) == 1)
                {
                    $returned_token->arbiter = "string";
                }
            }
            elseif(preg_match('/^(GF|LF|TF)@([a-z]|[A-Z]|[\_\-\$\&\%\*\!\?])([a-z]|[A-Z]|[0-9]|[\_\-\$\&\%\*\!\?])*$/', global_variables_main::$word) == 1)
            {
                $returned_token = new lexical_syntax_token("var", global_variables_main::$word);
            }
            elseif(preg_match('/^(int|string|bool)$/', global_variables_main::$word) == 1)
            {
                $returned_token = new lexical_syntax_token( "type", global_variables_main::$word);
            }
            elseif(($returned_token = check_if_is_keyword()) != false)
            {
                ;
            }
            else if(preg_match('/^([a-z]|[A-Z]|[\_\-\$\&\%\*\!\@])([a-z]|[A-Z]|[0-9]|[\_\-\$\&\%\*\!\@])*$/', global_variables_main::$word) == 1)
            {
                $returned_token = new lexical_syntax_token( "label", global_variables_main::$word);
            }
            else
             {
                 end_with_error("Lexical error3", 22);
             }
        }
        else
        {
            global_variables_main::$word = global_variables_main::$word . global_variables_main::$character;
            remove_comments();
        }
    }
    if(global_variables_main::$was_header === false)
    {
        end_with_error("Header is missing", 21);
    }
    return $returned_token;
}

function instruction_pushs()
{
    global_variables_main::$bug = true;
    $arg1 = checking_correctness_of_token('symbol', '');
    global_variables_main::$bug = false;
    checking_correctness_of_token('EOL', 'EOL');
    print_output_xml_file_to_stdout("PUSHS", $arg1);
    instructions_switch();
}

#-----------------------------------------------------------------------------------------------------------------
#-----------------------------------------SYNTAX FUNCTIONS-------------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------


///check if keyword and tokens keyword are same
/// parameters: --keyword for compare
/// --string for compare
function checking_correctness_of_token($keyword_for_compare, $string_for_compare)
{

    if(global_variables_main::$bug === true)
    {
        $keyword_for_compare = "symbol";
    }
    $token = lexical_analyze_and_get_token();

    $checked_keyword = $token->keyword;

    if ($string_for_compare != '' && strcmp($string_for_compare, $token->string) != 0)
    {
        end_with_error("Syntax error2", 23);
    }
    elseif ($keyword_for_compare != '' && strcmp($keyword_for_compare, $checked_keyword) != 0)
    {
        if($keyword_for_compare != 'symbol' || $checked_keyword != 'var' )
        {
            if($keyword_for_compare === "label")
            {
                foreach (global_variables_main::$keywords as $keywords_member)
                {
                    if(strcasecmp($checked_keyword, $keywords_member) == 0)
                    {
                        $token->keyword = "label";
                        return $token;
                    }
                }
                end_with_error("Syntax error11", 23);
            }
            else
            {
                end_with_error("Syntax error12", 23);
            }
        }
    }
    return $token;
}


/// beginning of syntax analysis
/// called from main
function start_of_syntax_analyzer() {
    checking_correctness_of_token('', '.IPPcode19');
    checking_correctness_of_token('EOL', 'EOL');
    instructions_switch();
    checking_correctness_of_token('EOF', 'EOF');
}


function frames($upper_string)
{
    checking_correctness_of_token('EOL', 'EOL');

    switch($upper_string)
    {
        case "CREATEFRAME":
            print_output_xml_file_to_stdout("CREATEFRAME");
            break;
        case "PUSHFRAME" :
            print_output_xml_file_to_stdout("PUSHFRAME");
            break;
        case "POPFRAME" :
            print_output_xml_file_to_stdout("POPFRAME");
            break;
        default:
            end_with_error("Internal error", 99);
    }
    instructions_switch();
}

function instruction_move($switcher) {
    $arg1 = checking_correctness_of_token('var', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($switcher === "MOVE")
    {
        print_output_xml_file_to_stdout("MOVE", $arg1, $arg2);
    }
    else
    {
        print_output_xml_file_to_stdout("INT2CHAR", $arg1, $arg2);
    }
    instructions_switch();
}



function instruction_defvar_pops($switcher) {
    $arg1 = checking_correctness_of_token('var', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($switcher === "DEFVAR")
    {
        print_output_xml_file_to_stdout("DEFVAR", $arg1);
    }
    else
    {
        print_output_xml_file_to_stdout("POPS", $arg1);
    }
    instructions_switch();

}


function add_sub_mul_idiv($operator) {
    $arg1 = checking_correctness_of_token('var', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    $arg3 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');

    switch($operator)
    {
        case "ADD":
            print_output_xml_file_to_stdout("ADD", $arg1, $arg2, $arg3);
        break;
        case "SUB":
            print_output_xml_file_to_stdout("SUB", $arg1, $arg2, $arg3);
            break;
        case "MUL":
            print_output_xml_file_to_stdout("MUL", $arg1, $arg2, $arg3);
            break;
        case "IDIV":
            print_output_xml_file_to_stdout("IDIV", $arg1, $arg2, $arg3);
            break;
    }
    instructions_switch();
}


function lt_gt_eq_and_or_not($switcher) {
    $arg1 = checking_correctness_of_token('var', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    $arg3 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    switch($switcher)
    {
        case "LT":
            print_output_xml_file_to_stdout("LT", $arg1, $arg2, $arg3);
        break;
        case "GT":
            print_output_xml_file_to_stdout("GT", $arg1, $arg2, $arg3);
        break;
        case "EQ":
            print_output_xml_file_to_stdout("EQ", $arg1, $arg2, $arg3);
        break;
        case "AND":
            print_output_xml_file_to_stdout("AND", $arg1, $arg2, $arg3);
        break;
        case "OR":
            print_output_xml_file_to_stdout("OR", $arg1, $arg2, $arg3);
        break;
    }
    instructions_switch();
}

function read_type($switcher) {
    $arg1 = checking_correctness_of_token('var', '');


    if($switcher === "READ")
    {
        $arg2 = checking_correctness_of_token('type', '');
        print_output_xml_file_to_stdout("READ", $arg1, $arg2);
    }
    else
    {
        $arg2 = checking_correctness_of_token('symbol', '');
        print_output_xml_file_to_stdout("TYPE", $arg1, $arg2);
    }
    checking_correctness_of_token('EOL', 'EOL');
    instructions_switch();
}


function string_to_int_concatenate($disc) {
    $arg1 = checking_correctness_of_token('var', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    $arg3 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($disc === "STRI2INT")
    {
        print_output_xml_file_to_stdout("STRI2INT", $arg1, $arg2, $arg3);
    }
    else
    {
        print_output_xml_file_to_stdout("CONCAT", $arg1, $arg2, $arg3);
    }
    instructions_switch();
}


function CHARS($char)
{
    $arg1 = checking_correctness_of_token('var', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    $arg3 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($char === "GETCHAR")
    {
        print_output_xml_file_to_stdout("GETCHAR", $arg1, $arg2, $arg3);
    }
    else {
        print_output_xml_file_to_stdout("SETCHAR", $arg1, $arg2, $arg3);
    }
    instructions_switch();
}
//
function jump($jump_type) {
    $arg1 = checking_correctness_of_token('label', '');
    $arg2 = checking_correctness_of_token('symbol', '');
    $arg3 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    global_variables_main::$jumps_counter++;
    if($jump_type === "JUMPIFEQ")
    {
        print_output_xml_file_to_stdout("JUMPIFEQ", $arg1, $arg2, $arg3);
    }
    else
    {
        print_output_xml_file_to_stdout("JUMPIFNEQ", $arg1, $arg2, $arg3);
    }
    instructions_switch();
}
#
function label_jump($jumper) {
    $arg1 = checking_correctness_of_token('label', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($jumper === "LABEL")
    {
        print_output_xml_file_to_stdout("LABEL", $arg1);
        global_variables_main::$labels_counter++;
    }
    else {
        print_output_xml_file_to_stdout("JUMP", $arg1);
        global_variables_main::$jumps_counter++;
    }
    instructions_switch();
}

///
function print_exit($switcher) {
    $arg1 = checking_correctness_of_token('symbol', '');
    checking_correctness_of_token('EOL', 'EOL');
    if($switcher === "DPRINT")
    {
        print_output_xml_file_to_stdout("DPRINT", $arg1);
    }
    else
    {
        print_output_xml_file_to_stdout("EXIT", $arg1);
    }
    instructions_switch();
}

/// function give control to  other functions
function instructions_switch()
{
    $token = lexical_analyze_and_get_token();
    $upper_string = strtoupper($token->keyword);
    switch ($upper_string)
    {
        case "MOVE":
        case "INT2CHAR":
            instruction_move($upper_string);
            break;
        case "CREATEFRAME":
        case "PUSHFRAME":
        case "POPFRAME":
            frames($upper_string);
            break;
        case "CALL":
        case "PUSHS":
        case "STRLEN":
            switcher($upper_string);
            break;
        case "RETURN":
            checking_correctness_of_token('EOL', 'EOL');
            print_output_xml_file_to_stdout("RETURN");
            instructions_switch();
            break;
        case "ADD":
        case "SUB":
        case "MUL":
        case "IDIV":
            add_sub_mul_idiv($upper_string);
            break;
        case "DEFVAR":
        case "POPS":
            instruction_defvar_pops($upper_string);
            break;
        case "LT":
        case "GT":
        case "EQ":
        case "AND":
        case "OR":
            lt_gt_eq_and_or_not($upper_string);
            break;
        case "NOT":
            $arg1 = checking_correctness_of_token('var', '');
            $arg3 = checking_correctness_of_token('symbol', '');
            print_output_xml_file_to_stdout("NOT", $arg1, $arg3);
            checking_correctness_of_token('EOL', 'EOL');
            instructions_switch();
            break;
        case "STRI2INT":
        case "CONCAT":
            string_to_int_concatenate($upper_string);
            break;
        case "READ":
        case "TYPE":
            read_type($upper_string);
            break;
        case "WRITE":
            $arg1 = checking_correctness_of_token('symbol', '');
            checking_correctness_of_token('EOL', 'EOL');
            print_output_xml_file_to_stdout("WRITE", $arg1);
            instructions_switch();
            break;
        case "GETCHAR":
        case "SETCHAR":
            CHARS($upper_string);
            break;
        case "LABEL":
        case "JUMP":
            label_jump($upper_string);
            break;
        case "JUMPIFEQ":
        case "JUMPIFNEQ":
            jump($upper_string);
            break;
        case "DPRINT":
        case "EXIT":
            print_exit($upper_string);
            break;
        case "BREAK":
            checking_correctness_of_token('EOL', 'EOL');
            print_output_xml_file_to_stdout("BREAK");
            instructions_switch();
            break;
        case "EOL":
            instructions_switch();
            break;
        default:
            ;
    }
}

/// function switches
function switcher ($switchers)
{
    switch($switchers)
    {
        case "CALL":
            $arg1 = checking_correctness_of_token('label', '');
            checking_correctness_of_token('EOL', 'EOL');
            print_output_xml_file_to_stdout("CALL", $arg1);
            instructions_switch();
            break;
        case "PUSHS":
            instruction_pushs();
            break;
        default:
            $arg1 = checking_correctness_of_token('var', '');
            $arg2 = checking_correctness_of_token('symbol', '');
            checking_correctness_of_token('EOL', 'EOL');
            print_output_xml_file_to_stdout("STRLEN", $arg1, $arg2);
            instructions_switch();
            break;
    }
}

/// function print output
/// XML to standard output
function print_output_xml_file_to_stdout()
{
        $xml_name = func_get_arg(0);
        $add_XML_instruction = global_variables_main::$output->createElement('instruction');
        global_variables_main::$xml_count ++;
        $add_XML_instruction->setAttribute('order', global_variables_main::$loc_counter);
        $add_XML_instruction->setAttribute('opcode', $xml_name);
        $int_i = 1;
        while( $int_i < func_num_args() )
        {
            $xml_arg_token_argument = func_get_arg($int_i);
            switch ($xml_arg_token_argument->keyword)
            {
                case "var":
                case "label":
                case "type":
                    $argument = switch_add_to_XML($xml_arg_token_argument, $int_i);
                    break;
                case "symbol":
                    if($xml_arg_token_argument->arbiter === "int")
                    {
                        $edited_token_string = substr($xml_arg_token_argument->str, 4);
                        global_variables_main::$xml_count ++;
                        $argument = global_variables_main::$output->createElement("arg$int_i", $edited_token_string);
                        $argument->setAttribute('type', 'int');
                    }
                    elseif ($xml_arg_token_argument->arbiter === "string")
                    {
                        $edited_token_string = substr($xml_arg_token_argument->str, 7);
                        global_variables_main::$xml_count--;
                        $argument = global_variables_main::$output->createElement("arg$int_i", $edited_token_string);
                        $argument->setAttribute('type', 'string');
                    }
                    elseif($xml_arg_token_argument->arbiter === "bool")
                    {
                        $edited_token_string = substr($xml_arg_token_argument->str, 5);
                        global_variables_main::$xml_count ++;
                        $argument = global_variables_main::$output->createElement("arg$int_i", $edited_token_string);
                        $argument->setAttribute('type', 'bool');
                    }
                    else
                    {
                        end_with_error("Internal XML error", 99);
                    }
                    break;

                default:
                    end_with_error("Internal XML error", 99);
            }
            $add_XML_instruction->appendChild($argument);
            $int_i++;
        }
        global_variables_main::$output_program->appendChild($add_XML_instruction);
}


/// function helps printing XML
function switch_add_to_XML($argument_token, $i)
{
    $ument_argument = global_variables_main::$output->createElement("arg$i", $argument_token->str);
    $t = 'type';
    switch ($argument_token->keyword)
    {

        case "var":
            $ument_argument->setAttribute($t, 'var');
            break;
        case "label":
            $ument_argument->setAttribute($t, 'label');
            break;
        case "type":
            $ument_argument->setAttribute($t, 'type');
            break;
        default:
            end_with_error("Internal XML error", 99);
    }
    return $ument_argument;
}

#-----------------------------------------------------------------------------------------------------------------
#----------------------------------------------------MAIN-------------------------------------------------------
#-----------------------------------------------------------------------------------------------------------------



global_variables_main::$standard_input = fopen('php://stdin', 'r');
init_XML();

$ret_val = check_arguments($argc, $argv);

if($ret_val === 10)
{
    end_with_error("bad arguments error", 10);
}
elseif($ret_val === 12)
{
    end_with_error("output file error", 12);
}
elseif($ret_val === 0)
{
    start_of_syntax_analyzer();
    if(global_variables_main::$process_loc === true || global_variables_main::$process_comments === true ||
        global_variables_main::$process_jumps === true || global_variables_main::$process_labels === true)
    {

        if($argv[1] === "--loc" || $argv[1] === "--comments" || $argv[1] === "--labels" || $argv[1] === "--jumps" )
        {
            end_with_error("bad arguments error", 10);
        }
        else
        {
            process_stats($argv);
        }
    }
}
else
{
    end_with_error("Internal  error", 99);
}

close_XML();

return 0;
