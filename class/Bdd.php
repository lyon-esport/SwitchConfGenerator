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


namespace Project;

use PDO;
use Exception;

class Bdd
{
    private $PATH = "";
    private $BDD = null;

    public function __construct($path = "")
    {
        $this->PATH = $path;
        if(!file_exists ($this->PATH.'database.sqlite'))
        {
            throw new Exception("Database doesn't exist !");
        }
        $this->BDD = new PDO('sqlite:' . $this->PATH . 'database.sqlite');
        $this->BDD->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->BDD->exec('PRAGMA foreign_keys = ON;');
    }

    public function get_setting($id = 0)
    {
        if($id === 0)
        {
            $stmt = $this->BDD->prepare('SELECT * FROM setting');
            $stmt->execute();
            $i = 0;
            $SETTING_LIST = array();
            while ($results = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                foreach($results as $key => $value)
                {
                    $SETTING_LIST[$i][$key] = $value;
                }
                $i++;
            }
            $stmt->closeCursor();
        }
        else
        {
            $stmt = $this->BDD->prepare('SELECT * FROM setting WHERE id = :id');
            $stmt->execute(array(':id' => $id));
            $SETTING_LIST = array();
            while ($results = $stmt->fetch(PDO::FETCH_ASSOC))
            {
                foreach($results as $key => $value)
                {
                    $SETTING_LIST[$key] = $value;
                }
            }
            $stmt->closeCursor();
        }

        return $SETTING_LIST;
    }

    public function get_port_config($id)
    {
        $stmt = $this->BDD->prepare('SELECT * FROM port_config WHERE setting_id = :id');
        $stmt->execute(array(':id' => $id));
        $SETTING_LIST = array();
        while ($results = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            foreach($results as $key => $value)
            {
                $SETTING_LIST[$key] = $value;
            }
        }
        $stmt->closeCursor();
        return $SETTING_LIST;
    }

    public function get_vlans($id)
    {
        $stmt = $this->BDD->prepare('SELECT * FROM vlan WHERE setting_id = :id');
        $stmt->execute(array(':id' => $id));
        $SETTING_LIST = array();
        while ($results = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            $SETTING_LIST[$results["vlan_id"]]["vlan_id"] = $results["vlan_id"];
            $SETTING_LIST[$results["vlan_id"]]["name"] = $results["name"];
            $SETTING_LIST[$results["vlan_id"]]["ip"] = $results["ip"];
            $SETTING_LIST[$results["vlan_id"]]["netmask"] = $results["netmask"];
            $SETTING_LIST[$results["vlan_id"]]["port_config_assignation"] = json_decode($results["port_config_assignation"]);
        }
        $stmt->closeCursor();
        return $SETTING_LIST;
    }

    public function get_switchs()
    {
        $stmt = $this->BDD->prepare('SELECT company.name AS compName, switch.id AS swId, switch.name AS swName FROM company LEFT JOIN switch ON company.id = switch.company_id ORDER BY compName ASC');
        $stmt->execute();
        $SWITCH_LIST = array();
        $i=0;
        $previousComp = "";
        while ($results = $stmt->fetch(PDO::FETCH_ASSOC))
        {
            if($i === 0)
            {
                $previousComp = $results['compName'];
            }
            if($previousComp !== $results['compName'])
            {
                $i = 0;
            }
            $SWITCH_LIST[$results['compName']][$i]['name'] = $results['swName'];
            $SWITCH_LIST[$results['compName']][$i]['id'] = $results['swId'];

            $i++;
            $previousComp = $results['compName'];
        }
        $stmt->closeCursor();

        return $SWITCH_LIST;
    }

    public function get_template_id($template)
    {
        $stmt1 = $this->BDD->prepare('SELECT id FROM setting WHERE template_name = :template_name LIMIT 1');
        $stmt1->execute(array(':template_name' => $template));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor();

        return $result[0]['id'];
    }

    public function insert_company($company_name)
    {
        $stmt = $this->BDD->prepare('INSERT INTO company (name) VALUES (:name)');
        $stmt->execute(array(':name' => $company_name));
        $stmt->closeCursor();

        return [['title' => 'Success !', 'content' => 'You have added the company '.$company_name, 'color' => 'success', 'delete' => true, 'container' => true]];
    }

