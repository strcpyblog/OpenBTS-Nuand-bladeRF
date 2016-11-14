<?php

function get_default_fields_enb()
{
	global $server_name, $request_protocol;

$enodeb_params = array(
/* "section" => array(
	"param" => array(  // value to be used in display_pair()
		$value,
		"display"  =>"", 
		"comment"  =>
		"validity" => ""
    )*/
"radio" => array(
"enodeb" => array(
    // this configurations will be sent in the 'basic' section when actually sending request to API
    // they were grouped here because they are unique per enodeb or per enodeb in overlapping coverage area

    "enodebId" => array(
	"column_name" => "eNodeB Id",
	"comment" => "eNodeB ID
20 bits
Unique to every eNodeB in the network.
This value is concatinated with the PLMN ID to create
a 44-bit global eNodeB identity.
This value must be set; there is no default.",
	"required" => true,
	"validity"=> array("check_valid_enodebid")
	),

    "MCC" => array(
	"comment" => "Mobile Country Code part of the PLMN Identity
The same for every eNodeB in the network.",
	"required" => true,
	"validity" => array("check_field_validity", false, false,"^[0-9]{3}$")
    ),

    "MNC" => array(
	"comment" => "Mobile Network Code part of the PLMN Identity
The same for every eNodeB in the network.",
	"required" => true,
	"validity" => array("check_field_validity", false, false,"^[0-9]{2,3}$")
    ),

    "TAC" => array(
	"comment" => "Tracking Area Code
This value must be set; there is no default.",
	"required" => true,
	"validity" => array("check_field_validity", 0, 65535)
    ),

    "CellIdentity" => array(
	"column_name" => "Cell Identity",
	"comment" => "Must 7 digits in length
Ex: 0000001",
	"validity" => array("validate_cell_identity")
    ),

    "Name" => array(
	"comment" =>"Human readable eNB Name that is sent to the MME during S1-MME setup.
According to 3GPP 36.413, this parameter is optional; if it is set,
the MME may use it as a human readable name of the eNB. See paragraphs
8.7.3.2 and 9.1.8.4 of the above referenced specification.",
	"validity" => array("check_field_validity", false, false, "^[a-zA-Z0-9]+$")
    ),

	"Band" => array(
	 array("selected"=> 1, 1,2,3,4,5,6,7,8,9,10,11,12,13,14,17,18,19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36,37,38,39,40,41,42,43,44),
	"display" => "select",	
	"comment" => 'Band selection ("freqBandIndicator" in SIB1)
In most systems, this is set by the hardware type and should not be changed.'
    ),

    "Bandwidth" => array(
	array("selected"=>25, 6,15,25,50,75,100),
	"display" => "select",
	"comment" => format_comment("
Bandwidth is the LTE radio channel BW, in number of resource blocks
(in the frequency domain). The allowed values and the corresponding
bandwidth (in MHz) is listed in the following table:
   Value (N RBs)  Bandwidth (MHz)
   -------------  ---------------
   6              1.4
   15             3
   25             5
   50             10
   75             15
   100            20
 A simple formula for calculating the bandwidth in MHz for a given
 number of RBs is: MHz BW = (N_RB * 2) / 10, except for 6 RBs.
 Probably the same for all eNodeBs in a network that are operating in the same band.

Example: Bandwidth = 25"),
	"required" => true
    ),

    "EARFCN" => array(
	"comment" => format_comment('Downlink ARFCN
Uplink ARFCN selected automatically based on this downlink.
Must be compatible with the band selection.
Probably the same for all eNodeBs in a network that are operating in the same band.
Some handy examples of downlink EARFCNs:
EARFCN 900, 1960 MHz, Band 2 ("PCS1900")
EARFCN 1575, 1842.5 MHz, Band 3 ("DCS1800")
EARFCN 2175, 2132,5 MHz, Band 4 ("AWS-1")
EARFCN 2525, 881.5 MHz, Band 5 ("850")
EARFCN 3100, 2655.0 MHz, Band 7 ("2600")
EARFCN 5790, 740.0 MHz, Band 17 ("700 b")
EARFCN 6300, 806.0 MHz, Band 20 ("800 DD")
Special ISM EARFCN extension: 2400 MHz @ EARFCN 50000 offset; valid range: 50000 - 50959'),
	"required" => true,
	"validity" => array("validate_earfcn_band", "Band"),
    ),

    "__" => array(
	"display" => "objtitle",
	"value" => "Physical Layer Cell ID 
Phy Cell ID = 3*NID1 + NID2,
NID1 is 0..167
NID2 is 0..2
This gives a phy cell id range of 0..503
The combination 3*NID1+NID2 should never be the same for cells with overlapping coverage."
    ),

    "NID1" => array(
	"required" => true,
	"comment"  => "NID1 is between 0 and 167",
	"required" => true,
	"validity" => array("check_field_validity", 0, 167)

    ),
    "NID2" => array(
	array(1, 2, 3),
	"display" => "select",
	"required" => true,
    ),

    "Prach.RootSequence" => array(
	"value" => "0",
	"comment" => 'Root Sequence Index ("rootSequenceIndex" in SIB2)
Cells with overlapping coverage should have different values.
Allowed values 0..837',
	"validaty" => array("check_field_validity", 0, 837)
    ),

    "Prach.FreqOffset" => array(
	"value" => 9,
	"comment" => 'Frequency Offset ("prach_ConfigIndex" in SIB2)
Cells with overlapping coverage should have different values.
Allowed values 0..94',
	"validity" => array("check_field_validity", 0, 94)
    ),

    "Pusch.RefSigGroup" => array(
	array(0,1,2,3,4,5,6,7,8,9,10, 11,12,13,14,15,16,17,18,19,20, 21,22,23,24,25,26,27,28,29, "selected" => 2),
	"display" => "select",
	"comment" => 'Reference Signal Group Assignment ("groupAssignmentPUSCH" in SIB2)
Cells with overlapping coverage should have different values. Default 2.'
    ),

    "OutputLevel" => array(
	"value" => "40",
	"comment" => "Settable output level, dBm
Valid range for a SatSite 142 is 0..43",
	"validity" => array("check_field_validity",0,43)
    ),

    "CrestFactor" => array(
	array(5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20, "selected"=>13),
	"display" => "select",
	"comment" => "OFDM crest factor allowance in dB
allowed range 5 .. 20, default 13
lower value -> higher output power
Can be used to push higher output levels, but also
can produce higher distortion and clipping."
    ),

    "DistributedVrbs" => array(
	"value" => true,
	"column_name" => "Distributed Virtual Resource Blocks",
	"display" => "checkbox",
	"comment" => "Allowed values: false for localized or true(checked) for distributed type.
This option improves multipath performance,
but limits resource allocations to 16 RBs (2.88 MHz).",
    )
),

"bearers" => array(

    "__" => array(
	"value" => "SRB configuration. Don't change mode from 'default' unless you are really sure.",
	"display" => "objtitle",
    ),

    // TBI!! How is this shown

    "Srb1.mode" => array(
	array("default", "unacknowledged", "acknowledged", "selected"=>"default"),
	"display" => "select",
	"required" => true,
	"javascript" => "onchange='srb_mode(1);'",
	"comment" => 'Default: default.
Alternately, specify "unacknowledged" mode configuration for SRB1:
Srb1.mode = unacknowledged
Srb1.rlcSnFieldLength = 10
Srb1.rlcTReordering = 35

Alternately, specify "acknowledged" mode configuration for SRB1:
Srb1.mode = acknowledged
Srb1.rlcTPollRetransmit = 45
Srb1.rlcTReordering = 35
Srb1.rlcTStatusProhibit = 0
Srb1.rlcMaxRetxThreshold = 4
Srb1.rlcPollPdu = 0
Srb1.rlcPollByte = 0
'
    ),
    "Srb1.rlcSnFieldLength" => array(
	    array(5,10,"selected"=>10),
	    "display" => "select",
	    "comment" => "Indicates the UM RLC SN field size, see TS 36.322 [7], in bits. Default 10.",
	    "triggered_by" => "srb1.mode",
    ),
    "Srb1.rlcTReordering" => array(
	    array("0", 5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,110,120,130,140,150,160,170,180,190,200, "selected" => "35"),
	    "display" => "select",
	    "comment" => "Timer for reordering in TS 36.322 [7], in milliseconds. Default 35.",
	    "triggered_by" => "srb1.mode"
    ),
    "Srb1.rlcTPollRetransmit" => array(
	    array(5,10,15,20,25, 30,35,40,45,50 ,55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected"=>"45"),
	    "display" => "select",
	    "comment" => "Timer for RLC AM in TS 36.322 [7], in milliseconds. Default 45.",
	    "triggered_by" => "srb1.mode"
    ),
    "Srb1.rlcTStatusProhibit" => array(
	    array("0", 5,10,15,20,25, 30,35,40,45,50 ,55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected" => "0"),
	    "display" => "select",
	    "comment" => "Timer for status reporting in TS 36.322 [7], in milliseconds. Default 0.",
	    "triggered_by" => "srb1.mode"
    ),
    "Srb1.rlcMaxRetxThreshold" => array(
	    array(1,2,3,4,6,8,16,32, "selected" => "4"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. Value t1 corresponds to 1 retransmission, t2 to 2 retransmissions and so on. Default 4.",
	    "triggered_by" => "srb1.mode"
    ),
    "Srb1.rlcPollPdu" => array(
	    array("0", 4,8,16,32,64,128,256,"selected"=>"0"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. Value 4 corresponds to 4 PDUs, 8 to 8 PDUs and so on. Infinity (0) corresponds to an infinite number of PDUs. Default 0. Value 0 matches infinity from TS.",
	    "triggered_by" => "srb1.mode"
    ),
    "Srb1.rlcPollByte" => array(
	    array("0", 25, 50, 75, 100, 125, 250, 375, 500, 750, 1000, 1250, 1500, 2000, 3000, "selected" => "0"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. In kB. Default 0. Value 0 matches infinity value from TS",
	    "triggered_by" => "srb1.mode"
    ),

    "Srb2.mode" => array(
	array("default", "unacknowledged", "acknowledged", "selected"=>"default"),
	"display" => "select",
	"required" => true,
	"javascript" => "onchange='srb_mode(2);'",
	"comment" => 'Default: default.
Alternately, specify "unacknowledged" mode configuration for SRB2:
Srb2.mode = unacknowledged
Srb2.rlcSnFieldLength = 10
Srb2.rlcTReordering = 35

Alternately, specify "acknowledged" mode configuration for SRB2:
Srb2.mode = acknowledged
Srb2.rlcTPollRetransmit = 45
Srb2.rlcTReordering = 35
Srb2.rlcTStatusProhibit = 0
Srb2.rlcMaxRetxThreshold = 4
Srb2.rlcPollPdu = 0
Srb2.rlcPollByte = 0
'
    ),
    "Srb2.rlcSnFieldLength" => array(
	    array(5,10,"selected"=>10),
	    "display" => "select",
	    "comment" => "Indicates the UM RLC SN field size, see TS 36.322 [7], in bits. Default 10.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcTReordering" => array(
	    array("0", 5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,110,120,130,140,150,160,170,180,190,200, "selected" => "35"),
	    "display" => "select",
	    "comment" => "Timer for reordering in TS 36.322 [7], in milliseconds. Default 35.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcTPollRetransmit" => array(
	    array(5,10,15,20,25, 30,35,40,45,50, 55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected"=>"45"),
	    "display" => "select",
	    "comment" => "Timer for RLC AM in TS 36.322 [7], in milliseconds. Default 45.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcTStatusProhibit" => array(
	    array("0", 5,10,15,20,25, 30,35,40,45,50 ,55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected" => "0"),
	    "display" => "select",
	    "comment" => "Timer for status reporting in TS 36.322 [7], in milliseconds. Default 0.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcMaxRetxThreshold" => array(
	    array(1,2,3,4,6,8,16,32, "selected" => "4"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. Value t1 corresponds to 1 retransmission, t2 to 2 retransmissions and so on. Default 4.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcPollPdu" => array(
	    array("0", 4,8,16,32,64,128,256,"selected"=>"0"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. Value 4 corresponds to 4 PDUs, 8 to 8 PDUs and so on. Infinity (0) corresponds to an infinite number of PDUs. Default 0. Value 0 matches infinity from TS.",
	    "triggered_by" => "srb2.mode"
    ),
    "Srb2.rlcPollByte" => array(
	    array("0", 25, 50, 75, 100, 125, 250, 375, 500, 750, 1000, 1250, 1500, 2000, 3000, "selected" => "0"),
	    "display" => "select",
	    "comment" => "Parameter for RLC AM in TS 36.322 [7]. In kB. Default 0. Value 0 matches infinity value from TS",
	    "triggered_by" => "srb2.mode"
    ),

    "drb" => array(
	"value" => "DRB default configuration - for QCI 9",
	"display" => "objtitle"
    ),

    "drb_um" => array(
	"value" => 'DRB "unacknowledged" mode - See 3GPP 36.508 - 4.8.2.1.2.1, 4.8.2.1.3.1',
	"display" => "objtitle"
    ),
    "DrbUm.rlcSnFieldLength" => array(
	array(5, 10, "selected" => "10"),
	"display" => "select",
	"comment" => "Indicates the UM RLC SN field size, see TS 36.322 [7], in bits. Default 10.",
	"required"=> true
    ),
    "DrbUm.rlcTReordering" => array(
	array("0", 5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,110,120,130,140,150,160,170,180,190,200, "selected" => "50"),
	"display" => "select",
	"comment" => "Timer for reordering in TS 36.322 [7], in milliseconds. Default 50.",
	"required"=> true
    ),
    "DrbUm.pdcpSnFieldLength" => array(
	array(7, 12, "selected"=>"12"),
	"display" => "select",
	"comment" => "Indicates the PDCP Sequence Number length in bits. Default 12.",
	"required"=> true
    ),
    "DrbUm.pdcpDiscardTimer" => array(
	array("0",50,100,150,300,500,750,1500, "selected"=>"100"),
	"display" => "select",
	"comment" => "Indicates the discard timer value specified in TS 36.323 [8]. Value in milliseconds. Default 100. Value 0 matches infinity from TS.",
	"required"=> true
    ),

    "drb_am" => array(
	"value" => 'DRB "acknowledged" mode - See 3GPP 36.508 - 4.8.2.1.2.2, 4.8.2.1.3.2',
	"display" => "objtitle"
    ),
    "DrbAm.rlcTPollRetransmit" => array(
	array(5,10,15,20,25, 30,35,40,45,50 ,55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected"=>"80"),
	"display" => "select",
	"comment" => "Timer for RLC AM in TS 36.322 [7], in milliseconds. Default 80.",
	"required"=> true
    ),
    "DrbAm.rlcTReordering" => array(
	array("0", 5,10,15,20,25,30,35,40,45,50,55,60,65,70,75,80,85,90,95,100,110,120,130,140,150,160,170,180,190,200, "selected" => "80"),
	"display" => "select",
	"comment" => "Timer for reordering in TS 36.322 [7], in milliseconds. Default 80.",
	"required"=> true
    ),
    "DrbAm.rlcTStatusProhibit" => array(
	array("0", 5,10,15,20,25, 30,35,40,45,50 ,55,60,65,70,75, 80,85,90,95,100, 105,110,115,120,125, 130,135,140,145,150, 155,160,165,170,175, 180,185,190,195,200, 205,210,215,220,225, 230,235,240,245,250, 300,350,400,450,500, "selected" => "60"),
	"display" => "select",
	"comment" => "Timer for status reporting in TS 36.322 [7], in milliseconds. Default 60.",
	"required"=> true
    ),
    "DrbAm.rlcMaxRetxThreshold" => array(
	array(1,2,3,4,6,8,16,32, "selected" => "4"),
	"display" => "select",
	"comment" => "Parameter for RLC AM in TS 36.322 [7]. Value t1 corresponds to 1 retransmission, t2 to 2 retransmissions and so on. Default 4.",
	"required"=> true
    ),
    "DrbAm.rlcPollPdu" => array(
	array("0", 4,8,16,32,64,128,256,"selected"=>"128"),
	"display" => "select",
	"comment" => "Parameter for RLC AM in TS 36.322 [7]. Value 4 corresponds to 4 PDUs, 8 to 8 PDUs and so on. Infinity (0) corresponds to an infinite number of PDUs. Default 128. Value 0 matches infinity value from TS.",
	"required"=> true
    ),
    "DrbAm.rlcPollByte" => array(
	array("0", 25, 50, 75, 100, 125, 250, 375, 500, 750, 1000, 1250, 1500, 2000, 3000, "selected" => "125"),
	"display" => "select",
	"comment" => "Parameter for RLC AM in TS 36.322 [7]. In kB. Default 125. Value 0 matches infinity value from TS",
	"required"=> true
    ),
    "DrbAm.pdcpSnFieldLength" => array(
	array(7, 12, "selected"=>"12"),
	"display" => "select",
	"comment" => "Indicates the PDCP Sequence Number length in bits. Default 12.",
	"required"=> true
    ),
    "DrbAm.pdcpDiscardTimer" => array(
	array("0",50,100,150,300,500,750,1500, "selected"=>"0"),
	"display" => "select",
	"comment" => "Indicates the discard timer value specified in TS 36.323 [8]. Value in milliseconds. Default 0. Value 0 matches infinity from TS.",
	"required"=> true
    ),
    "DrbAm.pdcpStatusRequired" => array(
	"value" => true,
	"display" => "checkbox",
	"comment" => "Indicates whether or not the UE shall send a PDCP Status Report upon re-establishment of the PDCP entity and upon PDCP data recovery as specified in TS 36.323 [8]. Default true."
    )
)
),

"core" => array(

"gtp" => array(

	"error_get_network" => array(
		"display" => "message",
		"column_name"=> "",
	    "value" => ""
	),	
    "addr4" => array(
	    "comment" => "IPv4 address to use with the eNodeB tunnel end",
	),

    "addr6" => array(
		"comment" => "IPv6 address to use with the eNodeB tunnel end",
	),
),

"mme" => array(

    "__" => array(
	"value" => "Hand-configured MME",
	"display" => "objtitle"
    ),

    "mme_address" => array(
	"column_name" => "Address",
	"comment" => "Ex: 192.168.56.62",
    ),
    "local" => array(
	"comment" => "Ex: 192.168.56.1",
    ),
    "streams" => array(
	"comment" => "Ex: 5",
    ),
    "dscp" => array(
	"column_name" => "DSCP",
	"comment" => "Ex: expedited"
    ),
    "add_mme_1" => array("value"=>"<a id=\"add_mme_1\" class='llink' onclick=\"fields_another_obj(2, 'add_mme_');\">Add another MME</a>", "display"=>"message"),


    "objtitle2" => array("display"=>"objtitle", "triggered_by"=>"2", "value"=>"2<sup>nd</sup> MME"),

    "mme_address_2" => array(
	"column_name" => "Address",
	"comment" => "Ex: 192.168.56.62",
	"triggered_by" => "2",
    ),
    "local_2" => array(
	"column_name" => "Local",
	"comment" => "Ex: 192.168.56.1",
	"triggered_by" => "2",
    ),
    "streams_2" => array(
	"column_name" => "Streams",
	"comment" => "Ex: 5",
	"triggered_by" => "2",
    ),
    "dscp_2" => array(
	"column_name" => "DSCP",
	"comment" => "Ex: expedited",
	"triggered_by" => "2"
    ),
    "add_mme_2" => array("value"=>"<a id=\"add_mme_2\" class='llink' onclick=\"fields_another_obj(3, 'add_mme_');\">Add another MME</a>", "display"=>"message", "triggered_by" => "2"),

    "objtitle3" => array("display"=>"objtitle", "triggered_by"=>"3", "value"=>"3<sup>rd</sup> MME"),

    "mme_address_3" => array(
	"column_name" => "Address",
	"comment" => "Ex: 192.168.56.62",
	"triggered_by" => "3",
    ),
    "local_3" => array(
	"column_name" => "Local",
	"comment" => "Ex: 192.168.56.1",
	"triggered_by" => "3",
    ),
    "streams_3" => array(
	"column_name" => "Streams",
	"comment" => "Ex: 5",
	"triggered_by" => "3",
    ),
    "dscp_3" => array(
	"column_name" => "DSCP",
	"comment" => "Ex: expedited",
	"triggered_by" => "3"
    ),
    "add_mme_3" => array("value"=>"<a id=\"add_mme_3\" class='llink' onclick=\"fields_another_obj(4, 'add_mme_');\">Add another MME</a>", "display"=>"message", "triggered_by" => "3"),

    "objtitle4" => array("display"=>"objtitle", "triggered_by"=>"4", "value"=>"4<sup>th</sup> MME"),

    "mme_address_4" => array(
	"column_name" => "Address",
	"comment" => "Ex: 192.168.56.62",
	"triggered_by" => "4",
    ),
    "local_4" => array(
	"column_name" => "Local",
	"comment" => "Ex: 192.168.56.1",
	"triggered_by" => "4",
    ),
    "streams_4" => array(
	"column_name" => "Streams",
	"comment" => "Ex: 5",
	"triggered_by" => "4",
    ),
    "dscp_4" => array(
	"column_name" => "DSCP",
	"comment" => "Ex: expedited",
	"triggered_by" => "4"
    ),
    "add_mme_4" => array("value"=>"<a id=\"add_mme_4\" class='llink' onclick=\"fields_another_obj(5, 'add_mme_');\">Add another MME</a>", "display"=>"message", "triggered_by" => "4"),

    "objtitle5" => array("display"=>"objtitle", "triggered_by"=>"5", "value"=>"5<sup>th</sup> MME"),

    "mme_address_5" => array(
	"column_name" => "Address",
	"comment" => "Ex: 192.168.56.62",
	"triggered_by" => "5",
    ),
    "local_5" => array(
	"column_name" => "Local",
	"comment" => "Ex: 192.168.56.1",
	"triggered_by" => "5",
    ),
    "streams_5" => array(
	"column_name" => "Streams",
	"comment" => "Ex: 5",
	"triggered_by" => "5",
    ),
    "dscp_5" => array(
	"column_name" => "DSCP",
	"comment" => "Ex: expedited",
	"triggered_by" => "5"
    )
),

"s1ap" => array(
)

),

"hardware" => array(
"site_info" => array(
    "location" => array(
	"column_name" => "Site location",
	"comment" => "No default is provided
Latitude, longitude
ddd.dddddd,ddd.dddddd format",
	"validity" => array("check_valid_geolocation")
    ),
    "siteName" => array(
	"column_name" => "Site name"
    ),
    "antennaDirection" => array(
	"column_name" => "Antenna direction",
	"comment" => "The direction the antenna is pointed, in degrees from true north.
No default is provided",
	"validity" => array("check_valid_float")
    ),
    "antennaBeamwidth" => array(
	"value" => "360",
	"column_name" => "Antenna beam width",
	"comment" => "Default is omni, 360 degrees",
	"validity" => array("check_field_validity", 1, 360)
    ),
    "reportingPeriod" => array(
	"value" => 15,
	"column_name" => "Reporting period",
	"comment" => "Reporting period in minutes for HW status. 
Default is 15 minutes",
	"validity" => array("check_valid_integer")
    ),
),
"site_equipment" => array(
    "antenna_type"=> array(
	"comment" => "String"
    ),
    "antenna_serial_number"=> array(
	"comment" => "String"
    ),
    "antenna_cable_type"=> array(
	"comment" => "String"
    ),
    "antenna_cable_length"=> array(
	"comment" => "String"
    ),
    "power_suply_type"=> array(
	"comment" => "String"
    ),
    "power_suply_serial_number"=> array(
	"comment" => "String"
    ),
    "custom_parameters" => array(
	"display" => "textarea",
	"comment" => "Custom parameters that will be stored on the equipment. <br/>
They are not used in configuration but are kept for informative purpose.<br/>
They are stored in key=value form. The keys should not match any of the above keys.<br/>
Ex:<br/>
antenna_serial_number2=342134<br/>
antenna_cable_length2=0.5<br/>
"
    ),
),
"shutdown" => array(
    "maxVswr" => array(
	"value" => "1.5",
	"comment" => "VSWR level for automatic shutdown of the power amplifier if the RF feed is damaged.
Default is 1.5",
	"validity" => array("check_valid_float")
    ),
    "amplifierShutdownTemp" => array(
	"value" => 80,
	"comment" => "Transistor temperature in deg C for safety shutdown of the power amplifier. 
Default is 80.",
	"validity" => array("check_field_validity", 1, 85)
    ),
    "amplifierRestartTemp" => array(
	"value" => 70,
	"comment" => "Default is 70.",
	"validity" => array("check_field_validity", 1, 85)
    ),
    "powerSupplyShutdownTemp" => array(
	"value" => 85,
	"comment" => "Transformer temperature in deg C for safety shutdown of power supply sections.
Default is 85",
	"validity" => array("check_field_validity", 1, 85)
    ),
    "powerSupplyRestartTemp" => array(
	"value" => 75,
	"comment" => "Default 75.",
	"validity" => array("check_field_validity", 1, 85)
    ),
    "softwareShutdownTemp" => array(
	"value" => 100,
	"comment" => "CPU core temperature for shutdown of the SatSite
If the temperature exceeds this level, all digital radio processing functions are suspended. Default 100.",
	"validity" => array("check_field_validity", 1, 100)
    ),
    "softwareRestartTemp" => array(
	"value" => 80,
	"comment" => "Default 80",
	"validity" => array("check_field_validity", 1, 85)
    )
),
),

"system" => array(
"system_information" => array(
    // System Information repetition parameters and Paging parameters
    // This params will be sent in "basic" section when sending request to API

    "SiWindowLength" => array(
	array(1, 2, 5, 10, 15, 20, 40, "selected"=>20),
	"display" => "select",
	"comment" => "Scheduler SI Window Length in milliseconds (frames)"
    ),

    "SiPeriodicity" => array(
	array(8, 16, 32, 64, 128, 256, 512, "selected"=>8),
	"display" => "select",
	"comment" => "Allowed values: powers of two between 8 and 512"
    ),

    "SiRedundancy" => array(
	array(1,2,3,4,5,6,7,8, "selected"=> 2),
	"display" => "select",
	"comment" => "Should be larger for cell with large coverage area."
    ),

    "DefaultPagingCycle" => array(
	array(32,64,126,256,"selected"=>32),
	"display" => "select",
	"comment" => "Default Paging Cycle for UE DRX",
    ),

    "RxLevelMinimum" => array(
	"value" => "-70",
	"comment" => 'Minimum power level for cell reselection, dBm ("q_RxLevMin" in SIB1)
Allowed range -70 .. -22. Default -70.'
    ),
),

"advanced" => array(

    "GridLength" => array(
	"value" => 8,
	"comment" => "The length of the resource grid circular buffer in subframes. Default 8",
	"validity" => array("check_field_validity", 1, 16)
    ),

    "LeadModulation" => array(
	"value" => 14,
	"comment" => "Maximum samples in the future to compute radio modulation
Given in OFDM symbol periods
Minimum 2 symbols, maximum 2 subframes, default 1 subframe.
Default value: 14",
	"validity" => array("check_field_validity", 2, 28)
    ),

    "LeadScheduling" => array(
	array(1,2,3,4,5,"selected"=>2),
	"display" => "select",
	"comment" => "Maximum number of subframes to schedule in advance, 1 to 5, default 2."
    ),

    "RadioPriority" => array(
	array("normal","high","highest","selected"=>"high"),
	"display" => "select",
	"comment" => 'Radio thread priority, default "high", can be also "normal" or "highest"'
    ),

    "ModulatorPriority"  => array(
	array("normal","high","highest","selected"=>"high"),
	"display" => "select",
	"comment" => 'Modulator thread priority, default "high", can be also "normal" or "highest"'
    ),

    "SchedulerPriority" => array(
	array("normal","high","highest","selected"=>"normal"),
	"display" => "select",
	"comment" => 'Scheduler thread priority, default "normal", can be also "high" or "highest"'
    ),
),

"scheduler" => array(

    "SibModulationRate" => array(
	array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,"selected"=>2),
	"display" => "select",
	"comment" => "Sib modulation rate"
    ),

    "SibDci" => array(
	// commented ones are not supported
	array(/*"dci0", "dci1", */ "dci1a", /*"dci1a_pdcch", "dci1b",*/ "dci1c", /*"dci1d", "dci2", "dci2a", "dci3", "dci3a",*/ "selected"=>"dci1a"),
	"display" => "select",
	"comment" => "DCI for SIB"
    ),

    "PcchMcs" => array(
	array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,"selected"=>2),
	"display" => "select",
	"comment" => "PCCH MCS"
    ),

    "PcchDci" => array(
	array("dci0", "dci1", "dci1a", "dci1a_pdcch", "dci1b", "dci1c", "dci1d", "dci2", "dci2a", "dci3", "dci3a", "selected"=>"dci1a"),
	"display" => "select",
	"comment" => "DCI for PCCH"
    ),

    "RarMcs" => array(
	array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,"selected"=>2),
	"display" => "select",
	"comment" => "RAR MCS"
    ), 

    "RarDci" => array(
	array("dci0", "dci1", "dci1a", "dci1a_pdcch", "dci1b", "dci1c", "dci1d", "dci2", "dci2a", "dci3", "dci3a","selected"=>"dci1a"),
	"display" => "select",
	"comment" => "DCI for RAR"
    ),

    "downlinkDci" => array(
	array("dci0", "dci1", "dci1a", "dci1a_pdcch", "dci1b", "dci1c", "dci1d", "dci2", "dci2a", "dci3", "dci3a","selected"=>"dci1a"),
	"display" => "select",
	"comment" => "DCI for downlink"
    ), 

    "uplinkDci" => array(
	// commented ones are not supported
	array("dci0", /*"dci1", "dci1a", "dci1a_pdcch", "dci1b", "dci1c", "dci1d", "dci2", "dci2a", "dci3", "dci3a",*/ "selected"=>"dci0"),
	"display" => "select",
	"comment" => "DCI for uplink"
    ),

    "DistributedVrbs" => array(
	"display" => "checkbox",
	"comment" => "Checked if the resource blocks are distributed"
    ),

    "PrachResponseDelay" => array(
	"comment" => "Integer. Response delay for PRACH events in subrames.",
	"validity" => array("check_valid_integer")
    ),
),

"measurements" => array(

    "reportingPeriod" => array(
	"value" => "15",
	"comment" => 'Measurement reporting period in minutes. Default 15',
	"validity" => array("check_field_validity",1,14400)
    ),

    "reportingPath" => array( 
	"comment" => 'Path to store XML measurement file for FTP access.
If this is not set, no file is written.',
    ),
),

"radiohardware" => array(

    "MaximumOutput" => array(
	"value" => "43",
	"comment" => 'Radio maximum output power, dBm
Set by calibration and should not be changed.
Default value for SatSite 142 is 43 dBm',
	"validity" => array("check_field_validity",false,false,false,"43")
    ),

    "ReceiverReference" => array(
	"value" => "-20",
	"comment" => "Receiver saturation point at full gain, referenced to the antenna port.
Set by calibration and should not be changed.
Default value for SatSite 142 is -20 dBm.",
	"validity" => array("check_field_validity",false,false,false,"-20")
    ),
),

),


"access_channels" => array(
"pdsch" => array(
     // This params will be sent in "basic" section when sending request to API

    'Pdsch.RefPower' => array(
	"value" => -20,
	"comment" => 'Reference Signal Power in dB ("referenceSignalPower" in SIB2)
Allowed values -60 .. 50. Default -20.',
	"validity" => array("check_field_validity",-60,50)
    ),
),

"pusch" => array(
    // This params will be sent in "basic" section when sending request to API

    "Pusch.Qam64" => array(
	"value" => false,
	"display" => "checkbox",
	"comment" => 'Allow use of QAM64 in uplink ("enable64QAM" in SIB2). Default false.'
    ),

    "Pusch.CyclicShift" => array(
	array(0,1,2,3,4,5,6,7,"selected"=>3),
	"display" => "select",
	"comment" => 'Reference Signal Cyclic Shift ("cyclicShift" in SIB2). Default 3.'
    ),
),

"pucch" => array(
    // This params will be sent in "basic" section when sending request to API

    "Pucch.Delta" => array(
	array(1,2,3,"selected"=>1),
	"display" => "select",
	"comment" => 'Delta Shift ("deltaPUCCH_Shift" in SIB2). Default 1.'
    ),

    "Pucch.RbCqi" => array(
	"value" => 3,
	"comment" => 'Bandwidth available for use by PUCCH formats 2/2a/2b, in RBs ("nRB_CQI" in SIB2)
Allowed values 0..98, but must not exceed number of RBs in system bandwidth
Larger values support larger number of connect UEs at the expense of uplink BW.
Default 3.',
	"validity" => array("check_field_validity",0,98)
    ),

    "Pucch.CsAn" => array(
	array(0,1,2,3,4,5,6,7,"selected"=>3),
	"display" => "select",
	"comment" => 'Number of cyclic shifts used for PUCCH formats 1/1a/1b in a resource block with a mix of
formats 1/1a/1b and 2/2a/2b ("nCS_AN" in SIB2)
Default 3'
    ),

    "Pucch.An" => array(
	"value" => "0",
	"column_name" => "Resource allocation offset",
	"comment" => 'Resource allocation offset parameter ("n1PUCCH_AN" in SIB2)
Allowed values 0..2047. Default 45',
	"validity" => array("check_field_validity",0,2047)
    )
),
"prach" => array(
    // This params will be sent in "basic" section when sending request to API

    "Prach.Preambles" => array(
	array(4,8,12,16,20,24,28,32,36,40,44,48,52,56,60,64,"selected"=>4),
	"display" => "select",
	"comment" => 'Number of PRACH preambles ("numberOfRA_Preambles" in SIB2)
Allowed values multiples of 4, 4..64. Default 4.
Larger values reduce PRACH contention at the expense of computational load.'
    ),

    "Prach.PowerStep" => array(
	array(0,2,4,6,"selected"=>4),
	"display" => "select",
	"comment" => 'Power ramping step, dB ("powerRampingStep" in SIB2)'
    ),

    "Prach.InitialTarget" => array(
	array("-90","-92", "-94", "-96", "-98", "-100", "-102", "-104", "-106", "-108", "-110", "-112", "-114", "-116", "-118", "-120", "selected" => "-90"),
	"column_name" => 'Initial RSSI Target',
	"comment" => 'Initial RSSI Target, dBm ("preambleInitialReceivedTargetPower" in SIB2)
Allows values multiples of 2, -90 .. -120. Default -90.',
    ),

    "Prach.TransMax" => array(
	array(3, 4, 5, 6, 7, 8, 10, 20, 50, 100, 200, "selected"=>200),
	"display" => "select",
	"column_name" => "Maximum transmissions",
	"comment" => 'Maximum transmissions ("preambleTransMax" in SIB2)'
    ),

    "Prach.ResponseWindow" => array(
	array(/*2,3,4,5,6,7,8,*/10,"selected"=>"10"),
	"display" => "select",
	"comment" => 'Response window size in subframes ("ra_ResponseWindowSize" in SIB2)
Allowed values 2..8, 10 (not 9) by the spec,
but we only support value 10.'
    ),

    "Prach.ContentionTimer" => array(
	array(8,16,24,32,40,48,56,64,"selected"=>64),
	"display" => "select",
	"comment" => 'Contention Resolution Timer in subframes ("mac_ContentionResolutionTimer" in SIB2)'
    ),

    "Prach.ConfigIndex" => array(
	array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15, "selected"=>14),
	"display" => "select",
	"comment" => 'Configuration Index ("prach_ConfigIndex" in SIB2)',
	//"validity" => array("check_field_validity",0,63)  // prev validation and valid range
    ),

    "Prach.ZeroCorr" => array(
	array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,"selected"=>1),
	"display" => "select",
	"comment" => 'Zero Correlation Zone ("zeroCorrelationZoneConfig" in SIB2)'
    ),
),

"pdcch" => array(
    "CFI" => array(
	array(1,2,3,"selected"=>2),
	"display" => "select",
	"comment" => "Control format indicator
Determines available bandwidth for the PDCCH."
    ),

    "Ng" => array(
	array("oneSixth", "half", "one", "two", "selected"=>"one"),
	"display" => "select",
	"column_name" => "PHICH Ng factor",
	"comment" => "PHICH Ng factor (from MIB in PBCH)
Determines bandwidth used for PHICH, affects available bandwidth for PDCCH."
    ),

    "PdcchFormat" => array(
	array(0,1,2,3,"selected"=>2),
	"display" => "select",
	"comment" => "PDCCH format as specified in TS 136.211 Table 6.8.1-1
Also called the aggregation level.
Large aggregation level gives more robust PDCCH transmission,
at the expense of PDCCH capacity."
    ),
)
),


"developers" => array(

"general" => array(

    "mode" => array(
	"value" => "ENB",
	"comment" => "Operation mode
This setting determines which control Javascript file to load.
Optional Javascript files are used for special test modes. Default ENB."
    ),

    "autostart" => array(
	"value"   => true,
	"display" => "checkbox",
	"comment" => "Start the cell operation at load time
Disabling autostart allows deferring cell startup"
    ),

    "transceiver" => array(
	"value" => true,
	"display" => "checkbox",
	"comment" => "Start the radio transceiver (TrxManager).
This setting is mainly used for testing, since most of the ENB is
driven by the radio transceiver."
    ),
),

"radio" => array(

    "radio_driver" => array(
	"comment" => 'Name of the radio device driver to use
Leave it empty to try any installed driver',
    ),
),

"uu-simulator" => array(

    "address" => array(
	"comment"=> "Example: 127.0.0.1",
	"validity"=> array("check_valid_ipaddress")
    ),
    
    "port" => array(
	"comment" => "Example: 9350",
	"validity"=> array("check_valid_integer") 
    ),

    "pci" => array(
	"comment" => "Example: 0",
	"validity"=> array("check_valid_integer")
    ),

    "earfcn" => array(
	"comment" => "Example: 0",
	"validity"=> array("check_valid_integer")
    ),

    "broadcast" => array( 
	"comment" => "Example: 127.0.0.1:9351, 127.0.0.1:9352",
	"validity"=> array("check_broadcast")
    ),

    "max_pdu" => array(
	"comment" => "Example: 128",
    )
),

"uu-loopback" => array(

    "AddrSubnet" => array(
	"comment" => "IPv4 subnet for allocating IP addresses to TUN interfaces
Ex: 10.167.227.%u",
	"validity" => array("check_field_validity", false, false, "^([0-9]{1,3}\.){3}%u$")
    ),

    "AuxRoutingTable" => array(
	"comment" => "Auxiliary routing table that is used for configuring TUN interfaces
routes. Can be the table number or name (if it's configured in
/etc/iproute2/rt_tables)
Ex: 131",
	"validity" => array("check_auxrouting_table")
    ),

    "SymmetricMacCapture" => array(
	"display" => "checkbox",
	"comment" => "Whether to submit wireshark capture for MAC PDUs on both LteConnection
instances. If disabled, only the LteConnection that represents the
eNodeB submits wireshark capture, causing each MAC PDUs to appear only
once and in the right direction (DL-SCH vs. UL-SCH)."
    ),
),

"test-enb" => array(

    "__" => array(
	"value" => "PDCCH Test",
	"display" => "objtitle"
    ),

    "PdcchTestMode" => array(
	"display" => "checkbox",
	"comment" => "Specifies if the PDCCH test mode is on or off.
PDCCH test mode sends extra messages on PDCCH."
    ),

    "___" => array(
	"value" => "PDSCH traffic simulator",
	"display" => "objtitle"
    ),

    "DownlinkTrafficGenererator" => array(
	"display" => "checkbox",
	"comment" => "Specifies if the traffic generator test mode is on or off.
The test mode generate random traffic on shared channel (PDSCH) on random resource blocks
NOTE: When test mode is enabled, regular PDSCH traffic is suppressed,included SIBs."
    ),

    "DownlinkTrafficGeneratorLoad" => array(
	"value" => "0",
	"comment" => "The PDSCH utilisation for the traffic generator, in percentages.
; Allowed integer range: 0 - 100. Default value: 0."
    ),

    "DownlinkTrafficGeneratorSubframes" => array(
	"value" => "10",
	"comment" => "The number of subframes to use for traffic simulation.
Traffic simulator uses this many subframes, starting from subframe 0."
    ),

    "____" => array(
	"value" => "Channel controls for testing
Enable and disable specific channel types.

Indicates if the named PHY channel's data can be sent on the TX grid.
Note that the RsEnabled is the master enabler for all the reference signals,
currently only CSRS. To enable CSRS, RsEnabled must also be true.
Default is yes for all.
",
	"display" => "message"
    ),

    "PhichEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "PcfichEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "PssEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "SssEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "CsrsEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "PdschEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ), 

    "PdcchEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ), 

    "PbchEnabled" => array(
	"display" => "checkbox",
	"value" => true
    ),

    "RsEnabled" => array(
	"display" => "checkbox",
	"value" => true
    )
),

"test-scheduler" => array(
    
    "scheduler-generator" => array(
	"value" => true,
	"display" => "checkbox",
	"comment" => "Flag which enables/disables scheduler UE traffic generator"
    ),

    "scheduler-generator.ues" => array(
	"value" => 5,
	"comment" => "Integer; The number of UE's which generates traffic"
    ),

    "scheduler-generator.qci" => array(
	"value" => "7",
	"comment" => "Integer; Downlink cqi to obtain MCS"
    ),

    "scheduler-generator.cqi" => array(
	"value" => "7",
	"comment" => "Integer; Uplink cqi to obtain MCS",
    ),

    "scheduler-generator.gbr" => array(
	"value" => 10000,
	"comment" => "Integer; UE GBR"
    ),

    "scheduler-generator.ambr" => array(
	"value" => "0",
	"comment" => "Integer; UE AMBR"
    ),

    "scheduler-generator.upload-rate" => array(
	"value" => "10000",
	"comment" => "Integer; This specifies the maximum number of bytes to be generated in a second for upload."
    ),

    "scheduler-generator.download-rate" => array(
	"value" => "10000",
	"comment" => "Integer; This specifies the maximum number of bytes to be generated in a second for download."
    )
)
)

);

	if (isset($_SESSION["enb_fields"]["interfaces_ips"])) {
		$interfaces_ips = $_SESSION["enb_fields"]["interfaces_ips"]["both"];
		$ipv4 = $_SESSION["enb_fields"]["interfaces_ips"]["ipv4_gtp"];
		$ipv6 = $_SESSION["enb_fields"]["interfaces_ips"]["ipv6_gtp"];

	} else {
		if (!$request_protocol)
			$request_protocol = "http";

		$url = "$request_protocol://$server_name/api.php";
		$out = array("request"=>"get_net_address","node"=>"satsite","params"=>"net_address");
		$res = make_request($out, $url);

		if ($res["code"]=="0") {

			$interfaces_ips = build_net_addresses_dropdown($res, true);
			$_SESSION["enb_fields"]["interfaces_ips"]["both"] = $interfaces_ips;

			$ipv4 = build_net_addresses_dropdown($res, true, "ipv4");
			$_SESSION["enb_fields"]["interfaces_ips"]["ipv4_gtp"] = $ipv4;
			$ipv6 = build_net_addresses_dropdown($res, true, "ipv6");
			$_SESSION["enb_fields"]["interfaces_ips"]["ipv6_gtp"] = $ipv6;

			// keep the error message in session if request 'get_net_address' failed
		} else {
			$_SESSION["enb_fields"]["error_get_net_interfaces"] = "[".$res["code"]."] ".$res["message"];
		}
	}						

	if (isset($_SESSION["enb_fields"]["error_get_net_interfaces"])) {
		$enodeb_params["core"]["gtp"]["error_get_network"] = array("display"=>"message", "value"=> "<div class=\"notice\"><font class=\"error\">Error!! </font><font style=\"font-weight:bold;\">".$_SESSION["enb_fields"]["error_get_net_interfaces"]. " Please fix the error before setting the addresses.</font></div>");
	} else {
		$enodeb_params["core"]["gtp"]["addr4"] = array($ipv4,"display"=>"select", "comment" => "IPv4 address to use with the eNodeB tunnel end");
		$enodeb_params["core"]["gtp"]["addr6"] = array($ipv6,"display"=>"select", "comment" => "IPv6 address to use with the eNodeB tunnel end");

		$enodeb_params["core"]["mme"]["local"] = array($interfaces_ips,"display"=>"select","comment"=>"Ex: 192.168.56.1");
		$enodeb_params["core"]["mme"]["local_2"] = array($interfaces_ips,"display"=>"select","comment"=>"Ex: 192.168.56.1","column_name"=>"Local", "triggered_by" => "2");
		$enodeb_params["core"]["mme"]["local_3"] = array($interfaces_ips,"display"=>"select","comment"=>"Ex: 192.168.56.1", "column_name"=>"Local", "triggered_by" => "3");
		$enodeb_params["core"]["mme"]["local_4"] = array($interfaces_ips,"display"=>"select","comment"=>"Ex: 192.168.56.1", "column_name"=>"Local", "triggered_by" => "4");
		$enodeb_params["core"]["mme"]["local_5"] = array($interfaces_ips,"display"=>"select","comment"=>"Ex: 192.168.56.1", "column_name"=>"Local", "triggered_by" => "5");
	}

	return $enodeb_params;
}
?>
