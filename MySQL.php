<?php
/**
 * Created by PhpStorm.
 * User: NunoEsteves
 * Date: 17/01/2018
 * Time: 11:00
 */

define("MYSQL_USER", "root");
define("MYSQL_PASS", "radio2012");
define("MYSQL_HOST", "127.0.0.1");
define("MYSQL_PORT", 3306);
define("MYSQL_SCHEMA", "schema_FlightFinder");

define("VERBOSE", 1);
define (
    "MYSQL_CREATE_SCHEMA",
    //"create schema `".MYSQL_SCHEMA."`;" //backtick / acento grave deveria ser delimitador
    "create schema ".MYSQL_SCHEMA.";" //backtick / acento grave deveria ser delimitador
//"create schema schema_aca1718;"
);

define ("MYSQL_TABLE_REGISTO", "Registo");

define ("MYSQL_CREATE_TABLE_Registo",
    "CREATE TABLE `".MYSQL_SCHEMA."`.`".MYSQL_TABLE_REGISTO."` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `pDataPartida` VARCHAR(64) NULL,
  `pDataChegada` VARCHAR(512) NOT NULL,
  `DataRegisto` VARCHAR(512) NULL,
  PRIMARY KEY (`id`));
");


$resultSelect;

/*
$dataPesquisa = date("Y/m/d");
$wd = date("l");
$dataP = $dataPesquisa.", ".$wd;
*/

$INSTALL_PROCEDURE = [MYSQL_CREATE_SCHEMA,
    MYSQL_CREATE_TABLE_Registo];

function dbInstaller($pDB, $pInstallProcedure){
    $errosAdmissiveis = [0,1007,1050];
    if($pDB){
        foreach ($pInstallProcedure as $i){
            $result = $pDB->query($i);
            $e = mysqli_errno($pDB);
            $eM = mysqli_error($pDB);

            $bErroAdmissivel = array_search($e,$errosAdmissiveis)!==false;
            if(VERBOSE && !$bErroAdmissivel){
                fb(
                    __FUNCTION__,
                    $i,
                    $e,
                    $eM
                );
            }
        }
    }
}

function fb (
    $pQuemFazChamada,
    $pMsg,
    $pErro,
    $pMsgErro
){
    $msg = sprintf("caller; %s\nmsg: %s\ne: %d\nEm: %s\n\n",
        $pQuemFazChamada,
        $pMsg,
        $pErro,
        $pMsgErro);
    ob_end_flush();
    //echo $msg;
    ob_start();
}

function dbConnect(
    $pHost = MYSQL_HOST,
    $pUser = MYSQL_USER,
    $pPwd = MYSQL_PASS,
    $pPort = MYSQL_PORT
)
{
    $db = mysqli_connect($pHost,$pUser,$pPwd);
    $e = mysqli_connect_errno();
    $eM = mysqli_connect_error();

    if(VERBOSE){
        fb(__FUNCTION__,
            $msg = "",
            $e,
            $eM);
    }
    return $e === 0 ? $db : false;
}

function dbInsert(

    $pDataPartida,
    $pDataChegada,
    $dataP
){
        //$pDataPartida($pDataPartida === false) ?date("Y-m-d"): $pDataPartida;
        //$pDataChegada($pDataChegada === false) ?date("Y-m-d"): $pDataChegada;
    $db = dbConnect();
        $q = "insert into " .MYSQL_SCHEMA. "." .MYSQL_TABLE_REGISTO. " 
        VALUES(null,'$pDataPartida','$pDataChegada','$dataP')";
        if($db){
            $result=$db->query($q);
            $e = mysqli_errno($db);
            $eM = mysqli_connect_error($db);
            if(VERBOSE){
                fb(
                    __FUNCTION__,
                    $q,
                    $e,
                    $eM
                );
            }
            return $result;
        }
    return false;
}
$db = dbConnect();
$resultadoTrueEmSucessoFalseEmFailure = dbInstaller($db,$INSTALL_PROCEDURE);

function SelectAllReg ()
{
    $db = dbConnect();
    $q = " SELECT * FROM " .MYSQL_SCHEMA. "." .MYSQL_TABLE_REGISTO. "";
    $result = $db ->query($q);
    if ($result->num_rows > 0) {

        while ($row = $result->fetch_assoc())
        {

            echo "Id: ".$row["id"].PHP_EOL." DataPartida: " .$row["pDataPartida"].PHP_EOL. " DataChegada: " .$row["pDataChegada"].PHP_EOL. " DataRegisto: ".$row["DataRegisto"].PHP_EOL;
        }
    }
    else
        echo "0 Resultados";
}



$resultadoTrueEmSucessoFalseEmFailure = dbInstaller(
    $db,
    $INSTALL_PROCEDURE
);

//dbInsert($db,"2017/02/03","2017/05/20",$dataP);
//echo SelectAllReg() ;
$db->close();


