<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CovidBackController extends Controller
{
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
    }

   
    function covid19ImpactEstimator($data){
   
        //Question 1
      $Impact_currentlyInfected = $data['reportedCases'] *10;
    
      $SevereImpact_currentlyInfected = $data['reportedCases']*50;
    
     
    
           //Question 2 answered .....
      function infectionsByRequestedTimeFactor ($dayss , $InfectedMultiplier ){
     
         $calFactorial = floor($dayss / 3) ;
        
        return $InfectedMultiplier * pow(2,$calFactorial);
        }
    
        switch ($data["periodType"]) {
          case 'days':
            $Impact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor($data["timeToElapse"], $Impact_currentlyInfected));
            break;
          case 'weeks':
            $Impact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor(($data["timeToElapse"] * 7), $Impact_currentlyInfected));
            break;
          case 'months':
            $Impact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor(($data["timeToElapse"] * 7 * 30), $Impact_currentlyInfected));
            break;
    
          default:
            $Impact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor($data["timeToElapse"], $Impact_currentlyInfected));
            break;
        }
    
        switch ($data["periodType"]) {
          case 'days':
            $SevereImpact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor($data["timeToElapse"], $SevereImpact_currentlyInfected));
            break;
          case 'weeks':
            $SevereImpact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor(($data["timeToElapse"] * 7), $SevereImpact_currentlyInfected));
            break;
          case 'months':
            $SevereImpact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor(($data["timeToElapse"] * 7 * 30), $SevereImpact_currentlyInfected));
            break;
    
          default:
            $SevereImpact_infectionsByRequestedTime =floor(infectionsByRequestedTimeFactor($data["timeToElapse"], $SevereImpact_currentlyInfected));
            break;
        }
    
    
        $Severe_severeCasesByRequestedTime=floor( 0.15 * $SevereImpact_infectionsByRequestedTime);
        $Impact_severeCasesByRequestedTime = floor(0.15 * $Impact_infectionsByRequestedTime);
        $Impact_hospitalBedsByRequestedTime = floor($data["totalHospitalBeds"] * 0.35 ) +1- $Impact_severeCasesByRequestedTime;
        $Severe_hospitalBedsByRequestedTime = floor($data["totalHospitalBeds"] * 0.35 )+1 - $Severe_severeCasesByRequestedTime;
            //Question 3
    
        $Impact_casesForICUByRequestedTime = floor($Impact_infectionsByRequestedTime *0.05);
        $Severe_casesForICUByRequestedTime = floor($SevereImpact_infectionsByRequestedTime * 0.05);
    
        $Severe_casesForVentilatorsByRequestedTime = floor($SevereImpact_infectionsByRequestedTime *0.02);
        $Impact_casesForVentilatorsByRequestedTime =floor($Impact_infectionsByRequestedTime * 0.02);
    
        $Impact_dollarsInFlight = floor(($Impact_infectionsByRequestedTime* $data["region"]["avgDailyIncomeInUSD"]* $data["region"]["avgDailyIncomePopulation"])/30);
        $Severe_dollarsInFlight = floor(($SevereImpact_infectionsByRequestedTime * $data["region"]["avgDailyIncomeInUSD"]* $data["region"]["avgDailyIncomePopulation"])/30);
    
    
        $Jresult =  [ 'data' => $data , 'impact'=> [ 'currentlyInfected' => $Impact_currentlyInfected ,'infectionsByRequestedTime'=>
          $Impact_infectionsByRequestedTime,'severeCasesByRequestedTime'=> $Impact_severeCasesByRequestedTime,'hospitalBedsByRequestedTime'=>$Impact_hospitalBedsByRequestedTime,'casesForICUByRequestedTime'=> $Impact_casesForICUByRequestedTime,'casesForVentilatorsByRequestedTime'=>$Impact_casesForVentilatorsByRequestedTime, 'dollarsInFlight'=>$Impact_dollarsInFlight ],'severeImpact' =>['currentlyInfected'=>$SevereImpact_currentlyInfected ,
              'infectionsByRequestedTime'=>$SevereImpact_infectionsByRequestedTime ,'severeCasesByRequestedTime'=> $Severe_severeCasesByRequestedTime,'hospitalBedsByRequestedTime' =>$Severe_hospitalBedsByRequestedTime,'casesForICUByRequestedTime'=>$Severe_casesForICUByRequestedTime,'casesForVentilatorsByRequestedTime'=>$Severe_casesForVentilatorsByRequestedTime,'dollarsInFlight' =>$Severe_dollarsInFlight]];
    
        return $Jresult ;
    }
}
