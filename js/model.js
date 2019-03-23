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

import {checkSelect, checkInputRegex, disableForm} from "./function_form.js";

document.addEventListener('DOMContentLoaded', function ()
{

    const regexName = new RegExp("^[0-9a-zA-Z_\\- ]{1,30}$");

    let checkField = [];
    let checkField2 = [];

    checkField['add_company'] = checkInputRegex('add_company', regexName);
    document.getElementById("add_company").addEventListener("keyup", function (){
        checkField['add_company'] = checkInputRegex('add_company', regexName);
        disableForm(checkField, 'button_add_company');
    }, false);

    if(document.getElementById('add_model'))
    {
        checkField2['add_model'] = checkInputRegex('add_model', regexName);
        document.getElementById("add_model").addEventListener("keyup", function (){
            checkField2['add_model'] = checkInputRegex('add_model', regexName);
            disableForm(checkField2, 'button_add_model');
        }, false);

        let company = document.getElementById('company');
        let switch_list = JSON.parse(company.dataset.info);

        checkField2['company'] = checkSelect(company, Object.keys(switch_list));
        document.getElementById("company").addEventListener("change", function (){
            checkField2['company'] = checkSelect(company, Object.keys(switch_list));
            disableForm(checkField2, 'button_add_model');
        }, {passive: true});
    }

    disableForm(checkField, 'button_add_company');
    disableForm(checkField2, 'button_add_model');

}, false);