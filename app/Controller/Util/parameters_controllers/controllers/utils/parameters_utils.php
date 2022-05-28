<?php

class ParametersUtils {

    public static function addUnbalanceEntry(&$targetDataProvider, $sourceDataProvider, $indicators) {
        $indicatorsCount = count($indicators);
        $firstIndicator = $indicators[0];
        foreach ($targetDataProvider as $i => &$obj) {
            $average = 0;
            $currentMin = abs(floatval($sourceDataProvider[$i][$firstIndicator]));
            foreach ($indicators as $n => $ind) {
                $val = abs(floatval($sourceDataProvider[$i][$ind]));
                $average += $val;
                if ($currentMin > $val)
                    $currentMin = $val;
            }
            $average /= $indicatorsCount;

            // Keep devision by cero out!!!
            ($average == 0) ? $unbalance = 0 : $unbalance = round((($average - $currentMin) / $average) * 100, 2);
            $obj["unbalance"] = $unbalance;
        }
    }

    public static function addTemperatureIncreaseEntry(&$targetDataProvider, $sourceDataProvider) {
        foreach ($targetDataProvider as $i => &$obj) {
            $unbalance = floatval($sourceDataProvider[$i]['unbalance']);
            $temperatureInc = 2*($unbalance*$unbalance);
            $obj["temperature-increase"] = round($temperatureInc, 2);
        }
    }
    
    public static function addMotorLifeDecreaseEntry(&$targetDataProvider, $sourceDataProvider) {
        foreach ($targetDataProvider as $i => &$obj) {
            $unbalance = floatval($sourceDataProvider[$i]['unbalance']);
            $temperatureInc = 2*($unbalance*$unbalance);
            
            $temperatureFinal_B = 80*(1 + $temperatureInc/100);
            $M_B = ($temperatureFinal_B - 80)/10;
            $lifeReduction_B = pow(0.5, $M_B)*100;
            $lifeReduction_Libby_B = pow(0.63, $M_B)*100;
            $obj["life-reduction-b"] = round(100 - $lifeReduction_B, 2);
            $obj["life-reduction-libby-b"] = round(100 - $lifeReduction_Libby_B, 2);
            
            $temperatureFinal_A = 60*(1 + $temperatureInc/100);
            $M_A = ($temperatureFinal_A - 60)/10;
            $lifeReduction_A = pow(0.5, $M_A)*100;
            $lifeReduction_Libby_A = pow(0.44, $M_A)*100;
            $obj["life-reduction-a"] = round(100 - $lifeReduction_A, 2);
            $obj["life-reduction-libby-a"] = round(100 - $lifeReduction_Libby_A, 2);
            
            $temperatureFinal_F = 105*(1 + $temperatureInc/100);
            $M_F = ($temperatureFinal_F - 105)/10;
            $lifeReduction_F = pow(0.5, $M_F)*100;
            $obj["life-reduction-f"] = round(100 - $lifeReduction_F, 2);
            
            $temperatureFinal_H = 125*(1 + $temperatureInc/100);
            $M_H = ($temperatureFinal_H - 125)/10;
            $lifeReduction_H = pow(0.5, $M_H)*100;
            $obj["life-reduction-h"] = round(100 - $lifeReduction_H, 2);
        }
    }

    public static function addPowerFactorNoHarmoEntry(&$targetDataProvider, $sourceDataProvider, $indicators_PF, $indicators_THD) {
        $pfNoHarmo = array();
        foreach ($targetDataProvider as $i => &$obj) {
            // TODO: Make sure that the target and source have the same number of entries (bad things can happen if they don't!!!)

            $pfNoHarmo = 0;
            foreach ($indicators_PF as $j => $ind) {
                $pf = $sourceDataProvider[$i][$indicators_PF[$j]];
                $thd = $sourceDataProvider[$i][$indicators_THD[$j]];
                $pfNoHarmo += $pf * sqrt(1 + pow($thd, 2));
            }
            $pfNoHarmo /= count($indicators_PF);

            $obj["PF_SYS_NO_HARMO"] = round($pfNoHarmo, 2);
        }
    }

}

?>
