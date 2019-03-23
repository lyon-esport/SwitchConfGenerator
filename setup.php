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

if(file_exists ('database.sqlite'))
{
    echo "Database already exist !<br> <a href='index.php'>Go back to homepage</a>";
}
else
{
    try
    {
        $BDD = new PDO('sqlite:database.sqlite');
        $BDD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt_setting = $BDD->prepare('CREATE TABLE IF NOT EXISTS setting (
                                        id            INTEGER         PRIMARY KEY AUTOINCREMENT, 
                                        template_name               VARCHAR( 255 ),
                                        snmp_community              VARCHAR( 255 ),
                                        snmp_permissions            VARCHAR( 255 ),
                                        auth_username               VARCHAR( 255 ),
                                        auth_password               VARCHAR( 255 ),
                                        banner_motd                 VARCHAR( 255 ),
                                        spanning_tree               VARCHAR( 255 ),
                                        default_gateway_ip          VARCHAR( 255 ),
                                        default_gateway_netmask     VARCHAR( 255 ),
                                        UNIQUE(template_name)
                                        );');
        $stmt_setting->execute();
        $stmt_setting->closeCursor();
        $stmt_company = $BDD->prepare('CREATE TABLE IF NOT EXISTS company ( 
                                        id            INTEGER         PRIMARY KEY AUTOINCREMENT,
                                        name          VARCHAR( 255 ),
                                        UNIQUE(name)
                                        );');
        $stmt_company->execute();
        $stmt_company->closeCursor();
        $stmt_switch = $BDD->prepare('CREATE TABLE IF NOT EXISTS switch ( 
                                        id            INTEGER         PRIMARY KEY AUTOINCREMENT,
                                        name          VARCHAR( 255 ),
                                        company_id    INTEGER,
                                        FOREIGN KEY(company_id) REFERENCES company(id) ON DELETE CASCADE,
                                        UNIQUE(name, company_id)
                                        );');
        $stmt_switch->execute();
        $stmt_switch->closeCursor();
        $stmt_port_config = $BDD->prepare('CREATE TABLE IF NOT EXISTS port_config (
                                        setting_id                INTEGER         PRIMARY KEY,
                                        distribution_port_nb            INTEGER,
                                        distribution_port_position      VARCHAR( 255 ),
                                        uplink_port_nb                  INTEGER,
                                        uplink_port_position            VARCHAR( 255 ),
                                        FOREIGN KEY(setting_id) REFERENCES setting(id) ON DELETE CASCADE,
                                        UNIQUE(setting_id)
                                        );');
        $stmt_port_config->execute();
        $stmt_port_config->closeCursor();
        $stmt_vlan = $BDD->prepare('CREATE TABLE IF NOT EXISTS vlan ( 
                                        setting_id                INTEGER,
                                        vlan_id                   INTEGER,
                                        name                      VARCHAR( 255 ),
                                        ip                        VARCHAR( 255 ),
                                        netmask                   VARCHAR( 255 ),
                                        port_config_assignation   VARCHAR( 255 ),
                                        UNIQUE(setting_id, vlan_id),
                                        PRIMARY KEY(setting_id, vlan_id),
                                        FOREIGN KEY(setting_id) REFERENCES setting(id) ON DELETE CASCADE
                                        );');
        $stmt_vlan->execute();
        $stmt_vlan->closeCursor();

        echo "Database created !<br> <a href='index.php'>Go back to homepage</a>";
    }
    catch (Exception $e)
    {
        echo "Error : " . $e->getMessage();
        die();
    }
}