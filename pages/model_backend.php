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

if(!check_csrf('model_csrf'))
{
    create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
    header('Location: '.$CONFIG["BASE_URL"].'pages/model.php');
    die();
}
if(isset($_POST['choice']) && !empty($_POST['choice']))
{
    if($_POST['choice'] === 'add_company')
    {
        add_company($BDD, $CONFIG["BASE_URL"]);
    }
    elseif($_POST['choice'] === 'add_model')
    {
        add_model($BDD, $CONFIG["BASE_URL"]);
    }
    elseif($_POST['choice'] === 'delete_company')
    {
        remove_company($BDD, $CONFIG["BASE_URL"]);
    }
    elseif($_POST['choice'] === 'delete_model')
    {
        remove_model($BDD, $CONFIG["BASE_URL"]);
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$CONFIG["BASE_URL"].'pages/model.php');
        die();
    }
}

function add_company($BDD, $BASE_URL)
{
    if(isset($_POST['company']) && !empty($_POST['company']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['company']))
    {
        $company_name = $_POST['company'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Company field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/model.php');
        die();
    }
    try
    {
        $message = $BDD->insert_company($company_name);
    }
    catch (Exception $e)
    {
        switch($e->getCode())
        {
            case 23000:
                $message = [['title' => 'Error !', 'content' => 'Company '.$company_name.' already exist', 'color' => 'error', 'delete' => true, 'container' => true]];
                break;
            default:
                $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
                break;
        }
    }
    create_message($message);
    header('Location: '.$BASE_URL.'pages/model.php');
    die();
}

function add_model($BDD, $BASE_URL)
{
    if(isset($_POST['company']) && !empty($_POST['company']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['company']))
    {
        $company_name = $_POST['company'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Company field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/model.php');
        die();
    }

    if(isset($_POST['model']) && !empty($_POST['model']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['model']))
    {
        $model_name = $_POST['model'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Model field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/model.php');
        die();
    }
    try
    {
        $message = $BDD->insert_model($company_name, $model_name);
    }
    catch (Exception $e)
    {
        switch($e->getCode())
        {
            case 23000:
                $message = [['title' => 'Error !', 'content' => 'Model '.$model_name.' already exist', 'color' => 'error', 'delete' => true, 'container' => true]];
                break;
            default:
                $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
                break;
        }
    }
    create_message($message);
    header('Location: '.$BASE_URL.'pages/model.php');
    die();
}

function remove_company($BDD, $BASE_URL)
{
    if(isset($_POST['company']) && !empty($_POST['company']) && preg_match_all("/^[0-9a-zA-Z_\- ]{1,30}$/", $_POST['company']))
    {
        $company_name = $_POST['company'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Company field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/model.php');
        die();
    }
    try
    {
        $message = $BDD->delete_company($company_name);
    }
    catch (Exception $e)
    {
        $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
    }
    create_message($message);
    header('Location: '.$BASE_URL.'pages/model.php');
    die();
}

function remove_model($BDD, $BASE_URL)
{
    if(isset($_POST['model_id']) && !empty($_POST['model_id']) && ctype_digit($_POST['model_id']))
    {
        $model_id = $_POST['model_id'];
    }
    else
    {
        create_message([['title' => 'Error !', 'content' => 'Model field is not filled properly /!\ ', 'color' => 'error', 'delete' => true, 'container' => true]]);
        header('Location: '.$BASE_URL.'pages/model.php');
        die();
    }
    try
    {
        $message = $BDD->delete_model($model_id);
    }
    catch (Exception $e)
    {
        $message = [['title' => 'Error !', 'content' => $e->getMessage(), 'color' => 'error', 'delete' => true, 'container' => true]];
    }
    create_message($message);
    header('Location: '.$BASE_URL.'pages/model.php');
    die();

}