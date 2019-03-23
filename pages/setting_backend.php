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

require_once '../functions/csrf.php';
require_once '../functions/check_regex.php';
require_once '../functions/message.php';

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

if(!check_csrf('setting_csrf'))
{
    create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
    die();
}

if(!isset($_POST['new']))
{
    if(isset($_POST['choice']) && !empty($_POST['choice']))
    {
        //ID of template
        if(isset($_POST['id']) && !empty($_POST['id']) && ctype_digit($_POST['id']))
        {
            $id = $_POST['id'];
            try
            {
                $setting = $BDD->get_setting($id);
            }
            catch (Exception $e)
            {
                create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
                header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
                die();
            }

            if(!isset($setting) || empty($setting))
            {
                create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
                header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
                die();
            }
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
            die();
        }

        //Choice edit, delete, save, change password of template
        if($_POST['choice'] === 'delete')
        {
            delete_setting($BDD, $CONFIG["BASE_URL"], $id);
        }
        elseif($_POST['choice'] === 'edit')
        {
            header('Location: '.$CONFIG["BASE_URL"]."pages/setting_edit.php?id=".$id);
            die();
        }
        elseif($_POST['choice'] === 'save')
        {
            save_setting($BDD, $CONFIG["BASE_URL"], $id);
        }
        elseif($_POST['choice'] === 'changePass')
        {
            changePass($BDD, $CONFIG["BASE_URL"], $id);
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
            die();
        }

    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'pages/setting.php');
        die();
    }
}
else
{
    save_setting($BDD, $CONFIG["BASE_URL"], "0");
}

