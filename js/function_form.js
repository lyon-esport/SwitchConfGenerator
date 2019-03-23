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

//Init Select with an array
export function initSelect(select, array_option)
{
    select.options.length = 0;

    for (let i = 0; i < array_option.length; i++)
    {
        let option = document.createElement("option");
        option.text = array_option[i];
        select.appendChild(option);
    }
}

//check input of type regex
export function checkInputRegex(id, regex)
{
    let inputObject = document.getElementById(id);
    let favIconObject = document.getElementById(id).nextElementSibling.children[0];

    if(regex.test(inputObject.value))
    {
        inputObject.classList.add("is-success");
        inputObject.classList.remove("is-danger");

        favIconObject.classList.add("fa-check");
        favIconObject.classList.remove("fa-exclamation-triangle");

        return true;
    }
    else
    {
        inputObject.classList.add("is-danger");
        inputObject.classList.remove("is-success");

        favIconObject.classList.add("fa-exclamation-triangle");
        favIconObject.classList.remove("fa-check");

        return false;
    }
}

//Check if InputNumber value is a number in the right range
export function checkInputNumber(id, min, max)
{
    let inputObject = document.getElementById(id);
    let favIconObject = document.getElementById(id).nextElementSibling.children[0];

    if(Number.isInteger(parseFloat(inputObject.value)) && inputObject.value >= min && inputObject.value <= max)
    {
        inputObject.classList.add("is-success");
        inputObject.classList.remove("is-danger");

        favIconObject.classList.add("fa-check");
        favIconObject.classList.remove("fa-exclamation-triangle");

        return true;
    }
    else
    {
        inputObject.classList.add("is-danger");
        inputObject.classList.remove("is-success");

        favIconObject.classList.add("fa-exclamation-triangle");
        favIconObject.classList.remove("fa-check");

        return false;
    }
}

//Check if Select has a right value
export function checkSelect(selectObject, array_select)
{
    if(array_select.indexOf(selectObject.value) !== -1)
    {
        selectObject.parentNode.classList.add("is-success");
        selectObject.parentNode.classList.remove("is-danger");

        return true;
    }
    else
    {
        selectObject.parentNode.classList.add("is-danger");
        selectObject.parentNode.classList.remove("is-success");

        return false;
    }
}

//Check if two input has the same value
export function checkSameValue(idFirstInput, idSecondInput)
{
    let firstInput = document.getElementById(idFirstInput);
    let secondInput = document.getElementById(idSecondInput);

    if(firstInput.value === secondInput.value)
    {
        secondInput.classList.add("is-success");
        secondInput.classList.remove("is-danger");
        secondInput.nextElementSibling.children[0].classList.add("fa-check");
        secondInput.nextElementSibling.children[0].classList.remove("fa-exclamation-triangle");
        secondInput.nextSibling.nextSibling.nextSibling.nextSibling.nextElementSibling.innerHTML = "";

        return true;
    }
    else
    {
        secondInput.classList.add("is-danger");
        secondInput.classList.remove("is-success");
        secondInput.nextElementSibling.children[0].classList.add("fa-exclamation-triangle");
        secondInput.nextElementSibling.children[0].classList.remove("fa-check");
        secondInput.nextSibling.nextSibling.nextSibling.nextSibling.nextElementSibling.innerHTML = "Password are not the same";

        return false;
    }
}

//Disable button send form
export function disableForm(checkField, id)
{
    let buttonSendForm = document.getElementById(id);

    for(let key in checkField)
    {
        if(checkField[key]===false)
        {
            buttonSendForm.setAttribute("disabled", "disabled");
            return 0;
        }
        else
        {
            buttonSendForm.removeAttribute("disabled");
        }
    }
}