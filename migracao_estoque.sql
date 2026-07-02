-- Migracao: coluna estoque + carga completa a partir de dados/estoque.csv
-- Gerado em: 02/07/2026 02:18:48
USE `calculadora_offgrid`;

ALTER TABLE `bateria` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;
ALTER TABLE `placa_solar` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;
ALTER TABLE `controlador_carga` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;
ALTER TABLE `disjuntor` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;
ALTER TABLE `estrutura_solar` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;
ALTER TABLE `inversor` ADD COLUMN IF NOT EXISTS `estoque` DECIMAL(10,4) DEFAULT NULL AFTER `preco`;

-- BATERIA
UPDATE `bateria` SET `estoque` = 12.0000 WHERE `sku` = 13917;
UPDATE `bateria` SET `estoque` = 0.0000 WHERE `sku` = 14121;
UPDATE `bateria` SET `estoque` = 22.0000 WHERE `sku` = 14372;
UPDATE `bateria` SET `estoque` = 2.0000 WHERE `sku` = 27548;
UPDATE `bateria` SET `estoque` = 8.0000 WHERE `sku` = 27612;
UPDATE `bateria` SET `estoque` = 2.0000 WHERE `sku` = 27919;
UPDATE `bateria` SET `estoque` = 0.0000 WHERE `sku` = 28887;
UPDATE `bateria` SET `estoque` = 0.0000 WHERE `sku` = 31570;

-- PLACA SOLAR
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 18450;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 20745;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 20746;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 22444;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 22473;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 22479;
UPDATE `placa_solar` SET `estoque` = 1.0000 WHERE `sku` = 23069;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 23236;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 23237;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 23904;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 24248;
UPDATE `placa_solar` SET `estoque` = 221.0000 WHERE `sku` = 24533;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 24534;
UPDATE `placa_solar` SET `estoque` = 11.0000 WHERE `sku` = 25220;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 25896;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 26035;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 26036;
UPDATE `placa_solar` SET `estoque` = 5.0000 WHERE `sku` = 26386;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 26387;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 26863;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 26890;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 27193;
UPDATE `placa_solar` SET `estoque` = 1.0000 WHERE `sku` = 27233;
UPDATE `placa_solar` SET `estoque` = 2.0000 WHERE `sku` = 27254;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 28138;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 28233;
UPDATE `placa_solar` SET `estoque` = 2.0000 WHERE `sku` = 28824;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 28825;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 28864;
UPDATE `placa_solar` SET `estoque` = 2.0000 WHERE `sku` = 29546;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 29547;
UPDATE `placa_solar` SET `estoque` = 12.0000 WHERE `sku` = 30214;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 30448;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 30510;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 30520;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 30606;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 30720;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 31169;
UPDATE `placa_solar` SET `estoque` = 0.0000 WHERE `sku` = 31228;

-- CONTROLADOR CARGA
UPDATE `controlador_carga` SET `estoque` = 62.0000 WHERE `sku` = 22776;
UPDATE `controlador_carga` SET `estoque` = 2.0000 WHERE `sku` = 22777;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22778;
UPDATE `controlador_carga` SET `estoque` = 1.0000 WHERE `sku` = 22779;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22780;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22781;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22782;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22783;
UPDATE `controlador_carga` SET `estoque` = 6.0000 WHERE `sku` = 22785;
UPDATE `controlador_carga` SET `estoque` = 1.0000 WHERE `sku` = 22786;
UPDATE `controlador_carga` SET `estoque` = 8.0000 WHERE `sku` = 22790;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22791;
UPDATE `controlador_carga` SET `estoque` = 2.0000 WHERE `sku` = 22792;
UPDATE `controlador_carga` SET `estoque` = 0.0000 WHERE `sku` = 22793;

-- DISJUNTOR
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 21341;
UPDATE `disjuntor` SET `estoque` = 1.0000 WHERE `sku` = 21342;
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 21343;
UPDATE `disjuntor` SET `estoque` = 2.0000 WHERE `sku` = 21344;
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 21345;
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 21346;
UPDATE `disjuntor` SET `estoque` = 1.0000 WHERE `sku` = 21347;
UPDATE `disjuntor` SET `estoque` = 61.0000 WHERE `sku` = 21348;
UPDATE `disjuntor` SET `estoque` = 7.0000 WHERE `sku` = 26935;
UPDATE `disjuntor` SET `estoque` = 3.0000 WHERE `sku` = 26936;
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 26937;
UPDATE `disjuntor` SET `estoque` = 2.0000 WHERE `sku` = 26938;
UPDATE `disjuntor` SET `estoque` = 1.0000 WHERE `sku` = 26939;
UPDATE `disjuntor` SET `estoque` = 1.0000 WHERE `sku` = 26940;
UPDATE `disjuntor` SET `estoque` = 5.0000 WHERE `sku` = 26941;
UPDATE `disjuntor` SET `estoque` = 0.0000 WHERE `sku` = 26942;

-- ESTRUTURA SOLAR
UPDATE `estrutura_solar` SET `estoque` = 1.0000 WHERE `sku` = 21749;
UPDATE `estrutura_solar` SET `estoque` = 0.0000 WHERE `sku` = 28521;

-- INVERSOR
UPDATE `inversor` SET `estoque` = 1.0000 WHERE `sku` = 22768;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 22769;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 22770;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 22771;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 25378;
UPDATE `inversor` SET `estoque` = 57.0000 WHERE `sku` = 25379;
UPDATE `inversor` SET `estoque` = 42.0000 WHERE `sku` = 25380;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 25381;
UPDATE `inversor` SET `estoque` = 1.0000 WHERE `sku` = 25382;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 25414;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 29763;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 31377;

-- SKUs do banco sem linha no CSV (bateria): estoque zerado
UPDATE `bateria` SET `estoque` = 0.0000 WHERE `sku` = 31457;

-- SKUs do banco sem linha no CSV (estrutura_solar): estoque zerado
UPDATE `estrutura_solar` SET `estoque` = 0.0000 WHERE `sku` = 0;

-- SKUs do banco sem linha no CSV (inversor): estoque zerado
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 25413;
UPDATE `inversor` SET `estoque` = 0.0000 WHERE `sku` = 25415;

-- SKUs presentes no CSV, mas ausentes no banco:
-- SKU 31467 ignorado