function save_setting($BDD, $BASE_URL, $id)
{
    $setting = array();

    $vlan_list = "";
    $distribution_port_untagged_list = "";
    $distribution_port_tagged_list = "";
    $uplink_port_untagged_list = "";
    $uplink_port_tagged_list = "";

    $inputs = [
        "template_name" => [
            "input_name" => "template_name",
            "regex" => "/^[0-9a-zA-Z_\- ]{1,30}$/",
            "custom_error_message" => "Template name field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "snmp_community" => [
            "input_name" => "snmp_community",
            "regex" => "/^[0-9a-zA-Z]{3,20}$/",
            "custom_error_message" => "SNMP community field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "auth_username" => [
            "input_name" => "auth_username",
            "regex" => "/^[0-9a-zA-Z_\-]{4,20}$/",
            "custom_error_message" => "Authorization username field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "banner_motd" => [
            "input_name" => "banner_motd",
            "regex" => "/^[0-9a-zA-Z@!*\n\r -]{4,400}$/",
            "custom_error_message" => "Banner motd field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "default_gateway_ip" => [
            "input_name" => "default_gateway_ip",
            "regex" => "/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$/",
            "custom_error_message" => "Default gateway IP field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "default_gateway_netmask" => [
            "input_name" => "default_gateway_netmask",
            "regex" => "/^(((255\.){3}(255|254|252|248|240|224|192|128|0+))|((255\.){2}(255|254|252|248|240|224|192|128|0+)\.0)|((255\.)(255|254|252|248|240|224|192|128|0+)(\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\.0+){3}))$/",
            "custom_error_message" => "Default gateway netmask field is not filled properly /!\ ",
            "empty_check" => True
        ],
        "vlan_list" => [
            "input_name" => "vlan_list",
            "regex" => "/^[0-9,]*$/",
            "custom_error_message" => "VLAN is not filled properly /!\ ",
            "empty_check" => False
        ],
        "distribution_port_untagged" => [
            "input_name" => "distribution_port_untagged",
            "regex" => "/^[0-9,]*$/",
            "custom_error_message" => "VLAN is not filled properly /!\ ",
            "empty_check" => False
        ],
        "distribution_port_tagged" => [
            "input_name" => "distribution_port_tagged",
            "regex" => "/^[0-9,]*$/",
            "custom_error_message" => "VLAN is not filled properly /!\ ",
            "empty_check" => False
        ],
        "uplink_port_untagged" => [
            "input_name" => "uplink_port_untagged",
            "regex" => "/^[0-9,]*$/",
            "custom_error_message" => "VLAN is not filled properly /!\ ",
            "empty_check" => False
        ],
        "uplink_port_tagged" => [
            "input_name" => "uplink_port_tagged",
            "regex" => "/^[0-9,]*$/",
            "custom_error_message" => "VLAN is not filled properly /!\ ",
            "empty_check" => False
        ]
    ];

    $selects = [
        "template_name" => [
            "input_name" => "snmp_permissions",
            "possible_values" => ["RO", "RW"],
            "custom_error_message" => "SNMP permissions field is not filled properly /!\ "
        ],
        "spanning_tree" => [
            "input_name" => "spanning_tree",
            "possible_values" => ["No", "Yes"],
            "custom_error_message" => "Spanning tree field is not filled properly /!\ "
        ],
        "uplink_port_position" => [
            "input_name" => "uplink_port_position",
            "possible_values" => ["First ports", "Last ports"],
            "custom_error_message" => "Uplink port position field is not filled properly /!\ "
        ]
    ];

    foreach ($inputs as &$input)
    {
        if(isset($_POST[$input["input_name"]]) && preg_match_all($input["regex"], $_POST[$input["input_name"]]))
        {
            if($input["empty_check"] && empty($_POST[$input["input_name"]]))
            {
                create_message([['title' => 'Error !', 'content' => $input["custom_error_message"], 'color' => 'error', 'delete' => true, 'container' => true]]);
                header('Location: '.$BASE_URL.'pages/setting.php');
                die();
            }
            $setting[$input["input_name"]] = $_POST[$input["input_name"]];
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => $input["custom_error_message"], 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }
    }

    foreach ($selects as &$select)
    {
        if(isset($_POST[$select["input_name"]]) && !empty($_POST[$select["input_name"]]) && in_array($_POST[$select["input_name"]], $select["possible_values"]))
        {
            $setting[$select["input_name"]] = $_POST[$select["input_name"]];
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => $select["custom_error_message"], 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }
    }

    if(isset($_POST['uplink_port_nb']) && !empty($_POST['uplink_port_nb']))
    {
        $uplink_port_nb_int = intval($_POST['uplink_port_nb'], 10);
        if($uplink_port_nb_int >= 0 && $uplink_port_nb_int <= 5)
        {
            $setting['uplink_port_nb'] = $uplink_port_nb_int;
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'Uplink port number field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }
    }
    elseif(isset($_POST['uplink_port_nb']) && empty($_POST['uplink_port_nb']))
    {
        $setting['uplink_port_nb'] = 0;
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Uplink port number field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/setting.php');
        die();
    }

    if(!empty($setting["vlan_list"]))
    {
        $vlan_list_explode = explode(",", $setting["vlan_list"]);
        foreach($vlan_list_explode as &$vlan)
        {
            if(!empty($vlan))
            {
                $setting["vlan"][$vlan]["vlan_id"] = $vlan;

                if(isset($_POST["vlan_".$vlan."_name"]) && !empty($_POST["vlan_".$vlan."_name"]) && preg_match_all($inputs["auth_username"]["regex"], $_POST["vlan_".$vlan."_name"]))
                {
                    $setting["vlan"][$vlan]["name"] = $_POST["vlan_".$vlan."_name"];
                }
                else
                {
                    create_message([['title' => 'Error !', 'content' => "VLAN ".$vlan." name is not filled properly /!\ ", 'color' => 'error', 'delete' => true, 'container' => true]]);
                    header('Location: '.$BASE_URL.'pages/setting.php');
                    die();
                }

                if(isset($_POST["vlan_".$vlan."_ip"]) && preg_match_all($inputs["default_gateway_ip"]["regex"], $_POST["vlan_".$vlan."_ip"]))
                {
                    $setting["vlan"][$vlan]["ip"] = $_POST["vlan_".$vlan."_ip"];
                }
                elseif(empty($_POST["vlan_".$vlan."_ip"]))
                {
                    $setting["vlan"][$vlan]["ip"] = "";
                }
                else
                {
                    create_message([['title' => 'Error !', 'content' => "VLAN ".$vlan." IP is not filled properly /!\ ", 'color' => 'error', 'delete' => true, 'container' => true]]);
                    header('Location: '.$BASE_URL.'pages/setting.php');
                    die();
                }

                if(isset($_POST["vlan_".$vlan."_netmask"]) && preg_match_all($inputs["default_gateway_netmask"]["regex"], $_POST["vlan_".$vlan."_netmask"]))
                {
                    $setting["vlan"][$vlan]["netmask"] = $_POST["vlan_".$vlan."_netmask"];
                }
                elseif(empty($_POST["vlan_".$vlan."_netmask"]))
                {
                    $setting["vlan"][$vlan]["netmask"] = "";
                }
                else
                {
                    create_message([['title' => 'Error !', 'content' => "VLAN ".$vlan." netmask is not filled properly /!\ ", 'color' => 'error', 'delete' => true, 'container' => true]]);
                    header('Location: '.$BASE_URL.'pages/setting.php');
                    die();
                }

                $port_config_assignation["distribution_port_untagged"] = in_array($setting["vlan"][$vlan]["vlan_id"], explode(",", $setting["distribution_port_untagged"])) ? True : False;
                $port_config_assignation["distribution_port_tagged"] = in_array($setting["vlan"][$vlan]["vlan_id"], explode(",", $setting["distribution_port_tagged"])) ? True : False;
                $port_config_assignation["uplink_port_untagged"] = in_array($setting["vlan"][$vlan]["vlan_id"], explode(",", $setting["uplink_port_untagged"])) ? True : False;
                $port_config_assignation["uplink_port_tagged"] = in_array($setting["vlan"][$vlan]["vlan_id"], explode(",", $setting["uplink_port_tagged"])) ? True : False;

                $setting["vlan"][$vlan]["port_config_assignation"] = json_encode($port_config_assignation);
            }
        }
    }

    if($id === "0")
    {
        if(isset($_POST['password']) && !empty($_POST['password']) && preg_match_all("/^[0-9a-zA-Z]{4,20}$/", $_POST['password']))
        {
            $setting['password'] = $_POST['password'];
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'New password field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }

        if(isset($_POST['confirm_password']) && !empty($_POST['confirm_password']) && preg_match_all("/^[0-9a-zA-Z]{4,20}$/", $_POST['confirm_password']))
        {
            $setting['confirm_password'] = $_POST['confirm_password'];
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'Confirm new password field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }

        if($setting['password'] === $setting['confirm_password'])
        {
            $password_protected = $setting['password'];
            try
            {
                $info = $BDD->insert_template($setting, $password_protected);
                $message = $info[1];
                $BDD->insert_port_config(-1, "default", $setting['uplink_port_nb'], $setting['uplink_port_position'], $info[0]);
                $BDD->insert_vlans($setting["vlan"], $info[0]);
            }
            catch(Exception $e)
            {
                switch($e->getCode())
                {
                    case 23000:
                        $message = [['title' => 'Error !', 'content' => 'Template name '.$setting['template_name'].' already exist', 'color' => 'error', 'delete' => true, 'container' => true]];
                        break;
                    default:
                        $message = [['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]];
                        break;
                }
            }
            create_message($message);
            header('Location: '.$BASE_URL.'pages/setting.php');
            die();
        }
        else
        {
            create_message([['title' => 'Error !', 'content' => 'Passwords doesn\'t match', 'color' => 'error', 'delete' => true, 'container' => true]]);
            header('Location: '.$BASE_URL.'pages/setting_edit.php?new');
            die();
        }
    }
    else
    {
        try
        {
            $message = $BDD->update_template_general($setting, $id);
            $BDD->update_port_config(-1, "default", $setting['uplink_port_nb'], $setting['uplink_port_position'], $id);
            $BDD->delete_vlans($id);
            $BDD->insert_vlans($setting["vlan"], $id);
        }
        catch (Exception $e)
        {
            switch($e->getCode())
            {
                case 23000:
                    $message = [['title' => 'Error !', 'content' => 'Template name '.$setting['template_name'].' already exist'.$e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
                    break;
                default:
                    $message = [['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]];
                    break;
            }
        }
        create_message($message);
        header('Location: '.$BASE_URL.'pages/setting.php');
        die();
    }
}

function delete_setting($BDD, $BASE_URL, $id)
{
    try
    {
        $message = $BDD->delete_template($id);
    }
    catch (Exception $e)
    {
        $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
    }
    create_message($message);
    header('Location: '.$BASE_URL.'pages/setting.php');
    die();
}

function changePass($BDD, $BASE_URL, $id)
{
    $setting = array();

    if(isset($_POST['password']) && !empty($_POST['password']) && preg_match_all("/^[0-9a-zA-Z]{4,20}$/", $_POST['password']))
    {
        $setting['password'] = $_POST['password'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'New password field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/setting.php');
        die();
    }

    if(isset($_POST['confirm_password']) && !empty($_POST['confirm_password']) && preg_match_all("/^[0-9a-zA-Z]{4,20}$/", $_POST['confirm_password']))
    {
        $setting['confirm_password'] = $_POST['confirm_password'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Confirm new password field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/setting.php');
        die();
    }

    if($setting['password'] === $setting['confirm_password'])
    {
        $password_protected = $setting['password'];
        try
        {
            $message = $BDD->update_template_password($id, $password_protected);
        }
        catch (Exception $e)
        {
            $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
        }
        create_message($message);
        header('Location: '.$BASE_URL.'pages/setting.php');
        die();
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Passwords doesn\'t match', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL."pages/setting_edit.php?id=".$id);
        die();
    }
}
