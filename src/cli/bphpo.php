#!/usr/bin/env php
<?php
//========================================================================
// Author:  Pascal KISSIAN
// Resume:  http://pascal.kissian.net
//
// Copyright (c) 2015-2020 Pascal KISSIAN
//
// Published under the MIT License
//          Consider it as a proof of concept!
//          No warranty of any kind.
//          Use and abuse at your own risks.
//========================================================================
namespace BPHPO;

use \PhpParser\Error;
use \PhpParser\ParserFactory;
use \PhpParser\NodeTraverser;
use \PhpParser\PrettyPrinter;

require_once __DIR__ . '/../vendor/autoload.php';

if (isset($_SERVER["SERVER_SOFTWARE"]) && ($_SERVER["SERVER_SOFTWARE"]!="") ){ echo "<h1>Comand Line Interface Only!</h1>"; die; }

$yakpro_po_version = (string) \Jean85\PrettyVersions::getVersion('markhughes/better-php-obfuscator');


require_once __DIR__ . '/../include/get_default_defined_objects.php';     // include this file before defining something....


require_once __DIR__ . '/../include/functions.php';

include      __DIR__ . '/../include/retrieve_config_and_arguments.php';

if ($clean_mode && file_exists("$target_directory/yakpro-po/.yakpro-po-directory") )
{
    if (!$conf->silent) fprintf(STDERR,"Info:\tRemoving directory\t= [%s]%s","$target_directory/yakpro-po",PHP_EOL);
    remove_directory("$target_directory/yakpro-po");
    exit(31);
}


switch($conf->parser_mode)
{
    case 'PREFER_PHP7': $parser_mode = ParserFactory::PREFER_PHP7;  break;
    case 'PREFER_PHP5': $parser_mode = ParserFactory::PREFER_PHP5;  break;
    case 'ONLY_PHP7':   $parser_mode = ParserFactory::ONLY_PHP7;    break;
    case 'ONLY_PHP5':   $parser_mode = ParserFactory::ONLY_PHP5;    break;
    default:            $parser_mode = ParserFactory::PREFER_PHP5;  break;
}

$parser = (new ParserFactory)->create($parser_mode);


$traverser          = new NodeTraverser;

if ($conf->obfuscate_string_literal)    $prettyPrinter      = new MyPrettyPrinter();
else                                    $prettyPrinter      = new PrettyPrinter\Standard();

$t_scrambler = array();
//foreach(array('variable','function','method','property','class','class_constant','constant','label') as $scramble_what)
foreach(array('variable','function_or_class','method','property','class_constant','constant','label') as $scramble_what)
{
    $t_scrambler[$scramble_what] = new Scrambler($scramble_what, $conf, ($process_mode=='directory') ? $target_directory : null);
}
if ($whatis!=='')
{
    if ($whatis[0] == '$') $whatis = substr($whatis,1);
//    foreach(array('variable','function','method','property','class','class_constant','constant','label') as $scramble_what)
    foreach(array('variable','function_or_class','method','property','class_constant','constant','label') as $scramble_what)
    {
        if ( ( $s = $t_scrambler[$scramble_what]-> unscramble($whatis)) !== '')
        {
            switch($scramble_what)
            {
                case 'variable':
                case 'property':
                    $prefix = '$';
                    break;
                default:
                    $prefix = '';
            }
            echo "$scramble_what: {$prefix}{$s}".PHP_EOL;
        }
    }
    exit(32);
}

$traverser->addVisitor(new MyNodeVisitor);

switch($process_mode)
{
    case 'file':
        $obfuscated_str =  obfuscate($source_file);
        if ($obfuscated_str===null) { exit(33);                                       }
        if ($target_file   ===''  ) { echo $obfuscated_str.PHP_EOL.PHP_EOL; exit(34); }
        file_put_contents($target_file,$obfuscated_str);
        exit(0);
    case 'directory':
        if (isset($conf->t_skip) && is_array($conf->t_skip)) foreach($conf->t_skip as $key=>$val) $conf->t_skip[$key] = "$source_directory/$val";
        if (isset($conf->t_keep) && is_array($conf->t_keep)) foreach($conf->t_keep as $key=>$val) $conf->t_keep[$key] = "$source_directory/$val";
        obfuscate_directory($source_directory,"$target_directory/yakpro-po/obfuscated");
        exit(0);
}
