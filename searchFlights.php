<?php
/**
 * Created by PhpStorm.
 * User: ruben
 * Date: 23/01/2018
 * Time: 15:05
 */

require('momondo.php');
require ('MySQL.php');
if($_GET){
    if(isset($_GET['teste'])){
        results();
    }elseif(isset($_GET['teste'])){
        results();
    }
}

$indexDeVoo;
$classeBilhete;
$precoTotal;
$durationIda;
$durationVolta;
$partida; //= $results[3]["Legs"][4]["Departure"];
$chegada;
function results(){
//______________________________________________Form Data____________________________________________//
    $aeroP = $_GET['aeroP'];
    $aeroD = $_GET['aeroD'];
    echo "Aeroportos : ".$aeroP." => ".$aeroD."<br>";

    $dataPartida = $_GET['dataP'];
    $dataChegada = $_GET['dataV'];
    $frase = "Datas : ".$dataPartida." ===> ".$dataChegada;
    echo $frase."<br>";

    $quantosAdultos = $_GET['passageirosA'];
    echo "Nº de Adultos : ".$quantosAdultos."<br>";

//______________________________________________Form Data____________________________________________//
    $momondo = new Momondo();

// Complete Airports
    $airport = $momondo->airport('opo');
//-----------------------------------------------------------------------------------------------//
// Search (include search_url && search_results)
    $results = $momondo->search($aeroP, $aeroD, $dataPartida, $dataChegada, $quantosAdultos, 'ECO', array());    // Children: 6 && 10 years
//-----------------------------------------------------------------------------------------------//
// 1 EUR = ? XXX
    $currency = $momondo->currency();

    $idx = 0;
    $quantosResultados=0;

    echo "<table border='1'><tr><th>Index</th><th>Aeroporto Partida</th><th>Aeroporto Chegada</th><th>Data Partida</th><th>Data Chegada</th><th>Preço</th></tr>";
    foreach ($results[2]["Offers"] as $offers) {
        $indexDeVoo = $offers["FlightIndex"];
        $classeBilhete = $offers["TicketClassIndex"];
        $precoTotal = $offers["TotalPriceEUR"];
        foreach ($currency as $moeda) {
            /*
            if($modea ==$currency[27] ){ //EURO
                $precoTotal = $precoTotal * $modea["Rate"];
            }
            elseif($modea ==$currency[80] ){//USD
                $precoTotal = $precoTotal * $modea["Rate"];
            }
            elseif($modea ==$currency[29] ){//LIBRAS
                $precoTotal = $precoTotal * $modea["Rate"];
            }
            */
        }//Money
        foreach ($results[2]["Flights"] as $voos) {
            if ($results[2]["Flights"][$indexDeVoo] == $voos) {
                $ida = $voos["SegmentIndexes"]["0"];
                $volta = $voos["SegmentIndexes"]["1"];
                foreach ($results[2]["Segments"] as $segmentos) {
                    if ($results[2]["Segments"][$ida] === $segmentos || $results[2]["Segments"][$volta] === $segmentos) {
                        $indexDeLegIda = $segmentos["LegIndexes"][0];
                        $indexDeLegVolta = $segmentos["LegIndexes"][0];
                        foreach ($results[2]["Legs"] as $dados) {
                            if ($results[2]["Legs"][$indexDeLegIda] === $dados || $results[2]["Legs"][$indexDeLegVolta] === $dados) {
                                $partida = $dados["Departure"];
                                $chegada = $dados["Arrival"];
                                if ($idx <= 20) {
                                    $resultado = $idx . " ==> " . $indexDeVoo . " => " . $classeBilhete . " => " . $precoTotal . " => " . $partida . " => " . $chegada . "\n";
                                    echo "<tr><td>" . $indexDeVoo . "</td><td>" . $aeroP . "</td><td>" . $aeroD . "</td><td>" . $partida . '</td><td>' . $chegada . '</td><td>' . $precoTotal . "€ </td></tr>" . PHP_EOL;
                                    $idx++;
                                }
                                $quantosResultados++;
                            }
                        }
                    }
                }
            }
        }
    }
    echo "</table><br><br>Pesquisas totais =======> ".$quantosResultados."<br>";
    $dataPesquisa = date("Y/m/d");
    $wd = date("l");
    $dataP = $dataPesquisa.", ".$wd;
    dbInsert($dataPartida,$dataChegada,$dataP);
}