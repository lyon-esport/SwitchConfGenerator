<?php
// ----------------------------------------------------------------------------
// Copyright © Lyon e-Sport, 2018
//
// Contributeur(s):
//     * Ortega Ludovic - ludovic.ortega@lyon-esport.fr
//     * Etienne Guilluy - etienne.guilluy@lyon-esport.fr
//
// Ce logiciel, SwitchConfGenerator, est un programme informatique servant à générer
// des configurations de switch via une interface web.
//
// Ce logiciel est régi par la licence CeCILL soumise au droit français et
// respectant les principes de diffusion des logiciels libres. Vous pouvez
// utiliser, modifier et/ou redistribuer ce programme sous les conditions
// de la licence CeCILL telle que diffusée par le CEA, le CNRS et l'INRIA
// sur le site "http://www.cecill.info".
//
// En contrepartie de l'accessibilité au code source et des droits de copie,
// de modification et de redistribution accordés par cette licence, il n'est
// offert aux utilisateurs qu'une garantie limitée.  Pour les mêmes raisons,
// seule une responsabilité restreinte pèse sur l'auteur du programme,  le
// titulaire des droits patrimoniaux et les concédants successifs.
//
// A cet égard  l'attention de l'utilisateur est attirée sur les risques
// associés au chargement,  à l'utilisation,  à la modification et/ou au
// développement et à la reproduction du logiciel par l'utilisateur étant
// donné sa spécificité de logiciel libre, qui peut le rendre complexe à
// manipuler et qui le réserve donc à des développeurs et des professionnels
// avertis possédant  des  connaissances  informatiques approfondies.  Les
// utilisateurs sont donc invités à charger  et  tester  l'adéquation  du
// logiciel à leurs besoins dans des conditions permettant d'assurer la
// sécurité de leurs systèmes et ou de leurs données et, plus généralement,
// à l'utiliser et l'exploiter dans les mêmes conditions de sécurité.
//
// Le fait que vous puissiez accéder à cet en-tête signifie que vous avez
// pris connaissance de la licence CeCILL, et que vous en avez accepté les
// termes.
// ----------------------------------------------------------------------------

session_start();

//Get config
require_once '../functions/get_config.php';
try
{
    $CONFIG = get_config("../config/");
}
catch (Exception $e)
{
    echo "Error : " . $e->getMessage();
    die();
}

//Connect to Bdd
use \Project\Bdd;
require_once '../class/Bdd.php';
try
{
    $BDD = new Bdd('../');
}
catch (Exception $e)
{
    echo "Error : " . $e->getMessage() . "<br> <a href='" . $CONFIG["BASE_URL"] . "index.php'>Go back to homepage</a>";
    die();
}

//Get messages notification
require_once '../functions/message.php';
$message = get_message();

//CSRF protection
require_once '../functions/csrf.php';

//Load Project class
use \Project\Autoloader;
use \Project\SwitchConfGenerator\{Authorization, Snmp, Network, Vlan};
require_once '../class/Autoloader.php';
Autoloader::register();

