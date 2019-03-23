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

document.addEventListener('DOMContentLoaded', function ()
{
    let edit_button = document.getElementById("edit_button");
    let edit_text = document.getElementById("edit_text");
    let edit_icon = document.getElementById("edit_icon");

    let copy_button = document.getElementById("copy_data");

    let textarea = document.getElementById("sw_config");
    let company = document.getElementById("company");
    let model = document.getElementById("model");

    document.getElementById("edit_button").addEventListener("click", function (){
        inverseEditionMode(edit_button, edit_text, edit_icon, textarea);
    }, false);

    document.getElementById("copy_data").addEventListener("click", function (){
        copy(textarea, copy_button);
    }, false);

    document.getElementById("download_config").addEventListener("click", function (){
        fetchFile('../functions/generate_file.php', textarea, company, model);
    }, false);

    function inverseEditionMode(button, buttonText, buttonIcon, target)
    {
        if(target.readOnly === true)
        {
            button.classList.remove('is-link');
            button.classList.add('is-danger');

            buttonText.innerHTML = 'Edit mode';

            buttonIcon.classList.remove('fa-edit');
            buttonIcon.classList.add('fa-times');

            target.readOnly = false;
        }
        else
        {
            button.classList.remove('is-danger');
            button.classList.add('is-link');

            buttonText.innerHTML = 'Readonly mode';

            buttonIcon.classList.remove('fa-times');
            buttonIcon.classList.add('fa-edit');

            target.readOnly = true;
        }
    }

    function copy(copyText, buttonCoppy)
    {
        copyText.select();
        console.log(copyText.innerHTML);
        document.execCommand("copy");

        buttonCoppy.classList.add("is-loading");

        setTimeout(function(){buttonCoppy.classList.remove("is-loading")}, 500);
    }

    function fetchFile(url, data, company, model)
    {
        let body = 'hostname='+model.innerHTML+'&content='+data.value;

        fetch(url, {
            method: 'POST',
            body: body,
            headers:
            {
                "Content-Type": "application/x-www-form-urlencoded"
            }
        }).then(function(response) {
            return response.blob();
        }).then(function(blob) {
            let url = window.URL.createObjectURL(blob);
            let a = document.createElement('a');
            let filename = company.innerHTML+'_'+model.innerHTML;

            a.href = url;
            a.download = filename;
            a.click();
        }).catch(function(error) {
            console.log('status: ', error.status);
        });
    }

}, false);