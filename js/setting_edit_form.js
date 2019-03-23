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

import {checkInputRegex, checkSelect, checkSameValue, checkInputNumber, disableForm} from "./function_form.js";

document.addEventListener('DOMContentLoaded', function ()
{
    const regexIP = new RegExp("^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])$");
    const regexNetmask = new RegExp("^(((255\\.){3}(255|254|252|248|240|224|192|128|0+))|((255\\.){2}(255|254|252|248|240|224|192|128|0+)\\.0)|((255\\.)(255|254|252|248|240|224|192|128|0+)(\\.0+){2})|((255|254|252|248|240|224|192|128|0+)(\\.0+){3}))$");
    const regexTemplateName = new RegExp("^[0-9a-zA-Z_\\- ]{1,30}$");
    const regexAuthUsername = new RegExp("^[0-9a-zA-Z_\\-]{4,20}$");
    const regexPassword = new RegExp("^[0-9a-zA-Z]{4,20}$");
    const regexMotd = new RegExp("^[0-9a-zA-Z@!*\\n -]{4,400}$");
    const regexSNMPCommunity = new RegExp("^[0-9a-zA-Z]{3,20}$");

    ///////////////////
    ////CHECK FORM/////
    ///////////////////

    let vlan_list = document.getElementById("vlan_list");
    let init_vlan = JSON.parse(vlan_list.dataset.info);
    let valueSnmpPermissions = ['RO', 'RW'];
    let valueSpanningTree = ['Yes', 'No'];
    let valueAssignement = ['First ports', 'Last ports'];
    let checkField = [];

    let selectSnmp = document.getElementById('snmp_permissions');
    let selectSpanningTree = document.getElementById('spanning_tree');
    let selectUplinkPortPosition = document.getElementById('uplink_port_position');

    if(document.getElementById('password'))
    {
        checkField['password'] = checkInputRegex('password', regexPassword);
        document.getElementById("password").addEventListener("keyup", function (){
            checkField['password'] = checkInputRegex('password', regexPassword);
            if(checkField['password'] === true)
            {
                checkField['password_equal'] = checkSameValue('password', 'confirm_password');
            }
            disableForm(checkField, 'save');
        }, false);
    }
    if(document.getElementById('confirm_password'))
    {
        checkField['confirm_password'] = checkInputRegex('confirm_password', regexPassword);
        document.getElementById("confirm_password").addEventListener("keyup", function (){
            checkField['confirm_password'] = checkInputRegex('confirm_password', regexPassword);
            if(checkField['password'] === true)
            {
                checkField['password_equal'] = checkSameValue('password', 'confirm_password');
            }
            disableForm(checkField, 'save');
        }, false);
    }
    if(checkField['password'] === true)
    {
        checkField['password_equal'] = checkSameValue('password', 'confirm_password');
    }
    checkField['template_name'] = checkInputRegex("template_name", regexTemplateName);
    checkField['snmp_community'] = checkInputRegex('snmp_community', regexSNMPCommunity);
    checkField['auth_username'] = checkInputRegex('auth_username', regexAuthUsername);
    checkField['banner_motd'] = checkInputRegex('banner_motd', regexMotd);
    checkField['snmp_permissions'] = checkSelect(selectSnmp, valueSnmpPermissions);
    checkField['spanning_tree'] = checkSelect(selectSpanningTree, valueSpanningTree);
    checkField['default_gateway_ip'] = checkInputRegex("default_gateway_ip", regexIP);
    checkField['default_gateway_netmask'] = checkInputRegex("default_gateway_netmask", regexNetmask);
    checkField['uplink_port_nb'] = checkInputNumber('uplink_port_nb', 0, 5);
    checkField['uplink_port_position'] = checkSelect(selectUplinkPortPosition, valueAssignement);

    disableForm(checkField, 'save');

    document.getElementById("template_name").addEventListener("keyup", function (){
        checkField['template_name'] = checkInputRegex("template_name", regexTemplateName);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("snmp_community").addEventListener("keyup", function (){
        checkField['snmp_community'] = checkInputRegex('snmp_community', regexSNMPCommunity);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("auth_username").addEventListener("keyup", function (){
        checkField['auth_username'] = checkInputRegex('auth_username', regexAuthUsername);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("banner_motd").addEventListener("keyup", function (){
        checkField['banner_motd'] = checkInputRegex('banner_motd', regexMotd);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("snmp_permissions").addEventListener("change", function (){
        checkField['snmp_permissions'] = checkSelect(selectSnmp, valueSnmpPermissions);
        disableForm(checkField, 'save');
    }, {passive: true});
    document.getElementById("spanning_tree").addEventListener("change", function (){
        checkField['spanning_tree'] = checkSelect(selectSpanningTree, valueSpanningTree);
        disableForm(checkField, 'save');
    }, {passive: true});
    document.getElementById("default_gateway_ip").addEventListener("keyup", function (){
        checkField['default_gateway_ip'] = checkInputRegex("default_gateway_ip", regexIP);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("default_gateway_netmask").addEventListener("keyup", function (){
        checkField['default_gateway_netmask'] = checkInputRegex("default_gateway_netmask", regexNetmask);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("uplink_port_nb").addEventListener("input", function (){
        checkField['uplink_port_nb'] = checkInputNumber('uplink_port_nb', 0, 5);
        disableForm(checkField, 'save');
    }, false);
    document.getElementById("uplink_port_position").addEventListener("change", function (){
        checkField['uplink_port_position'] = checkSelect(selectUplinkPortPosition, valueAssignement);
        disableForm(checkField, 'save');
    }, {passive: true});

    ///////////////////
    //VLAN MANAGEMENT//
    ///////////////////

    let listVlan = [];

    let tabVlan = document.getElementById("tabVlan");

    let selectPortTemplate = document.getElementById("select_port_template");
    let selectVlanType = document.getElementById("select_vlan_type");
    let selectVlan = document.getElementById("select_vlan");
    let buttonAddVlanToPort = document.getElementById("add_vlan_to_port");

    let inputVlanId = document.getElementById("add_vlan_id");
    let inputVlanName = document.getElementById("add_vlan_name");
    let inputVlanIp = document.getElementById("add_vlan_ip");
    let inputVlanNetmask = document.getElementById("add_vlan_netmask");

    let listPortVlan = initListPortVlan(selectPortTemplate);

    if(init_vlan)
    {
        initVlanTab(init_vlan);
    }

    disableButton(buttonAddVlanToPort, listVlan);

    document.getElementById("create_vlan").addEventListener("click", function (){
        if(Number.isInteger(parseFloat(inputVlanId.value)) === false || inputVlanId.value < 1 || inputVlanId.value > 4096)
        {
            displayError("vlan", "VLAN id need to be an integer number in range 1 to 4096");
        }
        else if(inputVlanName.value !== "" && regexAuthUsername.test(inputVlanName.value) === false)
        {
            displayError("vlan", "VLAN name need to be a string with a size in range 4 to 10");
        }
        else if(inputVlanIp.value !== "" && inputVlanNetmask.value === "" || inputVlanIp.value === "" && inputVlanNetmask.value !== "")
        {
            if(inputVlanIp.value === "")
            {
                displayError("vlan", "You need to add an IP if you want to add a network to the VLAN");
            }
            else if(inputVlanNetmask.value === "")
            {
                displayError("vlan", "You need to add a Netmask if you want to add a network to the VLAN");
            }
        }
        else if(inputVlanIp.value !== "" && regexIP.test(inputVlanIp.value) === false)
        {
            displayError("vlan", "VLAN IP need to be an IPv4 address like 192.168.0.1");
        }
        else if(inputVlanNetmask.value !== "" && regexNetmask.test(inputVlanNetmask.value) === false)
        {
            displayError("vlan", "VLAN Netmask need to be an IPv4 netmask like 255.255.255.0");
        }
        else if(vlanIsUnique(listVlan, inputVlanId.value, inputVlanName.value))
        {
            displayError("vlan", "VLAN ID and VLAN Name must be unique");
        }
        else
        {
            addVlan(tabVlan, inputVlanId.value, inputVlanName.value, inputVlanIp.value, inputVlanNetmask.value);
            listVlan.push(inputVlanId.value + " - " + inputVlanName.value);
            addVlanDeleteEventListener(inputVlanId.value, inputVlanName.value);
            changeSelect(selectVlan, listVlan);
            disableButton(buttonAddVlanToPort, listVlan);
            inputVlanId.value = "";
            inputVlanName.value = "";
            inputVlanIp.value = "";
            inputVlanNetmask.value = "";
        }
    }, false);

    document.getElementById("add_vlan_to_port").addEventListener("click", function (){
        if(document.getElementById("add_vlan_to_port").hasAttribute("disabled") === false)
        {
            if(selectPortTemplate.value === "")
            {
                displayError("port", "You need to select the Port template");
            }
            else if(selectVlanType.value === "")
            {
                displayError("port", "You need to select Untagged or Tagged");
            }
            else if(selectVlan.value === "")
            {
                displayError("port", "You need to select a VLAN");
            }
            else if(listPortVlan[selectPortTemplate.value.replace(" ", "_").toLowerCase()]['untagged'].indexOf(selectVlan.value.replace(" ", "").split("-")[0]) !== -1 || listPortVlan[selectPortTemplate.value.replace(" ", "_").toLowerCase()]['tagged'].indexOf(selectVlan.value.replace(" ", "").split("-")[0]) !== -1)
            {
                displayError("port", "You can't add VLAN " + selectVlan.value + " as " + selectVlanType.value);
            }
            else if(selectVlanType.value.toLowerCase() === "untagged" && listPortVlan[selectPortTemplate.value.replace(" ", "_").toLowerCase()]['untagged'].length > 0)
            {
                displayError("port", "You can add only one VLAN as untagged");
            }
            else
            {
                addVlanToPort(selectPortTemplate.value, selectVlanType.value, selectVlan.value);
                listPortVlan[selectPortTemplate.value.replace(" ", "_").toLowerCase()][selectVlanType.value.toLowerCase()].push(selectVlan.value.replace(" ", "").split("-")[0]);
                addVlanRemoveEventListener(selectPortTemplate.value, selectVlanType.value, selectVlan.value);
            }
        }
    }, false);

    //Init listPortVlan with value of select port Template
    function initListPortVlan(selectPortTemplate)
    {
        let selectLength = selectPortTemplate.options.length;
        let listPortVlan = [];
        let i;

        for(i=0; i<selectLength;i++)
        {
            let portName = selectPortTemplate.options[i].value.replace(" ", "_").toLowerCase();

            listPortVlan[portName] = [];
            listPortVlan[portName]['untagged'] = [];
            listPortVlan[portName]['tagged'] = [];
        }
        return listPortVlan;
    }

    function changeSelect(select, data_list)
    {
        select.options.length = 0;
        for (let i = 0; i < data_list.length; i++)
        {
            let option = document.createElement("option");
            option.text = data_list[i];
            select.appendChild(option);
        }
    }

    //Disable Add VLAN to port when list of vlan is empty
    function disableButton(button, element)
    {
        if(element.length === 0)
        {
            button.setAttribute("disabled", "disabled");
        }
        else
        {
            button.removeAttribute("disabled");
        }
    }

    //Show error message on hidden message
    function displayError(name, message)
    {
        document.getElementById("error_" + name).classList.remove("is-hidden");
        document.getElementById("error_" + name + "_message").innerHTML = message;
    }

    //Chek if VLAN ID and VLAN Name are unique
    function vlanIsUnique(listVlan, id, name)
    {
        let i;

        for(i=0;i<listVlan.length; i++)
        {
            let elementId = listVlan[i].split("-")[0].replace(" ", "");
            let elementName = listVlan[i].split("-")[1].replace(" ", "");

            if(elementId === id || elementName === name)
            {
                return true;
            }
        }
        return false;
    }

    //Delete vlan in listPortVlan when vlan is delete on vlan table
    function deleteVlanInVlanPort(id, listPortVlan)
    {
        let i;
        for(i in listPortVlan)
        {
            let vlan_untagged = document.getElementById(i.replace(" ", "_").toLowerCase() + "_untagged_" + id);
            let vlan_tagged = document.getElementById(i.replace(" ", "_").toLowerCase() + "_tagged_" + id);

            listPortVlan[i]['untagged'].splice(listPortVlan[i]['untagged'].indexOf(id), 1);
            listPortVlan[i]['tagged'].splice(listPortVlan[i]['tagged'].indexOf(id), 1);

            if(vlan_untagged)
            {
                vlan_untagged.remove();
            }
            if(vlan_tagged)
            {
                vlan_tagged.remove();
            }
        }
        return listPortVlan;
    }

    //Create VLAN to html tab VLAN
    function addVlan(tabVlan, id, name, ip, netmask)
    {
        if(tabVlan)
        {
            let vlanRow = tabVlan.insertRow(tabVlan.rows.length);
            vlanRow.id = "vlan_id_" + id;

            let vlanId = vlanRow.insertCell(0);
            let vlanName = vlanRow.insertCell(1);
            let vlanIp = vlanRow.insertCell(2);
            let vlanNetmask = vlanRow.insertCell(3);
            let action = vlanRow.insertCell(4);

            vlanId.innerHTML = "<span>" + id + "</span><input type='hidden' name='vlan_" + id + "_id' value='" + id + "'>";
            vlanId.className = "has-text-centered";

            vlanName.innerHTML = "<span>" + name + "</span><input type='hidden' name='vlan_" + id + "_name' value='" + name + "'>";
            vlanName.className = "has-text-centered";

            vlanIp.innerHTML = "<span>" + ip + "</span><input type='hidden' name='vlan_" + id + "_ip' value='" + ip + "'>";
            vlanIp.className = "has-text-centered";

            vlanNetmask.innerHTML = "<span>" + netmask + "</span><input type='hidden' name='vlan_" + id + "_netmask' value='" + netmask + "'>";
            vlanNetmask.className = "has-text-centered";

            action.innerHTML = "<a id=\"vlan_id_" + id + "_delete\" class=\"button is-danger\">" +
                "<span class=\"icon is-small\">" +
                "<i class=\"fas fa-times\"></i>" +
                "</span>" +
                "<span>Delete</span>" +
                "</a>";
            action.className = "has-text-centered";

            vlan_list.value = vlan_list.value + "," + id;
        }
    }

    //Add listener on button for delete vlan
    function addVlanDeleteEventListener(id, name)
    {
        let vlanID = id;
        let vlan = "vlan_id_" + id;

        document.getElementById(vlan + "_delete").addEventListener("click", function (){
            listPortVlan = deleteVlanInVlanPort(vlan.split("_")[2], listPortVlan);
            listVlan.splice(listVlan.indexOf(id + " - " + name), 1);
            changeSelect(selectVlan, listVlan);
            disableButton(buttonAddVlanToPort, listVlan);
            document.getElementById(vlan).remove();
            let vlan_list = document.getElementById("vlan_list");
            vlan_list.value = vlan_list.value.replace(',' + id, '');

            let port_templates = ["distribution_port", "uplink_port"];
            let port_types = ["untagged", "tagged"];

            port_templates.forEach(function(port_template) {
                port_types.forEach(function(port_type) {
                    console.log(port_template + "_" + port_type + "_form");
                    let vlan_assignation = document.getElementById(port_template + "_" + port_type + "_form");
                    vlan_assignation.value = vlan_assignation.value.replace(',' + vlanID, '');
                });
            });
        }, false);
    }

    //Add VLAN to html port table
    function addVlanToPort(selectPortTemplate, selectVlanType, selectVlan)
    {
        let elementId = selectPortTemplate.replace(" ", "_").toLowerCase() + "_" + selectVlanType.replace(" ", "_").toLowerCase();
        let element = document.getElementById(elementId);

        let vlan_assignation = document.getElementById(selectPortTemplate.replace(" ", "_").toLowerCase() + "_" + selectVlanType.toLowerCase() + "_form");

        if(element)
        {
            let id = selectVlan.replace(" ", "").split('-')[0];

            element.insertAdjacentHTML("beforeend", "<div id=\"" + elementId + "_" + id + "\" class=\"control\"><span class=\"tag is-link\">" +
                selectVlan +
                "<a id=\"" + elementId + "_" + id + "_delete" + "\" class=\"delete is-small\"></a></span></div>");

            vlan_assignation.value = vlan_assignation.value + "," + selectVlan.replace(" ", "").split('-')[0];
        }
    }

    //Add listener on button for delete vlan in port
    function addVlanRemoveEventListener(selectPortTemplate, selectVlanType, selectVlan)
    {
        let vlanid = selectVlan.replace(" ", "").split('-')[0];
        let vlan = selectPortTemplate.replace(" ", "_").toLowerCase() + "_" + selectVlanType.replace(" ", "_").toLowerCase() +  "_" + vlanid;
        let PortTemplate = selectPortTemplate.replace(" ", "_").toLowerCase();
        let PortType = selectVlanType.toLowerCase();

        document.getElementById(vlan + "_delete").addEventListener("click", function (){
            listPortVlan[PortTemplate][PortType].splice(listPortVlan[PortTemplate][PortType].indexOf(selectVlan.replace(" ", "").split('-')[0]), 1);
            document.getElementById(vlan).remove();
            let vlan_assignation = document.getElementById(PortTemplate + "_" + PortType + "_form");
            vlan_assignation.value = vlan_assignation.value.replace(',' + vlanid, '');
        }, false);
    }


    //Init VLAN tab
    function initVlanTab(init_vlan)
    {
        Object.keys(init_vlan).forEach(function(i)
        {
            addVlan(tabVlan, init_vlan[i]["vlan_id"], init_vlan[i]["name"], init_vlan[i]["ip"], init_vlan[i]["netmask"]);
            listVlan.push(init_vlan[i]["vlan_id"] + " - " + init_vlan[i]["name"]);
            addVlanDeleteEventListener(init_vlan[i]["vlan_id"], init_vlan[i]["name"], listPortVlan);
            changeSelect(selectVlan, listVlan);
            disableButton(buttonAddVlanToPort, listVlan);

            ////
            Object.keys(init_vlan[i]["port_config_assignation"]).forEach(function(k)
            {
                if(init_vlan[i]["port_config_assignation"][k] === true)
                {
                    let field_split = k.split('_');
                    addVlanToPort(field_split[0] + " " + field_split[1], field_split[2], init_vlan[i]["vlan_id"] + " - " + init_vlan[i]["name"]);
                    listPortVlan[field_split[0] + "_" + field_split[1]][field_split[2]].push(init_vlan[i]["vlan_id"]);
                    addVlanRemoveEventListener(field_split[0] + " " + field_split[1], field_split[2], init_vlan[i]["vlan_id"] + " - " + init_vlan[i]["name"]);
                }
            });
        });
    }
}, false);