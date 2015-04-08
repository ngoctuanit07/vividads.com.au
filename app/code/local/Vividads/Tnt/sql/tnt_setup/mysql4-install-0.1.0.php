<?php

$installer = $this;

$installer->startSetup();

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('tnt_services')};
CREATE TABLE `tnt_services` (
  `tnt_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tnt_code` varchar(45) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `status` smallint(6) NOT NULL DEFAULT '0',
  `service_type` VARCHAR(10) NOT NULL,
  PRIMARY KEY (`tnt_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8

{$this->getTable('tnt_routingC')};
CREATE TABLE `tnt_routingC` (
`service` varchar(10) DEFAULT NULL,
`suburb` varchar(100) DEFAULT NULL,
`state` varchar(100) DEFAULT NULL,
`postcode` varchar(4) DEFAULT NULL,
`origindepot` varchar(100) DEFAULT NULL,
`gateway` varchar(100) DEFAULT NULL,
`onforwarding` varchar(100) DEFAULT NULL,
`routebin` varchar(10) DEFAULT NULL,
KEY `index1` (`postcode`,`state`),
KEY `indexstate` (`state`)
)ENGINE=MyISAM DEFAULT CHARSET=latin1


{$this->getTable('tnt_typeA')};
CREATE TABLE `tnt_typeA` (
  `TransmissionIdentifier` int(20) NOT NULL AUTO_INCREMENT,
  `SenderInterchangeAddress` varchar(15) NOT NULL,
  `ReceiverInterchangeAddress` varchar(15) NOT NULL,
  `TradingPartnerIdentifier` varchar(12) NOT NULL,
  `Carrier` varchar(3) NOT NULL,
  `FileGenerationDate` date NOT NULL,
  `FileGenerationTime` varchar(15) NOT NULL,
  `FileVersionNumber` varchar(2) NOT NULL DEFAULT '12',
  `RoutingAffectiveDate` varchar(8) NOT NULL,
  `RoutingVersionNumber` varchar(3) NOT NULL,
  `uploaded` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`TransmissionIdentifier`)
) ENGINE=MyISAM AUTO_INCREMENT=860 DEFAULT CHARSET=latin1

{$this->getTable('tnt_typeB')};
CREATE TABLE `tnt_typeB` (
  `ManifestIdentifier` int(10) NOT NULL,
  `SenderIdentifierCode` varchar(15) NOT NULL,
  `SenderAccountNumber` varchar(10) DEFAULT '21664906',
  `SenderName` varchar(30) NOT NULL,
  `SenderAddressline1` varchar(30) NOT NULL,
  `SenderAddressLine2` varchar(30) NOT NULL,
  `SenderSuburb` varchar(20) NOT NULL,
  `SenderState` varchar(3) NOT NULL,
  `SenderPostCode` varchar(4) NOT NULL,
  `SenderContactName` varchar(20) NOT NULL,
  `SenderContactPhone` varchar(13) NOT NULL,
  PRIMARY KEY (`ManifestIdentifier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1


{$this->getTable('tnt_typeC')};
CREATE TABLE `tnt_typeC` (
  `ManifestIdentifier` int(10) NOT NULL,
  `ConsignmentNoteNumber` varchar(15) NOT NULL,
  `LinesthisConsignment` int(3) NOT NULL,
  `ReceiverIdentifierCode` varchar(15) NOT NULL,
  `ReceiverAccountNumber` varchar(10) NOT NULL,
  `ReceiverName` varchar(30) NOT NULL,
  `ReceiverAddressline1` varchar(30) NOT NULL,
  `ReceiverAddressline2` varchar(30) NOT NULL,
  `ReceiverSuburb` varchar(20) NOT NULL,
  `ReceiverState` varchar(3) NOT NULL,
  `ReceiverPostcode` varchar(4) NOT NULL,
  `ReceiverContactName` varchar(20) NOT NULL,
  `ReceiverContactPhone` varchar(13) NOT NULL,
  `ConNoteDate` varchar(10) NOT NULL,
  `Service` varchar(4) NOT NULL,
  `DangerousGoodsFlag` varchar(1) NOT NULL,
  `PayFlag` varchar(1) NOT NULL,
  `CancelledFlag` varchar(1) NOT NULL,
  `FoodsstuffFlag` varchar(1) NOT NULL,
  `ExtendedWarrantyValue` int(8) NOT NULL,
  `ExtendedWarrantyClass` varchar(1) NOT NULL,
  `AdditionalService1` varchar(3) NOT NULL,
  `AdditionalService2` varchar(3) NOT NULL,
  `AdditionalService3` varchar(3) NOT NULL,
  `AdditionalService4` varchar(3) NOT NULL,
  `AdditionalService5` varchar(3) NOT NULL,
  `HandRateAmount` decimal(7,2) NOT NULL,
  `OtherCharges` decimal(7,2) NOT NULL,
  `CustomerConsignmentReference` varchar(30) NOT NULL,
  UNIQUE KEY `ConsignmentNoteNumber` (`ConsignmentNoteNumber`),
  UNIQUE KEY `ConsignmentNoteNumber_2` (`ConsignmentNoteNumber`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

{$this->getTable('tnt_typeD')};
CREATE TABLE `tnt_typeD` (
  `ManifestIdentifier` int(10) NOT NULL,
  `ConNoteNumber` varchar(15) NOT NULL,
  `ThirdPartyIdentifierCode` varchar(15) NOT NULL,
  `ThirdPartyAccountNumber` varchar(10) DEFAULT '21664906',
  `ThirdPartyName` varchar(30) NOT NULL,
  `ThirdPartyAddressline1` varchar(30) NOT NULL,
  `ThirdPartyAddressLine2` varchar(30) NOT NULL,
  `ThirdPartySuburb` varchar(20) NOT NULL,
  `ThirdPartyState` varchar(3) NOT NULL,
  `ThirdPartyPostCode` varchar(4) NOT NULL,
  PRIMARY KEY (`ManifestIdentifier`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1

{$this->getTable('tnt_typeE')};
CREATE TABLE `tnt_typeE` (
  `ManifestIdentifier` int(10) NOT NULL,
  `ConNoteNumber` varchar(15) NOT NULL,
  `TextType` int(1) NOT NULL,
  `TextLine` varchar(80) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1


{$this->getTable('tnt_typeF')};
CREATE TABLE `tnt_typeF` (
  `ManifestIdentifier` int(10) NOT NULL,
  `ConNoteNumber` varchar(15) NOT NULL,
  `LineSequence` int(3) NOT NULL,
  `CustomerReference` varchar(15) NOT NULL,
  `DescriptionofGoods` varchar(20) NOT NULL,
  `CommodityCode` varchar(6) NOT NULL,
  `NumberofItems` int(5) NOT NULL,
  `Weight` decimal(7,3) NOT NULL,
  `UnitofMeasureWeight` varchar(2) NOT NULL,
  `ItemDimensionLength` decimal(5,3) NOT NULL,
  `ItemDimensionWidth` decimal(5,3) NOT NULL,
  `ItemDimensionHeight` decimal(5,3) NOT NULL,
  `UnitofMeasureDimension` varchar(2) NOT NULL,
  `CubicVolume` decimal(8,4) NOT NULL,
  `UnitofMeasureCubicVolume` varchar(2) NOT NULL,
  `ArticleQty` int(5) NOT NULL,
  `GarmentQty` int(5) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1

{$this->getTable('tnt_typeH')};
CREATE TABLE `tnt_typeH` (
  `ManifestIdentifier` int(10) NOT NULL,
  `ConNoteNumber` varchar(15) NOT NULL,
  `Sequence` int(3) NOT NULL,
  `ItemIdentifier1` varchar(20) NOT NULL,
  `ItemIdentifier2` varchar(20) NOT NULL,
  `ItemIdentifier3` varchar(20) NOT NULL,
  `ItemIdentifier4` varchar(20) NOT NULL,
  `ItemIdentifier5` varchar(20) NOT NULL,
  `ItemIdentifier6` varchar(20) NOT NULL,
  `ItemIdentifier7` varchar(20) NOT NULL,
  `ItemIdentifier8` varchar(20) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1


");

$installer->endSetup(); 