    public function insert_model($company_name, $model_name)
    {
        $stmt1 = $this->BDD->prepare('SELECT id FROM company WHERE name = :company_name');
        $stmt1->execute(array(':company_name' => $company_name));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor();
        $stmt2 = $this->BDD->prepare('INSERT INTO switch (name, company_id) VALUES (:model_name, :company_id)');
        $stmt2->execute(array(':model_name' => $model_name, ':company_id' => $result[0]['id']));
        $stmt2->closeCursor();

        return [['title' => 'Success !', 'content' => 'You have added the model '.$model_name, 'color' => 'success', 'delete' => true, 'container' => true]];
    }

    public function delete_company($company_name)
    {
        $stmt1 = $this->BDD->prepare('SELECT id FROM company WHERE name = :name');
        $stmt1->execute(array(':name' => $company_name));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor();

        if(isset($result[0]['id']) && !empty($result[0]['id']))
        {
            $stmt2 = $this->BDD->prepare('DELETE FROM company WHERE id = :company_id');
            $stmt2->execute(array(':company_id' => $result[0]['id']));
            $stmt2->closeCursor();

            return [['title' => 'Success !', 'content' => 'You have deleted the company '.$company_name, 'color' => 'success', 'delete' => true, 'container' => true]];
        }
        else
        {
            return [['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]];
        }
    }

    public function delete_model($model_id)
    {
        $stmt1 = $this->BDD->prepare('SELECT name FROM switch WHERE id = :id');
        $stmt1->execute(array(':id' => $model_id));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor();

        if(isset($result[0]['name']) && !empty($result[0]['name'])) {

            $stmt2 = $this->BDD->prepare('DELETE FROM switch WHERE id = :id');
            $stmt2->execute(array(':id' => $model_id));
            $stmt2->closeCursor();

            return [['title' => 'Success !', 'content' => 'You have deleted the model ' . $result[0]['name'], 'color' => 'success', 'delete' => true, 'container' => true]];
        }
        else
        {
            return [['title' => 'Error !', 'content' => 'An error occurred when processing your request', 'color' => 'error', 'delete' => true, 'container' => true]];
        }
    }

    public function insert_template($setting, $password_protected)
    {
        $stmt = $this->BDD->prepare('INSERT INTO setting
                           (template_name, snmp_community, snmp_permissions, auth_username, auth_password, banner_motd, spanning_tree, default_gateway_ip, default_gateway_netmask)
                            VALUES (:template_name, :snmp_community, :snmp_permissions, :auth_username, :auth_password, :banner_motd, :spanning_tree, :default_gateway_ip, :default_gateway_netmask)');
        $stmt->execute(array(':template_name' => $setting['template_name'], ':snmp_community' => $setting['snmp_community'], ':snmp_permissions' => $setting['snmp_permissions'], ':auth_username' => $setting['auth_username'], ':auth_password' => $password_protected, ':banner_motd' => $setting['banner_motd'], ':spanning_tree' => $setting['spanning_tree'], ':default_gateway_ip' => $setting['default_gateway_ip'], ':default_gateway_netmask' => $setting['default_gateway_netmask']));
        $id = $this->BDD->lastInsertId();
        $stmt->closeCursor();

        return [$id, [['title' => 'Success !', 'content' => 'You have created the template '.$setting['template_name'], 'color' => 'success', 'delete' => true, 'container' => true]]];
    }

    public function update_template_general($setting, $id)
    {
        $stmt = $this->BDD->prepare('UPDATE setting
                       SET template_name = :template_name, 
                           snmp_community = :snmp_community,
                           snmp_permissions = :snmp_permissions,
                           auth_username = :auth_username,
                           banner_motd = :banner_motd,
                           spanning_tree = :spanning_tree,
                           default_gateway_ip = :default_gateway_ip,
                           default_gateway_netmask = :default_gateway_netmask
                       WHERE id = :id');
        $stmt->execute(array(':template_name' => $setting['template_name'], ':snmp_community' => $setting['snmp_community'], ':snmp_permissions' => $setting['snmp_permissions'], ':auth_username' => $setting['auth_username'], ':banner_motd' => $setting['banner_motd'], ':spanning_tree' => $setting['spanning_tree'], ':default_gateway_ip' => $setting['default_gateway_ip'], ':default_gateway_netmask' => $setting['default_gateway_netmask'], ':id' => $id));
        $stmt->closeCursor();

        return [['title' => 'Success !', 'content' => 'You have updated the settings of template '.$setting['template_name'], 'color' => 'success', 'delete' => true, 'container' => true]];
    }

    public function update_template_password($id, $password_protected)
    {
        $stmt1 = $this->BDD->prepare('SELECT template_name FROM setting WHERE id= :id');
        $stmt1->execute(array(':id' => $id));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt2 = $this->BDD->prepare('UPDATE setting
                       SET auth_password = :password WHERE id = :id');
        $stmt2->execute(array(':password' => $password_protected, ':id' => $id));
        $stmt2->closeCursor();

        return [['title' => 'Success !', 'content' => 'You have updated the password of template ' . $result['template_name'], 'color' => 'success', 'delete' => true, 'container' => true]];
    }

    public function delete_template($id)
    {
        $stmt1 = $this->BDD->prepare('SELECT template_name FROM setting WHERE id= :id');
        $stmt1->execute(array(':id' => $id));
        $result = $stmt1->fetchAll(PDO::FETCH_ASSOC);
        $stmt1->closeCursor();
        $stmt2 = $this->BDD->prepare('DELETE FROM setting WHERE id= :id');
        $stmt2->execute(array(':id' => $id));
        $stmt2->closeCursor();

        return [['title' => 'Success !', 'content' => 'You have deleted the template '.$result[0]['template_name'], 'color' => 'success', 'delete' => true, 'container' => true]];
    }

    public function insert_port_config($distribution_port_nb, $distribution_port_position, $uplink_port_nb, $uplink_port_position, $setting_id)
    {
        $stmt = $this->BDD->prepare('INSERT INTO port_config (distribution_port_nb, distribution_port_position, uplink_port_nb, uplink_port_position, setting_id) VALUES (:distribution_port_nb, :distribution_port_position, :uplink_port_nb, :uplink_port_position, :setting_id)');
        $stmt->execute(array(':distribution_port_nb' => $distribution_port_nb, ':distribution_port_position' => $distribution_port_position, ':uplink_port_nb' => $uplink_port_nb, ':uplink_port_position' => $uplink_port_position, ':setting_id' => $setting_id));
        $stmt->closeCursor();
    }

    public function update_port_config($distribution_port_nb, $distribution_port_position, $uplink_port_nb, $uplink_port_position, $setting_id)
    {
        $stmt = $this->BDD->prepare('UPDATE port_config SET distribution_port_nb = :distribution_port_nb, distribution_port_position = :distribution_port_position, uplink_port_nb = :uplink_port_nb, uplink_port_position = :uplink_port_position WHERE setting_id = :setting_id');
        $stmt->execute(array(':distribution_port_nb' => $distribution_port_nb, ':distribution_port_position' => $distribution_port_position, ':uplink_port_nb' => $uplink_port_nb, ':uplink_port_position' => $uplink_port_position, ':setting_id' => $setting_id));
        $stmt->closeCursor();
    }

    public function insert_vlans($vlan_list, $setting_id)
    {
        $stmt = $this->BDD->prepare('INSERT INTO vlan (setting_id, vlan_id, name, ip, netmask, port_config_assignation) VALUES (:setting_id, :vlan_id, :name, :ip, :netmask, :port_config_assignation)');
        foreach ($vlan_list as &$vlan)
        {
            $stmt->execute(array(':setting_id' => $setting_id, ':vlan_id' => $vlan["vlan_id"], ':name' => $vlan["name"], ':ip' => $vlan["ip"], ':netmask' => $vlan["netmask"], ':port_config_assignation' => $vlan["port_config_assignation"]));
        }
        $stmt->closeCursor();
    }

    public function delete_vlans($id)
    {
        $stmt = $this->BDD->prepare('DELETE FROM vlan WHERE setting_id= :id');
        $stmt->execute(array(':id' => $id));
        $stmt->closeCursor();
    }
}