if(!check_csrf('sw_config'))
{
    create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

if(isset($_POST['company']) && !empty($_POST['company']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['company']))
{
    $company = $_POST['company'];
}
else
{
    create_message([['title' => 'Error !', 'content' => 'Company field is not filled correctly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

if(isset($_POST['model']) && !empty($_POST['model']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['model']))
{
    $model = str_replace('-', '_', $_POST['model']);
    $path_template = $model;
}
else
{
    create_message([['title' => 'Error !', 'content' => 'Model field is not filled correctly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

if(isset($_POST['template']) && !empty($_POST['template']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['template']))
{
    $template = $_POST['template'];
    try
    {
        $template_id = $BDD->get_template_id($template);
        $setting = $BDD->get_setting($template_id);
        $port_config = $BDD->get_port_config($template_id);
    }
    catch(Exception $e)
    {
        create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'index.php');
        die();
    }

    if(!isset($setting) || empty($setting))
    {
        create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'index.php');
        die();
    }

    if(!isset($port_config) || empty($port_config))
    {
        create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'index.php');
        die();
    }
}
else
{
    create_message([['title' => 'Error !', 'content' => 'Template field is not filled correctly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

if(isset($_POST['sw_number']) && !empty($_POST['sw_number']))
{
    $sw_number_int = intval($_POST['sw_number'], 10);
    if($sw_number_int >= 1 && $sw_number_int <= 254)
    {
        $sw_number = strval($sw_number_int);
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Switch number field is not filled correctly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'index.php');
        die();
    }
}
else
{
    create_message([['title' => 'Error !', 'content' => 'Switch number field is not filled correctly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

$file_class_company = '../class/SwitchConfGenerator/'.$company.'/'.$company.'.php';
$file_class_acl = '../class/SwitchConfGenerator/'.$company.'/Acl.php';
$file_class_crypto = '../class/SwitchConfGenerator/'.$company.'/Crypto.php';
$file_class_model = '../class/SwitchConfGenerator/'.$company.'/Model/'.$company.'_'.$model.'.php';

$class_name_model = 'Project\SwitchConfGenerator\\'.$company.'\Model\\'.$company.'_'.$model;
$class_name_acl = 'Project\SwitchConfGenerator\\'.$company.'\\Acl';
$class_name_crypto = 'Project\SwitchConfGenerator\\'.$company.'\\Crypto';

if(!file_exists($file_class_model))
{
    create_message([['title' => 'Error !', 'content' => $company.'_'.$model.'.php class file is missing', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

if(isset($class_name_model::$model) && !empty($class_name_model::$model))
{
    $path_template = $class_name_model::$model;
}
$file_class_template = '../templates/project/'.$company.'/'.$company.'_'.$path_template.'.twig';

//Check if file class (company, model, acl) and template model exists
if(!file_exists($file_class_company) || !file_exists($file_class_acl) || !file_exists($file_class_crypto) || !file_exists($file_class_template))
{
    $content = "";
    if(!file_exists($file_class_company))
    {
        $content = $company.'.php class file is missing';
    }
    if(!file_exists($file_class_acl))
    {
        if(empty($content))
        {
            $content = $content.'Acl.php class file is missing';
        }
        else
        {
            $content = $content.', Acl.php class file is missing';
        }
    }
    if(!file_exists($file_class_crypto))
    {
        if(empty($content))
        {
            $content = $content.'Crypto.php class file is missing';
        }
        else
        {
            $content = $content.', Crypto.php class file is missing';
        }
    }
    if(!file_exists($file_class_template))
    {
        if(empty($content))
        {
            $content = $content.$company.'_'.$path_template.'.twig template file is missing';
        }
        else
        {
            $content = $content.', '.$company.'_'.$path_template.'.twig template file is missing';
        }
    }
    $content = $content.' doesn\'t exists /!\\';

    create_message([['title' => 'Error !', 'content' => $content, 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'index.php');
    die();
}

//ACL
$acl = new $class_name_acl('1', ['172.16.99.1'], ['255.255.192.0'], ['permit']);

//SWITCH
$switch = new $class_name_model(
    strtoupper('SW-'.$template.'-'.$sw_number),
    new Network($setting['default_gateway_ip'], $setting['default_gateway_netmask']),
    $setting['banner_motd'],
    $setting['spanning_tree'],
    new Snmp($setting['snmp_community'], $setting['snmp_permissions']),
    new Authorization($setting['auth_username'], new $class_name_crypto($setting['auth_password'])),
    ["vlan_array" => $BDD->get_vlans($template_id), "resume_port" => $class_name_model::$resumePort, "port_config" => $port_config, "switch_number" => $sw_number],
    $acl
);

//Load Twig
require_once '../vendor/autoload.php';
$loader = new Twig_Loader_Filesystem('../templates/');
$twig = new Twig_Environment($loader, array(
    'debug' => $CONFIG["DEBUG"],
    'cache' => '../templates/twig_compilation_cache'
));

echo $twig->render('site/page_new_config.twig', array(
    'index_path' => '../',
    'pages_path' => '',
    'images_path' => '../',
    'css_path' => '../',
    'js_path' => '../',
    'site_template_path' => 'site/',
    'active' => ['index' => 'is-active'],
    'messages' => $message,
    'template_model_path' => 'project/'.$company.'/'.$company.'_'.$path_template.'.twig',
    'company' => $company,
    'model' => str_replace('_', '-', $model),
    'switch' => $switch,
));