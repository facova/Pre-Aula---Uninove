SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL';

CREATE SCHEMA IF NOT EXISTS `chico` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci ;;


USE `chico` ;

-- -----------------------------------------------------
-- Table `chico`.`categoria`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`categoria` (
  `idCategoria` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(45) NOT NULL ,
  PRIMARY KEY (`idCategoria`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chico`.`status`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`status` (
  `idStatus` INT NOT NULL AUTO_INCREMENT ,
  `descricao` VARCHAR(250) NOT NULL ,
  PRIMARY KEY (`idStatus`) )
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chico`.`produto`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`produto` (
  `idproduto` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(100) NOT NULL ,
  `descricao` VARCHAR(250) NOT NULL ,
  `valor` DOUBLE NOT NULL ,
  `categoria_idCategoria` INT NOT NULL ,
  PRIMARY KEY (`idproduto`) ,
  INDEX `fk_produto_categoria` (`categoria_idCategoria` ASC) ,
  CONSTRAINT `fk_produto_categoria`
    FOREIGN KEY (`categoria_idCategoria` )
    REFERENCES `chico`.`categoria` (`idCategoria` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chico`.`cliente`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`cliente` (
  `idcliente` INT NOT NULL AUTO_INCREMENT ,
  `nome` VARCHAR(50) NOT NULL ,
  `sobrenome` VARCHAR(50) NOT NULL ,
  `cpf` INT(11) NOT NULL ,
  `dt_nasc` DATE NOT NULL ,
  `sexo` CHAR(1) NOT NULL ,
  `email` VARCHAR(60) NOT NULL ,
  `login` VARCHAR(45) NOT NULL ,
  `senha` VARCHAR(45) NOT NULL ,
  `cep` VARCHAR(7) NOT NULL ,
  `endereco` VARCHAR(100) NOT NULL ,
  `numero` VARCHAR(15) NULL ,
  `complemento` VARCHAR(50) NULL ,
  `bairro` VARCHAR(45) NOT NULL ,
  `cidade` VARCHAR(45) NOT NULL ,
  `estado` VARCHAR(2) NOT NULL ,
  `telefone_1` INT(12) NOT NULL ,
  `telefone_2` INT(12) NULL ,
  `referencia` VARCHAR(45) NULL ,
  PRIMARY KEY (`idcliente`) )
ENGINE = InnoDB
COMMENT = '	';


-- -----------------------------------------------------
-- Table `chico`.`pedido`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`pedido` (
  `idpedido` INT NOT NULL ,
  `data` DATETIME NULL ,
  `valor_pedido` DOUBLE NULL ,
  `valor_pago` DOUBLE NULL ,
  `troco` DOUBLE NULL ,
  `cliente_idcliente` INT NOT NULL ,
  `status_idStatus` INT NOT NULL ,
  PRIMARY KEY (`idpedido`) ,
  INDEX `fk_pedido_cliente1` (`cliente_idcliente` ASC) ,
  INDEX `fk_pedido_status1` (`status_idStatus` ASC) ,
  CONSTRAINT `fk_pedido_cliente1`
    FOREIGN KEY (`cliente_idcliente` )
    REFERENCES `chico`.`cliente` (`idcliente` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pedido_status1`
    FOREIGN KEY (`status_idStatus` )
    REFERENCES `chico`.`status` (`idStatus` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `chico`.`pedido_itens`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `chico`.`pedido_itens` (
  `pedido_idpedido` INT NOT NULL ,
  `produto_idproduto` INT NOT NULL ,
  `quantidade` VARCHAR(45) NULL ,
  INDEX `fk_pedido_itens_pedido1` (`pedido_idpedido` ASC) ,
  INDEX `fk_pedido_itens_produto1` (`produto_idproduto` ASC) ,
  CONSTRAINT `fk_pedido_itens_pedido1`
    FOREIGN KEY (`pedido_idpedido` )
    REFERENCES `chico`.`pedido` (`idpedido` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_pedido_itens_produto1`
    FOREIGN KEY (`produto_idproduto` )
    REFERENCES `chico`.`produto` (`idproduto` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;



SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
