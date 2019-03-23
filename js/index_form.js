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

import {initSelect, checkSelect, checkInputNumber, disableForm} from "./function_form.js";

document.addEventListener('DOMContentLoaded', function ()
{
    /////////////////////////////
    //Get Switch_list and setting
    /////////////////////////////

    if(document.getElementById('switch_list'))
    {
        let data_switchs = document.getElementById('switch_list');
        let switch_list = create_switch_list(JSON.parse(data_switchs.dataset.info));
        let data_setting = document.getElementById('setting');
        let setting = JSON.parse(data_setting.dataset.info);

        let company = document.getElementById('company');
        let model = document.getElementById('model');
        let template = document.getElementById('template');

        let checkField = [];

        initSelect(company, Object.keys(switch_list));
        initSelect(model, switch_list[company.value]);
        initSelect(template, setting);

        checkField['company'] = checkSelect(company, Object.keys(switch_list));
        checkField['model'] = checkSelect(model, switch_list[company.value]);
        checkField['template'] = checkSelect(template, setting);
        checkField['sw_number'] = checkInputNumber('sw_number', 1, 254);

        disableForm(checkField, 'generate');

        document.getElementById("company").addEventListener("change", function () {
            changeSelect(company, model, switch_list);
            checkField['company'] = checkSelect(company, Object.keys(switch_list));
            disableForm(checkField, 'generate');
        }, {passive: true});
        document.getElementById("model").addEventListener("change", function () {
            checkField['model'] = checkSelect(model, switch_list[company.value]);
            disableForm(checkField, 'generate');
        }, {passive: true});
        document.getElementById("template").addEventListener("change", function () {
            checkField['template'] = checkSelect(template, setting);
            disableForm(checkField, 'generate');
        }, {passive: true});
        document.getElementById("sw_number").addEventListener("input", function () {
            checkField['sw_number'] = checkInputNumber('sw_number', 1, 254);
            disableForm(checkField, 'generate');
        }, false);
    }

    function create_switch_list(object)
    {
        let switch_list = [];
        for (let company in object)
        {
            switch_list[company] = [];
            for (let model in object[company])
            {
                switch_list[company].push(object[company][model].name);
            }
        }

        return switch_list;
    }

    function changeSelect(masterSelect, slaveSelect, switch_list)
    {
        slaveSelect.options.length = 0;

        if(Object.keys(switch_list).indexOf(masterSelect.value) !== -1)
        {
            for (let i = 0; i < switch_list[masterSelect.value].length; i++)
            {
                let option = document.createElement("option");
                option.text = switch_list[masterSelect.value][i];
                slaveSelect.appendChild(option);
            }
        }
    }

}, false);