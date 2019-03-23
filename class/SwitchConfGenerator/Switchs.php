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

namespace Project\SwitchConfGenerator;

class Switchs
{
    public $hostname;
    public $default_gateway;
    public $bannerMotd;
    public $spanningTree;
    public $snmp;
    public $authorization;
    public $listVlan;
    public $port;

    public function __construct($hostname = null, $default_gateway = null, $bannerMotd = null, $spanningTree = false, $snmp = null, $authorization = null, $vlans = array())
    {
        $this->hostname = $hostname;
        $this->default_gateway = $default_gateway;
        $this->bannerMotd = $bannerMotd;
        $this->spanningTree = $spanningTree;
        $this->snmp = $snmp;
        $this->authorization = $authorization;
        $temp_port_list_vlan = $this->create_port_list_vlan(["vlans" => $vlans["vlan_array"], "resume_port" => $vlans["resume_port"], "port_config" => $vlans["port_config"], "switch_number" => $vlans["switch_number"]]);
        $this->listVlan = $temp_port_list_vlan["list_vlan"];
        $this->port = $temp_port_list_vlan["list_ports"];
    }

    private function create_port_list_vlan($vlans_ports)
    {
        $vlans = $vlans_ports["vlans"];
        $resume_port = $vlans_ports["resume_port"];
        $port_config = $vlans_ports["port_config"];
        $switch_number = $vlans_ports["switch_number"];

        $list_vlan = array();
        $list_vlan_assignation = array(
            "distribution_port_untagged" => "",
            "distribution_port_tagged" => array(),
            "uplink_port_untagged" => "",
            "uplink_port_tagged" => array()
        );
        $list_ports = array();

        if(!empty($vlans))
        {
            foreach($vlans as $id => $vlan)
            {
                if(!empty($vlan["ip"]))
                {
                    $ip = long2ip(ip2long($vlan["ip"]) + $switch_number);
                    if(!$this->ip_in_range($ip, $vlan["ip"]."/".$this->netmask_to_cidr($vlan["netmask"])))
                    {
                        $vlan["ip"] = "";
                        $vlan["netmask"] = "";
                    }
                    else
                    {
                        $vlan["ip"] = $ip;
                    }
                }
                $list_vlan[$id] = new Vlan($id, $vlan["name"], new Network($vlan["ip"], $vlan["netmask"]));
                foreach($vlans[$id]["port_config_assignation"] as $assignation => $value)
                {
                    if($value)
                    {
                        if(is_array($list_vlan_assignation[$assignation]))
                        {
                            array_push($list_vlan_assignation[$assignation], $id);
                        }
                        else
                        {
                            $list_vlan_assignation[$assignation] = $id;
                        }
                    }
                }
            }
        }
        else
        {
            $list_vlan = null;
        }
        if(!empty($resume_port))
        {
            for($i=0; $i<count($resume_port); $i++)
            {
                for($j=$resume_port[$i]["first_port_id"]; $j<=$resume_port[$i]["last_port_id"]; $j++)
                {
                    if(count($resume_port) === $i+1 && count($resume_port) > 1) //If it's the last range of ports in the switch it's set auto to Uplink port
                    {
                        $port = new Port($resume_port[$i]["name"].$j, $list_vlan_assignation["uplink_port_untagged"], $list_vlan_assignation["uplink_port_tagged"]);
                    }
                    else if(($port_config["uplink_port_position"] === "First ports" && $j <= $port_config["uplink_port_nb"] -1) || ($port_config["uplink_port_position"] === "Last ports" && $j >= $resume_port[$i]["last_port_id"] - $port_config["uplink_port_nb"] + 1))
                    {
                        $port = new Port($resume_port[$i]["name"].$j, $list_vlan_assignation["uplink_port_untagged"], $list_vlan_assignation["uplink_port_tagged"]);
                    }
                    else
                    {
                        $port = new Port($resume_port[$i]["name"].$j, $list_vlan_assignation["distribution_port_untagged"], $list_vlan_assignation["distribution_port_tagged"]);
                    }
                    array_push($list_ports, $port);
                }
            }
        }
        else
        {
            $list_ports = null;
        }
        return ["list_vlan" => $list_vlan, "list_ports" => $list_ports];
    }

    private function ip_in_range( $ip, $range )
    {
        if(strpos( $range,'/')==false )
        {
            $range .= '/32';
        }
        list($range, $netmask) = explode('/', $range,2);
        $range_decimal = ip2long($range);
        $ip_decimal = ip2long($ip);
        $wildcard_decimal = pow(2, (32-$netmask)) - 1;
        $netmask_decimal = ~ $wildcard_decimal;
        return (($ip_decimal & $netmask_decimal) == ($range_decimal & $netmask_decimal));
    }

    private function netmask_to_cidr($netmask)
    {
        $long = ip2long($netmask);
        $base = ip2long('255.255.255.255');
        return 32-log(($long ^ $base)+1,2);
    }
}