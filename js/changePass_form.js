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

'use strict';

import {checkInputRegex, checkSameValue, disableForm} from "./function_form.js";

document.addEventListener('DOMContentLoaded', function ()
{
    const regexPassword = new RegExp("^[0-9a-zA-Z]{4,20}$");

    /////////////
    //Check form
    /////////////

    let checkField = [];

    checkField['password'] = checkInputRegex('password', regexPassword);
    checkField['confirm_password'] = checkInputRegex('confirm_password', regexPassword);
    if(checkField['password'] === true)
    {
        checkField['password_equal'] = checkSameValue('password', 'confirm_password');
    }
    disableForm(checkField, 'changePass');

    document.getElementById("password").addEventListener("keyup", function (){
        checkField['password'] = checkInputRegex('password', regexPassword);
        if(checkField['password'] === true)
        {
            checkField['password_equal'] = checkSameValue('password', 'confirm_password');
        }
        disableForm(checkField, 'changePass');
    }, false);
    document.getElementById("confirm_password").addEventListener("keyup", function (){
        checkField['confirm_password'] = checkInputRegex('confirm_password', regexPassword);
        if(checkField['password'] === true)
        {
            checkField['password_equal'] = checkSameValue('password', 'confirm_password');
        }
        disableForm(checkField, 'changePass');
    }, false);

}, false);